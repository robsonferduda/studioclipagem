<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use App\Boletim;
use App\Models\Cliente;
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
        $clientes = Cliente::orderBy('nome')->get();

        return view('boletim/index',compact('clientes'));
    }

    public function detalhes($id)
    {   
        $boletim = Boletim::where('id', $id)->first();
        $dados = $this->getDadosBoletim($id);        
    
        return view('boletim/detalhes', compact('boletim', 'dados'));
    }

    public function cadastrar()
    {   
        $clientes = Cliente::orderBy('nome')->get();

        return view('boletim/cadastrar', compact('clientes'));
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
        
      
    }
}