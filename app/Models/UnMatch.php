<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnMatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [ 'custom_id', 'unmatch_by', 'unmatch_to' ];

    public function unmatchBy(){ return $this->belongsTo('App\Models\User','unmatch_by','id'); }
    public function unmatchTo(){ return $this->belongsTo('App\Models\User','unmatch_to','id'); }
}
