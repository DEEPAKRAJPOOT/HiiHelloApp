<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['custom_id', 'user_id', 'plan_id', 'amount', 'start_date', 'end_date', 'payment_date', 'status'];
}
