<?php

namespace App\Console;

use App\Console\Commands\TrustScore;
use App\Console\Commands\BirthDayWish;
use App\Console\Commands\AdminDashboard;
use App\Console\Commands\ChatMediaCheker;
use App\Console\Commands\ImageModeration;
use App\Console\Commands\RenewSwipeLimit;
use App\Console\Commands\VideoModeration;
use App\Console\Commands\AutoVerifyProfile;
use App\Console\Commands\GoogleTranslation;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\LocationTranslations;

use App\Console\Commands\NotifySubScriptionExpire;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ImageModeration::class,
        VideoModeration::class,
        GoogleTranslation::class,
        BirthDayWish::class,
        NotifySubScriptionExpire::class,
        RenewSwipeLimit::class,
        ChatMediaCheker::class,
        AutoVerifyProfile::class,
        LocationTranslations::class,
        TrustScore::class,
        AdminDashboard::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
        /*
        // Image Moderation Command
        $schedule->call(function () {
            $scheculeCommand = new ImageModeration;
            $scheculeCommand->handle();
        })->everyMinute();

        // Image Moderation Command
        $schedule->call(function () {
            $scheculeCommand = new VideoModeration;
            $scheculeCommand->handle();
        })->everyMinute();

         // Chat Image Moderation Command
        $schedule->call(function () {
            $scheculeCommand = new ChatMediaCheker;
            $scheculeCommand->handle();
        })->everyMinute();
        // ->everyFiveMinutes();

        // Auto Verify Profiles Command
        

        // Subscription Expirt Notification At Every Morning 8 AM
        $schedule->call(function () {
            $scheculeCommand = new NotifySubScriptionExpire;
            $scheculeCommand->handle();
        })->dailyAt('08:00');
        
       

        $schedule->call(function () {
            $scheculeCommand = new AutoVerifyProfile;
            $scheculeCommand->handle();
        })->everyMinute();
        
        */

        // Google Translate Command
        $schedule->call(function () {
            $scheculeCommand = new GoogleTranslation;
            $scheculeCommand->handle();
        })->everyMinute();
       

        // Location Translations Command
        $schedule->call(function () {
            $scheculeCommand = new LocationTranslations;
            $scheculeCommand->handle();
        })->daily();

    
        // Birthday Wise At Every Night 12 AM
        // $schedule->call(function () {
        //     $scheculeCommand = new BirthDayWish;
        //     $scheculeCommand->handle();
        // })->daily();


        // Users Daily Swipe Limit Renew
        $schedule->call(function () {
            $scheculeCommand = new RenewSwipeLimit;
            $scheculeCommand->handle();
        })->twiceDaily(0,12);

        // Analytic Dashboard cron
        $schedule->call(function () {
            $scheculeCommand = new AdminDashboard;
            $scheculeCommand->handle();
        })->name('AdminDashboardUpdate')->everyFifteenMinutes()->withoutOverlapping();
        

        // Calculate Trust Scroe on the first day of every month at 2:00
        $schedule->call(function () {
            $scheculeCommand = new TrustScore;
            $scheculeCommand->handle();
        })->daily(); 


        /*$schedule->call(function () {
            $scheculeCommand = new TrustScore;
            $scheculeCommand->handle();
        })->monthlyOn(1, '2:00'); */       
    
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
