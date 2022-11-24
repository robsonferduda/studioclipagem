<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\JornalImpresso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class JornalImpressoController extends Controller
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
        $dados = JornalImpresso::all();
        return view('jornal-impresso/index',compact('dados'));
    }

    public function upload()
    {
        return view('jornal-impresso/upload');
    }

    public function processamento()
    {
        return view('jornal-impresso/processamento');
    }
}