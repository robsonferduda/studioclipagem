<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Cliente;
use App\Models\JornalWeb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClienteController extends Controller
{
    private $client_id;
    private $periodo_padrao;

    public function __construct()
    {
        $this->middleware('auth');

        $cliente = null;

        $clienteSession = ['id' => 1, 'nome' => 'Teste'];

        Session::put('cliente', session('cliente') ? session('cliente') : $clienteSession);

        $this->client_id = session('cliente')['id'];
        
        Session::put('url','home');

        $this->periodo_padrao = 7;
    }

    public function index()
    {
        $clientes = Cliente::all(); //Lista todos os clientes
        
        $pessoa = Cliente::find(4)->pessoa; //Lista a pessoa

        $nome = Cliente::find(4)->pessoa->nome; //Mostra nome da pessoa

        $emails = Cliente::find(4)->pessoa->enderecoEletronico; //Mostra os endereços da pessoa

        return view('cliente/index',compact('clientes'));
    }
}