<?php

namespace App\Broadcasting;

use App\Http\Controllers\Controller;
use App\Http\Traits\FirebaseTrait;
use Illuminate\Notifications\Notification;
use App\Models\User;

class PushNotificationChannel extends Controller
{
    use FirebaseTrait;
    public function send($notifiable, Notification $notification)
    {
        $deviceToken = $notifiable->deviceToken;

        if( empty($deviceToken) ) return "No device token found!";
        $message = $notification->data;
        $unReadNotifications = 0;

        $n_data = [
            'title'     =>  $message->title,
            'body'      =>  $message->message,
            'badge'     =>  $unReadNotifications,
        ];
        
        $data = [
            'id'    =>  $message->id ?? NULL,
            'type'  =>  $message->type ?? "general-notification",
            // 'image' =>  'https://dwisi.s3.me-south-1.amazonaws.com/product/images/WOjOEV5wS17qzYnkKcJKlpDV6xdsianMBONJM0V8.jpg',
            'image' =>  $notifiable->profile_photo ? generateURL($notifiable->profile_photo) : "",
        ];

        if ($message->type == 'chat-message') {
            $data['name'] = $message->name;
            $data['profile'] = $message->profile;
        }

        $send_notification = [
            'priority'  => 'high',
            'to'        => $deviceToken->token,
            'sound'     => 'default',
        ];

        if( $deviceToken->type == 'android' ) {
            $n_data = array_merge($n_data, $data);
            $send_notification['data'] = $n_data;
        } else {
            $send_notification['notification'] = $n_data;
            $send_notification['data'] = $data;
        }

        $data = json_encode($send_notification);
        $result = $this->sendPushNotification($data);
    }
}
