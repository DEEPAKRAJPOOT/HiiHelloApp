<?php

namespace App\Http\Controllers\WebHooks;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Models\Trainer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Exception;
use Illuminate\Http\Request;

class IosWebHook extends Controller
{
    public function getTestRequest(Request $request)
    {
        $orderLog = new Logger('TestRequest');
        $orderLog->pushHandler(new StreamHandler(storage_path('logs/webhooks-ios/TestRequestWebHook.log')), Logger::INFO);
        $reqData['web_hook_type'] = $request->notification_type ?? "N/A";
        $reqData['unified_receipt_data'] = $request->unified_receipt ?? "N/A";
        $orderLog->info('RequestData', $reqData);
    }

    // public function manageAllRequest(Request $request)
    // {
    //     $notification_type =  $request->notification_type ?? NULL;
    //     $unifiedReceipt = $request->unified_receipt;
    //     $receipt = $unifiedReceipt['latest_receipt'] ?? NULL;
    //     $orderLog = new Logger('TestRequest');
    //     $orderLog->pushHandler(new StreamHandler(storage_path('logs/webhooks-ios/TestRequestWebHook.log')), Logger::INFO);
    //     $reqData['web_hook_type'] = $request->notification_type ?? "N/A";
    //     $reqData['unified_receipt_data'] = $request->unified_receipt ?? "N/A";
    //     $orderLog->info('RequestData', $reqData);


    //     if( !empty($receipt) ) {
    //         switch ($notification_type) {
    //             case 'INTERACTIVE_RENEWAL':
    //             case 'DID_CHANGE_RENEWAL_STATUS':
    //             case 'DID_RECOVER':
    //             case 'DID_RENEW':
    //             case 'RENEWAL';
    //                 $webhook = $this->renewSubscriptionAddDetails($receipt);
    //                 if( $webhook->status == 'fail' ) {
    //                     $this->logFailerResponse($request);
    //                 }
    //                 break;
    //             case 'DID_FAIL_TO_RENEW':
    //             case 'DID_CHANGE_RENEWAL_PREF':
    //             case 'CANCEL':
    //                  $webhook = $this->cancelSubscriptionAddDetails($receipt);
    //                 if( $webhook->status == 'fail' ) {
    //                     $this->logFailerResponse($request);
    //                 }
    //                 break;
    //             case 'PRICE_INCREASE_CONSENT':
    //             case 'REFUND':
    //             case 'REVOKE':
    //                 $webhook = $this->updateFailSubscriptionDetails($receipt);
    //                 if( $webhook->status == 'fail' ) {
    //                     $this->logFailerResponse($request);
    //                 }
    //                 break;
    //             default:
    //                 $details['request_data'] = $request->all();
    //                 $details[] = $request->all();
    //                 $orderLog = new Logger('testLogs');
    //                 $orderLog->pushHandler(new StreamHandler(storage_path('logs/webhooks-ios/'.(\Carbon\Carbon::now()->format('Y-m-d')).'/testLogsWebHook.log')), Logger::INFO);
    //                 $orderLog->info('testLogs', $details);
    //                 break;
    //         }
    //     }
    // }

