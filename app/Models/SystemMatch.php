<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemMatch extends Model
{
    use HasFactory;

    protected $table = 'system_match';

    protected $fillable = ['custom_id', 'user_id', 'match_id', 'is_connected', 'match_date'];

    public function mainUser(){
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    public function matchUser(){
        return $this->belongsTo('App\Models\User','match_id','id');
    }
    
}
