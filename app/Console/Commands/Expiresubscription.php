<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class Expiresubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:expiresubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command used for remvoe subscription to less then today date';

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
        $message            =   " No subscription expire records found.";
        $today_date         =   date('Y-m-d');
        $users = User::select('id','is_subscribed','subscription_end_date')
            ->where('subscription_end_date','<',$today_date)
            ->get();
        if($users->isNotEmpty()){
            foreach($users as $user){
                // update user table set is subscribed = n
                User::where('id',$user->id)->update([ 
                    'is_subscribed' =>  'n',
                ]);
            }
            $message = "Subscription expire records update successfully.";
        }

        return $message;

    }
}
