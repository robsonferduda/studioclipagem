<?php

namespace App\Console;

use App\Classes\FBFeed;
use App\Classes\FbHashtag;
use App\Classes\FBMention;
use App\Classes\FbTerm;
use App\Classes\IGHashTag;
use App\Classes\IGMention;
use App\Classes\Rule;
use App\Twitter\TwitterCollect;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\EmailCron::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('email:cron')->everyMinute();
        
        $schedule->call(function () {
                          
        })->hourly()->between('7:00', '22:00');

    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}