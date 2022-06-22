<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ImageModeration;
use App\Console\Commands\GoogleTranslation;
use App\Console\Commands\BirthDayWish;
use App\Console\Commands\NotifySubScriptionExpire;
use App\Console\Commands\RenewSwipeLimit;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ImageModeration::class,
        GoogleTranslation::class,
        BirthDayWish::class,
        NotifySubScriptionExpire::class,
        RenewSwipeLimit::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Google Translate Command
        $schedule->call(function () {
            $scheculeCommand = new GoogleTranslation;
            $scheculeCommand->handle();
        })->everyFiveMinutes();
        // ->everyMinute();
        
        // Image Moderation Command
        $schedule->call(function () {
            $scheculeCommand = new ImageModeration;
            $scheculeCommand->handle();
        })->everyMinute();
        // ->everyTenMinutes();

        // Birthday Wise At Every Night 12 AM
        $schedule->call(function () {
            $scheculeCommand = new BirthDayWish;
            $scheculeCommand->handle();
        })->dailyAt();

        // Subscription Expirt Notification At Every Night 8 AM
        $schedule->call(function () {
            $scheculeCommand = new NotifySubScriptionExpire;
            $scheculeCommand->handle();
        })->dailyAt('08:00');

        // Users Daily Swipe Limit Renew
        $schedule->call(function () {
            $scheculeCommand = new RenewSwipeLimit;
            $scheculeCommand->handle();
        })->dailyAt();

        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
