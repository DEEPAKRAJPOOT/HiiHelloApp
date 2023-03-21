<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            "id" => $this->custom_id,
            "plan" =>   new SubscriptionPlanResource($this->plan),
            "coupon" =>   $this->coupon,
            "value" =>   $this->value,
            "title" =>   $this->title,
            "description" =>   $this->description,
            "type" =>   $this->type,
            "is_universal" =>   $this->is_universal,
            "is_reusable" =>   $this->is_reusable
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
