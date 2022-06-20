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
        
        Mail::send('notificacoes.teste', $data, function($message){
            $message->to("robsonferduda@gmail.com")
                    ->subject('Notificação de Monitoramento - Teste de Envio');
            $message->from('boletins@clipagens.com.br','Studio Social');
        }); 
    }
}
