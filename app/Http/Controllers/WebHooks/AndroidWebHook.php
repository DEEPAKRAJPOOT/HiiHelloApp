<?php

namespace App\Http\Controllers\WebHooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AndroidWebHook extends Controller
{
    public function storeDetails(Request $request)
    {
    	info($request->all());
        // Log::info($request->payload['payment']['entity']['order_id']);
        // if($request->entity == 'event') {
        //     if($request->event == 'payment.authorized') {
        //         $order = RetailerOrder::where('order_id', $request->payload['payment']['entity']['order_id'])->first();
        //         if($order != null) {
        //             if($order->status != 'captured') {
        //                 $order->status = $request->payload['payment']['entity']['status'];
        //                 $order->payment_id = $request->payload['payment']['entity']['id'];
        //                 $order->save();
        //             }
        //         }
        //     }

        //     if($request->event == 'payment.captured') {
        //         $order = RetailerOrder::where('order_id', $request->payload['payment']['entity']['order_id'])->first();
        //         if($order != null) {
        //             $order->status = $request->payload['payment']['entity']['status'];
        //             $order->payment_id = $request->payload['payment']['entity']['id'];
        //             $order->save();
        //         }
        //     }

        //     if($request->event == 'payment.failed') {
        //         $order = RetailerOrder::where('order_id', $request->payload['payment']['entity']['order_id'])->first();
        //         if($order != null) {
        //             $order->status = $request->payload['payment']['entity']['status'];
        //             $order->payment_id = $request->payload['payment']['entity']['id'];
        //             $order->save();
        //         }
        //     }
        // }
    }
}
