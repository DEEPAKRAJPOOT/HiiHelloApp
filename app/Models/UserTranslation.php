<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'locale', 'user_id', 'full_name', 'about_me', 'fav_movie'];

    public $timestamps = false;
}
