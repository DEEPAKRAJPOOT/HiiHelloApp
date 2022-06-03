<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class RazorPayOrderResource extends JsonResource
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
            'id'            =>  $this->id,
            'entity'        =>  $this->entity,
            'amount'        =>  $this->amount,
            'amount_paid'   =>  $this->amount_paid,
            'amount_due'    =>  $this->amount_due,
            'currency'      =>  $this->currency,
            'receipt'       =>  $this->receipt,
            'offer_id'      =>  $this->offer_id,
            'status'        =>  $this->status,
            'attempts'      =>  $this->attempts,
            'created_at'    =>  $this->created_at,
            'subscription'  =>  [
                'id'            =>  $this->subscription ? $this->subscription->custom_id : "",
                'months'        =>  $this->subscription ? $this->subscription->months : "",
                'amount'        =>  $this->subscription ? $this->subscription->amount : "",
                'start_date'    =>  $this->subscription ? $this->subscription->start_date : "",
                'end_date'      =>  $this->subscription ? $this->subscription->end_date : "",
                'status'        =>  $this->subscription ? $this->subscription->status : "",
            ],
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
