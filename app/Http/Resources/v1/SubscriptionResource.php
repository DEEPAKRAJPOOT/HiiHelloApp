<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            =>  $this->custom_id ?? "",
            'months'        =>  $this->months ?? "",
            'amount'        =>  $this->amount ?? "",
            'start_date'    =>  $this->start_date ?? "",
            'end_date'      =>  $this->end_date ?? "",
            'status'        =>  $this->status ?? "",
            'name'          =>  $this->subscriptionPlan ? 
                                    $this->subscriptionPlan->subscriptionPlanTransEn ? $this->subscriptionPlan->subscriptionPlanTransEn->name : ""  
                                : "",
            'type'          =>  $this->subscriptionPlan ? 
                                    $this->subscriptionPlan->subscriptionPlanTransEn ? $this->subscriptionPlan->subscriptionPlanTransEn->name : ""  
                                : "",
            'android_product'       =>  $this->subscriptionPlan ? $this->subscriptionPlan->android_product : "",
            'ios_product'           =>  $this->subscriptionPlan ? $this->subscriptionPlan->ios_product : "",
        ];
    }

    public function with($request)
    {
        $is_subscribed = $is_feature_allow = false;
        $subscription_end_date = "";
        if( !Auth::guest() ) {
            if( Auth::user()->is_subscribed == 'y' && Auth::user()->subscription_end_date >= \Carbon\Carbon::today()->format('Y-m-d') ){
                $is_subscribed = true;
            } elseif (Auth::user()->gender == 'Female'){
                $is_subscribed = true;
            }
            $subscription_end_date = Auth::user()->subscription_end_date ?? "";

            if($is_subscribed && ($this->start_date == \Carbon\Carbon::today()->format('Y-m-d')) ){
                $is_feature_allow = true;
            }else if($is_subscribed && Auth::user()->verify_status == 'verified'){
                $is_feature_allow = true;
            }
        }

        return [
            'meta' => [ 
                'api'                       =>  'v.1.0',
                'url'                       =>  url()->current(),
                'language'                  =>  app()->getLocale(),
                'is_subscribed'             =>  $is_subscribed,
                'subscription_end_date'     =>  $subscription_end_date,
                'is_feature_allow'          =>  $is_feature_allow,
            ],
        ];
    }
}
