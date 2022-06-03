<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['custom_id', 'user_id', 'plan_id', 'months', 'amount', 'start_date', 'end_date', 'payment_date', 'status'];

    public function subscriptionPlan(){ return $this->belongsTo('App\Models\SubscriptionPlan','plan_id','id'); }
}
