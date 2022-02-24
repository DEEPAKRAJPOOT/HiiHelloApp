<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory, SoftDeletes;

    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = ['custom_id', 'creator_id', 'participate_id'];

    public function creator(){ return $this->belongsTo('App\Models\User','creator_id','id'); }
    public function participator(){ return $this->belongsTo('App\Models\User','participate_id','id'); }
    public function latestMessage() { return $this->hasOne(ChatMessage::class,'room_id','id')->latest('id'); }
    public function chatMessages() { return $this->hasMany(ChatMessage::class,'room_id','id'); }
}
