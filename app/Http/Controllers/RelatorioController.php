<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
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
    }

    public function word()
    {
        //dd(public_path().'/word/word.docx');

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

        dd($text);
    }

    public function index(Request $request)
    {   
        if($request->isMethod('GET')){

            $sql = $this->sqlDiario();
            $dados = DB::connection('mysql')->select($sql);
            
        }

        if($request->isMethod('POST')){

            switch($request->acao) {

                case 'gerar-pdf':

                    $dados = $this->dadosTv();

                    //dd($dados);

                    $dt_inicial = date('d/m/Y');
                    $dt_final = date('d/m/Y');
                    $nome = "Relatório Completo";
                    $nome_arquivo = date('YmdHis').".pdf";

                    $pdf = \App::make('dompdf.wrapper');
                    //$pdf->getDomPDF()->set_option("enable_php", true);

                    $pdf->loadView('relatorio/pdf/principal', compact('dt_inicial','dt_final','nome','dados'));

                    //$pdf->render();

                    /*

                    $canvas = $pdf->get_canvas();
                    $canvas->page_script('
                    if ($pdf->get_page_number() != $pdf->get_page_count()) {

                        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                        $x = ($pdf->get_width() - $width) / 2;
                        $x = ($pdf->get_width() - $width - 5);
                        $y = $pdf->get_height() - 30;

                        $font = Font_Metrics::get_font("helvetica", "12");                  
                        $pdf->page_text($x, $y, "Page {PAGE_NUM} - {PAGE_COUNT}", $font, 10, array(0,0,0));
                    }
                    ');
                    //$pdf = $dompdf->output();
                    */

                    //return $pdf->output();
                    
                    return $pdf->download($nome_arquivo);
                break;
            
                case 'pesquisar': 
                    return view('relatorio/index', compact('dados'));
                break;
            }


            $sql = $this->sqlDiario();
            $dados = DB::connection('mysql')->select($sql);
        }

        return view('relatorio/index', compact('dados'));
        
    }

    public function dadosTv()
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
                WHERE tv.data = '2023-06-29'
                LIMIT 8";

        return DB::connection('mysql')->select($sql);
    }

    public function diario()
    {
        
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
                WHERE tv.data = '2023-06-29'
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
                WHERE radio.data = '2023-06-29'
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
                WHERE data_clipping = '2023-06-29'
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
                WHERE web.data_clipping = '2023-06-29'
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