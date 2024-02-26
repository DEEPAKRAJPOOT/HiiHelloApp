<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppStatus extends Model
{
    use HasFactory;
    protected $fillable = ['flag_constant', 'flag_value', 'value', 'hint'];
}
