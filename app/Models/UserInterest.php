<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model
{
    use HasFactory;

    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = ['custom_id', 'user_id', 'interest_id'];

    public function interest(){ return $this->belongsTo('App\Models\Interest'); }

}
