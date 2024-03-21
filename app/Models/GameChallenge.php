<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameChallenge extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'challenger_id', 'status'];
}
