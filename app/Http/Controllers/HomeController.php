<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\FonteWeb;
use App\Models\JornalImpresso;
use App\Models\JornalWeb;
use App\Models\NoticiaRadio;
use App\Models\NoticiaWeb;
use App\Models\NoticiaTv;
use App\Models\ColetaWeb;
use App\Models\MonitoramentoExecucao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    private $client_id;
    private $data_atual;
    private $periodo_padrao;
    private $noticias = array();

    public function __construct()
    {
        $this->middleware('auth');

        $cliente = null;

        $clienteSession = ['id' => 1, 'nome' => 'Teste'];

        Session::put('cliente', session('cliente') ? session('cliente') : $clienteSession);

        $this->data_atual = session('data_atual');
        
        Session::put('url','home');

        $this->periodo_padrao = 7;
    }

    public function index()
    {
        $totais = array();
        $execucoes = array();
        $coletas = array();
        $top_sites = array();
        $total_sem_area = array();
        $sem_coleta = array();

        /*
        $totais = array('impresso' => JornalImpresso::where('dt_clipagem', $this->data_atual)->count(),
                        'web' => JornalWeb::where('dt_clipagem', $this->data_atual)->count(),
                        'radio' => 0,
                        'tv' => 0);
        */

        //$total_sem_area = JornalWeb::where('dt_clipagem', $this->data_atual)->where('categoria','')->count(); 
        //$coletas = ColetaWeb::whereBetween('created_at', [$this->data_atual.' 00:00:00', $this->data_atual.' 23:59:59'])->get();
        ////$execucoes = MonitoramentoExecucao::whereBetween('created_at', [$this->data_atual.' 00:00:00', $this->data_atual.' 23:59:59'])->orderBy('created_at', 'DESC')->take(5)->get();

        $top_sites = (new FonteWeb())->getTopColetas();
        $sem_coleta = (new FonteWeb())->getSemColetas();

        return view('index', compact('totais','coletas','total_sem_area','execucoes','top_sites','sem_coleta'));
    }

    public function atualizarData(Request $request)
    {
        $carbon = new Carbon();
        $data = ($request->data) ? $carbon->createFromFormat('d/m/Y', $request->data)->format('Y-m-d') : date("Y-m-d");

        Session::put('data_atual', $data);

        return redirect('/');
    }

    public function estatisticas()
    {
        $totais = array('impresso' => JornalImpresso::where('dt_clipagem', $this->data_atual)->count(),
                        'web' => JornalWeb::where('dt_clipagem', $this->data_atual)->count(),
                        'radio' => NoticiaRadio::where('dt_noticia', $this->data_atual)->count(),
                        'tv' => NoticiaTv::where('dt_noticia', $this->data_atual)->count());

        return response()->json($totais);
    }
}