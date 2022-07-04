<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Support\Facades\ { Auth };
use App\Models\ { User, Subscription, SubscriptionPlan, Transaction };
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SubscriptionController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    public function iosDetails(Request $request)
    {
        $plan_ids = SubscriptionPlan::whereIsActive('y')->pluck('custom_id')->toArray();

        $rules = [
            'plan_id'           =>  'required|in:'.implode(',', $plan_ids),
            'token'             =>  'required',
            'purchase_token'    =>  'nullable', //(productId: '', transactionId: '', transactionDate:'', transactionReceipt: MIIotwYJKoZIhvcNAQcCoIIoqDCCKKQCAQExCzAJBgUrDgMCGgUAMIIYWAY)
            // 'plan_id'           =>      'required|exists:subscription_plans,ios_plan_id',
        ];

        if( $this->apiValidator($request->all(), $rules) ) {
            $user = $request->user();
            $plan = SubscriptionPlan::whereCustomId($request->plan_id)->whereIsActive('y')->firstOrFail();

            // dd($plan, $user);

            $url = config('utility.in_app.ios_url');
            $data = [
                'password'      =>  config('utility.in_app.ios_password'),
                'receipt-data'  =>  $request->token,
            ];

            $response = fireCURL($url, 'POST', json_encode($data));

            // dd($response->latest_receipt_info);
            if( !empty($response->latest_receipt_info)  ) {
                $latest_receipt_info = $response->latest_receipt_info;
                $paymetDetails = end($latest_receipt_info);
            
                // trial period allowed
                if( $paymetDetails['is_trial_period'] == 'true' ) {
                    $end = \Carbon\Carbon::createFromTimestamp($paymetDetails['expires_date_ms'] / 1000);
                    $now = \Carbon\Carbon::createFromTimestamp($paymetDetails['purchase_date_ms'] / 1000);
                    $length = $end->diffInDays($now);   
                    
                    // Update User's Data
                        // $user->subscription_status = 'trial';
                        $user->is_subscription = 'y';
                        // $user->trial_end_date = date('Y-m-d H:i:s',$paymetDetails['expires_date_ms'] / 1000);
                        $user->subscription_end_date = date('Y-m-d H:i:s',$paymetDetails['expires_date_ms'] / 1000);
                } else {
                    // Update User's Table Details
                        if( date('Y-m-d H:i:s',$paymetDetails['expires_date_ms'] / 1000) >= \Carbon\Carbon::now() ) {
                            // $user->subscription_status = 'active';
                            $user->is_subscription = 'y';
                            $user->subscription_end_date = date('Y-m-d H:i:s',$paymetDetails['expires_date_ms'] / 1000);
                        } else {
                            // $user->subscription_status = 'deactive';
                            $user->is_subscription = 'n';
                            $user->subscription_end_date = date('Y-m-d H:i:s',$paymetDetails['expires_date_ms'] / 1000);
                        }
                }
                // $auto_renew = $response->pending_renewal_info ? $response->pending_renewal_info[0]['auto_renew_status'] : '0';
                // $user->auto_renew = ( $auto_renew == 0 ? 'n' : 'y' );
                $user->save();

                // Add Details To Subscription
                $subscriptions = Subscription::create([
                    'custom_id'         =>  getUniqueString('subscriptions'),
                    'user_id'           =>  $user->id,
                    'plan_id'           =>  $plan->id,
                    // 'email'             =>  $user->email,
                    'payment_token'     =>  $request->token,
                    'payment_date'      =>  date('Y-m-d H:i:s',$paymetDetails['purchase_date_ms'] / 1000),
                    'payment_type'      =>  'ios',
                    'transaction_id'    =>  $paymetDetails['original_transaction_id'],
                    // 'trial_allowed'     =>  $paymetDetails['is_trial_period'] ? 'y' : 'n' ,
                    'is_active'         =>  'y',
                ]);

                // Add Details To Transaction
                Transaction::create([
                    'custom_id'                 =>  getUniqueString('transactions'),
                    'user_id'                   =>  $user->id,
                    'plan_id'                   =>  $plan->id,
                    'subscription_id'           =>  $subscriptions->id,
                    'email'                     =>  $user->email,
                    'transaction_id'            =>  $paymetDetails['transaction_id'],
                    'web_order_line_item_id'    =>  $paymetDetails['web_order_line_item_id'],
                    'purchase_date'             =>  date('Y-m-d H:i:s', $paymetDetails['purchase_date_ms'] / 1000),
                    'subscription_end_date'     =>  date('Y-m-d H:i:s',$paymetDetails['expires_date_ms'] / 1000),
                    'payment_status'            =>  $response->status == 0 ? 'success' : 'fail',
                    'payable_amount'            =>  $plan->amount,
                    'actual_amount'             =>  $plan->amount,
                    'payment_token'             =>  $request->token,
                    'device_type'               =>  'ios',
                    'is_active'                 =>  'y',
                    // 'created_at'                =>  \Carbon\Carbon::now(),  
                ]);

                // webhooklog for testing 
                $details['purchase_date_ms'] = $paymetDetails['purchase_date_ms'];
                $details['expires_date_ms'] = $paymetDetails['expires_date_ms'];
                $details['purchase_date_ms_converted'] = date('Y-m-d H:i:s', $paymetDetails['purchase_date_ms'] / 1000);
                $details['expires_date_ms_converted'] = date('Y-m-d H:i:s',$paymetDetails['expires_date_ms'] / 1000);
                $details['auto_renew'] = $response->pending_renewal_info[0]['auto_renew_status'];
                $details['transaction_id'] = $paymetDetails['transaction_id'];
                $details['is_trial_period'] = $paymetDetails['is_trial_period'];
                $details['data'] = $data;
                // $details['full_response'] = $response;
                $orderLog = new Logger('DateCheck');
                $orderLog->pushHandler(new StreamHandler(storage_path('logs/webhooks-ios/'.(\Carbon\Carbon::now()->format('Y-m-d')).'/DateCheck.log')), Logger::INFO);
                $orderLog->info('DateCheck', $details);

                // api response 
                $this->response['meta']['message']  =  trans('api.add', ['entity' => 'Payment details']);
                $this->status = Response::HTTP_OK;
            } else {
                $this->response['meta']['message']  =  trans('api.went_wrong');
                $this->status = Response::HTTP_GATEWAY_TIMEOUT;
            }
        }
        return $this->returnResponse();
    }
}
