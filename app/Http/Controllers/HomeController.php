<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Events\RelatorioEvent;
use App\Models\Emissora;
use App\Models\EmissoraGravacao;
use App\Models\JornalImpresso;
use App\Models\JornalWeb;
use App\Models\Monitoramento;
use App\Models\FonteImpressa;
use App\Models\VideoEmissoraWeb;
use App\Models\NoticiaRadio;
use App\Models\NoticiaImpresso;
use App\Models\NoticiaTv;
use App\Models\NoticiaWeb;
use App\Models\FonteWeb;
use App\Models\ColetaWeb;
use App\Models\MonitoramentoExecucao;
use App\Models\ProgramaEmissoraWeb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    private $cliente_id;
    private $data_atual;
    private $periodo_padrao;
    private $noticias = array();

    public function __construct()
    {
        $this->middleware('auth',['except' => ['site']]);
        $this->data_atual = session('data_atual');
        
        Session::put('url','home');
    }

    public function site()
    {
        return view('site');
    }

    public function evento()
    {
        event(new RelatorioEvent('hello world'));
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

        $programas_erros = ProgramaEmissoraWeb::where('id_situacao',2)->count();
        $programas_radio_erros = Emissora::where('id_situacao',2)->count();

        return view('index', compact('totais','coletas','total_sem_area','execucoes','coletas','total_coletas','total_monitoramentos','programas_erros','programas_radio_erros'));
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

    public function graficoMidias(Request $request)
    {
        $periodo = $request->get('periodo', 7);

        if ($periodo == 'mes_anterior') {
            $inicio = now()->subMonth()->startOfMonth();
            $fim = now()->subMonth()->endOfMonth();
        } else {
            $inicio = now()->subDays((int)$periodo - 1)->startOfDay();
            $fim = now()->endOfDay();
        }

        $user = auth()->user();
        $cliente = Auth::user()->client_id;
     
        // Monta os dias do período
        $labels = [];
        $dataWeb = [];
        $dataJornal = [];
        $dataRadio = [];
        $dataTv = [];

        $dias = \Carbon\CarbonPeriod::create($inicio, $fim);

        foreach ($dias as $dia) {

            $labels[] = $dia->format('d/m');
            $data = $dia->format('Y-m-d');

            $dataWeb[] = NoticiaWeb::whereHas('clientes', function($q) use($cliente) {
                            $q->where('noticia_cliente.cliente_id', $cliente)->where('noticia_cliente.tipo_id', 2);
                        })
                        ->whereBetween('created_at', [$data." 00:00:00", $data." 23:59:59"])
                        ->where('fl_boletim', true)
                        ->count();

            $dataJornal[] = NoticiaImpresso::whereHas('clientes', function($q) use($cliente) {
                            $q->where('noticia_cliente.cliente_id', $cliente)->where('noticia_cliente.tipo_id', 1);
                        })
                        ->whereBetween('created_at', [$data." 00:00:00", $data." 23:59:59"])
                        ->count();

            $dataRadio[] = NoticiaRadio::whereHas('clientes', function($q) use($cliente) {
                            $q->where('noticia_cliente.cliente_id', $cliente)->where('noticia_cliente.tipo_id', 3);
                        })
                        ->whereBetween('created_at', [$data." 00:00:00", $data." 23:59:59"])
                        ->count();

            $dataTv[] = NoticiaTv::whereHas('clientes', function($q) use($cliente) {
                            $q->where('noticia_cliente.cliente_id', $cliente)->where('noticia_cliente.tipo_id', 4);
                        })
                        ->whereBetween('created_at', [$data." 00:00:00", $data." 23:59:59"])
                        ->count();
        }

        return response()->json([
            'labels' => $labels,
            'data' => [
                'web' => $dataWeb,
                'jornal' => $dataJornal,
                'radio' => $dataRadio,
                'tv' => $dataTv,
            ],
        ]);
    }
}