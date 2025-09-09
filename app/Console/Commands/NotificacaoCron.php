<?php

namespace App\Console\Commands;

use Mail;
use Illuminate\Console\Command;

class NotificacaoCron extends Command
{
    protected $signature = 'notificacao:cron';
    protected $description = 'Envia notificações de redes sociais';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $data['dados'] = null;
        $titulo = "Envio de Notificações - Execução Automática - ".date("d/m/Y H:i:s"); 

        app('App\Http\Controllers\NotificacaoController')->notificar();
        
    }
}