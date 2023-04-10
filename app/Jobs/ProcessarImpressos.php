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

    public function __construct()
    {
        //
    }

    public function handle()
    {
        $data['dados'] = null;

        $process = new Process(['sudo python3', base_path().'/read-pdf-convert-to-jpg.py']);

        $process->run(function ($type, $buffer){

            if (Process::ERR === $type) {

                $data['dados'] = null;

                Mail::send('notificacoes.impressos.processamento', $data, function($message){
                    $message->to("robsonferduda@gmail.com")
                            ->subject('Erro - Processamento de Jornais Impresso');
                    $message->from('boletins@clipagens.com.br','Studio Clipagem');
                }); 
              
            }else{
                //Quando corre tudo bem
            }

        });

        Mail::send('notificacoes.impressos.processamento', $data, function($message){
            $message->to("robsonferduda@gmail.com")
                    ->subject('Tentou, mas não foi');
            $message->from('boletins@clipagens.com.br','Studio Clipagem');
        }); 
        
        return true;
    }
}