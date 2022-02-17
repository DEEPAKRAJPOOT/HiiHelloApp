<?php

namespace App\Models;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class City extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable =  ['custom_id', 'state_id'];
    protected $translatedAttributes = ['name'];

    public function getRouteKeyName(){ return 'custom_id'; }
    
    public function cityTranslations(){ return $this->hasMany('App\Models\CityTranslation'); }
    public function state(){ return $this->belongsTo('App\Models\State'); }
    
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
