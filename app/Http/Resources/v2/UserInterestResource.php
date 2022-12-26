<?php

namespace App\Http\Resources\v2;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\v2\InterestResource;

class UserInterestResource extends JsonResource
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
            'user_id'       =>  $this->user_id,
            'interest'      =>  new InterestResource($this->interest),
        ];
        return parent::toArray($request);
    }

    public function with($request)
    {
        return [
            'meta' => [ 
                'api'               =>  'v.2.0',
                'url'               =>  url()->current(),
                'language'          =>  app()->getLocale(),
            ],
        ];
    }
}
