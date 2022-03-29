<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\v1\UserInterestResource;

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
            'full_name'         =>  $this->full_name ?? "",
            'email'             =>  $this->email ?? "",
            'contact'       =>  [
                'code'      =>  $this->country_code,
                'number'    =>  $this->contact_no,
            ],
            'birth_date'        =>  $this->birth_date ?? "",
            'age'               =>  $this->getAge(),
            'gender'            =>  $this->gender ?? "",
            'interest'          =>  $this->interest ?? "",
            'country'           =>  new CountryResource($this->country),
            'location'          =>  new LocationResource($this->location),
            'language'          =>  new LanguageResource($this->language),
            'interests'         =>  UserInterestResource::collection($this->interests),
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "",
            'media' =>  [
                'profile_images'    =>  $this->getProfileImages(),
                'profile_videos'    =>  $this->getProfileVideos(),
            ],
            'flags'             =>  [
                'social_user'           =>  $this->isSocialUser(),
                'profile_setuped'       =>  $this->isProfileSetuped(),
                'verified_staus'        =>  $this->getVerifiedStatus(),
                'likes'                 =>  $this->likes_count ?? 0,
                'matches'               =>  $this->countMatches(),
                'chats'                 =>  $this->countChats(),
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
