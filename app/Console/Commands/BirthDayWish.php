<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\NotificationJob;

class BirthDayWish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:wish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to send the notification of birthday greetings.';

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
        $message = 'No birthday wishes found !!!';

        User::select('id','custom_id')->with('deviceToken')
                ->whereBirthDate(\Carbon\Carbon::today()->format('Y-m-d'))
                ->chunk(100, function($users) {
            if($users->isNotEmpty()){
                foreach($users as $user){
                    $notification = [
                        'custom_id'     =>  getUniqueString('notifications'),
                        'key'           =>  'user_id',
                        'value'         =>  $user->id,
                        'user_id'       =>  $user->id,
                        'title'         =>  trans('api.notify_message.profile_birthday.title'),
                        'message'       =>  trans('api.notify_message.profile_birthday.message'),
                        'image'         =>  '',
                        'type'          =>  config('utility.notification.type.profile_birthday'),
                    ];

                    // Notify
                    $notificationJob = new NotificationJob($notification, $user);
                    dispatch($notificationJob);
                }
                $message = 'birthday greetings notified successfully.';
            }
        });

        $this->info($message);
        return $message;
    }
}
