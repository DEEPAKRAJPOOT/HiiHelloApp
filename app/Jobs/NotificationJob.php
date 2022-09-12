<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;
use App\Models\NotificationStatus;
use App\Notifications\PushNotification;

class NotificationJob implements ShouldQueue 
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $notification, $user;

    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notification, $user)
    {
        $this->notification = $notification;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dbNotification = Notification::create([
            'custom_id'     =>  getUniqueString('notifications'),
            'key'           =>  $this->notification['key'],
            'value'         =>  $this->notification['value'],
            'user_id'       =>  $this->notification['user_id'],
            'title'         =>  $this->notification['title'],
            'message'       =>  $this->notification['message'],
            'image'         =>  $this->notification['image'],
            'type'          =>  $this->notification['type'],
        ]);

        $status = NotificationStatus::create([
            'custom_id'         =>  getUniqueString('notification_statuses'),
            'user_id'           =>  $this->user->id,
            'notification_id'   =>  $dbNotification->id,
            'is_read'           =>  'n',
        ]);

        $this->user->notify(new PushNotification($this->notification));
        $response = [
            'status' => 'success',
            'message' => 'notification added! successfully'
        ];
        return (object) $response;
    }
}
