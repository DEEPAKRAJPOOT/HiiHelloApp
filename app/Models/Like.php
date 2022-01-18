<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['custom_id', 'user_id', 'liker_id'];

    public function user(){ return $this->belongsTo('App\Models\User','user_id','id'); }
    public function likerUser(){ return $this->belongsTo('App\Models\User','liker_id','id'); }
}
