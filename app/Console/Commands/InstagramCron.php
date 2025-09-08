<?php

namespace App\Console\Commands;

use Mail;
use Illuminate\Console\Command;

class InstagramCron extends Command
{
    protected $signature = 'instagram:cron';
    protected $description = 'Executa monitoramentos de Instagram';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $data['dados'] = null;
        $titulo = " Notificação de Monitoramento Instagram - Execução Automática - ".date("d/m/Y H:i:s"); 

        app('App\Http\Controllers\MonitoramentoController')->executarInstagram();
        
    }
}