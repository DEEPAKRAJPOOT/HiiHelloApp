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
    
    protected $fillable = ['custom_id'];

    protected $translatedAttributes = ['title'];

    public function interestTranslations(){ return $this->hasMany('App\Models\InterestTranslation'); }

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
