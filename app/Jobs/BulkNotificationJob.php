<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\PushNotification;
use App\Http\Traits\FirebaseTrait;

class BulkNotificationJob implements ShouldQueue
{
    use FirebaseTrait, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    protected $notification, $users;

    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notification, $users)
    {
        $this->notification = $notification;
        $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendPushNotificationToAll($this->notification,$this->users);
        $response = [
            'status' => 'success',
            'message' => 'Notification sent successfully'
        ];
        return (object) $response;
    }
}
