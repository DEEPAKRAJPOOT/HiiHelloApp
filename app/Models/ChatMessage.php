<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ChatNotification;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = ['custom_id', 'room_id', 'sender_id', 'receiver_id', 'message', 'status'];

    public function room(){ return $this->belongsTo('App\Models\ChatRoom','room_id','id'); }
    public function sender(){ return $this->belongsTo('App\Models\User','sender_id','id'); }
    public function receiver(){ return $this->belongsTo('App\Models\User','receiver_id','id'); }

    public function getCreatedAtAttribute($created_at){ return date('Y-m-d H:i:s', strtotime($created_at)); }
    public function getUpdatedAtAttribute($updated_at){ return date('Y-m-d H:i:s', strtotime($updated_at)); }

    public function notifyChatMessageToUser($message) {
        $this->receiver ? $this->receiver->notify(new ChatNotification($this->chatPushNFData($this->sender, $this, $message))) : ""; 
    }

    protected function chatPushNFData($account, $chatMessage, $message = ""){
        $message = trim( preg_replace("/\r|\n/", " ", $message) );
        $full_name = $account->userTranslation ? $account->userTranslation->full_name : "";
        if( $message == "" ) {
            $message =  $full_name." has sent you a image 📷.";
        }
        return [
            'title'     =>  $full_name,
            'type'      =>  'chat-message',
            'id'        =>  $chatMessage->custom_id,
            'room_id'   =>  $chatMessage->room ? $chatMessage->room->custom_id : "",
            'name'      =>  $full_name,
            'profile'   =>  generateURL($account->profile_photo),
            'message'   =>  $message,
        ];
    }

    public function getMessage(){
        // $string = preg_replace("/[\r\n]+/", " ", $this->message);
        // $message = json_decode($string);

        $message = json_decode( preg_replace("/\r|\n/", " ", $this->message) );
        // dd($message);
        if(!empty($message) && !empty($message->type)){            
            if($message->type == 'location'){
                if(!empty($message->other) && !empty($message->other->lat && !empty($message->other->lng) ) ){
                    $message->other->url = 'https://maps.googleapis.com/maps/api/staticmap?center='.$message->other->lat.','.$message->other->lng.'&zoom=14&size=400x400&markers='.$message->other->lat.','.$message->other->lng.'&markers=color:red&key=AIzaSyA2GIt7Ld9duVo85H4Mr15Y_v7Sc6pfzlQ';
                }
            }
        }
        return $message;
    }
}
