<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\EmissoraGravacao;
use App\Models\JornalImpresso;
use App\Models\JornalWeb;
use App\Models\Monitoramento;
use App\Models\FonteImpressa;
use App\Models\VideoEmissoraWeb;
use App\Models\NoticiaRadio;
use App\Models\NoticiaWeb;
use App\Models\FonteWeb;
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
    }

    public function index()
    {
        $dt_inicial = date("Y-m-d")." 00:00:00";
        $dt_final = date("Y-m-d")." 23:59:59";

        $totais = array();
        $coletas = array();
        $top_sites = array();
        $total_sem_area = array();
        $sem_coleta = array();

        $total_monitoramentos = MonitoramentoExecucao::whereBetween('created_at', [$dt_inicial, $dt_final])->count();
        $execucoes = MonitoramentoExecucao::whereBetween('created_at', [$dt_inicial, $dt_final])->orderBy('created_at','DESC')->take(5)->get();

        $total_coletas = FonteWeb::whereBetween('crawlead_at', [$dt_inicial, $dt_final])->count();
        $coletas = FonteWeb::orderBy('crawlead_at','DESC')->take(5)->get();

        return view('index', compact('totais','coletas','total_sem_area','execucoes','coletas','total_coletas','total_monitoramentos'));
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
        $dt_inicial = date("Y-m-d")." 00:00:00";
        $dt_final = date("Y-m-d")." 23:59:59";

        $totais = array('impresso' => ((new FonteImpressa)->getTotais($dt_inicial,$dt_final)) ? (new FonteImpressa)->getTotais($dt_inicial,$dt_final)[0]->total : 0,
                        'web' => JornalWeb::whereBetween('data_insert', [$dt_inicial, $dt_final])->count(),
                        'radio' => EmissoraGravacao::whereBetween('created_at', [$dt_inicial, $dt_final])->count(),
                        'tv' => VideoEmissoraWeb::whereBetween('created_at', [$dt_inicial, $dt_final])->count());

        return response()->json($totais);
    }

    public function php()
    {
        phpinfo(); 
    }
}