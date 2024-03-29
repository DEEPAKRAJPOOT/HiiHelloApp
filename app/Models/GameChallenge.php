<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameChallenge extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'challenger_id', 'status'];

    public function user(){ return $this->belongsTo('App\Models\User','user_id','id'); }
    public function challengerUser(){ return $this->belongsTo('App\Models\User','challenger_id','id'); }
    public function challengeReceiverUser(){ return $this->belongsTo('App\Models\User','user_id','id'); }
    public function getStatus(){ 
        if($this->status == '0'){
            return 'Pending';
        }else if($this->status == '1'){
            return 'Accepted';
        }else{
            return 'Rejected';
        }
    
    }
}
