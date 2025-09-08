<?php

namespace App\Console\Commands;

use Mail;
use Illuminate\Console\Command;

class FacebookCron extends Command
{
    protected $signature = 'facebook:cron';
    protected $description = 'Executa monitoramentos de Facebook';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $data['dados'] = null;
        $titulo = " Notificação de Monitoramento Facebook - Execução Automática - ".date("d/m/Y H:i:s"); 

        app('App\Http\Controllers\MonitoramentoController')->executarFacebook();
        
    }
}