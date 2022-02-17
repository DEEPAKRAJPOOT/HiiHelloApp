<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Country extends Model implements TranslatableContract
{
    use Translatable;

    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = ['custom_id', 'code', 'phonecode'];

    protected $translatedAttributes = ['name'];

    public function countryTranslations(){ return $this->hasMany('App\Models\CountryTranslation'); }
    public function states(){ return $this->hasMany('App\Models\State'); }

    public function getDefaultValue($column){
        return $this->translate(config('utility.default_lang_code')) ? $this->translate(config('utility.default_lang_code'))->$column : "";
    }

    public function getValue($lang_code,$field){
        return $this->translate($lang_code) ? $this->translate($lang_code)->$field : "";
    }

    public function getName(){
        $lang_code = app()->getlocale();
        return $this->translate($lang_code) ? $this->translate($lang_code)->name 
            : ($this->translate(config('utility.default_lang_code')) ? $this->translate(config('utility.default_lang_code'))->name : "");
    }
}
