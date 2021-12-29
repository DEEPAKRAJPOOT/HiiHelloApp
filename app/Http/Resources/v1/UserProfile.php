<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfile extends JsonResource
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
            'first_name'        =>  $this->first_name ?? "",
            'last_name'         =>  $this->last_name ?? "",
            'email'             =>  $this->email ?? "",
            'contact'       =>  [
                'code'      =>  $this->country_code,
                'number'    =>  $this->contact_no,
            ],
            'birth_date'        =>  $this->birth_date ?? "",
            'gender'            =>  $this->gender ?? "",
            'interest'          =>  $this->interest ?? "",
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "",
            'flags'             =>  [
                'profile_setuped'     =>  $this->isProfileSetuped(),
            ],
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
