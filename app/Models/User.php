<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Like;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function getRouteKeyName(){ return 'custom_id'; }

    protected $fillable = [
        'custom_id', 'first_name', 'last_name', 'email', 'country_code', 'contact_no', 'birth_date', 'gender', 'interest', 'country_id', 'location_id', 'language_id', 'profile_photo', 'password',
    ];

    public function country(){ return $this->belongsTo('App\Models\Country'); }
    public function location(){ return $this->belongsTo('App\Models\Location'); }
    public function language(){ return $this->belongsTo('App\Models\Language'); }

    public function interests(){ return $this->hasMany('App\Models\UserInterest'); }
    public function userDetails(){ return $this->hasMany('App\Models\UserDetail'); }

    public function setBirthDateAttribute($birth_date){
        $this->attributes['birth_date'] = \Carbon\Carbon::createFromFormat('m/d/Y', $birth_date);
    }

    public function getAge(){ return \Carbon\Carbon::parse($this->birth_date)->diff(\Carbon\Carbon::now())->y; }
    public function getVerifiedStatus(){ return 'verified'; }
    public function countLikes(){ return Like::whereUserId($this->id)->count() ?? 0; }
    public function countMatches(){ return 0; }
    public function countChats(){ return 0; }
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
        return $this->userDetails->isNotEmpty() && $this->interests->isNotEmpty()
            && !empty($this->first_name) && !empty($this->last_name)
            && !empty($this->country_code) && !empty($this->contact_no) && !empty($this->birth_date)
            && !empty($this->gender) && !empty($this->interest)
            && !empty($this->country_id) && !empty($this->location_id) && !empty($this->language_id) 
            && !empty($this->profile_photo) ? true : false;
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
