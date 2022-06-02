<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class State extends Model implements TranslatableContract
{
    use Translatable;

    public function getRouteKeyName(){ return 'custom_id'; }

    protected $fillable = ['custom_id', 'country_id'];
    protected $translatedAttributes = ['name'];

    public function stateTranslations(){ return $this->hasMany('App\Models\StateTranslation'); }
    public function stateTranslation(){ 
        return $this->hasOne('App\Models\StateTranslation')->whereLocale(app()->getlocale());
    }
    public function stateTransDefault(){ 
        return $this->hasOne('App\Models\StateTranslation')->whereLocale(config('utility.default_lang_code'));
    }
    public function cities(){ return $this->hasMany('App\Models\City'); }
    public function country(){ return $this->belongsTo('App\Models\Country'); }

    public function getName(){
        $lang_code = app()->getlocale();
        return $this->translate($lang_code) ? $this->translate($lang_code)->name 
            : ($this->translate(config('utility.default_lang_code')) ? $this->translate(config('utility.default_lang_code'))->name : "");
    }
    public function getDefaultValue($column){
        return $this->translate(config('utility.default_lang_code')) ? $this->translate(config('utility.default_lang_code'))->$column : "";
    }

    public function getValue($lang_code,$field){
        return $this->translate($lang_code) ? $this->translate($lang_code)->$field : "";
    }

}
