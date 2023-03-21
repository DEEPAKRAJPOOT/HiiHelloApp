<?php

namespace App\Http\Controllers\api\v1;

use App\Models\User;
use App\Models\ApiLogs;
use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Classes\Payment\HDFCClass;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\PaymentHDFCResource;
use App\Http\Requests\Api\Payment\HDFCWebhookRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Api\Payment\HDFCInitTransactionRequest;
use App\Http\Requests\Api\Payment\HDFCTransactionStatusRequest;
use App\Http\Resources\v1\PaymentHDFCTransactionStatusResource;

class PaymentHDFCController extends Controller
{
    private $version = "v.1.0";

    public function getVersion()
    {
        return $this->version;
    }
    public function getAuthUser()
    {
        return auth('sanctum')->user();
    }

    public function initTransaction(Request $request)
    {
        $init_transaction_request = new HDFCInitTransactionRequest();

        if ($this->apiValidator($request->all(), $init_transaction_request->rules())) {
            try {
                $user = $request->user();
                $plan = SubscriptionPlan::whereCustomId($request->plan_id)->whereIsActive('y')->firstOrFail();

                $amount             =   $plan->amount;
                $orderData = [
                    'user_id'         =>    $user->custom_id,
                    'amount'          =>    $plan->amount,
                    'plan_custom_id'          =>    $plan->custom_id,
                    'plan_id'          =>    $plan->id,
                    'transaction_id' => generateTransactionId(),
                    'description' =>     "{$user->custom_id} {$amount} with plan id {$plan->custom_id}",
                ];


                $hdfc = new HDFCClass();
                $init_transaction = $hdfc->createTransactionRequest($orderData);
                $init_transaction['plan_name'] = $plan->android_product;
                return (new PaymentHDFCResource($init_transaction))
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
                dd($e);
                $this->response['meta']['is_ban'] = false;
                $this->storeErrorLog($e, 'error_hdfc_init_trans');
            }
        }
        return $this->returnResponse();
    }


