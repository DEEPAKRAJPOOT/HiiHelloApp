<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'locale', 'location_id', 'name', 'locality', 'state'];

    public function users()
    {
        return $this->hasMany('App\Models\LocationTranslation', 'location_id', 'location_id');
    }
}
