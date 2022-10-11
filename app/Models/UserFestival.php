<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFestival extends Model
{
    use HasFactory;

    protected $fillable = ['custom_id', 'user_id', 'festival_id'];

    public function festival(){ return $this->belongsTo('App\Models\ProfileDetail','festival_id','id'); }
}
