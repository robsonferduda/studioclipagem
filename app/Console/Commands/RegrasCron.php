<?php

namespace App\Console\Commands;

use Mail;
use Illuminate\Console\Command;

class RegrasCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regras:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa as regras de busca de expressão em notícias';

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
        
    }
}
