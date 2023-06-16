<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\ { NotificationJob };

class ChatRoom extends Model
{
    use HasFactory, SoftDeletes;

    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = ['custom_id', 'creator_id', 'participate_id', 'block_by', 'creator_cleared_at', 'participate_cleared_at', 'creator_deleted_at', 'participate_deleted_at', 'vanish_mode'];

    public function creator(){ return $this->belongsTo('App\Models\User','creator_id','id'); }
    public function participator(){ return $this->belongsTo('App\Models\User','participate_id','id'); }
    public function blockBy(){ return $this->belongsTo('App\Models\User','block_by','id'); }
    public function latestMessage() { return $this->hasOne(ChatMessage::class,'room_id','id')->where(function($query){
        $query->whereNull('expired_at');
        $query->orWhere('expired_at','>',now());
    })->latest('id'); }
    public function chatMessages() { return $this->hasMany(ChatMessage::class,'room_id','id'); }
    public function callLog() { return $this->hasOne(CallLog::class,'room_id','id')->latest(); }

    public function nofityCallTimeOut()
    {
        $title      =   trans('api.notify_message.voice_call_timeout.title');
        $message    =   trans('api.notify_message.voice_call_timeout.message');
        $type       =   config('utility.notification.type.voice_call_timeout');
        $creator        =   $this->creator;
        $participator   =   $this->participator;

        if($creator){
            $participator_name = $participator ? $participator->userTransEn ? $participator->userTransEn->full_name : "" : "";
            $participator_profile = $participator ? $participator->profile_photo : "";

            $notification = [
                'custom_id'     =>  getUniqueString('notifications'),
                'key'           =>  'user_id',
                'room_id'       =>  $this ? $this->custom_id : "",
                'value'         =>  $creator->custom_id,
                'user_id'       =>  $creator->id,
                'name'          =>  $participator_name,
                'profile'       =>  generateURL($participator_profile),
                'image'         =>  generateURL($participator_profile),
                'title'         =>  $title,
                'message'       =>  $message,
                'type'          =>  $type,
            ];
            
            // Notify
            $notificationJob = new NotificationJob($notification, $creator);
            dispatch($notificationJob);
        }

        if($participator){
            $creator_name = $creator ? $creator->userTransEn ? $creator->userTransEn->full_name : "" : "";
            $creator_profile = $creator ? $creator->profile_photo : "";

            $notification = [
                'custom_id'     =>  getUniqueString('notifications'),
                'key'           =>  'user_id',
                'room_id'       =>  $this ? $this->custom_id : "",
                'value'         =>  $participator->custom_id,
                'user_id'       =>  $participator->id,
                'name'          =>  $creator_name,
                'profile'       =>  generateURL($creator_profile),
                'image'         =>  generateURL($creator_profile),
                'title'         =>  $title,
                'message'       =>  $message,
                'type'          =>  $type,
            ];
            
            // Notify
            $notificationJob = new NotificationJob($notification, $participator);
            dispatch($notificationJob);
        }
    }
}
