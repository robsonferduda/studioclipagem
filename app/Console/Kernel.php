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
        //$schedule->command('email:cron')->everyMinute();

        //$schedule->command('web:cron')->everyFifteenMinutes(); //Executa a tarefa a cada 15 minutos

        //$schedule->command('web:cron')->everyThirtyMinutes(); //Executa a tarefa a cada 30 minutos

        //$schedule->command('web:cron')->hourly(); //Executa a tarefa a cada 1 hora 

        $schedule->command('web:cron')->everyTwoHours()->between('1:00', '22:00'); //Executa a tarefa a cada 2 hora 
        $schedule->command('impresso:cron')->everyTwoHours()->between('2:00', '22:00'); //Executa a tarefa a cada 2 hora 
        $schedule->command('radio:cron')->everyThreeHours()->between('0:00', '22:00'); //Executa a tarefa a cada 2 hora 
        $schedule->command('tv:cron')->everyThreeHours()->between('1:00', '22:00'); //Executa a tarefa a cada 2 hora 

    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}