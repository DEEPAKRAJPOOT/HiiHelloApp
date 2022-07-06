<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Support\Facades\ { Auth, DB };
use App\Models\ { User, Subscription, SubscriptionPlan, Transaction };
use App\Http\Requests\Api\Subscription\ { IosSubscription };
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SubscriptionController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    /**
     * For storing purchase details of ios subscription.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buyIosSubscription(Request $request)
    {
        $rules = IosSubscription::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            $user = $request->user();

            DB::beginTransaction();
            try{
                $plan   =   SubscriptionPlan::whereCustomId($request->plan_id)->whereIsActive('y')->firstOrFail();
                $url    =   config('utility.in_app.ios_url');
                $data   =   [
                    'password'      =>  config('utility.in_app.ios_password'),
                    'receipt-data'  =>  $request->receipt_data,
                    'exclude-old-transactions'   => true,  // For getting Single latest_receipt_info details
                ];

                $response = fireCURL($url, 'POST', json_encode($data));
                if( !empty($response->latest_receipt_info)  ) {
                    $latest_receipt_info = $response->latest_receipt_info;
                    $paymetDetails = current($latest_receipt_info);
                                
                    // Update Details
                    $new_subscription_start_date = \Carbon\Carbon::today()->format('Y-m-d');
                    if( $user->subscription_end_date >= $new_subscription_start_date ) {
                        $new_subscription_start_date = $user->subscription_end_date;
                    }

                    // $subscription_end_date = !empty($user->subscription_end_date)
                    //                         ? \Carbon\Carbon::parse($new_subscription_start_date)->addMonth($plan->months)->format('Y-m-d')
                    //                         : \Carbon\Carbon::today()->addMonth($plan->months)->format('Y-m-d');

                    $subscription_end_date = date('Y-m-d H:i:s',$paymetDetails['expires_date_ms'] / 1000);
                    
                    $subscription =  Subscription::create([
                        'custom_id'                 =>  getUniqueString('subscriptions'),
                        'user_id'                   =>  $user->id ?? NULL,
                        'plan_id'                   =>  $plan->id ?? NULL,
                        'email'                     =>  $user->email,
                        'months'                    =>  $plan->months,
                        'amount'                    =>  $plan->amount,
                        'start_date'                =>  $new_subscription_start_date,
                        'end_date'                  =>  $subscription_end_date,
                        'payment_date'              =>  date('Y-m-d H:i:s',$paymetDetails['purchase_date_ms'] / 1000) ?? now(),
                        'payment_type'              =>  'ios',
                        'receipt_data'              =>  $request->receipt_data,
                        'original_transaction_id'   =>  $paymetDetails['original_transaction_id'],
                    ]);

                    // Add Details To Transaction
                    $transaction =  Transaction::create([
                        'custom_id'                     =>  getUniqueString('transactions'),
                        'email'                         =>  $user->email,
                        'user_id'                       =>  $user->id ?? NULL,
                        'plan_id'                       =>  $plan->id ?? NULL,
                        'subscription_id'               =>  $subscription->id,
                        'payment_type'                  =>  'ios',
                        'transaction_id'                =>  $paymetDetails['transaction_id'],
                        'original_transaction_id'       =>  $paymetDetails['original_transaction_id'],
                        'web_order_line_item_id'        =>  $paymetDetails['web_order_line_item_id'],
                        'purchase_date'                 =>  date('Y-m-d H:i:s', $paymetDetails['purchase_date_ms'] / 1000),
                        'original_purchase_date'        =>  date('Y-m-d H:i:s', $paymetDetails['original_purchase_date_ms'] / 1000),
                        'subscription_end_date'         =>  date('Y-m-d H:i:s', $paymetDetails['expires_date_ms'] / 1000),
                        'receipt_data'                  =>  $request->receipt_data,
                        'in_app_ownership_type'         =>  $paymetDetails['in_app_ownership_type'],
                        'subscription_group_identifier' =>  $paymetDetails['subscription_group_identifier'],
                        'amount'                        =>  $plan->amount,
                    ]);

                    if( date('Y-m-d H:i:s',$paymetDetails['expires_date_ms'] / 1000) >= \Carbon\Carbon::now() ) {
                        $subscription->update(['status' => 'active']);
                        $subscription->save();

                        $transaction->update(['status' => 'success']);
                        $transaction->save();

                        $user->is_subscribed = 'y';
                        $user->subscription_end_date = $subscription_end_date;
                        $user->save();

                        DB::commit();

                        $subscription->notifySubScriptionPurchase('success');
                        $subscription_type = 'new'; // New Purchase
                        $renew_count = Subscription::withTrashed()->whereUserId($user->id)
                                        ->whereNotIn('status',['incomplete','incomplete_expired','unpaid'])->count();
                        if($renew_count > 0){
                            $subscription_type = 'renew';   // Renew Subscription
                        }
                        $subscription->sendSubScriptionPurchaseSMS($subscription_type);   

                        $this->response['meta']['message']  =  trans('api.ios_payment.success');
                        $this->status = Response::HTTP_OK;
                    } else {
                        $subscription->update(['payment_date' => NULL, 'status' => 'unpaid']);
                        $subscription->save();

                        $transaction->update(['status' => 'fail']);
                        $transaction->save();

                        $user->is_subscribed = 'n';
                        $user->subscription_end_date = $subscription_end_date;
                        $user->save();
                        
                        DB::commit();
                        
                        $subscription->notifySubScriptionPurchase('fail');
                        
                        $this->response['meta']['message']  =  trans('api.ios_payment.fail');
                        $this->status = Response::HTTP_FORBIDDEN;
                    }

                    // Add Payment log
                    $transaction_data['paymetDetails']  = $paymetDetails;
                    $transaction_data['request_data']   = $data;
                    $file = 'payment_' . $user->id;
                    $paymentLog = new Logger($file);
                    $paymentLog->pushHandler(new StreamHandler(storage_path('logs/ios/' . $file . '.log')), Logger::INFO);
                    $paymentLog->info($file, ['success' => $transaction_data]);
                }else {
                    $this->response['meta']['message']  =  trans('api.went_wrong');
                    $this->status = Response::HTTP_GATEWAY_TIMEOUT;
                }   
            } catch (\Exception $e) {
                DB::rollback();

                $user->is_subscribed = $user->subscription_end_date >= \Carbon\Carbon::now()->format('Y-m-d') ? 'n' : $user->is_subscribed;
                $user->subscription_end_date = $user->subscription_end_date >= \Carbon\Carbon::now()->format('Y-m-d')
                                                    ? NULL
                                                    : $user->subscription_end_date;
                $user->save();

                $file = 'ios/payment_' . $user->id;
                $this->storeErrorLog($e,$file,$e->getMessage());

                $this->response['meta']['message']  =  trans('api.went_wrong');
                $this->status = Response::HTTP_GATEWAY_TIMEOUT;
            }
        }
        return $this->returnResponse();
    }
}
