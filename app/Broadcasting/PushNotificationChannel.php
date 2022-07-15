<?php

namespace App\Broadcasting;

use App\Http\Controllers\Controller;
use App\Http\Traits\FirebaseTrait;
use Illuminate\Notifications\Notification;
use App\Models\NotificationStatus;
use App\Models\User;

class PushNotificationChannel extends Controller
{
    use FirebaseTrait;
    public function send($notifiable, Notification $notification)
    {
        $deviceToken = $notifiable->deviceToken;

        if( empty($deviceToken) ) return "No device token found!";
        $message = $notification->data;
        $badge = NotificationStatus::whereUserId($notifiable->id)->whereIsRead('n')->count() + $notifiable->chat_count;

        $n_data = [
            'title'     =>  $message['title'],
            'body'      =>  $message['message'],
            'badge'     =>  $badge,
        ];

        $data = [
            'id'        =>  $message['id'] ?? NULL,
            'type'      =>  $message['type'] ?? "general-notification",
            'image'     =>  $notifiable['profile_photo'] ? generateURL($notifiable['profile_photo']) : "",
        ];

        if ($message['type'] == config('utility.notification.type.chat_message') ||
            $message['type'] == config('utility.notification.type.image_moderation_chat')
        ) {
            $data['name']       =   $message['name'];
            $data['profile']    =   $message['profile'];
            $data['room_id']    =   $message['room_id'];
        }

        $send_notification = [
            'priority'  =>  'high',
            'to'        =>  $deviceToken->token,
            'sound'     =>  'default',
        ];

        if( $deviceToken['type'] == 'android' ) {
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
