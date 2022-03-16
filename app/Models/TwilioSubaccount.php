<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwilioSubaccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'sid', 'token', 'api_key', 'api_secret', 'app_sid', 'ios_push_token', 'android_push_token'
    ]; 

    public function user(){ return $this->belongsTo('App\Models\User'); }
    public function communication() { return $this->hasMany('App\Models\UserCommunication','subaccount_id','id'); }
}
