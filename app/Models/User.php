<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Like;
use Carbon\Carbon;
use App\Jobs\NotificationJob;

class User extends Authenticatable implements MustVerifyEmail, TranslatableContract
{
    use HasApiTokens, Notifiable, SoftDeletes, Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function getRouteKeyName(){ return 'custom_id'; }

    protected $fillable = [
        'custom_id', 'account_id', 'email', 'country_code', 'contact_no', 'birth_date', 'gender',
        'interest', 'country_id', 'location_id', 'profile_percentage', 'language_id', 'profile_photo', 'voice', 'voice_answer', 'password',
        'swipe_count', 'like_count', 'match_count',
        'is_social_user', 'is_trans_full_name', 'is_trans_about_me', 'is_trans_fav_movie', 
        'is_media_checked', 'is_subscribed', 'subscription_end_date',
        'facebook_id', 'google_id', 'apple_id',
        'personality_id', 'university_id', 'profession_id',
        'relationship_status_id', 'you_are_here_id', 'food_preference_id',
        'drinking_id', 'smoking_id', 'pet_id', 'star_sign_id', 
        'religion_id', 'community_id', 'education_id',
        'discover_distance', 'discover_start_age', 'discover_end_age', 'discover_location_id',
        'verify_email_send',
        'verify_photo', 'verify_video', 'photo_suggestion', 'video_suggestion',
        'verify_photo_status', 'verify_video_status',
        'verify_status', 'email_verified_at', 'contact_verified_at', 'photo_verified_at', 'video_verified_at',
        'reason_of_delete'
    ];
    
    protected $translatedAttributes = ['full_name', 'about_me', 'fav_movie'];

    public function getEmailVerifiedAtAttribute($email_verified_at){ 
        $email_value = "";
        $email_verified_at ? $email_value = date('Y-m-d H:i:s', strtotime($email_verified_at)) : $email_value = "";
        return $email_value; 
    }

    public function getContactVerifiedAtAttribute($contact_verified_at){ 
        $contact_value = "";
        $contact_verified_at ? $contact_value = date('Y-m-d H:i:s', strtotime($contact_verified_at)) : $contact_value = "";
        return $contact_value; 
    }

    // public function getCreatedAtAttribute($created_at){ 
    //     return date('Y-m-d H:i:s', strtotime($created_at));
    // }

    public function userTranslations(){ return $this->hasMany('App\Models\UserTranslation'); }
    public function userTranslation(){ 
        return $this->hasOne('App\Models\UserTranslation')->whereLocale(app()->getlocale());
    }
    public function userTransDefault(){ 
        return $this->hasOne('App\Models\UserTranslation')->whereLocale(config('utility.default_lang_code'));
    }

    public function deviceToken() { return $this->hasOne('App\Models\DeviceToken'); }
    public function country(){ return $this->belongsTo('App\Models\Country'); }
    public function location(){ return $this->belongsTo('App\Models\Location'); }
    public function language(){ return $this->belongsTo('App\Models\Language'); }

    public function blockBys(){ return $this->hasMany('App\Models\BlockUser','block_by','id'); }
    public function blockedTos(){ return $this->hasMany('App\Models\BlockUser','blocked_to','id'); }

    public function userSettings(){ return $this->hasMany('App\Models\UserSetting','user_id','id'); }
    public function discoveryLocation(){ return $this->belongsTo('App\Models\Location','discover_location_id'); }
    public function subscription() { return $this->hasOne('App\Models\Subscription')->latest(); }

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

