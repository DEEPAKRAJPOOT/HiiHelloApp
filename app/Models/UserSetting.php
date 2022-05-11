<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;

    protected $fillable = ['custom_id', 'user_id', 'language_id'];

    public function user(){ return $this->belongsTo('App\Models\User'); }
    public function language(){ return $this->belongsTo('App\Models\Language'); }
}