    // private function renewSubscriptionAddDetails($receipt)
    // {
    //     $internal_response = [
    //         'status'    =>  'fail',
    //         'message'   =>  'Oops! Something went wrong.',
    //     ];
    //     $data = [
    //         'password'      =>  config('utility.in_app.ios_password', NULL),
    //         'receipt-data'  =>  $receipt,
    //     ];
    //     try {
    //         $url = config('utility.in_app.ios_url', NULL);
    //         $response = fireCURL($url, 'POST', json_encode($data));
    //         if( !empty($response->latest_receipt_info)  ) {
    //             $latest_receipt_info = $response->latest_receipt_info;
    //             $paymetDetails = reset($latest_receipt_info);
    //             $plan = SubscriptionPlan::first();
    //             $old_subscription = Subscription::with('trainer')->where('transaction_id', $paymetDetails['original_transaction_id'])->latest()->first();
    //             $trainer = Trainer::withTrashed()->where('id',$old_subscription->trainer_id)->first();
    //             if( empty($plan) || empty($old_subscription) || empty($trainer) ) {
    //                 $internal_response = [
    //                     'status'    =>  'fail',
    //                     'message'   =>  'Unable to find plan / subscription or trainer.',
    //                 ];
    //                 throw new Exception("Unable to find plan / subscription or trainer", 1);   
    //             }
    //             // Add To Transaction History
    //              // Add Details To Subscription and transaction
    //                 $subscriptions = Subscription::updateOrCreate(
    //                     [ 'transaction_id'    =>  $paymetDetails['original_transaction_id'], ],[
    //                     'trainer_id'        =>  $trainer->id,
    //                     'plan_id'           =>  $plan->id,
    //                     'custom_id'         =>  getUniqueString('subscriptions'),
    //                     'email'             =>  $trainer->email,
    //                     'payment_token'     =>  $request->token,
    //                     'payment_date'      =>  date('Y-m-d H:i:s',$paymetDetails['purchase_date_ms'] / 1000),
    //                     'payment_type'      =>  'ios',
    //                     'transaction_id'    =>  $paymetDetails['original_transaction_id'],
    //                     'trial_allowed'     =>  $paymetDetails['is_trial_period'] ? 'y' : 'n' ,
    //                     'is_active'         =>  'y',
    //                 ]);

    //                 if( $old_subscription->id != $subscription->id ) 
    //                     $old_subscription->is_active = 'n';
    //                     $subscription->is_active = 'y';
    //                     $old_subscription->save();
    //                     $subscription->save();
    //                 if( date('Y-m-d H:i:s',$purchase['expiryTimeMillis'] / 1000) >= \Carbon\Carbon::today()->format('Y-m-d H:i:s') ) {
    //                     $trainer->subscription_status = 'active';
    //                     $trainer->is_subscription = 'y';
    //                     $trainer->subscription_end_date = \Carbon\Carbon::createFromTimestamp($purchase['expiryTimeMillis'] / 1000)->format('Y-m-d H:i:s');
    //                 } else {
    //                     $trainer->is_subscription = 'n';
    //                     $trainer->subscription_status = 'deactive';
    //                     $trainer->subscription_end_date = \Carbon\Carbon::createFromTimestamp($purchase['expiryTimeMillis'] / 1000)->format('Y-m-d H:i:s');
    //                 }

    //                 // Save Auto Renew Details
    //                 $trainer->auto_renew = $purchase->autoRenewing ? 'y' : 'n';
    //                 $trainer->save();

    //                 // Add Details To Transaction
    //                 Transaction::updateOrCreate(
    //                     [   'transaction_id'    =>  $paymetDetails['original_transaction_id'],
    //                         'subscription_id'   =>  $subscriptions->id,
    //                     ],[
    //                     'custom_id'                 => getUniqueString('transactions'),
    //                     'trainer_id'                =>  $trainer->id,
    //                     'email'                     =>  $trainer->email,
    //                     'plan_id'                   =>  $plan->id,
    //                     'subscription_id'           =>  $subscriptions->id,
    //                     'transaction_id'            =>  $paymetDetails['transaction_id'],
    //                     'web_order_line_item_id'    =>  $paymetDetails['web_order_line_item_id'],
    //                     'purchase_date'             =>  date('Y-m-d H:i:s', $paymetDetails['purchase_date_ms'] / 1000),
    //                     'subscription_end_date'     =>  date('Y-m-d H:i:s',$paymetDetails['expires_date_ms'] / 1000),
    //                     'payment_status'            =>  $response->status == 0 ? 'success' : 'fail',
    //                     'payable_amount'            =>  $plan->amount,
    //                     'actual_amount'             =>  $plan->amount,
    //                     'payment_token'             =>  $request->token,
    //                     'device_type'               =>  'ios',
    //                     'is_active'                 =>  'y',
    //                     'created_at'                =>  \Carbon\Carbon::now(),  
    //                 ]);

    //                $internal_response = [
    //                    'status'    =>  'success',
    //                    'message'   =>  'Details are updated successfully!',
    //                ];

