<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = [
        'custom_id', 'months', 'amount', 'is_popular',
    ]; 

    public function subscriptionPlanTranslation(){ 
        return $this->hasOne('App\Models\SubscriptionPlanTranslation', 'plan_id', 'id')->whereLocale(app()->getlocale());
    }

}
