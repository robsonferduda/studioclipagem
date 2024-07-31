<?php

namespace App\Console\Commands;

use Mail;
use Illuminate\Console\Command;

class ImportaWebCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importacao:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza dados de importação';

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
       
        app('App\Http\Controllers\ExportarController')->atualizar();
        
    }
}