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

    public function index(Request $request)
    {
        Session::put('sub-menu','pautas');
        $carbon = new Carbon();

        $dados = array();
        $id_cliente = ($request->cliente) ? $request->cliente : null;
        $dt_noticia = ($request->dt_noticia) ? $carbon->createFromFormat('d/m/Y', $request->dt_noticia)->format('Y-m-d') : date("Y-m-d");

        $clientes = Cliente::select('clientes.*', 'clientes.id as id_unico')
                            ->with('pessoa')
                            ->join('pessoas', 'pessoas.id', '=', 'clientes.pessoa_id')
                            ->orderBy('nome')
                            ->get();

        if($request->isMethod('POST')){
        
            $sql = array();

            if($request->check_tv){

                $sql[] = "( SELECT 
                                    tv.data as data,
                                    CONCAT('TV','') as clipagem,
                                    CONCAT('','') as titulo,                                     
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
                                tv.data = '$dt_noticia' 
                            AND id_cliente = $id_cliente
                        )";
            }

            if(!empty($request->check_radio)){

                //"Data","Tipo","Título","Sinopse","Veículo","Seção","Cidade","Estado","Link","Rotorno"
                
                $sql[] = "(SELECT 
                                radio.data as data, 
                                CONCAT('Rádio','') as clipagem,
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
                                radio.data = '$dt_noticia' 
                            AND id_cliente = $id_cliente
                    )";
            }

            if(!empty($request->check_jornal)){

                //"Data","Tipo","Título","Sinopse","Veículo","Seção","Cidade","Estado","Link","Rotorno"
                
                $sql[] = "(SELECT
                                jornal.data_clipping as data,
                                CONCAT('Impresso','') as clipagem,
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
                                jornal.data_clipping = '$dt_noticia' 
                            AND id_cliente = $id_cliente
                    )";
            }

            if(!empty($request->check_web)){

                //"Data","Tipo","Título","Sinopse","Veículo","Seção","Cidade","Estado","Link","Rotorno"
                
                $sql[] = "(SELECT 
                                web.data_clipping as data, 
                                CONCAT('Web','') as clipagem,
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
                                web.data_clipping =  '$dt_noticia' 
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