    public function transactionStatus(Request $request)
    {

        $transaction_request = new HDFCTransactionStatusRequest();

        if ($this->apiValidator($request->all(), $transaction_request->rules())) {
            try {
                $user = $request->user();
                $transaction = Transaction::select('razorpay_order_id', 'status')
                    ->where('razorpay_order_id', $request->transaction_id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();

                // TODO: Add this on production ( && $transaction->status === "pending")
                if ($transaction) {
                    $hdfc = new HDFCClass();
                    $trans_status = $hdfc->getTransactionStatus($request->all());
                    $transaction->status = $trans_status['mapped_response']['status'];
                    $transaction->save();
                    $this->customLogger($trans_status, 'hdfc_trans_status');
                }

                return (new PaymentHDFCTransactionStatusResource($transaction))
                    ->additional([
                        'meta' => [
                            'message'   =>  trans('api.razorpay.order.success'),
                            'is_ban'    =>  false,
                            'is_subscribed' => $user->is_subscribed === 'y',
                            'subscription_end_date' => $user->subscription_end_date
                        ]
                    ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Transaction':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Transaction")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->response['meta']['is_ban'] = false;
                $this->storeErrorLog($e, 'error_hdfc_trans_status');
            }
        }
        return $this->returnResponse();
    }

    public function webhookCallback(Request $request)
    {
        $webhook_request = new HDFCWebhookRequest();
        $api_logs = new ApiLogs();
        $api_logs->url = url()->current();
      
        if ($this->apiValidator($request->all(), $webhook_request->rules())) {
            try {
                $this->customLogger($request->all(), 'hdfc_webhook');

                $hdfc = new HDFCClass();
                $parsedData = $hdfc->parseHDFCCallback($request->meRes);
                $hdfc_data = [
                    'raw' => $request->all(),
                    'mapped' => $parsedData
                ];
                $this->customLogger($hdfc_data, 'hdfc_webhook');
                $user = User::whereCustomId($parsedData['user_id'])->firstOrFail();
                $plan = SubscriptionPlan::whereCustomId($parsedData['plan_id'])->whereIsActive('y')->firstOrFail();
                

                $new_sub_start_date = \Carbon\Carbon::now()->format('Y-m-d');

                $new_sub_end_date = !empty($user->subscription_end_date)
                    ? \Carbon\Carbon::parse($user->subscription_end_date)->addDays($plan->day)->format('Y-m-d')
                    : \Carbon\Carbon::now()->addDays($plan->day)->format('Y-m-d');

                $date_format = 'Y-m-d H:i:s';
                $new_subscription_start_date = \Carbon\Carbon::now()->format($date_format);
                if ($user->subscription_end_date >= $new_subscription_start_date) {
                    $new_subscription_start_date = $user->subscription_end_date;
                }

                $subscription_end_date = !empty($user->subscription_end_date)
                    ? \Carbon\Carbon::parse($new_subscription_start_date)->addDays($plan->day)->format($date_format)
                    : \Carbon\Carbon::now()->addDays($plan->day)->format($date_format);

                $transaction = Transaction::where('razorpay_order_id', $parsedData['transaction_id'])->first();

                $api_logs->request = json_encode($hdfc_data);
                $api_logs->api_status = 200;
                $api_logs->save();

                if ($transaction && $transaction->status === 'success') {
                    return response()->json(['statusCode' => 200, 'exists' => true]);
                }

                if ($transaction) {
                    $transaction->status =  $parsedData['status'];
                    $transaction->save();
                    return response()->json(['statusCode' => 200, 'exists' => true]);
                }

                $subscription = null;
                if ($parsedData['status'] === 'success') {
                    $subscription =  Subscription::firstOrCreate([
                        'user_id'       =>  $user->id ?? null,
                        'custom_id'     =>  getUniqueString('subscriptions'),
                        'plan_id'       =>  $plan->id ?? null,
                        'months'        =>  $plan->months,
                        'original_transaction_id'        =>  $parsedData['transaction_id'],
                        'day'           =>  $plan->day,
                        'amount'        =>  $parsedData['amount'],
                        'start_date'    =>  $new_sub_start_date,
                        'end_date'      =>  $new_sub_end_date,
                        'payment_date'  =>  null,

                        'payment_type'  =>   'UPI',

                        'status'        =>  'active',
                    ]);
                    $user->is_subscribed = 'y';
                    $user->subscription_end_date = $subscription_end_date;
                    $user->save();
                }

                Transaction::firstOrCreate([
                    'custom_id'             =>  getUniqueString('transactions'),
                    'user_id'               =>  $user->id ?? null,
                    'plan_id'               =>  $plan->id ?? null,
                    'subscription_id'       =>  $subscription ? $subscription->id : null,

                    'payment_type'          =>  'UPI',

                    'razorpay_order_id'     =>  $parsedData['transaction_id'],
                    'razorpay_payment_id'    =>  $parsedData['upi_transaction_id'],
                    'razorpay_signature'    =>  $request->meRes,
                    'amount'                =>  $parsedData['amount'],
                    'purchase_date'         =>  $new_subscription_start_date,
                    'original_purchase_date' =>  $new_subscription_start_date,
                    'subscription_end_date' =>  $subscription_end_date,
                    'in_app_ownership_type' =>  'PURCHASED',
                    'status'                =>  $parsedData['status'],
                ]);

             
                
                return response()->json(['statusCode' => 200, 'exists' => false]);
            } catch (\Exception $e) {
                $api_logs->request = json_encode($request->all());
                $api_logs->api_status = 500;
                $api_logs->save();
                $this->response['meta']['is_ban'] = false;
                $this->response['meta']['message'] = trans('api.went_wrong');
                $this->storeErrorLog($e, 'error_hdfc_webhook');
            }
        }
        return $this->returnResponse();
    }
}
