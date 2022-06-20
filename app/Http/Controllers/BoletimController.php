<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use App\Boletim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BoletimController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['detalhes','enviar','visualizar']]);
        Session::put('url','boletins');
    }

    public function index()
    {
        $boletins = Boletim::whereIn('id_cliente',[443,452])->orderBy('data','DESC')->get();
        return view('boletim/index',compact('boletins'));
    }

    public function detalhes($id)
    {   
        $boletim = Boletim::where('id', $id)->first();
        $dados = $this->getDadosBoletim($id);        
    
        return view('boletim/detalhes', compact('boletim', 'dados'));
    }

    public function visualizar($id)
    {   
        $boletim = Boletim::where('id', $id)->first();
        $dados = $this->getDadosBoletim($id);        
    
        return view('boletim/visualizar', compact('boletim', 'dados'));
    }

    public function outlook($id)
    {   
        $boletim = Boletim::where('id', $id)->first();
        $dados = $this->getDadosBoletim($id);    
            
        return view('boletim/outlook', compact('boletim', 'dados'));
    }

    public function enviar($id)
    {
        $boletim = Boletim::where('id', $id)->first();

        if($boletim->id_cliente == 452){

            $lista_email = array(
                array('nome' => 'Álvaro Lista', 'email' => 'alvaro@studioclipagem.com.br'),
                //array('nome' => 'Rafael de Moraes Costa', 'email' => 'rafael01costa@gmail.com'),
                array('nome' => 'Robson Fernando Duda', 'email' => 'robsonferduda@gmail.com'),
                array('nome' => 'Andre Couto', 'email' => 'andre.couto@zurichairportbrasil.com'),
                array('nome' => 'Armstron', 'email' => 'armstron.carvalho@zurichairportbrasil.com'),
                array('nome' => 'Cris Vieira', 'email' => 'cris.vieira@zurichairportbrasil.com'),
                array('nome' => 'Fabio Marques', 'email' => 'fabio.marques@zurichairportbrasil.com'),
                array('nome' => 'Fernando Castro', 'email' => 'fernando.castro@zurichairportbrasil.com'),
                array('nome' => 'Kleyton Mendes', 'email' => 'kleyton.mendes@zurichairportbrasil.com'),
                array('nome' => 'Natalia Santos Pereira', 'email' => 'natalia.pereira@zurichairportbrasil.com'),
                array('nome' => 'Ricardo Gesse', 'email' => 'ricardo.gesse@zurichairportbrasil.com '),
                array('nome' => 'Vanessa Bezerra', 'email' => 'vanessa.bezerra@zurichairportbrasil.com')
            );

        }else{

            $lista_email = array(
                            array('nome' => 'Álvaro Lista', 'email' => 'alvaro@studioclipagem.com.br'),
                            //array('nome' => 'Rafael de Moraes Costa', 'email' => 'rafael01costa@gmail.com'),
                            array('nome' => 'Robson Fernando Duda', 'email' => 'robsonferduda@gmail.com'),
                            array('nome' => 'Adrian Elkuch', 'email' => 'adrian.elkuch@zurich-airport.lat'),
                            array('nome' => 'Anderson Pinheiro', 'email' => 'anderson.pinheiro@zurichairportbrasil.com'),
                            array('nome' => 'Andrea Lima', 'email' => 'andrea.lima@aseb-airport.com'),
                            array('nome' => 'Anderson Ribeiro', 'email' => 'anderson.ribeiro@zurichairportbrasil.com'),
                            array('nome' => 'Andre Couto', 'email' => 'andre.couto@zurichaiportbrasil.com'),
                            array('nome' => 'Bruna Fischer', 'email' => 'bruna.fischer@zurichairportbrasil.com'),
                            array('nome' => 'Caio', 'email' => 'caio.napoli@zurichairportbrasil.com'),
                            array('nome' => 'Cris Vieira', 'email' => 'cris.vieira@zurichairportbrasil.com'),
                            array('nome' => 'Davi Piza', 'email' => 'davi.piza@zurichairportbrasil.com'),
                            array('nome' => 'Fabio Marques', 'email' => 'fabio.marques@zurichairportbrasil.com'),
                            array('nome' => 'Felipe', 'email' => 'felipe.schneider@zurichairportbrasil.com'),
                            array('nome' => 'Fernando Castro', 'email' => 'fernando.castro@zurichairportbrasil.com'),
                            array('nome' => 'Giovani Montibeller ', 'email' => 'giovani.montibeller@zurichairportbrasil.com'),
                            array('nome' => 'Gustavo Brighenti', 'email' => 'gustavo.brighenti@zurichairportbrasil.com'),
                            array('nome' => 'Jasmine Reis', 'email' => 'jasmine.reis@zurichairportbrasil.com'),
                            array('nome' => 'Johann Gigl', 'email' => 'johann.gigl@zurich-airport.lat'),
                            array('nome' => 'Jerco', 'email' => 'jerco.bacic@zurichairportbrasil.com'),
                            array('nome' => 'Karen Bonfim', 'email' => 'karen.bonfim1@gmail.com'),
                            array('nome' => 'Kleyton Mendes', 'email' => 'kleyton.mendes@zurichairportbrasil.com'),
                            array('nome' => 'Leila', 'email' => 'leila.martins@zurichairportbrasil.com'),
                            array('nome' => 'Lisiane Karan', 'email' => 'lisiane.karan@zurichairportbrasil.com'),
                            array('nome' => 'Maurício', 'email' => 'mauricio@studioclipagem.com.br'),
                            array('nome' => 'Neuza Wagner', 'email' => 'neuza.wagner@zurichairportbrasil.com'),
                            array('nome' => 'Michel Jung', 'email' => 'michel.jung@zurichairportbrasil.com'),
                            array('nome' => 'Renan Barcelos', 'email' => 'renan.barcelos@zurichairportbrasil.com'),
                            array('nome' => 'Ricardo Bresolin', 'email' => 'ricardo.bresolin@zurichairportbrasil.com'),
                            array('nome' => 'Ricardo Gesse', 'email' => 'ricardo.gesse@zurichairportbrasil.com'),
                            array('nome' => 'Simon Locher', 'email' => 'simon.locher@zurichairportbrasil.com'),
                            array('nome' => 'Tamara Oliveira', 'email' => 'tamara.oliveira@zurichairportbrasil.com'),
                            array('nome' => 'Tobias Markert', 'email' => 'tobias.markert@zurich-airport.lat'),
                            array('nome' => 'Tobias Markert', 'email' => 'tobias.markert@zurichairportbrasil.com'),
                            array('nome' => 'Vanessa Bezerra', 'email' => 'vanessa.bezerra@zurichairportbrasil.com'),
                            array('nome' => 'Wilson', 'email' => 'wilson.victer@zurichairportbrasil.com')
                        );
        }

        return view('boletim/lista-envio', compact('boletim', 'lista_email'));
    }

    public function enviarLista(Request $request)
    {
        $boletim = Boletim::where('id', $request->id)->first();
        $dados = $this->getDadosBoletim($request->id);   
        $logs = array();
        
        $data = array("dados"=> $dados, "boletim" => $boletim);
        $emails = $request->emails;

        for ($i=0; $i < count($emails); $i++) { 

            try{
                $nail_status = Mail::send('boletim.outlook', $data, function($message) use ($emails, $i) {
                $message->to($emails[$i])
                ->subject('Boletim de Clipagens');
                    $message->from('boletins@clipagens.com.br','Studio Clipagem');
                });
                $msg = "Email enviado com sucesso";
                $tipo = "success";
            }
            catch (\Swift_TransportException $e) {
                $msg = "Erro ao enviar para o endereço especificado";
                $tipo = "error";
            }

            $logs[] = array('email' => $emails[$i],'tipo' => $tipo, 'msg' => $msg);
        }

        $boletim->status_envio = 'enviado';
        $boletim->save();

        return view('boletim/resumo', compact('boletim', 'logs'));
    }
 
    public function getDadosBoletim($id)
    {
        $tipo = null;

        $boletim = Boletim::where('id', $id)->first();

        $conteudo = explode(",",$boletim->conteudo);

        if(!empty($boletim->tipo)) {
           
            $tipo = $boletim->tipo;

            switch ($tipo) {
                case 'tv': $idsTV[] = $boletim->conteudo; break;
                case 'radio': $idsRadio[] = $boletim->conteudo; break;
                case 'jornal': $idsJornal[] = $boletim->conteudo; break;
                case 'web': $idsWeb[] = $boletim->conteudo; break;
            }
    
        } else {

            $conteudo = str_replace('&quote;', '"', $boletim->conteudo);
            $conteudo = json_decode($conteudo, true);

            foreach($conteudo as $tipo => $value) {
                switch ($tipo) {
                    case 'tv': $idsTV[] = implode(',', $value); break;
                    case 'radio': $idsRadio[] = implode(',', $value); break;
                    case 'jornal': $idsJornal[] = implode(',', $value); break;
                    case 'web': $idsWeb[] = implode(',', $value); break;
                }
            }
        }

        $sql = array();

        if(!empty($idsTV)){

            $idsTVIn = implode(",",$idsTV);

            $sql[] = "( SELECT 
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
                        WHERE 
                            tv.id IN (".$idsTVIn.") 
                    )";
        }

        if(!empty($idsRadio)){

            $idsRadioIn = implode(",",$idsRadio);
            $sql[] = "(SELECT 
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
                            WHERE radio.id IN (".$idsRadioIn.") 
                )";
        }

        if(!empty($idsJornal)){
            $idsJornalIn = implode(",",$idsJornal);
            $sql[] = "(SELECT
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
                        WHERE jornal.id IN (".$idsJornalIn.") 
                )";
        }

        if(!empty($idsWeb)){
            $idsWebIn = implode(",",$idsWeb);
            $sql[] = "(SELECT 
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
                        WHERE web.id IN (".$idsWebIn.") 
                )";
        }
                
        $sql = implode(" UNION DISTINCT ",$sql);				
        $sql .= " ORDER BY ordem ASC, clipagem DESC, data DESC";
        
        $dados = DB::connection('mysql')->select($sql);

        foreach($dados as $key => $noticia){

            if($noticia->clipagem == 'web' or $noticia->clipagem == 'jornal'){

                $url = env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.jpg';
                $header_response = get_headers($url, 1);

                if(strpos( $header_response[0], "404" ) !== false){
                    $url = env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.jpeg';
                } 

                $dados[$key]->url = $url;    
            }       
        }

        return $dados;
    }
}