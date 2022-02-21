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
}
