<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\NotificationJob;
use App\Jobs\HappyBirthDayJob;

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

        User::select('id','custom_id','email','birth_date')
                ->with(['userTransEn:id,user_id,full_name'])
                ->whereMonth('birth_date', '=', \Carbon\Carbon::now()->format('m'))
                ->whereDay('birth_date', '=', \Carbon\Carbon::now()->format('d'))
                ->chunk(100, function($users) {
            if($users->isNotEmpty()){
                foreach($users as $user){
                    // Email
                    $happyBirthDayJob = new HappyBirthDayJob($user);
                    dispatch($happyBirthDayJob);

                    $message = 'birthday greetings email send successfully.';

                    // $status = $user->sendBirthDayWishSMS();
                    // if($status){
                    // }
                }
            }
        });

        // $this->info($message);
        return $message;
    }
}
