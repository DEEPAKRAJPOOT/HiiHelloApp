<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UsersMongoose;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDateTime;

class ChatMessageMongoose extends Eloquent
{
    use HasFactory, SoftDeletes;

   

    protected $connection = 'mongodb';
    protected $collection = 'messages';

    protected $fillable = ['custom_id', 'room_id', 'sender_id', 'receiver_id', 'message', 'status', 'expired_at', 'is_vanished', 'sender_deleted_at', 'is_verified', 'created_at', 'updated_at', 'reply_sender_name', 'reply_message_id', 'reply_type', 'reply_value', 'reply_message_file_path', 'reply_message_file_type','deleted_at','created_at', 'updated_at', ];

    // Enable timestamps
    public $timestamps = true;

    // Soft delete field
    protected $dates = ['deleted_at', 'expired_at', 'sender_deleted_at'];


    public function getRouteKeyName(){ return 'custom_id'; }
    public function room(){ return $this->belongsTo('App\Models\ChatRoomMongoose','room_id','_id'); }
    public function sender(){ return $this->belongsTo('App\Models\UsersMongoose','sender_id','user_id'); }
    public function receiver(){ return $this->belongsTo('App\Models\UsersMongoose','receiver_id','user_id'); }

    public function getCreatedAtAttribute($created_at){ return date('Y-m-d H:i:s', strtotime($created_at)); }
    public function getUpdatedAtAttribute($updated_at){ return date('Y-m-d H:i:s', strtotime($updated_at)); }
    public function getDeletedAtAttribute($deleted_at){ 
        return $deleted_at ? date('Y-m-d H:i:s', strtotime($deleted_at)) : ""; 
    }

    public function getLatestMessageCreatedAtAttribute()
    {
       
        if ($this->latestMessage && isset($this->latestMessage->created_at)) {
            return convertMongoUTCDateTimeToIST($this->latestMessage->created_at);
        }
        return '';
    }

    public function getLatestMessageUpdatedAtAttribute()
    {
        
        if ($this->latestMessage && isset($this->latestMessage->updated_at)) {
            return convertMongoUTCDateTimeToIST($this->latestMessage->updated_at);
        }
        return '';
    }

    public function convertMongoUTCDateTimeToIST($mongoUTCDateTime) {
        $dateTime = $mongoUTCDateTime->toDateTime();
        $carbonDate = Carbon::instance($dateTime);
        $istDate = $carbonDate->setTimezone('Asia/Kolkata');
        return $istDate->toDateTimeString();
    }

    public function notifyChatMessageToUser($message) {
        if($this->receiver){
            $this->receiver->increment('chat_count');
            $this->receiver->notify(new ChatNotification($this->chatPushNFData($this->sender, $this, $message))); 
        }
    }

    protected function chatPushNFData($account, $chatMessage, $message = ""){
        $message = trim( preg_replace("/\r|\n/", " ", $message) );

        $lang_code = $this->receiver ? $this->receiver->language ? $this->receiver->language->lang_code : "en" : "en";
        $userTranslation = UserTranslation::select('full_name')->whereUserId($account->id)->whereLocale($lang_code)->first();
        if($userTranslation){ 
            $full_name = $userTranslation->full_name ?? "";
        }else{
            $full_name = $account->userTranslation ? $account->userTranslation->full_name : "";
        }

        if( $message == "" ) {
            $message =  $full_name." has sent you a image 📷.";
        }
        return [
            'title'     =>  $full_name,
            'type'      =>  config('utility.notification.type.chat_message'),
            'id'        =>  $chatMessage->custom_id,
            'room_id'   =>  $chatMessage->room ? $chatMessage->room->custom_id : "",
            'user_id'   =>  $account ? $account->custom_id : "",
            'name'      =>  $full_name,
            'profile'   =>  generateURL($account->profile_photo),
            'message'   =>  $message,
        ];
    }

    public function getMessage(){
        
        $message = $this->message;
        if(!empty($message) && !empty($message->type)){            
            if($message->type == 'location'){
                if(!empty($message->other) && !empty($message->other->lat && !empty($message->other->lng) ) ){
                    $message->other->url = 'https://maps.googleapis.com/maps/api/staticmap?center='.$message->other->lat.','.$message->other->lng.'&zoom=14&size=400x400&markers='.$message->other->lat.','.$message->other->lng.'&markers=color:red&key=AIzaSyA2GIt7Ld9duVo85H4Mr15Y_v7Sc6pfzlQ';
                }
            }
        }

        return $message;
    }

    public function getLatest(){
        return ChatMessageMongoose::where('room_id',$this->room_id)->where('receiver_id',$this->receiver_id)->orderBy('created_at','desc')->first();
    }

    public function getSender($sender_id){
        if(!is_null($sender_id)){
            $senderData = UsersMongoose::select('custom_id')->where('user_id',$sender_id)->first();
            if(!empty($senderData)){
                return $senderData->custom_id;
            }
        }

        return "";
    }
}
