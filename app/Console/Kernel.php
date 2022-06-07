<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ImageModeration;
use App\Console\Commands\GoogleTranslation;

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
        })->everyMinute();
        // ->everyFiveMinutes();

        // Image Moderation Command
        $schedule->call(function () {
            $scheculeCommand = new ImageModeration;
            $scheculeCommand->handle();
        })->everyMinute();
        // ->everyTenMinutes();

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
