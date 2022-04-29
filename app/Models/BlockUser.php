<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [ 'custom_id', 'block_by', 'blocked_to' ];

    public function blockedTo(){ return $this->belongsTo('App\Models\User','blocked_to','id'); }
}
