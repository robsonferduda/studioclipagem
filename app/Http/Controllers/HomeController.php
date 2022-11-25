<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\JornalImpresso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    private $client_id;
    private $periodo_padrao;
    private $noticias = array();

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
        $totais = array();

        $totais = array('impresso' => JornalImpresso::count(),
                        'web' => 0,
                        'radio' => 0,
                        'tv' => 0);

        return view('index', compact('totais'));
    }
}