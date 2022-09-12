<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [ 'custom_id', 'room_id', 'date', 'start_time', 'end_time', 'remaining_time' ];

    public function room(){ return $this->belongsTo('App\Models\ChatRoom','room_id','id'); }
}
