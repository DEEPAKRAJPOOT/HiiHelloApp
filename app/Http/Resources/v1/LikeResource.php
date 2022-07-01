<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LikeResource extends JsonResource
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
            'id'            =>  $this->custom_id,
            'user'          =>  new CommonProfileResource($this->likerUser),
        ];
        return parent::toArray($request);
    }

    public function with($request)
    {
        $is_subscribed = false;
        $subscription_end_date = "";
        if( !Auth::guest() ) {
            if( Auth::user()->is_subscribed == 'y' && Auth::user()->subscription_end_date >= \Carbon\Carbon::today()->format('Y-m-d') ){
                $is_subscribed = true;
            }
            $subscription_end_date = Auth::user()->subscription_end_date ?? "";
        }
        return [
            'meta' => [ 
                'api'                       =>  'v.1.0',
                'url'                       =>  url()->current(),
                'language'                  =>  app()->getLocale(),
                'is_subscribed'             =>  $is_subscribed,
                'subscription_end_date'     =>  $subscription_end_date,
            ],
        ];
    }
}
