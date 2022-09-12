<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use App\Http\Traits\RedisTrait;

class CmsPage extends Model implements TranslatableContract
{
    use Translatable, RedisTrait;

    // public static function boot(){
    //     parent::boot();
    //     /* Clear Cache Details */
    //         static::created(function() { 
    //             RedisTrait::deleteCache(config('redis.key.get-cms-pages'));
    //         });
    //         self::updated(function(){ 
    //             RedisTrait::deleteCache(config('redis.key.get-cms-pages'));
    //         });
    //         self::deleted(function(){ 
    //             RedisTrait::deleteCache(config('redis.key.get-cms-pages'));
    //         });
    // }

    protected $fillable = ['custom_id', 'file', 'edited_by'];
    protected $translatedAttributes = ['title', 'description'];

    public function getRouteKeyName()
    {
        return 'custom_id';
    }

    public function cmsPageTranslations()
    {
        return $this->hasMany('App\Models\CmsPageTranslation');
    }
    public function cmsPageTranslation()
    {
        return $this->hasOne('App\Models\CmsPageTranslation')->whereLocale(app()->getlocale());
    }
    public function cmsPageTransDefault()
    {
        return $this->hasOne('App\Models\CmsPageTranslation')->whereLocale(config('utility.default_lang_code'));
    }

    public function getDefaultValue($column)
    {
        return $this->translate(config('utility.default_lang_code')) ? $this->translate(config('utility.default_lang_code'))->$column : "";
    }

    public function getValue($lang_code, $field)
    {
        return $this->translate($lang_code) ? $this->translate($lang_code)->$field : "";
    }

    public function getTitle()
    {
        $lang_code = app()->getlocale();
        return $this->translate($lang_code) ? $this->translate($lang_code)->title
            : ($this->translate(config('utility.default_lang_code')) ? $this->translate(config('utility.default_lang_code'))->title : "");
    }

    public function getDescription()
    {
        $lang_code = app()->getlocale();
        return $this->translate($lang_code) ? $this->translate($lang_code)->description
            : ($this->translate(config('utility.default_lang_code')) ? $this->translate(config('utility.default_lang_code'))->description : "");
    }
}
