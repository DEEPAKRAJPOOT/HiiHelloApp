<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Interest extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = ['custom_id', 'parent_id', 'master_parent_id', 'location_id', 'level', 'sequence'];

    protected $translatedAttributes = ['title'];

    public function location(){ return $this->belongsTo('App\Models\Location'); }
    public function parentInterest(){ return $this->hasOne('App\Models\Interest','id','parent_id'); }
    public function masterInterest(){ return $this->hasOne('App\Models\Interest','id','master_parent_id'); }
    public function subInterests(){ return $this->hasMany('App\Models\Interest','parent_id','id'); }
    public function interestTranslations(){ return $this->hasMany('App\Models\InterestTranslation'); }
    public function interestTranslation(){ 
        return $this->hasOne('App\Models\InterestTranslation')->whereLocale(app()->getlocale());
    }
    public function interestTransDefault(){ 
        return $this->hasOne('App\Models\InterestTranslation')->whereLocale(config('utility.default_lang_code'));
    }

    public function getDefaultValue($column){
        return $this->translate(config('utility.default_lang_code')) ? $this->translate(config('utility.default_lang_code'))->$column : "";
    }

    public function getValue($lang_code,$field){
        return $this->translate($lang_code) ? $this->translate($lang_code)->$field : "";
    }

    public function getTitle(){
        $lang_code = app()->getlocale();
        return $this->translate($lang_code) ? $this->translate($lang_code)->title 
            : ($this->translate(config('utility.default_lang_code')) ? $this->translate(config('utility.default_lang_code'))->title : "");
    }
}
