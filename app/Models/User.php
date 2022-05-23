<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\ { DB, Auth };
use Laravel\Sanctum\HasApiTokens;
use App\Models\Like;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function getRouteKeyName(){ return 'custom_id'; }

    protected $fillable = [
        'custom_id', 'full_name', 'email', 'country_code', 'contact_no', 'birth_date', 'gender',
        'interest', 'country_id', 'location_id', 'language_id', 'profile_photo', 'voice', 'voice_answer', 'password',
        'facebook_id', 'google_id', 'apple_id',
        'about_me', 'fav_movie',
        'personality_id', 'university_id', 'profession_id',
        'relationship_status_id', 'you_are_here_id', 'food_preference_id',
        'drinking_id', 'smoking_id', 'pet_id', 'star_sign_id', 
        'religion_id', 'community_id', 'education_id',
        'discover_distance', 'discover_start_age', 'discover_end_age', 'discover_location_id',
        'verify_email_send',
        'verify_photo', 'verify_video', 'photo_suggestion', 'video_suggestion',
        'verify_photo_status', 'verify_video_status',
        'verify_status', 'email_verified_at', 'photo_verified_at', 'video_verified_at',
    ];
    
    public function getEmailVerifiedAtAttribute($email_verified_at){ 
        $email_value = "";
        $email_verified_at ? $email_value = date('Y-m-d H:i:s', strtotime($email_verified_at)) : $email_value = "";
        return $email_value; 
    }

    public function deviceToken() { return $this->hasOne('App\Models\DeviceToken'); }
    public function country(){ return $this->belongsTo('App\Models\Country'); }
    public function location(){ return $this->belongsTo('App\Models\Location'); }
    public function language(){ return $this->belongsTo('App\Models\Language'); }

    public function blockBys(){ return $this->hasMany('App\Models\BlockUser','block_by','id'); }
    public function blockedTos(){ return $this->hasMany('App\Models\BlockUser','blocked_to','id'); }

    public function userSettings(){ return $this->hasMany('App\Models\UserSetting','user_id','id'); }
    public function discoveryLocation(){ return $this->belongsTo('App\Models\Location','discover_location_id'); }

    public function likes(){ return $this->hasMany('App\Models\Like','user_id','id'); }
    public function interests(){ return $this->hasMany('App\Models\UserInterest'); }
    public function userDetails(){ return $this->hasMany('App\Models\UserDetail')->orderBy('sequence'); }
    public function subAccount(){ return $this->hasOne('App\Models\TwilioSubaccount','user_id','id'); }
    public function userCommunication(){ return $this->hasOne('App\Models\UserCommunication', 'user_id'); }

    // Basic
    public function personality(){ return $this->hasOne('App\Models\Personality','id','personality_id','id'); }
    public function education(){ return $this->hasOne('App\Models\ProfileDetail','id','education_id'); }
    public function university(){ return $this->hasOne('App\Models\ProfileDetail','id','university_id'); }
    public function profession(){ return $this->hasOne('App\Models\ProfileDetail','id','profession_id'); }
    public function religion(){ return $this->hasOne('App\Models\ProfileDetail','id','religion_id'); }

    public function relationshipStatus(){ return $this->hasOne('App\Models\ProfileDetail','id','relationship_status_id'); }
    public function youAreHere(){ return $this->hasOne('App\Models\ProfileDetail','id','you_are_here_id'); }
    public function foodPreference(){ return $this->hasOne('App\Models\ProfileDetail','id','food_preference_id'); }
    public function drinking(){ return $this->hasOne('App\Models\ProfileDetail','id','drinking_id'); }
    public function smoking(){ return $this->hasOne('App\Models\ProfileDetail','id','smoking_id'); }
    public function pet(){ return $this->hasOne('App\Models\ProfileDetail','id','pet_id'); }
    public function starSign(){ return $this->hasOne('App\Models\ProfileDetail','id','star_sign_id'); }
    public function community(){ return $this->hasOne('App\Models\ProfileDetail','id','community_id'); }

    public function getAge(){ return \Carbon\Carbon::parse($this->birth_date)->diff(\Carbon\Carbon::now())->y; }
    public function getVerifiedStatus(){ return $this->verify_status; }
    public function countMatches(){
        return DB::table('likes')
            ->join("likes as like", function($q){
                $q->on("likes.liker_id", "=", "like.user_id");
                $q->on("like.liker_id", "=", "likes.user_id");
            })
            ->join('users', function($q){
                $q->on('users.id',"=", "likes.user_id");
            })
            //to only get users details who likes current user
            ->where("likes.liker_id", '=', $this->id)
            ->where("likes.user_id", '!=', $this->id)
            ->count();
    }
    public function countChats(){ 
        return ChatRoom::whereHas('chatMessages')->whereIsActive('y')->whereCreatorId($this->id)
                ->orWhere('participate_id',$this->id)->count();
    }

    public function getProfileImages(){
        $imgs = [];
        if($this->userDetails){
            foreach($this->userDetails as $key => $userDetail){
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
        if($this->userDetails){
            foreach($this->userDetails as $key => $userDetail){
                $video = generateURL($userDetail->video);
                if(!empty($video)){
                    $videos[$key]['id']   =   $userDetail->custom_id;  
                    $videos[$key]['url']  =   $video; 
                }
            }
        }
        return $videos;
    }

    public function isProfileSetuped(){
        return !empty($this->full_name)
            && !empty($this->birth_date)
            && !empty($this->email)
            && !empty($this->gender) && !empty($this->interest)
            && !empty($this->location_id) && !empty($this->language_id) 
            && !empty($this->profile_photo) ? true : false;
    }

    public function isSocialUser(){
        return $this->is_social_user == 'y' ? true : false;
    }

    public function emailVerifyStatus(){
        $status = "pending";
        if($this->verify_email_send == 'y'){
            $status = "under_review";
            if(!empty($this->email_verified_at)){ $status = "verified"; }
        }
        return $status;
    }


    /**
     * Calculation Profile Completion In Percentage
     * return $percentage
     */
    public function calculateProfilePercent(){
        // Max Ponits
        $maximum_points        =  config('utility.profile.percent.maximum_points');

        $photo_detail          =  $this->userDetails->where('image','!=',null);
        $video_detail          =  $this->userDetails->where('video','!=',null);

        // Improtant Details
        $language              =  !empty($this->language_id) ? config('utility.profile.percent.language') : 0;
        $full_name             =  !empty($this->full_name) ? config('utility.profile.percent.full_name') : 0;
        $birth_date            =  !empty($this->birth_date) ? config('utility.profile.percent.birth_date') : 0;
        $location              =  !empty($this->location_id) ? config('utility.profile.percent.location') : 0;
        $interest              =  !empty($this->interest) ? config('utility.profile.percent.interest') : 0;
        
        // Verification
        $photo_verified        =  !empty($this->photo_verified_at) ? config('utility.profile.percent.photo_verified') : 0;
        $email_verified        =  !empty($this->email_verified_at) ? config('utility.profile.percent.email_verified') : 0;
        $video_verified        =  !empty($this->video_verified_at) ? config('utility.profile.percent.video_verified') : 0;
        $contact_no            =  !empty($this->contact_no) ? config('utility.profile.percent.contact_no') : 0;
        
        // Photos & Video
        $photo                  =  $photo_detail->count() * config('utility.profile.percent.photo_detail');
        $video                  =  $video_detail->isNotEmpty() ? config('utility.profile.percent.video_detail') : 0;

        // Basic Details
        $about_me              =  !empty($this->about_me) ? config('utility.profile.percent.about_me') : 0;
        $voice_prompt          =  !empty($this->voice) ? config('utility.profile.percent.voice_prompt') : 0;
        $personality           =  !empty($this->personality_id) ? config('utility.profile.percent.personality') : 0;
        $relationship_status   =  !empty($this->relationship_status_id) ? config('utility.profile.percent.relationship_status') : 0;
        $you_are_here          =  !empty($this->you_are_here_id) ? config('utility.profile.percent.you_are_here') : 0;
        $food_preference       =  !empty($this->food_preference_id) ? config('utility.profile.percent.food_preference') : 0;
        $drinking              =  !empty($this->drinking_id) ? config('utility.profile.percent.drinking') : 0;
        $smoking               =  !empty($this->smoking_id) ? config('utility.profile.percent.smoking') : 0;
        $pet                   =  !empty($this->pet_id) ? config('utility.profile.percent.pet') : 0;
        $education             =  !empty($this->education_id) ? config('utility.profile.percent.education') : 0;
        $university            =  !empty($this->university_id) ? config('utility.profile.percent.university') : 0;
        $profession            =  !empty($this->profession_id) ? config('utility.profile.percent.profession') : 0;
        $star_sign             =  !empty($this->star_sign_id) ? config('utility.profile.percent.star_sign') : 0;
        $religion              =  !empty($this->religion_id) ? config('utility.profile.percent.religion') : 0;
        $community             =  !empty($this->community_id) ? config('utility.profile.percent.community') : 0;

        // Interests
        $fav_movie             =  !empty($this->fav_movie) ? config('utility.profile.percent.favourite_movie') : 0;
        $interest_percenrage   =  $this->interests->count() * config('utility.profile.percent.interests');

        $percentage = intval(round(($language+$full_name+$birth_date+$location+$interest+$photo_verified+$email_verified+$video_verified+$contact_no+$photo+$video+$about_me+$voice_prompt+$personality+$relationship_status+$you_are_here+$food_preference+$drinking+$smoking+$pet+$education+$university+$profession+$star_sign+$religion+$community+$fav_movie+$interest_percenrage)
            *$maximum_points/100));
        
        return $percentage;
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
