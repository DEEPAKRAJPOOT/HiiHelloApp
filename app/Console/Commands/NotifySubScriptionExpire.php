<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\NotificationJob;
use App\Jobs\SubscriptionExpiredJob;
use App\Jobs\SubscriptionExpiringJob;

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
        $one_week_before_notify = 7;
        $one_day_before_notify = 1;

        User::select('id','custom_id','subscription_end_date')->with(['deviceToken','userTransEn:id,user_id,full_name'])
                ->whereNotNull('subscription_end_date')
                ->where(function($query) use($one_week_before_notify, $one_day_before_notify) {
                    $query
                        // ->where('subscription_end_date',\Carbon\Carbon::today()->addDays($one_week_before_notify)->format('Y-m-d')) 
                        // ->orWhere('subscription_end_date',\Carbon\Carbon::today()->addDays($one_day_before_notify)->format('Y-m-d')) 
                        ->where('subscription_end_date','<=',\Carbon\Carbon::today()->format('Y-m-d'));
                })    
                ->chunk(100, function($users) use ($message) {
            if($users->isNotEmpty()){
                foreach($users as $user){

                    // if($user->subscription_end_date >= \Carbon\Carbon::today()->format('Y-m-d')){
                    //     $title      =   trans('api.notify_message.subscription_expire.title');
                    //     $message    =   trans('api.notify_message.subscription_expire.message');
                    //     $type       =   config('utility.notification.type.subscription_expire');

                    //     // Email
                    //     $subscriptionExpiringJob = new SubscriptionExpiringJob($user);
                    //     dispatch($subscriptionExpiringJob);
                    // }else{

                        $title      =   trans('api.notify_message.subscription_already_expire.title');
                        $message    =   trans('api.notify_message.subscription_already_expire.message');
                        $type       =   config('utility.notification.type.subscription_already_expire');

                        // Email
                        $subscriptionExpiredJob = new SubscriptionExpiredJob($user);
                        dispatch($subscriptionExpiredJob);
                    // }

                    $notification = [
                        'custom_id'     =>  getUniqueString('notifications'),
                        'key'           =>  'user_id',
                        'value'         =>  $user->custom_id,
                        'user_id'       =>  $user->id,
                        'title'         =>  $title,
                        'message'       =>  $message,
                        'image'         =>  '',
                        'type'          =>  $type,
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
