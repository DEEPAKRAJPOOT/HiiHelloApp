<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpAutheticateLog extends Model
{
    use HasFactory;

    public function getRouteKeyName(){ return 'custom_id'; }

    protected $fillable = [
        'custom_id',
        'user_id',
        'phone_number',
        'otp_status',
        'verify_status',
        'otp',
        'response',
        'verify_response'
    ];

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = true;

}
