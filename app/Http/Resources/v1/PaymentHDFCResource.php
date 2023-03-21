<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentHDFCResource extends JsonResource
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
            'merchant_id'            =>  $this->resource['PGMerchantId'],
            'transaction_id'          =>  $this->resource['TransactionId'],
            'mc'     =>  $this->resource['MerchantCategoryCode'],
            'amount'          =>  $this->resource['Amount'],
            'cu' => "INR",
            "merchant_vpa" => $this->resource['PayeeVirtualId'],
            "merchant_name" => $this->resource['PayeeName'],
            "plan_name" => $this->resource['plan_name'],
            "request" =>  $this->resource['request'],
            "response" => $this->resource['response']
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
