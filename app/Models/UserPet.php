<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPet extends Model
{
    use HasFactory;

    protected $fillable = ['custom_id', 'user_id', 'pet_id'];

    public function pet(){ return $this->belongsTo('App\Models\ProfileDetail','pet_id','id'); }
}
