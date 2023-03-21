<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponVendor extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        "name",
        "custom_id",
        "is_active"
    ];

    public function getRouteKeyName(){
        return 'custom_id';
    }

    public function coupons() {
        return $this->hasMany('App\Models\Coupon','vendor_id','id');
    }
}
