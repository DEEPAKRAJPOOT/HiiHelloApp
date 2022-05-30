<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use App\Http\Requests\Api\General\ { PaginationRequest };
use App\Http\Resources\v1\ { SubscriptionPlanResource, RazorPayOrderResource };
use App\Models\ { SubscriptionPlan };
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class PaymentController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    /**
     * We create an razorpay order using orders api
     * Docs: https://docs.razorpay.com/docs/orders
     * 
     */
    public function createOrder(Request $request)
    {
        $plan_ids = SubscriptionPlan::whereIsActive('y')->pluck('custom_id')->toArray();
        $rules = [
            'plan_id'   =>  'required|in:'.implode(',', $plan_ids),
        ];

        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = $request->user();
                $plan = SubscriptionPlan::whereCustomId($request->plan_id)->whereIsActive('y')->firstOrFail();

                $keyId              =   config('utility.razorpay.api_key');
                $keySecret          =   config('utility.razorpay.api_secret');
                $partial_payment    =   config('utility.razorpay.partial_payment');
                $currency           =   config('utility.razorpay.currency');
                $time               =   \Carbon\Carbon::now()->timestamp;
                $amount             =   $plan->amount;

                // Create the Razorpay Order
                $api = new Api($keyId, $keySecret);

                $orderData = [
                    'receipt'         =>    'receipt_'.$user->custom_id.'_'.$time,
                    'amount'          =>    $amount * 100, // 2000 * 100  = 2000 rupees in paise
                    'currency'        =>    $currency,
                    'payment_capture' =>    $partial_payment // auto capture
                ];
                
                $razorpayOrder = $api->order->create($orderData);

                return (new RazorPayOrderResource($razorpayOrder))
                    ->additional([
                        'meta' => [
                            'message'   =>  trans('api.razorpay.order.success'), 
                        ] ]);
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\SubscriptionPlan':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Subscription plan")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            }catch (\Exception $e) {
                $this->storeErrorLog($e,'razorpay_create_order');
            }
        }
        return $this->returnResponse();
    }

    /**
     * Please note that the razorpay order ID must
     * come from a trusted source (this could be database or something else)
     */ 
    public function verifySignature(Request $request)
    {
        $rules = [
            'razorpay_order_id'     =>  'required',
            'razorpay_payment_id'   =>  'required',
            'razorpay_signature'    =>  'required',
        ];

        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $keyId      =   config('utility.razorpay.api_key');
                $keySecret  =   config('utility.razorpay.api_secret');

                $api = new Api($keyId, $keySecret);

                $attributes = array(
                    'razorpay_order_id'     =>  $request->razorpay_order_id,
                    'razorpay_payment_id'   =>  $request->razorpay_payment_id,
                    'razorpay_signature'    =>  $request->razorpay_signature,
                );  

                $api->utility->verifyPaymentSignature($attributes);

                $this->status = Response::HTTP_OK;
                $this->response['meta']['message'] = trans('api.razorpay.verify_signature.success');
                return $this->returnResponse();    

            }catch(SignatureVerificationError $e){
                $this->storeErrorLog($e,'razorpay_verify_signature',$e->getMessage());
            }catch (\Exception $e) {
                $this->storeErrorLog($e,'razorpay_verify_signature');
            }
        }
        return $this->returnResponse();
    }

    /**
     * Get Subscription Plans Details
     */
    public function getSubscriptionPlans(Request $request)
    {
        $rules = PaginationRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $subscription_plans = SubscriptionPlan::with('subscriptionPlanTranslation')->whereIsActive('y')->get();

                if($subscription_plans->isNotEmpty()){
                    return (SubscriptionPlanResource::collection($subscription_plans))
                    ->additional([
                        'meta' => [
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' => __('Subscription plans')]),
                        ] ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Subscription plans')]); 
                    $this->status = Response::HTTP_NOT_FOUND;     
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\SubscriptionPlan':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Subscription plans")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'get_subscription_plans');
            }
        }
        return $this->returnResponse();
    }
}
