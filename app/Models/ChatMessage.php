<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = ['custom_id', 'room_id', 'sender_id', 'receiver_id', 'message', 'status'];

    public function sender(){ return $this->belongsTo('App\Models\User','sender_id','id'); }
    public function receiver(){ return $this->belongsTo('App\Models\User','receiver_id','id'); }

    public function isSender(){ return Auth::id() == $this->sender_id ? true : false; }

    public function getMessage(){
        $message = json_decode($this->message);

        if(!empty($message) && !empty($message->type)){            
            if($message->type == 'location'){
                if(!empty($message->other) && !empty($message->other->lng) && !empty($message->other->lat) ){
                    $message->other->url = 'https://maps.googleapis.com/maps/api/staticmap?center='.$message->other->lng.','.$message->other->lat.'&zoom=14&size=400x400&markers='.$message->other->lng.','.$message->other->lat.'&markers=color:red&key=AIzaSyA2GIt7Ld9duVo85H4Mr15Y_v7Sc6pfzlQ';
                }
            }
        }
        return $message;
    }
}
