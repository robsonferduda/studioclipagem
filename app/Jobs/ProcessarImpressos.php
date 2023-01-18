<?php

namespace App\Jobs;

use Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ProcessarImpressos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data['dados'] = null;

        $process = new Process(['python3', base_path().'/read-pdf-convert-to-jpg.py']);

        $process->run();
        
        Mail::send('notificacoes.teste', $data, function($message){
            $message->to("robsonferduda@gmail.com")
                    ->subject('Notificação de Monitoramento - Teste de Envio');
            $message->from('boletins@clipagens.com.br','Studio Social');
        }); 

        return true;
    }
}
