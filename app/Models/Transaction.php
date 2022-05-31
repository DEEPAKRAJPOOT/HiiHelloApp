<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['custom_id', 'user_id', 'plan_id', 'subscription_id', 'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature', 'amount', 'status'];
}
