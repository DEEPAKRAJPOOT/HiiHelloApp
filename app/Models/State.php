<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name', 'country_id'];


    public function cities()
    {
        return $this->hasMany('App\Models\City');
    }


    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }
}
