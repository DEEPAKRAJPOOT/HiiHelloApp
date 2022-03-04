<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [ 'user_id', 'token', 'type', 'device_name', 'os_name', 'os_version', 'app_version' ];
}
