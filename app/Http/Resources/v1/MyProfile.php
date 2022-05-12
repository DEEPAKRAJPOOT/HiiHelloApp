<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class MyProfile extends JsonResource
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
            'extra'             =>  [
                'about_me'      =>  $this->about_me ?? "",
                'fav_movie'     =>  $this->fav_movie ?? "",
            ],
            'location'          =>  new LocationResource($this->location),
            'language'          =>  new LanguageResource($this->language),
            'interests'         =>  UserInterestResource::collection($this->interests),
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "",
            'personality'       =>  new PersonalityResource($this->personality),
            'personal'          =>  [
                new ProfileDetailResource($this->education),
                new ProfileDetailResource($this->university),
                new ProfileDetailResource($this->profession),
                new ProfileDetailResource($this->religion),
                new ProfileDetailResource($this->relationshipStatus),
                new ProfileDetailResource($this->youAreHere),
                new ProfileDetailResource($this->foodPreference),
                new ProfileDetailResource($this->drinking),
                new ProfileDetailResource($this->smoking),
                new ProfileDetailResource($this->pet),
                new ProfileDetailResource($this->starSign),
                new ProfileDetailResource($this->community),
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
