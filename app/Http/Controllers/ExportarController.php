<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Boletim;
use App\Utils;
use App\Models\Cliente;
use App\Models\Pauta;
use App\Models\PautaNoticia;
use App\Models\NoticiaRadio;
use Carbon\Carbon;
use App\Exports\OcorrenciasExport;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class ExportarController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('exportar','pautas');
    }

    public function importar()
    {
        $sql = "SELECT
                    jornal.id as id, 
                    jornal.id_cliente, 
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
                WHERE data_clipping BETWEEN '2020-01-01 00:00:00' AND '2020-12-31 23:59:59'";

        $dados = DB::connection('mysql')->select($sql);

        

    }

    public function index(Request $request)
    {
        Session::put('sub-menu','pautas');
        $carbon = new Carbon();

        $dados = array();
        $id_cliente = ($request->cliente) ? $request->cliente : null;
        $dt_inicio = ($request->dt_inicio) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicio)->format('Y-m-d') : date("Y-m-d");
        $dt_fim = ($request->dt_fim) ? $carbon->createFromFormat('d/m/Y', $request->dt_fim)->format('Y-m-d') : date("Y-m-d");
        $termo = ($request->termo) ? $request->termo : "";

        $complemento_termo = ($request->termo) ? " AND sinopse LIKE '%$request->termo%'" : "";
        $complemento_sentimento = ($request->sentimento) ? " AND status = '$request->sentimento' " : "";

        $clientes = Cliente::select('clientes.*', 'clientes.id as id_unico')
                            ->with('pessoa')
                            ->join('pessoas', 'pessoas.id', '=', 'clientes.pessoa_id')
                            ->orderBy('nome')
                            ->get();

        if($request->isMethod('POST')){

            $sql = 'SELECT * 
                    FROM base_knewin';
                    
            $dados = DB::connection('pgsql')->select($sql);

            $fileName = "noticias.xlsx";
            return Excel::download(new OcorrenciasExport($dados), $fileName);                  
        }                    

        return view('exportar/index', compact('clientes','dados'));
    }

    public function teste(Request $request)
    {
        Session::put('sub-menu','pautas');
        $carbon = new Carbon();

        $dados = array();
        $id_cliente = ($request->cliente) ? $request->cliente : null;
        $dt_inicio = ($request->dt_inicio) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicio)->format('Y-m-d') : date("Y-m-d");
        $dt_fim = ($request->dt_fim) ? $carbon->createFromFormat('d/m/Y', $request->dt_fim)->format('Y-m-d') : date("Y-m-d");
        $termo = ($request->termo) ? $request->termo : "";

        $complemento_termo = ($request->termo) ? " AND sinopse LIKE '%$request->termo%' OR titulo LIKE '%$request->termo%' " : "";
        $complemento_sentimento = ($request->sentimento) ? " AND status = '$request->sentimento' " : "";

        $tipos = array();
        ($request->check_tv) ? $tipos[] = (string) "'tv'" : "";
        ($request->check_radio) ? $tipos[] = (string) "'radio'" : "";
        ($request->check_web) ? $tipos[] = (string) "'web'" : "";
        ($request->check_jornal) ? $tipos[] = (string) "'jornal'" : "";

        $complemento_tipo = "";
        $complemento_tipo .= ($request->check_tv OR $request->check_radio OR $request->check_web OR $request->check_jornal) ? " AND tipo IN(".implode(',', $tipos).")" : ""; 

        //"Data","Tipo","Título","Sinopse","Veículo","Seção","Cidade","Estado","Link","Rotorno"

        $sql = "SELECT data,
                        tipo,
                        titulo,
                        sinopse,
                        info1, 
                        info2,
                        cidade_titulo,
                        uf,
                        link,
                        retorno 
                FROM base_knewin
                WHERE data BETWEEN '$dt_inicio 00:00:00' AND '$dt_fim 23:59:59'
                $complemento_termo
                $complemento_sentimento
                $complemento_tipo
                AND cliente_id = $id_cliente";

        $dados = DB::connection('pgsql')->select($sql);

        $fileName = "noticias.xlsx";

        return Excel::download(new OcorrenciasExport($dados), $fileName);

    }

    public function index_old(Request $request)
    {
        Session::put('sub-menu','pautas');
        $carbon = new Carbon();

        $dados = array();
        $id_cliente = ($request->cliente) ? $request->cliente : null;
        $dt_inicio = ($request->dt_inicio) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicio)->format('Y-m-d') : date("Y-m-d");
        $dt_fim = ($request->dt_fim) ? $carbon->createFromFormat('d/m/Y', $request->dt_fim)->format('Y-m-d') : date("Y-m-d");
        $termo = ($request->termo) ? $request->termo : "";

        $complemento_termo = ($request->termo) ? " AND sinopse LIKE '%$request->termo%'" : "";
        $complemento_sentimento = ($request->sentimento) ? " AND status = '$request->sentimento' " : "";

        $clientes = Cliente::select('clientes.*', 'clientes.id as id_unico')
                            ->with('pessoa')
                            ->join('pessoas', 'pessoas.id', '=', 'clientes.pessoa_id')
                            ->orderBy('nome')
                            ->get();

        if($request->isMethod('POST')){

            $sql = "SELECT * 
                    FROM base_knewin
                    WHERE data BETWEEN '$dt_inicio 00:00:00' AND '$dt_fim 23:59:59'
                    AND cliente_id = $id_cliente";

            $dados = DB::connection('pgsql')->select($sql);

            dd($dados);
        }

        if($request->isMethod('POST')){
        
            $sql = array();

            if($request->check_tv){

                $sql[] = "( SELECT 
                                    tv.data as data,
                                    CONCAT('TV','') as clipagem,
                                    CONCAT('','') as titulo, 
                                    status as sentimento,                                    
                                    tv.sinopse as sinopse, 
                                    veiculo.titulo as INFO1,
                                    parte.titulo as INFO2,                                                                       
                                    cidade.titulo as cidade_titulo, 
                                    tv.uf as uf, 
                                    CONCAT('','') as link,
                                    retornos_totais as retorno
                            FROM app_tv as tv 
                                    LEFT JOIN app_tv_emissora as veiculo ON veiculo.id = tv.id_emissora
                                    LEFT JOIN app_tv_programa as parte ON parte.id = tv.id_programa 
                                    LEFT JOIN app_cidades as cidade ON cidade.id = tv.id_cidade 
                                    LEFT JOIN app_areasmodalidade as area ON (tv.id_area = area.id)
                            WHERE 
                                tv.data BETWEEN '$dt_inicio 00:00:00' AND '$dt_fim 23:59:59'
                                $complemento_termo
                                $complemento_sentimento
                            AND id_cliente = $id_cliente
                        )";
            }

            if(!empty($request->check_radio)){

                //"Data","Tipo","Título","Sinopse","Veículo","Seção","Cidade","Estado","Link","Rotorno"
                
                $sql[] = "(SELECT 
                                radio.data as data, 
                                CONCAT('Rádio','') as clipagem,
                                status as sentimento,
                                CONCAT('','') as titulo,                                 
                                radio.sinopse as sinopse, 
                                veiculo.titulo as INFO1,
                                parte.titulo as INFO2, 
                                cidade.titulo as cidade_titulo, 
                                radio.uf as uf, 
                                radio.link as link,
                                retornos_totais as retorno
                            FROM app_radio as radio 
                                LEFT JOIN app_radio_emissora as veiculo ON veiculo.id = radio.id_emissora
                                LEFT JOIN app_radio_programa as parte ON parte.id = radio.id_programa 
                                LEFT JOIN app_cidades as cidade ON cidade.id = radio.id_cidade 
                                LEFT JOIN app_areasmodalidade as area ON (radio.id_area = area.id)
                            WHERE 
                                radio.data BETWEEN '$dt_inicio 00:00:00' AND '$dt_fim 23:59:59'
                                $complemento_termo
                                $complemento_sentimento
                            AND id_cliente = $id_cliente
                    )";
            }

            if(!empty($request->check_jornal)){

                //"Data","Tipo","Título","Sinopse","Veículo","Seção","Cidade","Estado","Link","Rotorno"
                
                $sql[] = "(SELECT
                                jornal.data_clipping as data,
                                CONCAT('Impresso','') as clipagem,
                                status as sentimento,
                                jornal.titulo as titulo,                                  
                                jornal.sinopse as sinopse, 
                                veiculo.titulo as INFO1,
                                parte.titulo as INFO2,
                                cidade.titulo as cidade_titulo,
                                jornal.uf as uf, 
                                CONCAT('','') as link,
                                retorno as retorno                               
                            FROM app_jornal as jornal 
                                LEFT JOIN app_jornal_impresso as veiculo ON veiculo.id = jornal.id_jornalimpresso
                                LEFT JOIN app_jornal_secao as parte ON parte.id = jornal.id_secao 
                                LEFT JOIN app_cidades as cidade ON cidade.id = jornal.id_cidade 
                                LEFT JOIN app_areasmodalidade as area ON (jornal.id_area = area.id)
                            WHERE 
                                jornal.data_clipping BETWEEN '$dt_inicio 00:00:00' AND '$dt_fim 23:59:59' 
                                $complemento_termo
                                $complemento_sentimento
                            AND id_cliente = $id_cliente
                    )";
            }

            if(!empty($request->check_web)){

                //"Data","Tipo","Título","Sinopse","Veículo","Seção","Cidade","Estado","Link","Rotorno"
                
                $sql[] = "(SELECT 
                                web.data_clipping as data, 
                                CONCAT('Web','') as clipagem,
                                status as sentimento,
                                web.titulo as titulo,                                 
                                web.sinopse as sinopse, 
                                veiculo.titulo as INFO1,
                                parte.titulo as INFO2, 
                                cidade.titulo as cidade_titulo,
                                web.uf as uf, 
                                web.link as link,
                                retorno as retorno   
                            FROM app_web as web 
                                LEFT JOIN app_web_sites as veiculo ON veiculo.id = web.id_site
                                LEFT JOIN app_web_secao as parte ON parte.id = web.id_secao 
                                LEFT JOIN app_cidades as cidade ON cidade.id = web.id_cidade 
                                LEFT JOIN app_areasmodalidade as area ON (web.id_area = area.id)
                            WHERE 
                                web.data_clipping BETWEEN '$dt_inicio 00:00:00' AND '$dt_fim 23:59:59' 
                                $complemento_termo
                                $complemento_sentimento
                            AND id_cliente = $id_cliente
                    )";
            }
                    
            $sql = implode(" UNION DISTINCT ",$sql);				
            $sql .= " ORDER BY clipagem DESC, data DESC";
            
            $dados = DB::connection('mysql')->select($sql);

            foreach($dados as $key => $noticia){

                if($noticia->clipagem != 'Web'){

                    switch ($noticia->clipagem) {
                        case 'TV':
                            $url = env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.mp4'; 
                            break;

                        case 'Rádio':

                            $url = env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.mp3';                                
                            break;

                        case 'Impresso':

                            $url = env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.jpg';
                            $header_response = get_headers($url, 1);
            
                            if(strpos( $header_response[0], "404" ) !== false){
                                $url = env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.jpeg';
                            } 
                                
                            break;
                        
                        default:
                            
                            break;
                    }  
                    
                    $dados[$key]->link = $url;    
                }       
            }

            $fileName = "noticias.xlsx";
            return Excel::download(new OcorrenciasExport($dados), $fileName);

        }
                    
        return view('exportar/index', compact('clientes','dados'));
    }
}