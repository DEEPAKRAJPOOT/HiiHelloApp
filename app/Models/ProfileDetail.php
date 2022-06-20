<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ProfileDetail extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public function getRouteKeyName(){ return 'slug'; }

    protected $fillable = ['id', 'slug', 'attribute', 'type', 'is_trans_value'];

    protected $translatedAttributes = ['value'];

    public function profileDetailTranslations(){ return $this->hasMany('App\Models\ProfileDetailTranslation'); }
    public function profileDetailTranslation(){ 
        return $this->hasOne('App\Models\ProfileDetailTranslation')->whereLocale(app()->getlocale());
    }
    public function profileDetailTransDefault(){ 
        return $this->hasOne('App\Models\ProfileDetailTranslation')->whereLocale(config('utility.default_lang_code'));
    }

    public function getValue($lang_code,$field){
        return $this->translate($lang_code) ? $this->translate($lang_code)->$field : "";
    }
    
    public $timestamps = false;
}




