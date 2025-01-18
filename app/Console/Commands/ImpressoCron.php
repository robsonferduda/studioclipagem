<?php

namespace App\Console\Commands;

use Mail;
use Illuminate\Console\Command;

class ImpressoCron extends Command
{
    protected $signature = 'impresso:cron';
    protected $description = 'Executa monitoramentos de Impresso';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $data['dados'] = null;
        $titulo = " Notificação de Monitoramento Web - Execução Automática - ".date("d/m/Y H:i:s"); 

        app('App\Http\Controllers\MonitoramentoController')->executarImpresso();
        
    }
}