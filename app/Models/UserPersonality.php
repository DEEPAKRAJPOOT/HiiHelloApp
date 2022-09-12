<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPersonality extends Model
{
    use HasFactory;

    protected $fillable = ['custom_id', 'user_id', 'personality_id'];

    public function personality(){ return $this->belongsTo('App\Models\Personality','personality_id','id'); }
}
