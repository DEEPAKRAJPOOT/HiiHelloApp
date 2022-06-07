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

    public function subscriptionPlanTranslations(){ return $this->hasMany('App\Models\SubscriptionPlanTranslation'); }
    public function subscriptionPlanTranslation(){ 
        return $this->hasOne('App\Models\SubscriptionPlanTranslation')->whereLocale(app()->getlocale());
    }
    public function subscriptionPlanTransDefault(){ 
        return $this->hasOne('App\Models\SubscriptionPlanTranslation')->whereLocale(config('utility.default_lang_code'));
    }

    public function getValue($lang_code,$field){
        return $this->translate($lang_code) ? $this->translate($lang_code)->$field : "";
    }

}
