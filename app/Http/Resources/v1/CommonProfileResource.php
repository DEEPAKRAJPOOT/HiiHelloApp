<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class CommonProfileResource extends JsonResource
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
            'id'                =>  $this->custom_id ?? "",
            'full_name'         =>  $this->userTranslation ? $this->userTranslation->full_name : "",
            'age'               =>  $this->getAge(),
            'location'          =>  new LocationResource($this->location),
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "",
            'gender'            =>  $this->gender,
            'isProfileVerified' => ($this->emailVerifyStatus()=='verified' && $this->contactVerifyStatus()=='verified' && $this->verify_photo_status=='verified') ? true : false,
            'onlineStatus'      => $this->onlineStatus(),
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
