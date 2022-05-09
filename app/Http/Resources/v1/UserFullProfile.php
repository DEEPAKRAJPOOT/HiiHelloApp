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
            'contact'       =>  [
                'code'      =>  $this->country_code,
                'number'    =>  $this->contact_no,
            ],
            'is_blocked'        =>  $this->blocked_tos_count ? $this->blocked_tos_count > 0 ? true : false : false,
            'birth_date'        =>  $this->birth_date ?? "",
            'age'               =>  $this->getAge(),
            'gender'            =>  $this->gender ?? "",
            'interest'          =>  $this->interest ?? "",
            'country'           =>  new CountryResource($this->country),
            'location'          =>  new LocationResource($this->location),
            'language'          =>  new LanguageResource($this->language),
            'interests'         =>  UserInterestResource::collection($this->interests),
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "",
            'personal'          =>  [
                'relationship_status'   =>  new ProfileDetailResource($this->relationshipStatus),
                'you_are_here'          =>  new ProfileDetailResource($this->youAreHere),
                'food_preference'       =>  new ProfileDetailResource($this->foodPreference),
                'drinking'              =>  new ProfileDetailResource($this->drinking),
                'smoking'               =>  new ProfileDetailResource($this->smoking),
                'star_sign'             =>  new ProfileDetailResource($this->starSign),
                'religion'              =>  new ProfileDetailResource($this->religion),
                'community'             =>  new ProfileDetailResource($this->community),
                'education'             =>  new ProfileDetailResource($this->education),
                'occupation'            =>  new ProfileDetailResource($this->occupation),
                'date_idea'             =>  new ProfileDetailResource($this->dateIdea),
                'social_cause'          =>  new ProfileDetailResource($this->socialCause),
                'risk_taken'            =>  new ProfileDetailResource($this->riskTaken),
                'perfect_relation'      =>  new ProfileDetailResource($this->perfectRelation),
                'my_mantra'             =>  new ProfileDetailResource($this->myMantra),
                'one_thing_know'        =>  new ProfileDetailResource($this->oneThingKnow),
                'worst_date'            =>  new ProfileDetailResource($this->worstDate),
                'intro_family'          =>  new ProfileDetailResource($this->introFamily),
                'found_one'             =>  new ProfileDetailResource($this->foundOne),
                'occupation'            =>  new ProfileDetailResource($this->aboutSurprising),
                'occupation'            =>  new ProfileDetailResource($this->politicalView),
                'fav_festivals'         =>  UserFestivalResource::collection($this->favFestivals),
                'pets'                  =>  UserPetResource::collection($this->pets),
                'about_me'              =>  $this->about_me ?? "",
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
