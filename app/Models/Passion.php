<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Passion extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public function getRouteKeyName(){ return 'custom_id'; }

    protected $fillable = ['custom_id'];

    protected $translatedAttributes = ['name', 'type'];

    public function passionTranslations(){ return $this->hasMany('App\Models\PassionTranslation'); }
    public function passionTranslation(){ 
        return $this->hasOne('App\Models\PassionTranslation')->whereLocale(app()->getlocale());
    }
    public function passionTransDefault(){ 
        return $this->hasOne('App\Models\PassionTranslation')->whereLocale(config('utility.default_lang_code'));
    }

    public function getValue($lang_code,$field){
        return $this->translate($lang_code) ? $this->translate($lang_code)->$field : "";
    }
}
