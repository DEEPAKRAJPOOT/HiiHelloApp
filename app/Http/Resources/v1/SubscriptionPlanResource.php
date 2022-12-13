<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
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
            'id'                =>  $this->custom_id,
            'name'              =>  $this->subscriptionPlanTranslation ? $this->subscriptionPlanTranslation->name: "",
            'description'       =>  $this->subscriptionPlanTranslation ? $this->subscriptionPlanTranslation->description: "",
            'note'              =>  $this->subscriptionPlanTranslation ? $this->subscriptionPlanTranslation->note: "",
            'months'            =>  $this->months,
            'day'               =>  $this->day ? $this->day : "",
            'amount'            =>  $this->amount,
            'android_product'   =>  $this->android_product,
            'ios_product'       =>  $this->ios_product,
            'is_popular'        =>  $this->is_popular,
        ];
        return parent::toArray($request);
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
