<?php

namespace App\Console\Commands;

use Mail;
use Illuminate\Console\Command;

class WebCron3 extends Command
{
    protected $signature = 'web_3:cron';
    protected $description = 'Executa monitoramentos de web';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $data['dados'] = null;
        $titulo = " Notificação de Monitoramento Web - Grupo 3 - ".date("d/m/Y H:i:s"); 

        app('App\Http\Controllers\MonitoramentoController')->executarWeb(3);
        
        /*
        Mail::send('notificacoes.teste', $data, function($message) use ($titulo){
            $message->to("robsonferduda@gmail.com")
                    ->subject($titulo);
            $message->from('boletins@clipagens.com.br','Studio Clipagem');
        }); */
    }
}
