<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class CallReceiverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $is_subscribed = false;
        if( $this->is_subscribed == 'y' && $this->subscription_end_date >= \Carbon\Carbon::today()->format('Y-m-d') ){
            $is_subscribed = true;
        } elseif ($this->gender == 'Female'){
            $is_subscribed = true;
        }
        
        return [
            'id'                        =>  $this->custom_id ?? "",
            'twilio_identifier'         =>  $this->userTransEn ? $this->userTransEn->full_name : "",  
            'twilio_rcv_show_name'      =>  $this->twilio_rcv_show_name ?? "",                   
            'gender'                    =>  $this->gender ?? "",
            'is_subscribed'             =>  $is_subscribed,
            'subscription_end_date'     =>  $this->subscription_end_date ?? "",
        ];
        return parent::toArray($request);
    }

    public function with($request)
    {
        return [
            'meta' => [
                'api'           =>  'v.1.0',
                'url'           =>  url()->current(),
                'language'      =>  app()->getLocale(),
            ],
        ];
    }
}
