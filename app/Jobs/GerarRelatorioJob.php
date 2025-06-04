<?php

namespace App\Jobs;

use PDFS;
use App\Models\Relatorio;
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
    public $relatorio;

    public $tries = 10; // Número de tentativas antes de falhar
    public $timeout = 1200; // 20 minutos

    public function __construct($data, $nome_arquivo, $relatorio)
    {
        $this->data = $data;
        $this->nome_arquivo = $nome_arquivo;
        $this->relatorio = $relatorio;
    }

    public function handle()
    {
        try {
            $pdf = PDFS::loadView('relatorio/pdf/principal', $this->data);
            Storage::disk('public')->put('relatorios-pdf/'.$this->nome_arquivo, $pdf->output());

            // Sucesso: atualiza situação para "pronto" (ex: 2)
            $this->relatorio->situacao = 1;
            $this->relatorio->dt_finalizacao = now();
            $this->relatorio->save();
        } catch (\Exception $e) {
            // Erro: atualiza situação para "erro" (ex: 3)
            $this->relatorio->situacao = 2;
            $this->relatorio->dt_finalizacao = now();
            $this->relatorio->save();

            // Opcional: log do erro
            \Log::error('Erro ao gerar relatório: '.$e->getMessage());
            throw $e; // para o job marcar como falha
        }      
    }
}