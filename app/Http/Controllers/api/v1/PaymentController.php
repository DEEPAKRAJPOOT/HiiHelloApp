<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use Illuminate\Support\Facades\ { Auth, DB };
use App\Http\Requests\Api\General\ { PaginationRequest };
use App\Http\Resources\v1\ { SubscriptionPlanResource, RazorPayOrderResource };
use App\Models\ { SubscriptionPlan, Subscription, Transaction };
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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

                $new_subscription_start_date = \Carbon\Carbon::today()->format('Y-m-d');
                if( $user->subscription_end_date >= $new_subscription_start_date ) {
                    $new_subscription_start_date = $user->subscription_end_date;
                }

                $subscription_end_date = !empty($user->subscription_end_date)
                                            ? \Carbon\Carbon::parse($new_subscription_start_date)->addMonth($plan->months)->format('Y-m-d')
                                            : \Carbon\Carbon::today()->addMonth($plan->months)->format('Y-m-d');

                $subscription =  Subscription::firstOrCreate([
                    'user_id'       =>  $user->id ?? NULL,
                    'plan_id'       =>  $plan->id ?? NULL,
                    'months'        =>  $plan->months,
                    'amount'        =>  $plan->amount,
                    'start_date'    =>  $new_subscription_start_date,
                    'end_date'      =>  $subscription_end_date,
                    'payment_date'  =>  NULL,
                    'status'        =>  'incomplete',
                ],[
                    'custom_id'     =>  getUniqueString('subscriptions'),
                ]);

                $razorpayOrder['subscription'] = $subscription;

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
        $subscription_ids = Subscription::whereUserId(Auth::id())->whereNull('payment_date')->pluck('custom_id')->toArray();

        $rules = [
            'subscription_id'       =>  'required|in:'.implode(',', $subscription_ids),
            'razorpay_order_id'     =>  'required',
            'razorpay_payment_id'   =>  'required',
            'razorpay_signature'    =>  'required',
        ];

        if( $this->apiValidator($request->all(), $rules) ) {
            $user = $request->user();

            DB::beginTransaction();
            try{
                $keyId      =   config('utility.razorpay.api_key');
                $keySecret  =   config('utility.razorpay.api_secret');
                $api        =   new Api($keyId, $keySecret);

                $subscription = Subscription::with('subscriptionPlan')->whereUserId(Auth::id())->whereCustomId($request->subscription_id)->firstOrFail();
                if($subscription->subscriptionPlan){
                   
                    $transaction =  Transaction::create([
                        'custom_id'             =>  getUniqueString('transactions'),
                        'user_id'               =>  $user->id ?? NULL,
                        'plan_id'               =>  $subscription->subscriptionPlan->id ?? NULL,
                        'subscription_id'       =>  $subscription->id ?? NULL,
                        'razorpay_order_id'     =>  $request->razorpay_order_id,
                        'razorpay_payment_id'   =>  $request->razorpay_payment_id,
                        'razorpay_signature'    =>  $request->razorpay_signature,
                        'amount'                =>  $subscription->amount,
                        'status'                =>  'pending',
                    ]);
                    DB::commit();

                    $attributes = array(
                        'razorpay_order_id'     =>  $request->razorpay_order_id,
                        'razorpay_payment_id'   =>  $request->razorpay_payment_id,
                        'razorpay_signature'    =>  $request->razorpay_signature,
                    );  
                    // if signature is verified (Payment Success)
                    $api->utility->verifyPaymentSignature($attributes);

                    $subscription->update(['payment_date' => now(), 'status' => 'active']);
                    $subscription->save();

                    $transaction->update(['status' => 'success']);
                    $transaction->save();

                    $user->is_subscribed = 'y';
                    $user->subscription_end_date = $subscription->end_date;
                    $user->save();

                    DB::commit();

                    // Notify
                    $subscription->notifySubScriptionPurchase('success');

                    // Add Payment success log
                    $transaction_data = json_decode($transaction, true);
                    $file = 'payment_' . $user->id;
                    $paymentLog = new Logger($file);
                    $paymentLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::INFO);
                    $paymentLog->info($file, ['success' => $transaction_data]);

                    $this->status = Response::HTTP_OK;
                    $this->response['meta']['message'] = trans('api.razorpay.verify_signature.success');
                    return $this->returnResponse();    
                }else{
                    $this->status = Response::HTTP_NOT_FOUND;  
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Subscription plan')]); 
                    return $this->returnResponse();  
                }

            }catch(SignatureVerificationError $e){
                DB::rollback();
                
                $user->is_subscribed = $user->subscription_end_date >= \Carbon\Carbon::now()->format('Y-m-d') ? 'n' : $user->is_subscribed;
                $user->subscription_end_date = $user->subscription_end_date >= \Carbon\Carbon::now()->format('Y-m-d')
                                                    ? NULL
                                                    : $user->subscription_end_date;
                $user->save();
                if($subscription){
                    $subscription->update(['payment_date' => NULL, 'status' => 'unpaid']);
                    $subscription->save();

                    // Notify
                    $subscription->notifySubScriptionPurchase('fail');
                }
                if($transaction){
                    $transaction->update(['status' => 'fail']);
                    $transaction->save();
                }

                $file = 'payment_' . $user->id;
                $this->storeErrorLog($e,$file,$e->getMessage());
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
                $subscription_plans = SubscriptionPlan::with('subscriptionPlanTranslation')->whereIsActive('y');
                
                $count = $subscription_plans->count();
                $subscription_plans = $subscription_plans->limit($request->limit ?? config('utility.pagination.limit'))
                            ->offset($request->offset ?? config('utility.pagination.offset'))
                            ->get();

                if($subscription_plans->isNotEmpty()){
                    return (SubscriptionPlanResource::collection($subscription_plans))
                    ->additional([
                        'meta' => [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
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
