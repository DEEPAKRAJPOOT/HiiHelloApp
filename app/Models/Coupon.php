<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model {
    use HasFactory,SoftDeletes;
    protected $fillable = [
        "plan_id",
        "vendor_id",
        "custom_id",
        "coupon",
        "title",
        "description",
        "image",
        "expired_at",
        "value",
        "type",
        "is_universal",
        "is_reusable",
        "is_self_hosted",
        "is_active"
    ];

    public function getRouteKeyName(){
        return 'custom_id';
    }

    public function plan(){
        return $this->belongsTo('App\Models\SubscriptionPlan');
    }

    public function vendor(){
        return $this->belongsTo('App\Models\CouponVendor');
    }

    public function subscriptionPlanTranslations(){
        return $this->hasMany('App\Models\SubscriptionPlanTranslation','subscription_plan_id','plan_id');
    }

    public function subscriptionPlanTranslation(){
        return $this->hasOne('App\Models\SubscriptionPlanTranslation','subscription_plan_id','plan_id')->whereLocale(app()->getlocale());
    }

    public function subscriptionPlanTransDefault(){ 
        return $this->hasOne('App\Models\SubscriptionPlanTranslation','subscription_plan_id','plan_id')->whereLocale(config('utility.default_lang_code'));
    }

    public function subscriptionPlanTransEn(){ 
        return $this->hasOne('App\Models\SubscriptionPlanTranslation','subscription_plan_id','plan_id')->whereLocale('en');
    }
}