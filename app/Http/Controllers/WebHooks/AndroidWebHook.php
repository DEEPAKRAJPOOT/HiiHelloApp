<?php

namespace App\Http\Controllers\WebHooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class AndroidWebHook extends Controller
{
    public function storeDetails(Request $request)
    {
        // Log::info($request->payload['payment']['entity']['order_id']);
        if($request->entity == 'event') {

            if($request->event == 'payment.authorized' || $request->event == 'payment.captured') {
                $transaction = Transaction::with('subscription')->where('razorpay_order_id',$request->payload['payment']['entity']['order_id'])->first();

                if($transaction && $transaction->subscription){
                    $transaction->status = 'success';
                    $transaction->razorpay_payment_id = $request->payload['payment']['entity']['id'];
                    $transaction->save();

                    $transaction->subscription->status = 'active';
                    $transaction->subscription->save();

                    // $transaction->status = $request->payload['payment']['entity']['status'];
                }
            }
            
            if($request->event == 'payment.failed') {
                $transaction = Transaction::with('subscription')->where('razorpay_order_id',$request->payload['payment']['entity']['order_id'])->first();

                if($transaction && $transaction->subscription){
                    $transaction->status = 'fail';
                    $transaction->razorpay_payment_id = $request->payload['payment']['entity']['id'];
                    $transaction->save();

                    $transaction->subscription->status = 'canceled';
                    $transaction->subscription->save();

                    // $transaction->status = $request->payload['payment']['entity']['status'];
                }
            }
        }
    }
}
