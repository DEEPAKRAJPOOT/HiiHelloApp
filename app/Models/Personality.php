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

    protected $fillable = ['custom_id', 'image'];

    protected $translatedAttributes = ['title', 'description'];

    public function personalityTranslations(){ return $this->hasMany('App\Models\PersonalityTranslation'); }
    public function personalityTranslation(){ 
        return $this->hasOne('App\Models\PersonalityTranslation')->whereLocale(app()->getlocale());
    }
    public function personalityTransDefault(){ 
        return $this->hasOne('App\Models\PersonalityTranslation')->whereLocale(config('utility.default_lang_code'));
    }
}
