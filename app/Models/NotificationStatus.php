<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationStatus extends Model
{
    use HasFactory;

    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = [
        'custom_id', 'notification_id', 'user_id', 'is_read',
    ];

    public function notification(){ return $this->belongsTo(Notification::class, 'notification_id'); }
    public function user(){ return $this->belongsTo(User::class, 'user_id'); }
}
