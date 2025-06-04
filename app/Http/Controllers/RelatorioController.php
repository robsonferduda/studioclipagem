<?php

namespace App\Http\Controllers;

use Auth;
use PDFS;
use App\Models\Cliente;
use App\Models\Relatorio;
use App\Models\NoticiaWeb;
use Carbon\Carbon;
use App\Models\NoticiaImpresso;
use App\Jobs\GerarRelatorioJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;
use PhpOffice\PhpWord\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf as DOMPDF;

class RelatorioController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','relatorio');
        $this->carbon = new Carbon();
    }

    public function index(Request $request)
    {   
        $dados = array();
        $clientes = array();

        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d")." 00:00:00";
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d")." 23:59:59";

        $dt_inicial_formatada = ($request->dt_inicial) ? $request->dt_inicial : date("d/m/Y");
        $dt_final_formatada = ($request->dt_final) ? $request->dt_final : date("d/m/Y");

        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();
        $cliente_selecionado = ($request->id_cliente) ? $request->id_cliente : null;

        $fl_web = $request->fl_web == true ? true : false;
        $fl_tv = $request->fl_tv == true ? true : false;
        $fl_impresso = $request->fl_impresso == true ? true : false;
        $fl_radio = $request->fl_radio == true ? true : false;

        if($request->isMethod('POST')){

            $dados_impresso = ($fl_impresso) ? $this->dadosImpresso($dt_inicial, $dt_final,$cliente_selecionado) : array();
            $dados_radio    = ($fl_radio) ? $this->dadosRadio($dt_inicial, $dt_final,$cliente_selecionado) : array();
            $dados_web      = ($fl_web) ? $this->dadosWeb($dt_inicial, $dt_final,$cliente_selecionado) : array();
            $dados_tv      = ($fl_tv) ? $this->dadosTv($dt_inicial, $dt_final,$cliente_selecionado) : array();

            $dados = array_merge($dados_impresso, $dados_radio, $dados_web, $dados_tv);

            switch($request->acao) {

                case 'gerar-pdf':

                    $nome_arquivo = date('YmdHis').".pdf";

                    // Cria o registro do relatório no banco
                    $relatorio = Relatorio::create([
                        'id_tipo' => 1, // Clipping
                        'ds_nome' => $nome_arquivo,
                        'cd_usuario' => Auth::user()->id,
                        'dt_requisicao' => now(),
                    ]);

                    $data = [
                        'dados_impresso' => $dados_impresso,
                        'dados_web' => $dados_web,
                        'dt_inicial_formatada' => $dt_inicial_formatada,
                        'dt_final_formatada' => $dt_final_formatada
                    ];

                    GerarRelatorioJob::dispatch($data, $nome_arquivo, $relatorio);
                  
                /*
                case 'gerar-pdf':

                    $nome = "Relatório Completo";
                    $nome_arquivo = date('YmdHis').".pdf";

                    $data = [
                        'dados_impresso' => $dados_impresso,
                        'dados_web' => $dados_web,
                        'dt_inicial_formatada' => $dt_inicial_formatada,
                        'dt_final_formatada' => $dt_final_formatada
                    ];

                    $pdf = PDFS::loadView('relatorio/pdf/principal', $data);
                    $ver = Storage::disk('public')->put('relatorios-pdf/'.$nome_arquivo, $pdf->output()); 

                    return $pdf->download($nome_arquivo);
                    */
                break;
            
                case 'pesquisar': 
                    
                    return view('relatorio/index', compact('dados','clientes','cliente_selecionado','dt_inicial','dt_final','fl_web','fl_tv','fl_radio','fl_impresso'));

                break;
            }

        }

        return view('relatorio/index', compact('dados','clientes','cliente_selecionado','dt_inicial','dt_final','fl_web','fl_tv','fl_radio','fl_impresso'));
    }

    public function clipping(Request $request)
    {
        $relatorios = Relatorio::orderBy('created_at','DESC')->get();

        return view('relatorio/clipping', compact('relatorios'));
    }

    public function getClipping($arquivo)
    {
        return Storage::disk('public')->download('relatorios-pdf/'.$arquivo); 
    }

    function pdfIndividual($tipo, $id)
    {
        $nome_arquivo = date("YmdHis").'_'.$tipo.'_'.$id.'.pdf';

        switch ($tipo) {
            case 'web':
                $noticia = NoticiaWeb::where('id', $id)->first();
                break;
            case 'impresso':
                $noticia = NoticiaImpresso::where('id', $id)->first();
                break;
        }

        $data = [
            'noticia' => $noticia,
            'tipo' => $tipo
        ];

        $pdf = PDFS::loadView('relatorio/pdf/individual', $data);
        return $pdf->download($nome_arquivo);
    }

    public function dadosImpresso($dt_inicial, $dt_final,$cliente_selecionado)
    {
        $sql = "SELECT t1.id, 
                    sg_estado,
                    nm_estado,
                    nm_cidade,
                    '' as secao,
                    nu_pagina_atual as pagina,
                    titulo, 
                    t4.nome as cliente,
                    tipo_id,
                    'impresso' as tipo, 
                    TO_CHAR(dt_clipagem, 'DD/MM/YYYY') AS data_formatada,
                    t2.nome as fonte,
                    t1.sinopse,
                    t3.sentimento,
                    'imagem' as tipo_midia,
                    ds_caminho_img as midia,
                    '' as url_noticia
                FROM noticia_impresso t1
                JOIN jornal_online t2 ON t2.id = t1.id_fonte
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND tipo_id = 1
                JOIN clientes t4 ON t4.id = t3.cliente_id
                LEFT JOIN cidade t5 ON t5.cd_cidade = t1.cd_cidade
                LEFT JOIN estado t6 ON t6.cd_estado = t1.cd_estado
                WHERE 1=1
                AND t1.dt_clipagem BETWEEN '$dt_inicial' AND '$dt_final'";

        if($cliente_selecionado){
            $sql .= ' AND t3.cliente_id = '.$cliente_selecionado;
        }

        return $dados = DB::select($sql);
    }

    public function dadosRadio($dt_inicial, $dt_final,$cliente_selecionado)
    {
        $sql = "SELECT t1.id, 
                    sg_estado,
                    nm_estado,
                    nm_cidade,
                    '' as secao,
                    '' as pagina,
                    titulo, 
                    t4.nome as cliente,
                    tipo_id,
                    'radio' as tipo, 
                    TO_CHAR(dt_clipagem, 'DD/MM/YYYY') AS data_formatada,
                    t2.nome_emissora as fonte,
                    t1.sinopse,
                    t3.sentimento,
                    'audio' as tipo_midia,
                    ds_caminho_audio as midia,
                     '' as url_noticia
                FROM noticia_radio t1
                JOIN emissora_radio t2 ON t2.id = t1.emissora_id
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND tipo_id = 3
                JOIN clientes t4 ON t4.id = t3.cliente_id
                LEFT JOIN cidade t5 ON t5.cd_cidade = t1.cd_cidade
                LEFT JOIN estado t6 ON t6.cd_estado = t1.cd_estado
                WHERE 1=1
                AND t1.dt_clipagem BETWEEN '$dt_inicial' AND '$dt_final'";

        if($cliente_selecionado){
            $sql .= ' AND t3.cliente_id = '.$cliente_selecionado;
        }

        return $dados = DB::select($sql);
    }

    public function dadosWeb($dt_inicial, $dt_final,$cliente_selecionado)
    {
        $sql = "SELECT t1.id, 
                    sg_estado,
                    nm_estado,
                    nm_cidade,
                    '' as secao,
                    '' as pagina,
                    titulo_noticia as titulo, 
                    t5.nome as cliente,
                    tipo_id,
                    'web' as tipo, 
                    TO_CHAR(data_noticia, 'DD/MM/YYYY') AS data_formatada,
                    t2.nome as fonte,
                    t4.conteudo as sinopse,
                    t3.sentimento,
                    'imagem' as tipo_midia,
                    ds_caminho_img as midia,
                    url_noticia
                FROM noticias_web t1
                JOIN fonte_web t2 ON t2.id = t1.id_fonte
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND tipo_id = 2
                JOIN conteudo_noticia_web t4 ON t4.id_noticia_web = t1.id
                JOIN clientes t5 ON t5.id = t3.cliente_id
                LEFT JOIN cidade t6 ON t6.cd_cidade = t1.cd_cidade
                LEFT JOIN estado t7 ON t7.cd_estado = t1.cd_estado
                WHERE 1=1
                AND t1.data_noticia BETWEEN '$dt_inicial' AND '$dt_final'";

        if($cliente_selecionado){
            $sql .= ' AND t3.cliente_id = '.$cliente_selecionado;
        }

        $dados = DB::select($sql);

        foreach($dados as $dado){

            $noticia_web = NoticiaWeb::where('id', $dado->id)->where('ds_caminho_img','=',null)->first();

            if($noticia_web){

                if (Storage::disk('s3')->exists($noticia_web->path_screenshot)) {
                    $arquivo = Storage::disk('s3')->get($noticia_web->path_screenshot);
                    $filename = $noticia_web->id.".jpg";
                    Storage::disk('web-img')->put($filename, $arquivo);

                    $noticia_web->ds_caminho_img = $filename;
                    $noticia_web->save();
                }

            }
        }            

        return $dados;
    }

    public function dadosTv($dt_inicial, $dt_final,$cliente_selecionado)
    {
        $sql = "SELECT t1.id, 
                    sg_estado,
                    nm_estado,
                    nm_cidade,
                    '' as secao,
                    '' as pagina,
                    '' as titulo, 
                    t4.nome as cliente,
                    tipo_id,
                    'tv' as tipo, 
                    TO_CHAR(dt_noticia, 'DD/MM/YYYY') AS data_formatada,
                    t2.nome_emissora as fonte,
                    sinopse,
                    t3.sentimento,
                    'imagem' as tipo_midia,
                    ds_caminho_video as midia,
                    '' as url_noticia
                FROM noticia_tv t1
                JOIN emissora_web t2 ON t2.id = t1.emissora_id
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND tipo_id = 4
                JOIN clientes t4 ON t4.id = t3.cliente_id
                LEFT JOIN cidade t5 ON t5.cd_cidade = t1.cd_cidade
                LEFT JOIN estado t6 ON t6.cd_estado = t1.cd_estado
                WHERE 1=1
                AND t1.dt_noticia BETWEEN '$dt_inicial' AND '$dt_final'";

        if($cliente_selecionado){
            $sql .= ' AND t3.cliente_id = '.$cliente_selecionado;
        }

        return $dados = DB::select($sql);
    }

    public function word()
    {
        $phpWord = IOFactory::createReader('Word2007')->load(public_path().'/word/word.docx');

        foreach($phpWord->getSections() as $section) {
            foreach($section->getElements() as $element) {

                switch (get_class($element)) {
                    case 'PhpOffice\PhpWord\Element\Text' :
                        $text[] = $element->getText();
                        break;
                    case 'PhpOffice\PhpWord\Element\TextRun':
                        $textRunElements = $element->getElements();
                        foreach ($textRunElements as $textRunElement) {
                            $text[] = $textRunElement->getText();
                        }
                        break;
                    case 'PhpOffice\PhpWord\Element\TextBreak':
                        $text[] = " ";
                        break;
                    default:
                        throw new Exception('Something went wrong...');
                }
            }
        }
    }

    function dividirImagem($caminhoImagem, $alturaMaxima)
    {

        $larguraMaxima = 2480;

        // Carregar a imagem
        $imagemOriginal = imagecreatefromjpeg($caminhoImagem);
        $larguraOriginal = imagesx($imagemOriginal);
        $alturaOriginal = imagesy($imagemOriginal);

        // Calcular a nova altura mantendo a proporção
        $novaLargura = $larguraMaxima;
        $novaAltura = intval(($alturaOriginal / $larguraOriginal) * $novaLargura);

        // Criar a nova imagem redimensionada
        $imagemRedimensionada = imagecreatetruecolor($novaLargura, $novaAltura);
        imagecopyresampled($imagemRedimensionada, $imagemOriginal, 0, 0, 0, 0, $novaLargura, $novaAltura, $larguraOriginal, $alturaOriginal);

        // Salvar a imagem redimensionada
        $caminhoRedimensionado = storage_path("app/public/redimensionada.jpg");
        imagejpeg($imagemRedimensionada, $caminhoRedimensionado, 90); // Qualidade 90%

        // Liberar memória
        imagedestroy($imagemOriginal);
        imagedestroy($imagemRedimensionada);

        $imagemOriginal = imagecreatefromjpeg($caminhoRedimensionado);

        $imagemOriginal = imagecreatefromjpeg($caminhoImagem);
        $largura = imagesx($imagemOriginal);
        $alturaTotal = imagesy($imagemOriginal);

        $partes = [];
        for ($i = 0; $i < $alturaTotal; $i += $alturaMaxima) {
            $novaAltura = min($alturaMaxima, $alturaTotal - $i);
            $parte = imagecreatetruecolor($largura, $novaAltura);
            imagecopy($parte, $imagemOriginal, 0, 0, 0, $i, $largura, $novaAltura);

            // Salvar a parte temporariamente
            $nomeParte = "parte_" . $i . ".jpg";
            $caminhoParte = "partes/".$nomeParte;
            imagejpeg($parte, $caminhoParte);
            $partes[] = $caminhoParte;
        }

        imagedestroy($imagemOriginal);
        return $partes;
    }


    public function sqlDiario()
    {
        $sql = "SELECT 
                        tv.id as id,
                        CONCAT('','') as titulo, 
                        tv.data as data,
                        tv.segundos_totais as segundos, 
                        tv.sinopse as sinopse, 
                        tv.uf as uf, 
                        CONCAT('','') as link, 
                        tv.status as status, 
                        '' as printurl,
                        cidade.titulo as cidade_titulo, 
                        veiculo.titulo as INFO1,
                        parte.titulo as INFO2, 
                        parte.hora as INFOHORA, 
                        CONCAT('tv','') as clipagem,
                        area.titulo as area,
                        area.ordem as ordem
                FROM app_tv as tv 
                    LEFT JOIN app_tv_emissora as veiculo ON veiculo.id = tv.id_emissora
                    LEFT JOIN app_tv_programa as parte ON parte.id = tv.id_programa 
                    LEFT JOIN app_cidades as cidade ON cidade.id = tv.id_cidade 
                    LEFT JOIN app_areasmodalidade as area ON (tv.id_area = area.id)
                WHERE tv.data = '$this->data_atual'
                UNION
                SELECT 
                    radio.id as id,
                    CONCAT('','') as titulo, 
                    radio.data as data, 
                    radio.segundos_totais as segundos, 
                    radio.sinopse as sinopse, 
                    radio.uf as uf, 
                    radio.link as link, 
                    radio.status as status, 
                    '' as printurl,
                    cidade.titulo as cidade_titulo, 
                    veiculo.titulo as INFO1,
                    parte.titulo as INFO2, 
                    parte.hora as INFOHORA, 
                    CONCAT('radio','') as clipagem,
                    area.titulo as area,
                    area.ordem as ordem      
                FROM app_radio as radio 
                    LEFT JOIN app_radio_emissora as veiculo ON veiculo.id = radio.id_emissora
                    LEFT JOIN app_radio_programa as parte ON parte.id = radio.id_programa 
                    LEFT JOIN app_cidades as cidade ON cidade.id = radio.id_cidade 
                    LEFT JOIN app_areasmodalidade as area ON (radio.id_area = area.id)
                WHERE radio.data = '$this->data_atual'
                UNION
                SELECT
                    jornal.id as id, 
                    jornal.titulo as titulo, 
                    jornal.data_clipping as data, 
                    '' as segundos,
                    jornal.sinopse as sinopse, 
                    jornal.uf as uf, 
                    CONCAT('','') as link, 
                    jornal.status as status, 
                    jornal.printurl as printurl,
                    cidade.titulo as cidade_titulo, 
                    veiculo.titulo as INFO1,
                    parte.titulo as INFO2,
                    ''  as INFOHORA,
                    CONCAT('jornal','') as clipagem,
                    area.titulo as area,
                    area.ordem as ordem  
                FROM app_jornal as jornal 
                    LEFT JOIN app_jornal_impresso as veiculo ON veiculo.id = jornal.id_jornalimpresso
                    LEFT JOIN app_jornal_secao as parte ON parte.id = jornal.id_secao 
                    LEFT JOIN app_cidades as cidade ON cidade.id = jornal.id_cidade 
                    LEFT JOIN app_areasmodalidade as area ON (jornal.id_area = area.id)
                WHERE data_clipping = '$this->data_atual'
                UNION
                SELECT 
                    web.id as id, 
                    web.titulo as titulo, 
                    web.data_clipping as data, 
                    '' as segundos,
                    web.sinopse as sinopse, 
                    web.uf as uf, 
                    web.link as link, 
                    web.status as status, 
                    web.printurl as printurl, 
                    cidade.titulo as cidade_titulo, 
                    veiculo.titulo as INFO1,
                    parte.titulo as INFO2, 
                    ''  as INFOHORA,
                    CONCAT('web','') as clipagem,
                    area.titulo as area,
                    area.ordem as ordem      
                FROM app_web as web 
                    LEFT JOIN app_web_sites as veiculo ON veiculo.id = web.id_site
                    LEFT JOIN app_web_secao as parte ON parte.id = web.id_secao 
                    LEFT JOIN app_cidades as cidade ON cidade.id = web.id_cidade 
                    LEFT JOIN app_areasmodalidade as area ON (web.id_area = area.id)
                WHERE web.data_clipping = '$this->data_atual'
                ORDER BY id";

        return $sql;
    }

    public function pdf(Request $request)
    {
        $dt_inicial = date('Y-m-d');
        $dt_final = date('Y-m-d');
        $nome = "Relatório de Sentimentos";

        $nome_arquivo = date('YmdHis').".pdf";

        $pdf = DOMPDF::loadView('relatorio/pdf/principal', compact('dt_inicial','dt_final','nome'));
        

        return $pdf->download($nome_arquivo);
    }
}