    //                 $orderLog = new Logger('interactiveRenewSubscriptionDetailsLogs');
    //                 $orderLog->pushHandler(new StreamHandler(storage_path('logs/webhooks-ios/'.(\Carbon\Carbon::now()->format('Y-m-d')).'/interactiveRenewSubscriptionDetailsWebhookLogs.log')), Logger::INFO);
    //                 $orderLog->info('interactiveRenewSubscriptionDetailsLogs', $details);
         
    //         } else {
    //             // Fail To Get Data From Apple
    //             $internal_response = [
    //                 'status'    =>  'fail',
    //                 'message'   =>  'Unable to fetch details from apple',
    //             ];
    //             throw new Exception("Unable to fetch details from apple", 1);
    //         }
    //     } catch(Exception $exception) {
    //         // Add Receipt Details To Log
    //         $details['exception'] = $exception;
    //         $orderLog = new Logger('ExceptionCatched');
    //         $orderLog->pushHandler(new StreamHandler(storage_path('logs/webhooks-ios/ExceptionCatchedWebHook.log')), Logger::INFO);
    //         $orderLog->info('ExceptionCatched', $details);   
    //     } finally {
    //         return (object) $internal_response;
    //     }
    // }
    // // DID_CHANGE_RENEWAL_STATUS
    // private function updateFailSubscriptionDetails($receipt)
    // {
    //     $details = [];
    //     $internal_response = [
    //         'status'    =>  'fail',
    //         'message'   =>  'Oops! Something went wrong.',
    //     ];
    //     $data = [
    //         'password'      =>  config('utility.in_app.ios_password', NULL),
    //         'receipt-data'  =>  $receipt,
    //     ];
    //     try {
    //         $url = config('utility.in_app.ios_url', NULL);
    //         $response = fireCURL($url, 'POST',json_encode($data));
    //         if( !empty($response->latest_receipt_info)  ) {
    //             $latest_receipt_info = $response->latest_receipt_info;
    //             $paymetDetails = reset($latest_receipt_info);
    //             $old_subscription = Subscriptipn::where('transaction_id', $paymetDetails['original_transaction_id'])->latest()->first();
    //             $trainer = Trainer::withTrashed()->where('id',$old_subscription->Trainer_id)->first();
    //             if(empty($old_subscription) || empty($trainer)) {
    //                 $internal_response = [
    //                     'status'    =>  'fail',
    //                     'message'   =>  'Unable to find plan / subscription or Trainer.',
    //                 ];
    //                 throw new Exception("Unable to find plan / subscription or Trainer", 1);    
    //             }
              
    //             if( date('Y-m-d H:i:s',$paymetDetails['expires_date_ms'] / 1000) >= \Carbon\Carbon::now() ) {
    //                 $trainer->subscription_status = 'active';
    //                 $trainer->is_subscription = 'y';
    //                 $trainer->subscription_end_date = date('Y-m-d',$paymetDetails['expires_date_ms'] / 1000); 
    //             }
    //             else{
    //                 $trainer->subscription_status = 'deactive';
    //                 $trainer->is_subscription = 'n';
    //                 $trainer->subscription_end_date = date('Y-m-d',$paymetDetails['expires_date_ms'] / 1000);
                   
    //             }
    //             $auto_renew = $response->pending_renewal_info ? $response->pending_renewal_info[0]['auto_renew_status'] : '0';
    //             $trainer->auto_renew = ( $auto_renew == 0 ? 'n' : 'y' );
    //             $trainer->save();
    //             $details['details']   =    $paymetDetails;
    //              $orderLog = new Logger('changeRenewSubscriptionDetailsLogs');
    //             $orderLog->pushHandler(new StreamHandler(storage_path('logs/webhooks-ios/'.(\Carbon\Carbon::now()->format('Y-m-d')).'/changeRenewSubscriptionDetailsLogs.log')), Logger::INFO);
    //             $orderLog->info('changeRenewSubscriptionDetailsLogs', $details);
    //            $internal_response = [
    //                'status'    =>  'success',
    //                'message'   =>  'Details are updated successfully!',
    //            ];
            
