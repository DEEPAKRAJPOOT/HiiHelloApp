<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'custom_id', 'email', 'user_id', 'plan_id', 'subscription_id', 'payment_type',
        'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature',
        'transaction_id', 'original_transaction_id', 'web_order_line_item_id', 'purchase_date', 'original_purchase_date',
        'subscription_end_date', 'receipt_data', 'in_app_ownership_type', 'subscription_group_identifier',
        'amount', 'status',
        "coupon_id",
        "coupon_name",
        'revoked_at',
        'refunded_at',
        'refunded_amount'
    ];

    public function getRouteKeyName()
    {
        return 'custom_id';
    }

    public function subscription()
    {
        return $this->belongsTo('App\Models\Subscription', 'subscription_id', 'id');
    }
    public function usersubscription()
    {
        return $this->hasOne('App\Models\Subscription', 'id', 'subscription_id');
    }
    public function subscriptionPlan()
    {
        return $this->belongsTo('App\Models\SubscriptionPlan', 'plan_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }
    public function coupon()
    {
        return $this->belongsTo('App\Models\Coupon');
    }
    public function couponVendor()
    {
        return $this->belongsTo('App\Models\CouponVendor');
    }
}
