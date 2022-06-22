<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\NotificationJob;

class NotifySubScriptionExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to notify the user that his/her subscription is expired soon !!!';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $message = 'No subscription expired details found !!!';
        $days = config('utility.notification.other.sub_expire_notify_days');

        User::select('id','custom_id','subscription_end_date')->with('deviceToken')
                ->whereNotNull('subscription_end_date')
                ->where('subscription_end_date',\Carbon\Carbon::today()->addDays($days)->format('Y-m-d'))     
                ->chunk(100, function($users) use ($message) {
            if($users->isNotEmpty()){
                foreach($users as $user){
                    $notification = [
                        'custom_id'     =>  getUniqueString('notifications'),
                        'key'           =>  'user_id',
                        'value'         =>  $user->id,
                        'user_id'       =>  $user->id,
                        'title'         =>  trans('api.notify_message.subscription_expire.title'),
                        'message'       =>  trans('api.notify_message.subscription_expire.message'),
                        'image'         =>  '',
                        'type'          =>  config('utility.notification.type.subscription_expire'),
                    ];

                    // Notify
                    $notificationJob = new NotificationJob($notification, $user);
                    dispatch($notificationJob);
                }
                $message = 'Subscription expired notified successfully.';
            }
        });

        $this->info($message);
        return $message;
    }
}
