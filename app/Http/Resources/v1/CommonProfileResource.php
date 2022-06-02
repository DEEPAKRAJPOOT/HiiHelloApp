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
            'contact'       =>  [
                'code'      =>  $this->country_code,
                'number'    =>  $this->contact_no,
            ],
            'birth_date'        =>  $this->birth_date ?? "",
            'age'               =>  $this->getAge(),
            'gender'            =>  $this->gender ?? "",
            'location'          =>  new LocationResource($this->location),
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "",
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
