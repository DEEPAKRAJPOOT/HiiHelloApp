<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\NotificationJob;
use App\Http\Traits\TwillioSmsTrait;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, TwillioSmsTrait;

    protected $fillable = ['custom_id', 'user_id', 'plan_id', 'months', 'amount', 'start_date', 'end_date', 'payment_date', 'status'];
    public function getRouteKeyName(){ return 'custom_id'; }

    public function subscriptionPlan()
    {
        return $this->belongsTo('App\Models\SubscriptionPlan', 'plan_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function notifySubScriptionPurchase($status)
    {
        if ($this->user) {
            if ($status == 'success') {
                $title      =   trans('api.notify_message.subscription_success.title');
                $message    =   trans('api.notify_message.subscription_success.message');
                $type       =   config('utility.notification.type.subscription_success');
            } else {
                $title      =   trans('api.notify_message.subscription_fail.title');
                $message    =   trans('api.notify_message.subscription_fail.message');
                $type       =   config('utility.notification.type.subscription_fail');
            }

            $notification = [
                'custom_id'     =>  getUniqueString('notifications'),
                'key'           =>  'subscription_id',
                'value'         =>  $this->id,
                'user_id'       =>  $this->user ? $this->user->id : NULL,
                'title'         =>  $title,
                'message'       =>  $message,
                'image'         =>  '',
                'type'          =>  $type,
            ];

            // Notify
            $notificationJob = new NotificationJob($notification, $this->user);
            dispatch($notificationJob);
        }
    }

    public function sendSubScriptionPurchaseSMS($type)
    {
        if ($this->user && !empty($this->user->country_code) && !empty($this->user->contact_no)) {
            $phone_number   =   '+' . $this->user->country_code . '' . $this->user->contact_no;
            $userName       =   $this->user->userTranslation ? $this->user->userTranslation->full_name : "";

            if ($type == 'renew') {
                $message = trans('api.sms.message.subscription_renew', ['entity' => $userName]);
            } elseif ($type == 'new') {
                $message = trans('api.sms.message.subscription_purchase', ['entity' => $userName]);
            } else {
                $message = trans('api.sms.message.subscription_purchase', ['entity' => $userName]);
            }

            return TwillioSmsTrait::sendSMS($phone_number, $message);
        }
    }
}
