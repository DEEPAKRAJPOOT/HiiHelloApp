<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Faq extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $fillable = [ 'custom_id' ];
    protected $translatedAttributes = ['question', 'answer'];

    public function getRouteKeyName(){ return 'custom_id'; }

    public function faqTranslations(){ return $this->hasMany('App\Models\FaqTranslation'); }

    public function getDefaultValue($column){
        return $this->translate(config('utility.default_lang_code')) ? $this->translate(config('utility.default_lang_code'))->$column : "";
    }

    public function getValue($lang_code,$field){
        return $this->translate($lang_code) ? $this->translate($lang_code)->$field : "";
    }

    public function getQuestion(){
        $lang_code = app()->getlocale();
        return $this->translate($lang_code) ? $this->translate($lang_code)->question 
            : ($this->translate(config('utility.default_lang_code')) ? $this->translate(config('utility.default_lang_code'))->question : "");
    }

    public function getAnswer(){
        $lang_code = app()->getlocale();
        return $this->translate($lang_code) ? $this->translate($lang_code)->answer 
            : ($this->translate(config('utility.default_lang_code')) ? $this->translate(config('utility.default_lang_code'))->answer : "");
    }
}
