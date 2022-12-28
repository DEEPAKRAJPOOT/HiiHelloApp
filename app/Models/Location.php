<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Location extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public function getRouteKeyName(){ return 'custom_id'; }

    protected $fillable = ['id','custom_id', 'is_trans_name','is_trans_locality','is_trans_state','is_new','is_used','is_active'];

    protected $translatedAttributes = ['name'];

    public function locationTranslations(){ return $this->hasMany('App\Models\LocationTranslation'); }
    public function locationTranslation(){ 
        return $this->hasOne('App\Models\LocationTranslation')->whereLocale(app()->getlocale());
    }
    public function locationTransDefault(){ 
        return $this->hasOne('App\Models\LocationTranslation')->whereLocale(config('utility.default_lang_code'));
    }

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
