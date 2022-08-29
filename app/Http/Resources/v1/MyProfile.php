<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
            'full_name'         =>  $this->userTranslation ? $this->userTranslation->full_name : "",
            'twilio_identifier' =>  $this->userTransEn ? $this->userTransEn->full_name : "",
            'age'               =>  $this->getAge(),
            'gender'            =>  $this->gender ?? "",
            'interest'          =>  $this->interest ?? "",
            'extra'             =>  [
                'about_me'      =>  $this->userTranslation ? $this->userTranslation->about_me : "",
                'fav_movie'     =>  $this->userTranslation ? $this->userTranslation->fav_movie : "",
            ],
            'location'          =>  new LocationResource($this->location),
            'language'          =>  new LanguageResource($this->language),
            'interests'         =>  UserInterestResource::collection($this->interests),
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "",
            'subscription'      =>  new SubscriptionResource($this->subscription),
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
                'personalities'         =>  UserPersonalityResource::collection($this->personalities),
                'education'             =>  new ProfileDetailResource($this->education),
                'university_college'    =>  new ProfileDetailResource($this->university),
                'profession'            =>  new ProfileDetailResource($this->profession),
                'religion'              =>  new ProfileDetailResource($this->religion),
            ],
            'media' =>  [
                'profile_images'    =>  $this->getProfileImages(),
                'profile_videos'    =>  $this->getProfileVideos(),
                'profile_voice'     =>  [
                    'voice'         =>  generateURL($this->voice),
                    'voice_answer'  =>  $this->voice_answer ?? "",
                ],
            ],
            'flags'             =>  [
                'social_user'           =>  $this->isSocialUser(),
                'profile_setuped'       =>  $this->isProfileSetuped(),
                'profile_percentage'    =>  $this->calculateProfilePercent(),
                'verified_staus'        =>  $this->verify_status,
                'likes'                 =>  $this->like_count ?? 0,
                'matches'               =>  $this->match_count ?? 0,
                'chats'                 =>  $this->countChats() ?? 0,
            ],
        ];
    }

    public function with($request)
    {
        $is_subscribed = $is_feature_allow = false;
        $subscription_end_date = "";
        if( !Auth::guest() ) {
            if( Auth::user()->is_subscribed == 'y' && Auth::user()->subscription_end_date >= \Carbon\Carbon::today()->format('Y-m-d') ){
                $is_subscribed = true;
            } elseif (Auth::user()->gender == 'Female'){
                $is_subscribed = true;
            }
            $subscription_end_date = Auth::user()->subscription_end_date ?? "";

            if($is_subscribed && ($this->start_date == \Carbon\Carbon::today()->format('Y-m-d')) ){
                $is_feature_allow = true;
            }else if($is_subscribed && Auth::user()->verify_status == 'verified'){
                $is_feature_allow = true;
            }
        }
        return [
            'meta' => [ 
                'api'                       =>  'v.1.0',
                'url'                       =>  url()->current(),
                'language'                  =>  app()->getLocale(),
                'is_subscribed'             =>  $is_subscribed,
                'subscription_end_date'     =>  $subscription_end_date,
                'is_feature_allow'          =>  $is_feature_allow,
            ],
        ];
    }
}
