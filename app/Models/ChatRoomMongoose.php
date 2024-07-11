<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ChatRoomMongoose extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'chat_room';

    protected $fillable = ['custom_id', 'creator_id', 'participate_id', 'block_by', 'vanish_mode', 'vanish_mode_by', 'disappear_mode', 'disappear_mode_by', 'creator_cleared_at', 'participate_cleared_at', 'creator_deleted_at', 'participate_deleted_at'];

    // Enable timestamps
    public $timestamps = true;

    // Soft delete field
    protected $dates = ['deleted_at', 'creator_cleared_at', 'participate_cleared_at', 'creator_deleted_at', 'participate_deleted_at'];

    public function creator(){ return $this->belongsTo('App\Models\UsersMongoose','creator_id','user_id'); }
    public function participator(){ return $this->belongsTo('App\Models\UsersMongoose','participate_id','user_id'); }
    public function blockBy(){ return $this->belongsTo('App\Models\UsersMongoose','block_by','user_id'); }
    public function latestMessage() { return $this->hasOne(ChatMessageMongoose::class,'room_id','_id')->where(function($query){
        $query->whereNull('expired_at');
        $query->orWhere('expired_at','>',now());
    })->latest('_id'); }
    public function chatMessages() { return $this->hasMany(ChatMessageMongoose::class,'room_id','_id'); }
    public function chatMessagesWithTrashed() { return $this->hasMany(ChatMessageMongoose::class,'room_id','_id')->withTrashed(); }
    // public function callLog() { return $this->hasOne(CallLog::class,'room_id','id')->latest(); }
}
