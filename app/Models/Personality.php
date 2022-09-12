<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Personality extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public function getRouteKeyName(){ return 'custom_id'; }

    protected $fillable = ['custom_id', 'image', 'is_trans_title', 'is_trans_description'];

    protected $translatedAttributes = ['title', 'description'];

    public function personalityTranslations(){ return $this->hasMany('App\Models\PersonalityTranslation'); }
    public function personalityTranslation(){ 
        return $this->hasOne('App\Models\PersonalityTranslation')->whereLocale(app()->getlocale());
    }
    public function personalityTransDefault(){ 
        return $this->hasOne('App\Models\PersonalityTranslation')->whereLocale(config('utility.default_lang_code'));
    }

    public function getValue($lang_code,$field){
        return $this->translate($lang_code) ? $this->translate($lang_code)->$field : "";
    }
}
