<?php

namespace App\Console\Commands;

use Mail;
use Illuminate\Console\Command;

class EmailCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia um email as cada 5 minutos';

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
        $data['dados'] = null;
        $titulo = " Notificação de Monitoramento - Execução automática - ".date("d/m/Y H:i:s"); 

        //app('App\Http\Controllers\MonitoramentoController')->executar();
        
        Mail::send('notificacoes.teste', $data, function($message) use ($titulo){
            $message->to("robsonferduda@gmail.com")
                    ->subject($titulo);
            $message->from('boletins@clipagens.com.br','Studio Social');
        }); 
    }
}
