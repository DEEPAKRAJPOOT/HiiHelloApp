<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon;

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
            'full_name'         =>  $this->full_name ? $this->full_name : "",
            'age'               =>  $this->getAge(),
            'gender'            =>  $this->gender ?? "",
            'isProfileVerified' =>  ($this->emailVerifyStatus()=='verified' && $this->contactVerifyStatus()=='verified' && $this->verify_photo_status=='verified') ? true : false,
            'is_email_verify'   =>  ($this->emailVerifyStatus()=='verified') ? true : false,
            'is_contact_verify' =>  ($this->contactVerifyStatus()=='verified') ? true : false,
            'is_photo_verify'   =>  ($this->verify_photo_status=='verified') ? true : false,
            'trusted_score'     =>  $this->trusted_score,
            'location'          =>  new LocationResource($this->location),
            'interests'         =>  HomeInterestResource::collection($this->interests),
            'profile_photo'     =>  generateURL($this->profile_photo) ?? "",           
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
                'last_seen'        =>  strtotime($this->last_online) * 1000,
                'likes_count'      => $this->likes_count??0
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

    public function getAge(){ 
        
        return \Carbon\Carbon::parse($this->birth_date)->diff(\Carbon\Carbon::now())->y; 
    }

    public function emailVerifyStatus(){
        $status = "pending";
        if($this->verify_email_send == 'y'){
            $status = "under_review";
        }
        if(!empty($this->email_verified_at)){ $status = "verified"; }
        
        return $status;
    }

    public function contactVerifyStatus(){
        $status = "pending";
        if(!empty($this->contact_verified_at)){ $status = "verified"; }
        return $status;
    }

    public function getProfileImages(){
        $imgs = [];
        if($this->user_details){
            foreach($this->user_details as $key => $userDetail){
                $image = generateURL($userDetail->image);
                if(!empty($image)){
                    $imgs[$key]['id']     =   $userDetail->custom_id; 
                    $imgs[$key]['url']    =   $image; 
                }
            }
        }
        return $imgs;
    }

    public function getProfileVideos(){ 
        $videos = [];
        if($this->user_details){
            foreach($this->user_details as $key => $userDetail){
                $video = generateURL($userDetail->video);
                if(!empty($video)){
                    $videos[$key]['id']   =   $userDetail->custom_id;  
                    $videos[$key]['url']  =   $video; 
                }
            }
        }
        return $videos;
    }

    public function onlineStatus(){
        if(!empty($this->last_online)){
            $online_time_limit = config('utility.profile.durations.online_time');
            $recent_time_limit = config('utility.profile.durations.recent_online_time');
            $time_difference = time() - strtotime($this->last_online);
            if($time_difference < ($online_time_limit * 60)){
                return 'online';
            }
            if($time_difference < ($recent_time_limit * 60)){
                return 'recent';
            }
        }
        return 'offline';
    }

    public function isNewAccount(){
        $new_account_limit = config('utility.profile.durations.new_profile_time'); // in days
        $time_in_seconds = intval($new_account_limit) * 86400;
        return ((time() - strtotime($this->created_at)) < $time_in_seconds);
    }
}
