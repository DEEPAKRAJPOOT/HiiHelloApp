<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ProfileDetail extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $fillable = ['id', 'slug', 'attribute', 'type'];

    protected $translatedAttributes = ['value'];

    public function profileDetailTranslations(){ return $this->hasMany('App\Models\ProfileDetailTranslation'); }
    public function profileDetailTranslation(){ 
        return $this->hasOne('App\Models\ProfileDetailTranslation')->whereLocale(app()->getlocale());
    }
}




