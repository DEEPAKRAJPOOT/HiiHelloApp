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
            //'isProfileVerified' =>  ($this->verify_status=='verified') ? true : false,
            'is_email_verify'   =>  ($this->emailVerifyStatus()=='verified') ? true : false,
            'is_contact_verify' =>  ($this->contactVerifyStatus()=='verified') ? true : false,
            'is_photo_verify'   =>  ($this->verify_photo_status=='verified') ? true : false,
            'trusted_score'     =>  $this->trusted_score,
            'location'          =>  new LocationResource($this->location),
            'interests'         =>  HomeInterestResource::collection($this->interests),
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "", 
            //'is_profile_photo' =>  ($this->profile_photo=='') ?  false : true,           
            'media' =>  [
                'profile_images'    =>  $this->getProfileImages(),
                'profile_videos'    =>  $this->getProfileVideos(),
                'profile_voice'     =>  [
                    'voice'         =>  generateURL($this->voice),
                    'voice_answer'  =>  $this->voice_answer ?? "",
                ],
            ],
            'flags'             =>  [
                'verified_staus'   =>  ($this->emailVerifyStatus()=='verified' && $this->contactVerifyStatus()=='verified' && $this->verify_photo_status=='verified') ? 'verified' : 'under_review',
                'verified_status'  =>  $this->verify_status,
                'distance'         =>  $this->distance ?? 0,
                'online_status'    =>  $this->onlineStatus(),
                'new_account'      =>  $this->isNewAccount(),
                'last_seen'        =>  strtotime($this->last_online) * 1000
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
