<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FreeSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:free';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to add free subscription details for girls who has already signup accounts.';

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
        $message = 'No free subscription found !!!';

        User::select('id','custom_id','gender')
                ->doesntHave('subscription')
                ->whereGender('Female')
                ->chunk(100, function($users) {
            if($users->isNotEmpty()){
                foreach($users as $user){
                    $user->buyFreeSubscription();
                    $message = 'free subscription details updated successfully.';
                }
            }
        });

        // $this->info($message);
        return $message;
    }
}
