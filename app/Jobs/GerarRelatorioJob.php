<?php

namespace App\Jobs;

use PDFS;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GerarRelatorioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $nome_arquivo;

    public function __construct($data, $nome_arquivo)
    {
        $this->data = $data;
        $this->nome_arquivo = $nome_arquivo;
    }

    public function handle()
    {
        $pdf = PDFS::loadView('relatorio/pdf/principal', $this->data);
        Storage::disk('public')->put('relatorios-pdf/'.$this->nome_arquivo, $pdf->output());        
    }
}