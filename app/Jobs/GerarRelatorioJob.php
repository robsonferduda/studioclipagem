<?php 

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;

class GerarRelatorioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data; // Dados JSON para gerar o PDF
    }

    public function handle()
    {
        // Renderizar a view com os dados
        $pdf = PDF::loadView('pdf.document', ['data' => $this->data]);

        // Salvar o PDF em disco (ex.: storage/app/public)
        $filePath = 'pdfs/relatorio_' . time() . '.pdf';
        Storage::disk('public')->put($filePath, $pdf->output());

        // Opcional: Notificar o usuário que o PDF está pronto
        // Exemplo: Enviar um email ou salvar o caminho do PDF no banco de dados

        // Notificar o usuário
        $user = User::find(1); // Substitua pelo ID do usuário real
        $url = Storage::disk('public')->url($filePath);
        Notification::send($user, new PdfProntoNotification($url));
    }
}