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
            'email'             =>  $this->email ?? "",
            'age'               =>  $this->getAge(),
            'interest'          =>  $this->interest ?? "",
            'extra'             =>  [
                'about_me'      =>  $this->about_me ?? "",
                'fav_movie'     =>  $this->fav_movie ?? "",
            ],
            'location'          =>  new LocationResource($this->location),
            'language'          =>  new LanguageResource($this->language),
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "",
            'my_things'         =>  [
                'relationship_status'   =>  new ProfileDetailResource($this->relationshipStatus),
                'i_am_here'             =>  new ProfileDetailResource($this->youAreHere),
                'food_preference'       =>  new ProfileDetailResource($this->foodPreference),
                'drinking'              =>  new ProfileDetailResource($this->drinking),
                'smoking'               =>  new ProfileDetailResource($this->smoking),
                'pet'                   =>  new ProfileDetailResource($this->pet),
                'star_sign'             =>  new ProfileDetailResource($this->starSign),
                'community'             =>  new ProfileDetailResource($this->community),
            ],
            'my_basics'         =>  [
                'personality'           =>  new PersonalityResource($this->personality),
                'education'             =>  new ProfileDetailResource($this->education),
                'university_college'    =>  new ProfileDetailResource($this->university),
                'profession'            =>  new ProfileDetailResource($this->profession),
                'religion'              =>  new ProfileDetailResource($this->religion),
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
