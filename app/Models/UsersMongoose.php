<?php

namespace App\Models;

use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UsersMongoose extends Eloquent
{

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = ['user_id', 'custom_id', 'full_name', 'profile_photo', 'language_id', 'lang_code', 'is_active', 'deleted_at'];

    // Enable timestamps 
    public $timestamps = true;

    // Soft delete field
    protected $dates = ['created_at','updated_at','deleted_at'];

    public function chat_initiations(){ return $this->hasMany('App\Models\ChatRoomMongoose', 'creator_id', 'user_id'); }
    public function countChats(){ 
        return ChatRoomMongoose::whereHas('chatMessages',  function ($query) {
                $query->where('status','!=' ,'read')
                       ->where('receiver_id',$this->id);
            })
            ->whereIsActive('y')
            ->where(function ($query) {
                $query->where('creator_id',$this->id)
                    ->orWhere('participate_id',$this->id);
            })->count();
    }

    public function lastOnlineTimeStamp() {
        // Check if last_online is a MongoDB\BSON\UTCDateTime instance
        if ($this->last_online instanceof UTCDateTime) {
            // Convert the MongoDB\BSON\UTCDateTime to a DateTime string
            $dateTimeString = $this->convertUTCDateTimeToString($this->last_online);
            // dd($dateTimeString);
            
            // Return the timestamp in milliseconds if the last_online is valid
            if (!empty($this->last_online) && strtotime($dateTimeString) > 0) {
                return strtotime($dateTimeString) * 1000;
            }
        }
        return '';
    }
    // public function lastOnlineTimeStamp(){
    //     $mongoUtcDateTime = new UTCDateTime($this->last_online);
    //     $dateTimeString = $this->convertUTCDateTimeToString($this->last_online);
    //     dd($dateTimeString);
    //     if(!empty($this->last_online) && strtotime($this->last_online) > 0){
    //         return strtotime($this->last_online) * 1000;
    //     }
    //     return '';
    // }
    
    public function convertUTCDateTimeToString($utcDateTime) {
        // Ensure the input is a MongoDB\BSON\UTCDateTime instance
        if ($utcDateTime instanceof UTCDateTime) {
            // Convert MongoDB\BSON\UTCDateTime to DateTime object
            $dateTime = $utcDateTime->toDateTime();
            
            // Convert DateTime object to Carbon instance
            $carbonDate = Carbon::instance($dateTime);
            
            // Format the Carbon instance to a datetime string
            return $carbonDate->toDateTimeString();
        }
        
        return null;
    }
    

}