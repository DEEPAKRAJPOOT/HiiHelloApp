<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisLike extends Model
{
    use HasFactory;

    protected $fillable = ['custom_id', 'user_id', 'dis_liker_id'];

    public function user(){ return $this->belongsTo('App\Models\User','user_id','id'); }
    public function disLikerUser(){ return $this->belongsTo('App\Models\User','dis_liker_id','id'); }
}
