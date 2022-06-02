<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
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
            'extra'             =>  [
                'about_me'      =>  $this->userTranslation ? $this->userTranslation->about_me : "",
            ],
            'location'          =>  new LocationResource($this->location),
            'interests'         =>  UserInterestResource::collection($this->interests),
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
            'media' =>  [
                'profile_images'        =>  $this->getProfileImages(),
                'profile_videos'        =>  $this->getProfileVideos(),
                'profile_voice'         =>  [
                    'voice'             =>  generateURL($this->voice),
                    'voice_answer'      =>  $this->voice_answer ?? "",
                ],
            ],
            'flags'            =>  [
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
