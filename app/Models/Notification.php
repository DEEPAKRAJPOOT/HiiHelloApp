<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = [
        'custom_id', 'key', 'value', 'user_id', 'title', 'message', 'image', 'type', 'created_at'
    ];

    public function notificationStatus(){ return $this->hasMany(Notification::class, 'notification_id'); }
}
