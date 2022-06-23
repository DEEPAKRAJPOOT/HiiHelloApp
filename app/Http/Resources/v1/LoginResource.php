<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LoginResource extends JsonResource
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
                'code'      =>  $this->country_code,
                'number'    =>  $this->contact_no,
            ],
            'age'               =>  $this->getAge(),
            'gender'            =>  $this->gender ?? "",
            'interest'          =>  $this->interest ?? "",
            'location'          =>  new LocationResource($this->location),
            'interests'         =>  HomeInterestResource::collection($this->interests),
            'language'          =>  new LanguageResource($this->language),
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "",
            'media' =>  [
                'profile_images'    =>  $this->getProfileImages(),
                'profile_videos'    =>  $this->getProfileVideos(),
            ],
            'flags'             =>  [
                'social_user'           =>  $this->isSocialUser(),
                'profile_setuped'       =>  $this->isProfileSetuped(),
                'profile_percentage'    =>  $this->calculateProfilePercent(),
                'verified_staus'        =>  $this->getVerifiedStatus(),
                'likes'                 =>  $this->like_count ?? 0,
                'matches'               =>  $this->match_count ?? 0,
                'chats'                 =>  $this->countChats() ?? 0,
                'email_verified_at'     =>  $this->email_verified_at ?? "",
                'contact_verified_at'   =>  $this->contact_verified_at ?? "",
                'photo_verified_at'     =>  $this->photo_verified_at ?? "",
                'video_verified_at'     =>  $this->video_verified_at ?? "",
            ],
        ];
    }

    public function with($request)
    {
        $is_subscribed = false;
        $subscription_end_date = "";
        if( !Auth::guest() ) {
            if( Auth::user()->is_subscribed == 'y' && Auth::user()->subscription_end_date >= \Carbon\Carbon::today()->format('Y-m-d') ){
                $is_subscribed = true;
            }
            $subscription_end_date = Auth::user()->subscription_end_date ?? "";
        }
        return [
            'meta' => [ 
                'api'                       =>  'v.1.0',
                'url'                       =>  url()->current(),
                'language'                  =>  app()->getLocale(),
                'is_subscribed'             =>  $is_subscribed,
                'subscription_end_date'     =>  $subscription_end_date,
            ],
        ];
    }
}
