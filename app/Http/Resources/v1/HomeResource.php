<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
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
            'gender'            =>  $this->gender ?? "",
            'isProfileVerified' =>  ($this->emailVerifyStatus()=='verified' && $this->contactVerifyStatus()=='verified' && $this->verify_photo_status=='verified') ? true : false,
            'location'          =>  new LocationResource($this->location),
            'interests'         =>  HomeInterestResource::collection($this->interests),
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "",            
            'media' =>  [
                'profile_images'    =>  $this->getProfileImages(),
                'profile_videos'    =>  $this->getProfileVideos(),
                'profile_voice'     =>  [
                    'voice'         =>  generateURL($this->voice),
                    'voice_answer'  =>  $this->voice_answer ?? "",
                ],
            ],
            'flags'             =>  [
                'verified_staus'   =>  $this->verify_status,
                'distance'         =>  $this->distance ?? 0,
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
