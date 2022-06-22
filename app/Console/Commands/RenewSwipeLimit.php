<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use App\Models\User;

class RenewSwipeLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swipe:renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to renew the daily swipe limit so unsubscribed use can again swipe the profile next day.';

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
        $message = "No swipe records found.";
        try{
            User::query()->update(['swipe_count' =>  0]);

            $message = 'Users swipe details updated successfully !!!';
        } catch (\Exception $e) {
            // Add error log
            $file = 'swipe_count';
            $iqTrackingLog = new Logger($file);
            $iqTrackingLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
            $iqTrackingLog->error($file, ['error' => $e->getMessage()]);
        }

        $this->info($message);
        return $message;
    }
}
