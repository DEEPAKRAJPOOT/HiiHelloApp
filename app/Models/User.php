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
        'interest', 'country_id', 'location_id', 'language_id', 'profile_photo', 'password',
        'facebook_id', 'google_id', 'apple_id',
        'about_me',
        'relationship_status_id', 'you_are_here_id', 'food_preference_id', 'drinking_id', 'smoking_id', 'star_sign_id', 
        'religion_id', 'community_id', 'education_id', 'occupation_id',
        'date_idea_id', 'social_cause_id', 'risk_taken_id', 'perfect_relation_id', 'my_mantra_id', 'one_thing_know_id', 'worst_date_id', 
        'intro_family_id', 'found_one_id', 'about_surprising_id', 'political_view_id',
        'verify_photo', 'verify_video', 'verify_status', 'email_verified_at', 'photo_verified_at', 'video_verified_at',
    ];
    
    public function getEmailVerifiedAtAttribute($email_verified_at){ return date('Y-m-d H:i:s', strtotime($email_verified_at)); }

    public function deviceToken() { return $this->hasOne('App\Models\DeviceToken'); }
    public function country(){ return $this->belongsTo('App\Models\Country'); }
    public function location(){ return $this->belongsTo('App\Models\Location'); }
    public function language(){ return $this->belongsTo('App\Models\Language'); }

    public function likes(){ return $this->hasMany('App\Models\Like','user_id','id'); }
    public function interests(){ return $this->hasMany('App\Models\UserInterest'); }
    public function favFestivals(){ return $this->hasMany('App\Models\UserFestival'); }
    public function pets(){ return $this->hasMany('App\Models\UserPet'); }
    public function userDetails(){ return $this->hasMany('App\Models\UserDetail'); }
    public function subAccount(){ return $this->hasOne('App\Models\TwilioSubaccount','user_id','id'); }
    public function userCommunication(){ return $this->hasOne('App\Models\UserCommunication', 'user_id'); }

    public function relationshipStatus(){ return $this->hasOne('App\Models\ProfileDetail','id','relationship_status_id'); }
    public function youAreHere(){ return $this->hasOne('App\Models\ProfileDetail','id','you_are_here_id'); }
    public function foodPreference(){ return $this->hasOne('App\Models\ProfileDetail','id','food_preference_id'); }
    public function drinking(){ return $this->hasOne('App\Models\ProfileDetail','id','drinking_id'); }
    public function smoking(){ return $this->hasOne('App\Models\ProfileDetail','id','smoking_id'); }
    public function starSign(){ return $this->hasOne('App\Models\ProfileDetail','id','star_sign_id'); }
    public function religion(){ return $this->hasOne('App\Models\ProfileDetail','id','religion_id'); }
    public function community(){ return $this->hasOne('App\Models\ProfileDetail','id','community_id'); }
    public function education(){ return $this->hasOne('App\Models\ProfileDetail','id','education_id'); }
    public function occupation(){ return $this->hasOne('App\Models\ProfileDetail','id','occupation_id'); }
    public function dateIdea(){ return $this->hasOne('App\Models\ProfileDetail','id','date_idea_id'); }
    public function socialCause(){ return $this->hasOne('App\Models\ProfileDetail','id','social_cause_id'); }
    public function riskTaken(){ return $this->hasOne('App\Models\ProfileDetail','id','risk_taken_id'); }
    public function perfectRelation(){ return $this->hasOne('App\Models\ProfileDetail','id','perfect_relation_id'); }
    public function myMantra(){ return $this->hasOne('App\Models\ProfileDetail','id','my_mantra_id'); }
    public function oneThingKnow(){ return $this->hasOne('App\Models\ProfileDetail','id','one_thing_know_id'); }
    public function worstDate(){ return $this->hasOne('App\Models\ProfileDetail','id','worst_date_id'); }
    public function introFamily(){ return $this->hasOne('App\Models\ProfileDetail','id','intro_family_id'); }
    public function foundOne(){ return $this->hasOne('App\Models\ProfileDetail','id','found_one_id'); }
    public function aboutSurprising(){ return $this->hasOne('App\Models\ProfileDetail','id','about_surprising_id'); }
    public function politicalView(){ return $this->hasOne('App\Models\ProfileDetail','id','political_view_id'); }

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

    public function getProfileVoices(){ 
        $voices = [];
        if($this->userDetails){
            foreach($this->userDetails as $key => $userDetail){
                $voice = generateURL($userDetail->voice);
                if(!empty($voice)){
                    $voices[$key]['id']   =   $userDetail->custom_id;  
                    $voices[$key]['url']  =   $voice; 
                }
            }
        }
        return $voices;
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

    /**
     * Calculation Profile Completion In Percentage
     * return $percentage
     */
    public function calculateProfilePercent(){
        // Max Ponits
        $maximum_points        =  config('utility.profile.percent.maximum_points');

        $voice_detail          =  $this->userDetails->where('voice','!=',null);
        // Improtant Details
        $full_name             =  !empty($this->full_name) ? config('utility.profile.percent.full_name') : 0;
        $photo_verified        =  !empty($this->photo_verified_at) ? config('utility.profile.percent.photo_verified') : 0;
        $email_verified        =  !empty($this->email_verified_at) ? config('utility.profile.percent.email_verified') : 0;
        $id_verified           =  $this->verify_status == 'verified' ? config('utility.profile.percent.id_verified') : 0;
        $all_photos_verified   =  !empty($this->photo_verified_at) ? config('utility.profile.percent.all_photos_verified') : 0;
        $video_verified        =  !empty($this->video_verified_at) ? config('utility.profile.percent.video_verified') : 0;
        $interest              =  !empty($this->interest) ? config('utility.profile.percent.interest') : 0;
        $voice_prompt          =  $voice_detail->isNotEmpty() ? config('utility.profile.percent.voice_prompt') : 0;
        $about_me              =  !empty($this->about_me) ? config('utility.profile.percent.about_me') : 0;

        // Basic Details
        $relationship_status   =  !empty($this->relationship_status_id) ? config('utility.profile.percent.relationship_status') : 0;
        $you_are_here          =  !empty($this->you_are_here_id) ? config('utility.profile.percent.you_are_here') : 0;
        $food_preference       =  !empty($this->food_preference_id) ? config('utility.profile.percent.food_preference') : 0;
        $drinking              =  !empty($this->drinking_id) ? config('utility.profile.percent.drinking') : 0;
        $smoking               =  !empty($this->smoking_id) ? config('utility.profile.percent.smoking') : 0;
        $star_sign             =  !empty($this->star_sign_id) ? config('utility.profile.percent.star_sign') : 0;
        $religion              =  !empty($this->religion_id) ? config('utility.profile.percent.religion') : 0;
        $community             =  !empty($this->community_id) ? config('utility.profile.percent.community') : 0;
        $pets                  =  $this->pets->isNotEmpty() ? config('utility.profile.percent.pets') : 0;
        $education             =  !empty($this->education_id) ? config('utility.profile.percent.education') : 0;
        $occupation            =  !empty($this->occupation_id) ? config('utility.profile.percent.occupation') : 0;

        // Extra Details
        $date_idea          =  !empty($this->date_idea_id) ? config('utility.profile.percent.date_idea') : 0;
        $social_cause       =  !empty($this->social_cause_id) ? config('utility.profile.percent.social_cause') : 0;
        $risk_taken         =  !empty($this->risk_taken_id) ? config('utility.profile.percent.risk_taken') : 0;
        $perfect_relation   =  !empty($this->perfect_relation_id) ? config('utility.profile.percent.perfect_relation') : 0;
        $my_mantra          =  !empty($this->my_mantra_id) ? config('utility.profile.percent.my_mantra') : 0;
        $one_thing_know     =  !empty($this->one_thing_know_id) ? config('utility.profile.percent.one_thing_know') : 0;
        $worst_date         =  !empty($this->worst_date_id) ? config('utility.profile.percent.worst_date') : 0;
        $intro_family       =  !empty($this->intro_family_id) ? config('utility.profile.percent.intro_family') : 0;
        $found_one          =  !empty($this->found_one_id) ? config('utility.profile.percent.found_one') : 0;
        $about_surprising   =  !empty($this->about_surprising_id) ? config('utility.profile.percent.about_surprising') : 0;
        $political_views    =  !empty($this->political_view_id) ? config('utility.profile.percent.political_views') : 0;

        $percentage = intval(round(($full_name+$photo_verified+$email_verified+$id_verified+$all_photos_verified+$video_verified+$interest+$voice_prompt+$about_me+$relationship_status+$you_are_here+$food_preference+$drinking+$smoking+$star_sign+$religion+$community+$pets+$education+$occupation+$date_idea+$social_cause+$risk_taken+$perfect_relation+$my_mantra+$one_thing_know+$worst_date+$intro_family+$found_one+$about_surprising+$political_views)*$maximum_points/100));
        
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