    public function countChats(){ 
       return ChatRoom::whereHas('chatMessages',  function ($query) {
                $query->where('status','!=' ,'read');
            })
            ->whereIsActive('y')
            ->whereCreatorId($this->id)
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
        $full_name = $this->userTranslation ? $this->userTranslation->full_name : "";

        return !empty($full_name)
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

    public function contactVerifyStatus(){
        $status = "pending";
        if(!empty($this->contact_verified_at)){ $status = "verified"; }
        return $status;
    }

    public function addSwipeCount(){
        $count = $this->swipe_count + 1;
        $this->swipe_count = $count;
        $this->save();
        return $count;
    }

    public function isSwipeAllow(){
        $daily_swipe_limit = config('utility.profile.swipe.daily_limit');
        $is_swipe_allow = true;
        
        if( $this->swipe_count >= $daily_swipe_limit
            &&  $this->gender != 'Female'
            &&  $this->subscription_end_date <= \Carbon\Carbon::today()->format('Y-m-d')
        ){ $is_swipe_allow = false; }

        return $is_swipe_allow;
    }

    public function notifySwipeAlert(){
        $daily_swipe_limit = config('utility.profile.swipe.daily_limit');
        if( $this->swipe_count == $daily_swipe_limit) {
            $notification = [
                'custom_id'     =>  getUniqueString('notifications'),
                'key'           =>  'user_id',
                'value'         =>  $this->id,
                'user_id'       =>  $this->id,
                'title'         =>  trans('api.notify_message.swipe_alert.title'),
                'message'       =>  trans('api.notify_message.swipe_alert.message'),
                'image'         =>  '',
                'type'          =>  config('utility.notification.type.swipe_alert'),
            ];
                    
            // Notify
            $notificationJob = new NotificationJob($notification, $this);
            dispatch($notificationJob);
        }
    }

    /**
     * Calculation Profile Completion In Percentage
     * return $percentage
     */
    public function calculateProfilePercent(){
        // Max Ponits
        $maximum_points        =  config('utility.profile.percent.maximum_points');
        $photo_max_point       =  config('utility.profile.percent.photo_max_point');
        $interests_max_point   =  config('utility.profile.percent.interests_max_point');

        $full_name             =  $this->userTranslation ? $this->userTranslation->full_name : "";
        $about_me              =  $this->userTranslation ? $this->userTranslation->about_me : "";
        $fav_movie             =  $this->userTranslation ? $this->userTranslation->fav_movie : "";
        
        $photo_detail          =  $this->userDetails->where('image','!=',null);
        $video_detail          =  $this->userDetails->where('video','!=',null);

        // Improtant Details
        $language              =  !empty($this->language_id) ? config('utility.profile.percent.language') : 0;
        $full_name             =  !empty($full_name) ? config('utility.profile.percent.full_name') : 0;
        $birth_date            =  !empty($this->birth_date) ? config('utility.profile.percent.birth_date') : 0;
        $location              =  !empty($this->location_id) ? config('utility.profile.percent.location') : 0;
        $interest              =  !empty($this->interest) ? config('utility.profile.percent.interest') : 0;
        
        // Verification
        $photo_verified        =  !empty($this->photo_verified_at) ? config('utility.profile.percent.photo_verified') : 0;
        $email_verified        =  !empty($this->email_verified_at) ? config('utility.profile.percent.email_verified') : 0;
        $video_verified        =  !empty($this->video_verified_at) ? config('utility.profile.percent.video_verified') : 0;
        $contact_verified      =  !empty($this->contact_verified_at) ? config('utility.profile.percent.contact_no') : 0;
        
        // Photos & Video
        $main_photo             =  !empty($this->profile_photo) ? config('utility.profile.percent.main_photo') : 0;
        $photo                  =  $photo_detail->count() * config('utility.profile.percent.photo_detail');
        $video                  =  $video_detail->isNotEmpty() ? config('utility.profile.percent.video_detail') : 0;

        // Basic Details
        $about_me              =  !empty($about_me) ? config('utility.profile.percent.about_me') : 0;
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
        $fav_movie             =  !empty($fav_movie) ? config('utility.profile.percent.favourite_movie') : 0;
        $interest_percent      =  $this->interests->count() * config('utility.profile.percent.interests');

        if($photo > $photo_max_point){ $photo = $photo_max_point; }
        if($interest_percent > $interests_max_point){ $interest_percent = $interests_max_point; }

        $percentage = intval(round(($language+$full_name+$birth_date+$location+$interest+$photo_verified+$email_verified+$video_verified+$contact_verified+$main_photo+$photo+$video+$about_me+$voice_prompt+$personality+$relationship_status+$you_are_here+$food_preference+$drinking+$smoking+$pet+$education+$university+$profession+$star_sign+$religion+$community+$fav_movie+$interest_percent)
            *$maximum_points/100));
            
        if($percentage != $this->profile_percentage){
            $this->profile_percentage = $percentage;
            $this->save();
        }

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
        'contact_verified_at' => 'datetime',
    ];
}
