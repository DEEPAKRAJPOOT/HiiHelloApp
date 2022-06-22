<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

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
                                    $this->subscriptionPlan->subscriptionPlanTranslation ? $this->subscriptionPlan->subscriptionPlanTranslation->name : ""  
                                : "",
        ];
    }

    public function with($request)
    {
        return [
            'meta' => [ 
                'api'               =>  'v.1.0',
                'url'               =>  url()->current(),
                'language'          =>  app()->getLocale(),
            ],
        ];
    }
}
