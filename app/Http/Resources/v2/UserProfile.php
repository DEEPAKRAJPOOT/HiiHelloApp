<?php

namespace App\Http\Resources\v2;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\v2\UserInterestResource;

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
            'full_name'         =>  $this->userTranslation ? $this->userTranslation->full_name : "",
            'email'             =>  $this->email ?? "",
            'contact'       =>  [
                'code'      =>  $this->country_code ?? "",
                'number'    =>  $this->contact_no ?? "",
            ],
            'birth_date'        =>  $this->birth_date ?? "",
            'age'               =>  $this->getAge() ?? "",
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
                'profile_percentage'    =>  $this->calculateProfilePercent(),
                'verified_staus'        =>  $this->verify_status,
                'likes'                 =>  $this->like_count ?? 0,
                'email_verified_at'     =>  $this->email_verified_at ?? "",
                'contact_verified_at'   =>  $this->contact_verified_at ?? "",
                'photo_verified_at'     =>  $this->photo_verified_at ?? "",
                'video_verified_at'     =>  $this->video_verified_at ?? "",
            ],
        ];
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
