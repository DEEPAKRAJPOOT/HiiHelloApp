<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
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
            'user'          =>  [
                'id'            =>  $this->custom_id ?? "",
                'full_name'     =>  $this->userTranslation ? $this->userTranslation->full_name : "",
                'profile_photo' =>  generateURL($this->profile_photo) ?? "",
                'is_system_generated' =>  $this->is_system_generated,
                'system_match_user_key' =>  $this->system_match_user_key,
                'gender'              => $this->gender,
                'isProfileVerified' => ($this->emailVerifyStatus()=='verified' && $this->contactVerifyStatus()=='verified' && $this->verify_photo_status=='verified') ? true : false,
                'location'          =>  new LocationResource($this->location),
                'age'               =>  $this->getAge(),

            ],
            'created_at'    =>  $this->created_at ?? "",
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
