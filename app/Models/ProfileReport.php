<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileReport extends Model
{
    use HasFactory;
    
    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = ['custom_id', 'user_id', 'reported_user_id', 'message', 'status'];

    public function user(){ return $this->belongsTo('App\Models\User','user_id','id'); }

    public function reportedUser(){ return $this->belongsTo('App\Models\User','reported_user_id','id'); }

    public function getCreatedAtAttribute($created_at){
        return date('d/m/Y', strtotime($created_at));
    }
}
