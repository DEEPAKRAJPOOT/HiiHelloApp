<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['custom_id', 'language', 'lang_code', 'hint'];

    public function getField($lang_code,$field){
        return $lang_code.'_'.$field;
    }
}
