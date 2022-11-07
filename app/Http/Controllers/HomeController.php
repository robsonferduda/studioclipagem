<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use App\Term;
use App\Client;
use App\Noticia;
use App\Configs;
use App\Hashtag;
use App\Media;
use App\FbPost;
use App\MediaFilteredVw;
use App\MediaRuleFilteredVw;
use App\MediaTwitter;
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
        return view('index');
    }
}