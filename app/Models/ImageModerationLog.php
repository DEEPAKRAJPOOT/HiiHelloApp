<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ImageModerationLog extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'image_moderation_log';

    protected $fillable = [ 'user_id', 'request', 'response', 'message','total_face_detected','image_type', 'is_approved' ];

    
}
