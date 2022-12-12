<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use Illuminate\Support\Facades\{Auth, DB};
use App\Http\Requests\Api\General\{PaginationRequest};
use App\Http\Resources\v1\{SubscriptionPlanResource, RazorPayOrderResource};
use App\Models\{User,SubscriptionPlan, Subscription, Transaction};
use App\Jobs\{SubscriptionPurchasedJob};
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
            'plan_id'   =>  'required|in:' . implode(',', $plan_ids),
        ];

        if ($this->apiValidator($request->all(), $rules)) {
            try {
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
                    'receipt'         =>    'receipt_' . $user->custom_id . '_' . $time,
                    'amount'          =>    $amount * 100, // 2000 * 100  = 2000 rupees in paise
                    'currency'        =>    $currency,
                    'payment_capture' =>    $partial_payment // auto capture
                ];

                $razorpayOrder = $api->order->create($orderData);

                $new_subscription_start_date = \Carbon\Carbon::today()->format('Y-m-d');
                if ($user->subscription_end_date >= $new_subscription_start_date) {
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
                    'payment_type'  =>  'android',
                    'status'        =>  'incomplete',
                ], [
                    'custom_id'     =>  getUniqueString('subscriptions'),
                ]);

                $razorpayOrder['subscription'] = $subscription;

                return (new RazorPayOrderResource($razorpayOrder))
                    ->additional([
                        'meta' => [
                            'message'   =>  trans('api.razorpay.order.success'),
                            'is_ban'    =>  false,
                        ]
                    ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\SubscriptionPlan':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Subscription plan")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->response['meta']['is_ban'] = false;
                $this->storeErrorLog($e, 'razorpay_create_order');
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
            'subscription_id'       =>  'required|in:' . implode(',', $subscription_ids),
            'razorpay_order_id'     =>  'required',
            'razorpay_payment_id'   =>  'required',
            'razorpay_signature'    =>  'required',
        ];

        if ($this->apiValidator($request->all(), $rules)) {
            $user = $request->user();

            DB::beginTransaction();
            try {
                $keyId      =   config('utility.razorpay.api_key');
                $keySecret  =   config('utility.razorpay.api_secret');
                $api        =   new Api($keyId, $keySecret);

                $subscription = Subscription::with('subscriptionPlan')->whereUserId(Auth::id())->whereCustomId($request->subscription_id)->firstOrFail();
                if ($subscription->subscriptionPlan) {

                    $transaction =  Transaction::create([
                        'custom_id'             =>  getUniqueString('transactions'),
                        'user_id'               =>  $user->id ?? NULL,
                        'plan_id'               =>  $subscription->subscriptionPlan->id ?? NULL,
                        'subscription_id'       =>  $subscription->id ?? NULL,
                        'payment_type'          =>  'android',
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

                    // Email
                    $subscriptionPurchasedJob = new SubscriptionPurchasedJob($user);
                    dispatch($subscriptionPurchasedJob);

                    // Notify
                    $subscription->notifySubScriptionPurchase('success');

                    $subscription_type = 'new'; // New Purchase
                    $renew_count = Subscription::withTrashed()->whereUserId($user->id)
                        ->whereNotIn('status', ['incomplete', 'incomplete_expired', 'unpaid'])->count();
                    if ($renew_count > 0) {
                        $subscription_type = 'renew';   // Renew Subscription
                    }
                    $subscription->sendSubScriptionPurchaseSMS($subscription_type);

                    // Add Payment success log
                    $transaction_data = json_decode($transaction, true);
                    $file = 'payment_' . $user->id;
                    $paymentLog = new Logger($file);
                    $paymentLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::INFO);
                    $paymentLog->info($file, ['success' => $transaction_data]);

                    $this->status = Response::HTTP_OK;
                    $this->response['data']['status'] = $subscription->status;
                    $this->response['meta']['message'] = trans('api.razorpay.verify_signature.success');
                    $this->response['meta']['is_ban'] = false;
                    return $this->returnResponse();
                } else {
                    $this->status = Response::HTTP_NOT_FOUND;
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Subscription plan')]);
                    $this->response['meta']['is_ban'] = false;
                    return $this->returnResponse();
                }
            } catch (SignatureVerificationError $e) {
                DB::rollback();

                $user->is_subscribed = $user->subscription_end_date >= \Carbon\Carbon::now()->format('Y-m-d') ? 'n' : $user->is_subscribed;
                $user->subscription_end_date = $user->subscription_end_date >= \Carbon\Carbon::now()->format('Y-m-d')
                    ? NULL
                    : $user->subscription_end_date;
                $user->save();
                if ($subscription) {
                    $subscription->update(['payment_date' => NULL, 'status' => 'unpaid']);
                    $subscription->save();

                    // Notify
                    $subscription->notifySubScriptionPurchase('fail');
                }
                if ($transaction) {
                    $transaction->update(['status' => 'fail']);
                    $transaction->save();
                }

                $file = 'payment_' . $user->id;
                $this->storeErrorLog($e, $file, $e->getMessage());
                
            }
        }
        return $this->returnResponse();
    }

    /**
     * Get Subscription Plans Details
     */
    public function getSubscriptionPlans(Request $request)
    {
        $paginationRequest = new PaginationRequest();
        if ($this->apiValidator($request->all(), $paginationRequest->rules())) {
            try {
                $subscription_plans = SubscriptionPlan::with('subscriptionPlanTranslation')->whereIsActive('y');

                $count = $subscription_plans->count();
                $subscription_plans = $subscription_plans->limit($request->limit ?? config('utility.pagination.limit'))
                    ->offset($request->offset ?? config('utility.pagination.offset'))
                    ->get();

                if ($subscription_plans->isNotEmpty()) {
                    return (SubscriptionPlanResource::collection($subscription_plans))
                        ->additional([
                            'meta' => [
                                'limit'     =>  $request->limit,
                                'offset'    =>  $request->offset,
                                'total'     =>  $count,
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'is_ban'    =>  false,
                                'message'   =>  trans('api.list', ['entity' => __('Subscription plans')]),
                            ]
                        ]);
                } else {
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Subscription plans')]);
                    $this->response['meta']['is_ban'] = false;
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\SubscriptionPlan':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Subscription plans")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'get_subscription_plans');
            }
        }
        return $this->returnResponse();
    }

    public function create_order(Request $request)
    {
        $plan_ids = SubscriptionPlan::whereIsActive('y')->pluck('custom_id')->toArray();
        $rules = [
            'plan_id'   =>  'required|in:' . implode(',', $plan_ids),
        ];

        if ($this->apiValidator($request->all(), $rules)) {
            try {
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
                    'receipt'         =>    'receipt_' . $user->custom_id . '_' . $time,
                    'amount'          =>    $amount * 100, // 2000 * 100  = 2000 rupees in paise
                    'currency'        =>    $currency,
                    'payment_capture' =>    $partial_payment // auto capture
                ];

                $razorpayOrder = $api->order->create($orderData);

                $new_subscription_start_date = \Carbon\Carbon::today()->format('Y-m-d');
                if ($user->subscription_end_date >= $new_subscription_start_date) {
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
                    'payment_type'  =>  'android',
                    'status'        =>  'incomplete',
                ], [
                    'custom_id'     =>  getUniqueString('subscriptions'),
                ]);

                $razorpayOrder['subscription'] = $subscription;

                return (new RazorPayOrderResource($razorpayOrder))
                    ->additional([
                        'meta' => [
                            'message'   =>  trans('api.razorpay.order.success'),
                            'is_ban'    =>  false,
                        ]
                    ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\SubscriptionPlan':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Subscription plan")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->response['meta']['is_ban'] = false;
                $this->storeErrorLog($e, 'razorpay_create_order');
            }
        }
        return $this->returnResponse();
    }

    public function instamojo_pay(Request $request){
        $rules = [
            'plan_id'   =>  'required',
        ];

        if ($this->apiValidator($request->all(), $rules)) {
            try {
                $user = $request->user();
                $matches = User::with('userTranslation:id')
                    ->where('id', '=', $user->id)
                    ->first();
                // echo "<pre>"; print_r($matches->full_name); die();
                // echo "<pre>"; print_r($matches->toArray()); die();
                // echo "<pre>"; print_r($matches->id); die();
                $plan = SubscriptionPlan::whereCustomId($request->plan_id)->whereIsActive('y')->firstOrFail();
                // echo "<pre>"; print_r($plan); die();
                $access_token = $this->generate_access_token();
                // echo $access_token; die();

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://test.instamojo.com/v2/payment_requests/');
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER,array('Authorization: Bearer '.$access_token));

                $payload = array(
                  'purpose' => $plan->months.' month subscription plan',
                  'amount' => $plan->amount,
                  'buyer_name' => isset($matches->full_name) ? $matches->full_name : '',
                  'email' => isset($user->email) ? $user->email : '',
                  'phone' => isset($user->contact_no) ? $user->contact_no : '',
                  'redirect_url' => 'https://localhost/api-and-admin-laravel/',
                  'send_email' => 'True',
                  'send_sms' => 'True',
                  'allow_repeated_payments' => 'False',
                );
                // echo "<pre>"; print_r($payload); die();
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                $response_ch = curl_exec($ch);
                curl_close($ch); 

                $result_api = json_decode($response_ch);
                // echo "<pre>"; print_r($result_api); die();
                // return redirect($result_api->longurl);
                // $this->status = Response::HTTP_OK;
                // $this->response['data']['payment_url'] = $result_api->longurl;
                // $this->response['meta']['message'] = "success";
                // $this->response['meta']['is_ban'] = false;
                // return $this->returnResponse();

                $this->status = Response::HTTP_OK;
                $this->response['data']['longurl'] = isset($result_api->longurl) ? $result_api->longurl : '';
                $this->response['meta']['message'] = $result_api->message;
                $this->response['meta']['is_ban'] = false;
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\SubscriptionPlan':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Subscription plan")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->response['meta']['is_ban'] = false;
                $this->storeErrorLog($e, 'razorpay_create_order');
            }
        }
        return $this->returnResponse();
    }

    public function update_payment_status(Request $request){
        $rules = [
            'plan_id'   =>  'required',
            'order_id'   =>  'required',
            'razorpay_signature'   =>  'required',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {
                $user = $request->user();
                $today_date = date('Y-m-d');
                // echo "<pre>"; print_r($user->toArray()); die();
                if ($user->is_subscribed == 'y' && !empty($user->subscription_end_date) && $today_date < $user->subscription_end_date) {
                    $this->status = Response::HTTP_NOT_FOUND;
                    $this->response['meta']['message']  =   "The subscription is still valid till ".$user->subscription_end_date;
                    $this->response['meta']['is_ban'] = false;
                    return $this->returnResponse();
                }
                else
                {
                    $user->is_subscribed = 'n';
                    $user->subscription_end_date = NULL;
                    $user->save();

                    $plan = SubscriptionPlan::whereCustomId($request->plan_id)->whereIsActive('y')->firstOrFail();
                    // echo "<pre>"; print_r($plan->toArray()); die();
                    // DB::beginTransaction();
                    if ($plan) {

                        $new_sub_start_date = \Carbon\Carbon::now()->format('Y-m-d');
                        // if ($user->subscription_end_date >= $new_sub_start_date) {
                        //     $new_sub_start_date = $user->subscription_end_date;
                        // }
                        $new_sub_end_date = !empty($user->subscription_end_date)
                            ? \Carbon\Carbon::parse($new_sub_end_date)->addDays($plan->day)->format('Y-m-d')
                        : \Carbon\Carbon::now()->addDays($plan->day)->format('Y-m-d');


                        $new_subscription_start_date = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                        if ($user->subscription_end_date >= $new_subscription_start_date) {
                            $new_subscription_start_date = $user->subscription_end_date;
                        }

                        $subscription_end_date = !empty($user->subscription_end_date)
                            ? \Carbon\Carbon::parse($new_subscription_start_date)->addDays($plan->day)->format('Y-m-d H:i:s')
                        : \Carbon\Carbon::now()->addDays($plan->day)->format('Y-m-d H:i:s');

                        $subscription =  Subscription::firstOrCreate([
                            'user_id'       =>  $user->id ?? NULL,
                            'custom_id'     =>  $user->custom_id ?? NULL,
                            'plan_id'       =>  $plan->id ?? NULL,
                            'months'        =>  $plan->months,
                            'day'           =>  $plan->day,
                            'amount'        =>  $plan->amount,
                            'start_date'    =>  $new_sub_start_date,
                            'end_date'      =>  $new_sub_end_date,
                            'payment_date'  =>  NULL,
                            'payment_type'  =>  'android',
                            'status'        =>  'active',
                        ]);

                        $transaction =  Transaction::firstOrCreate([
                            'custom_id'             =>  $user->custom_id,
                            'user_id'               =>  $user->id ?? NULL,
                            'plan_id'               =>  $plan->id ?? NULL,
                            'subscription_id'       =>  $subscription->id,
                            'payment_type'          =>  'android',
                            'razorpay_order_id'     =>  $request->order_id,
                            'razorpay_signature'    =>  $request->razorpay_signature,
                            'amount'                =>  $plan->amount,
                            'purchase_date'         =>  $new_subscription_start_date,
                            'original_purchase_date'=>  $new_subscription_start_date,
                            'subscription_end_date' =>  $subscription_end_date,
                            'in_app_ownership_type' =>  'PURCHASED',
                            'status'                =>  'success',
                        ]);
                        $user->is_subscribed = 'y';
                        $user->subscription_end_date = $subscription_end_date;
                        $user->save();

                        $this->status = Response::HTTP_OK;
                        $this->response['data']['status'] = 'active';
                        $this->response['meta']['message'] = trans('api.razorpay.verify_signature.success');
                        $this->response['meta']['is_ban'] = false;
                        return $this->returnResponse();
                    } else {
                        $this->status = Response::HTTP_NOT_FOUND;
                        $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Subscription plan')]);
                        $this->response['meta']['is_ban'] = false;
                        return $this->returnResponse();
                    }
                }

            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\SubscriptionPlan':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Subscription plan")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->response['meta']['is_ban'] = false;
                $this->storeErrorLog($e, 'razorpay_create_order');
            }
        }
        return $this->returnResponse();
    }

    public function generate_access_token()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://test.instamojo.com/oauth2/token/');     
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $payload = Array(
            'grant_type' => 'client_credentials',
            'client_id' => 'test_td6wIza0rhNmktBQrUPIlK6roekMNdIMOfD',
            'client_secret' => 'test_GW0fwbDwe8pny3ui9zkoNMDxJNeDgxH7UiEYhQQB6aIgTWEBStWRm5RnJE5t4qMka5XsY2YEcqitYP9Dp1R9634nYDsNWphqMfHrfmtb7tcPDCyNu5ytpm4G32z'
          );

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch); 
        $result = json_decode($response);

        return $result->access_token;
        // echo "<pre>"; print_r($result);
    }
}
