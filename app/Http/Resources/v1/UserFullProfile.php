<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserFullProfile extends JsonResource
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
            'age'               =>  $this->getAge(),
            'interest'          =>  $this->interest ?? "",
            'about_me'          =>  $this->about_me ?? "",
            'location'          =>  new LocationResource($this->location),
            'language'          =>  new LanguageResource($this->language),
            'interests'         =>  UserInterestResource::collection($this->interests),
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "",
            'personal'          =>  [
                'personality'           =>  new ProfileDetailResource($this->personality),
                'education'             =>  new ProfileDetailResource($this->education),
                'university'            =>  new ProfileDetailResource($this->university),
                'profession'            =>  new ProfileDetailResource($this->profession),
                'religion'              =>  new ProfileDetailResource($this->religion),
                'relationship_status'   =>  new ProfileDetailResource($this->relationshipStatus),
                'you_are_here'          =>  new ProfileDetailResource($this->youAreHere),
                'food_preference'       =>  new ProfileDetailResource($this->foodPreference),
                'drinking'              =>  new ProfileDetailResource($this->drinking),
                'smoking'               =>  new ProfileDetailResource($this->smoking),
                'pet'                   =>  new ProfileDetailResource($this->pet),
                'star_sign'             =>  new ProfileDetailResource($this->starSign),
                'community'             =>  new ProfileDetailResource($this->community),
            ],
            'media' =>  [
                'profile_images'    =>  $this->getProfileImages(),
                'profile_videos'    =>  $this->getProfileVideos(),
                'profile_voices'    =>  $this->getProfileVoices(),
            ],
            'flags'             =>  [
                'social_user'           =>  $this->isSocialUser(),
                'profile_setuped'       =>  $this->isProfileSetuped(),
                'profile_percentage'    =>  $this->calculateProfilePercent(),
                'verified_staus'        =>  $this->getVerifiedStatus(),
                'likes'                 =>  $this->likes_count ?? 0,
                'matches'               =>  $this->countMatches(),
                'chats'                 =>  $this->countChats(),
                'is_blocked'            =>  $this->blocked_tos_count ? $this->blocked_tos_count > 0 ? true : false : false,
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
