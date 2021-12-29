<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    public function getRouteKeyName(){ return 'custom_id'; }

    protected $fillable = [ 'custom_id', 'user_id', 'image', 'video'];
}
