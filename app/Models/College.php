<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class College extends Model {
    use HasFactory,SoftDeletes;
    public function getRouteKeyName(){
        return 'custom_id';
    }
    protected $fillable = [
        'custom_id',
        'name',
        'university',
        'state',
        'district',
        'abbreviation',
        'location_id',
        'approved_at'
    ];
    public function users(){
        return $this->hasMany('App\Models\User','college_id','id');
    }
}