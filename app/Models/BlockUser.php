<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class BlockUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [ 'custom_id', 'block_by', 'blocked_to','block_type'];
    public function scopeHiddenOnly($query){
        $query->where('block_type',['hide']);
    }
    public function scopeBlockedOnly($query){
        $query->where('block_type',['block']);
    }
    public function blockBy(){ return $this->belongsTo('App\Models\User','block_by','id'); }
    public function blockedTo(){ return $this->belongsTo('App\Models\User','blocked_to','id'); }
}
