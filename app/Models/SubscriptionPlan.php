<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = [
        'custom_id', 'months', 'amount', 'is_popular',
    ]; 

    protected $translatedAttributes = ['name', 'description', 'note'];

    public function subscriptionPlanTranslation(){ 
        return $this->hasOne('App\Models\SubscriptionPlanTranslation', 'plan_id', 'id')->whereLocale(app()->getlocale());
    }

}
