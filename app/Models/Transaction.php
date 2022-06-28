<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['custom_id', 'user_id', 'plan_id', 'subscription_id', 'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature', 'amount', 'status'];
    
    public function getRouteKeyName(){ return 'custom_id'; }

    
    public function subscriptionPlan()
    {
        return $this->belongsTo('App\Models\SubscriptionPlan', 'plan_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
