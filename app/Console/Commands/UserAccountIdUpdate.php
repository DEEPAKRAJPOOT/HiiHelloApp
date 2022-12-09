<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Str;

class UserAccountIdUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:Accountidupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command used to user account Id is N/A then run cron 24 hours and update account id';

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
        $message            =   "No any user account id N/A records found.";
        $all_users = User::with('userTranslation:id,locale,user_id,full_name')->whereNull('account_id')->limit(1000)->get();
        if(count($all_users) > 0){
            foreach ($all_users as $key => $user) {
                if (!empty($user->full_name)) {
                    $user->account_id = Str::slug(substr($user->full_name, 0, 4), "_").'_'.time();
                    $user->save();
                }

            }
            $message            = "User account id update successfully.";
        }
        return $message;
    }
}
