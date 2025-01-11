<?php

namespace App\Console\Commands;

use Mail;
use Illuminate\Console\Command;

class WebCron extends Command
{
    protected $signature = 'web:cron';
    protected $description = 'Executa monitoramentos de web';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $data['dados'] = null;
        $titulo = " Notificação de Monitoramento Web - Execução Automática - ".date("d/m/Y H:i:s"); 

        app('App\Http\Controllers\MonitoramentoController')->executarWeb();
        
        /*
        Mail::send('notificacoes.teste', $data, function($message) use ($titulo){
            $message->to("robsonferduda@gmail.com")
                    ->subject($titulo);
            $message->from('boletins@clipagens.com.br','Studio Clipagem');
        }); */
    }
}