    //         } else {
    //         // Fail To Get Data From Apple
    //             $internal_response = [
    //                 'status'    =>  'fail',
    //                 'message'   =>  'Unable to fetch details from apple',
    //             ];
    //             throw new Exception("Unable to fetch details from apple", 1);
    //         }
    //     } catch(Exception $exception) {
    //         // Add Receipt Details To Log
    //         $details['exception'] = $exception;
    //         $orderLog = new Logger('ExceptionCatched');
    //         $orderLog->pushHandler(new StreamHandler(storage_path('logs/webhooks-ios/ExceptionCatchedWebHook.log')), Logger::INFO);
    //         $orderLog->info('ExceptionCatched', $details);   
    //     } finally {
    //         return (object) $internal_response;
    //     }
    // }
    // // ALL_FAIL_TYPES
    // private function cancelSubscriptionAddDetails($receipt)
    // {
        
    //     $internal_response = [
    //         'status'    =>  'fail',
    //         'message'   =>  'Oops! Something went wrong.',
    //     ];
    //     $data = [
    //         'password'      =>  config('utility.in_app.ios_password', NULL),
    //         'receipt-data'  =>  $receipt,
    //     ];
    //     try {
    //         $url = config('utility.in_app.ios_url', NULL);
    //         $response = fireCURL($url, 'POST' , json_encode($data));
    //         if( !empty($response->latest_receipt_info)  ) {
    //             $latest_receipt_info = $response->latest_receipt_info;
    //             $paymetDetails = reset($latest_receipt_info);
    //             $old_subscription = Subscription::where('original_transaction_id', $paymetDetails['original_transaction_id'])->latest()->first();
    //             $trainer = Trainer::withTrashed()->where('id',$old_subscription->trainer_id)->first();
    //             if(empty($old_subscription) || empty($trainer)) {
    //                 $internal_response = [
    //                     'status'    =>  'fail',
    //                     'message'   =>  'Unable to find plan / subscription or Trainer.',
    //                 ];
    //                 throw new Exception("Unable to find plan / subscription or Trainer", 1);    
    //             }

    //              $old_subscription->is_active = 'n';
    //              $old_subscription->save();
               
    //             $trainer->is_subscription = 'n';
    //             $trainer->subscription_status = 'deactive';
    //             $trainer->subscription_end_date = \Carbon\Carbon::createFromTimestamp($purchase['expiryTimeMillis'] / 1000)->format('Y-m-d H:i:s');
    //             // Save Auto Renew Details
    //             $trainer->auto_renew = $purchase->autoRenewing ? 'y' : 'n';
    //             $trainer->save();
                
    //            $internal_response = [
    //                'status'    =>  'success',
    //                'message'   =>  'Details are updated successfully!',
    //            ];
            
    //         } else {
    //         // Fail To Get Data From Apple
    //             $internal_response = [
    //                 'status'    =>  'fail',
    //                 'message'   =>  'Unable to fetch details from apple',
    //             ];
    //             throw new Exception("Unable to fetch details from apple", 1);
    //         }
    //     } catch(Exception $exception) {
    //         // Add Receipt Details To Log
    //         $details['exception'] = $exception;
    //         $orderLog = new Logger('ExceptionCatched');
    //         $orderLog->pushHandler(new StreamHandler(storage_path('logs/webhooks-ios/ExceptionCatchedWebHook.log')), Logger::INFO);
    //         $orderLog->info('ExceptionCatched', $details);   
    //     } finally {
    //         return (object) $internal_response;
    //     }
    // }
    
    // public function logFailerResponse($receipt)
    // {
    //     $details['request-details'] = $receipt;
    //     $orderLog = new Logger('WebhookFail');
    //     $orderLog->pushHandler(new StreamHandler(storage_path('logs/webhooks-ios/'.(\Carbon\Carbon::now()->format('Y-m-d')).'/WebhookFailWebHook.log')), Logger::INFO);
    //     $orderLog->info('WebhookFail', $details);            
    // }
}
