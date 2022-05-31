<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    public function getRouteKeyName(){ return 'custom_id'; }
    
    protected $fillable = [
        'custom_id', 'period', 'interval', 'amount', 'is_popular',
    ]; 

    public function subscriptionPlanTranslation(){ 
        return $this->hasOne('App\Models\SubscriptionPlanTranslation', 'plan_id', 'id')->whereLocale(app()->getlocale());
    }

    public function calculateDays(){
        $days = 0;
        
        if($this->period == 'monthly'){ $days = 30 * $this->interval; }
        elseif($this->period == 'daily'){ $days = 1 * $this->interval; }
        elseif($this->period == 'weekly'){ $days = 7 * $this->interval; }
        elseif($this->period == 'yearly'){ $days = 365 * $this->interval; }

        return $days;
    }

    public function calculateEndDate(){
        $days = $this->calculateDays();
        return date('Y-m-d h:i:s',strtotime('+'.$days.' day'));
    }
}
