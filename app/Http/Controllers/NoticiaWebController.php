<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Utils;
use Carbon\Carbon;
use App\Models\Cliente;
use Laracasts\Flash\Flash;
use App\Models\LogAcesso;
use App\Models\FonteWeb;
use App\Models\NoticiaWeb;
use App\Models\ConteudoNoticiaWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NoticiaWebController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','jornal-web');
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','jornal-web');

        $carbon = new Carbon();
        $dt_inicial = date('Y-m-d')." 00:00:00";
        $dt_final = date('Y-m-d')." 23:59:59";
        $termo = "";
        $fonte = 0;
        $cliente = null;
        $noticias = array();
        $fl_print = false;

        $clientes = Cliente::orderBy('nome')->get();
        $fontes = FonteWeb::orderBy('nome')->get();

        if($request->isMethod('POST')){

            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d")." 00:00:00";
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d")." 23:59:59";
            $fl_print = ($request->fl_print) ? true : false;

            $noticia = NoticiaWeb::query();

            $noticia->when($fl_print, function ($q) use ($fl_print) {
                return $q->where('screenshot', $fl_print);
            });

            $noticias = $noticia->whereBetween('data_insert', [$dt_inicial, $dt_final])->orderBy('id_fonte')->orderBy('titulo_noticia')->paginate(10);
        }

        if($request->isMethod('GET')){

            if($request->page){
                
                $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d")." 00:00:00";
                $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d")." 23:59:59";
                $fl_print = ($request->fl_print) ? true : false;
    
                $noticia = NoticiaWeb::query();
    
                $noticia->when($fl_print, function ($q) use ($fl_print) {
                    return $q->where('screenshot', $fl_print);
                });
    
                $noticias = $noticia->whereBetween('data_insert', [$dt_inicial, $dt_final])->orderBy('id_fonte')->orderBy('titulo_noticia')->paginate(10);

            }

        }
        
        /*
        if($request->isMethod('POST')){

            $carbon = new Carbon();
            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d")." 00:00:00";
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d")." 23:59:59";
            $expressao = $request->expressao;
            $fonte = $request->fonte;

            $filtro_fonte = '';
            $filtro_expressao = '';

            $jornais = NoticiaWeb::query();

            $jornais->when($fonte, function ($q) use ($fonte) {
                return $q->where('id_fonte', $fonte);
            });

            $jornais->when($termo, function ($q) use ($termo) {
                $q->whereHas('conteudo', function($q) use($termo){
                    return $q->whereRaw("conteudo  ~* '$termo' ");
                });
            });

            //$dados = $jornais->whereBetween('data_insert', [$dt_inicial, $dt_final])->orderBy('id_fonte')->orderBy('titulo_noticia')->get();

            if($fonte) $filtro_fonte = " AND nw.id_fonte = $fonte";
            if($expressao) $filtro_expressao = " AND n.conteudo ~* '$expressao'";

            $sql = "SELECT 
                        n.id AS id,
                        'conteudo_noticia_web' AS origem,
                        n.id_noticia_web::TEXT AS id_referencia,
                        n.created_at AS data_hora,
                        nw.titulo_noticia as titulo
                    FROM public.conteudo_noticia_web n
                    JOIN public.noticias_web nw ON nw.id = n.id_noticia_web 
                    WHERE n.created_at::DATE BETWEEN '$dt_inicial' AND '$dt_final'
                    $filtro_fonte
                    $filtro_expressao";

            $dados = DB::select($sql);

            return response()->json($dados);

        }

        if($request->isMethod('GET')){

            Session::forget('busca_termo');
            Session::forget('burca_fonte');

            if($request->dt_inicial){

                $carbon = new Carbon();
                $dt_inicial = ($request->dt_inicial) ? $request->dt_inicial : date("Y-m-d")." 00:00:00";
                $dt_final = ($request->dt_final) ? $request->dt_final : date("Y-m-d")." 23:59:59";

                $dados = NoticiaWeb::whereBetween('data_insert', [$dt_inicial, $dt_final])->orderBy('data_noticia','DESC')->orderBy('id_fonte')->paginate(10);

            }

        }

        $total_noticias = count($dados);
        */

        return view('noticia-web/index',compact('fontes','noticias','dt_inicial','dt_final','termo','fonte','clientes','cliente','fl_print'));
    }

    public function dashboard()
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

        $top_sites = (new FonteWeb())->getTopColetas(10);
        $sem_coleta = (new FonteWeb())->getSemColetas(10);

        return view('noticia-web/dashboard', compact('totais','coletas','total_sem_area','execucoes','top_sites','sem_coleta'));
    }

    public function detalhes($id)
    {
        $noticia = NoticiaWeb::find($id);

        $acesso = array('tipo' => 'web',
                        'usuario' => Auth::user()->id,
                        'id_noticia' => $noticia->id);

        LogAcesso::create($acesso);

        return view('noticia-web/detalhes',compact('noticia'));
    }

    public function fontes()
    {
        Session::put('sub-menu','fonte-web');

        $fontes = FonteWeb::all();
        return view('jornal-web/fontes',compact('fontes'));
    }

    public function cadastrar()
    {
        Session::put('sub-menu','web-cadastrar');

        $fontes = FonteWeb::orderBy('nome')->get();
        return view('noticia-web/cadastrar',compact('fontes'));
    }

    public function listar()
    {
        $sites = FonteWeb::all();
        return view('jornal-web/listar',compact('sites'));
    }   

    public function estatisticas()
    {
        Session::put('sub-menu','web-estatisticas');

        $total_sites = FonteWeb::count();
        $ultima_atualizacao_web = FonteWeb::max('created_at');
        $ultima_atualizacao_noticia = JornalWeb::max('created_at');
        $fontes = FonteWeb::orderBy('nome')->get();
        $data_final = date("Y-m-d");
        $data_inicial = Carbon::now()->subDays(7)->format('Y-m-d');

        $total_noticias = JornalWeb::whereBetween('created_at', [$data_inicial.' 00:00:00', $data_final.' 23:59:59'])->count();

        return view('jornal-web/dashboard',compact('fontes','total_sites', 'total_noticias','ultima_atualizacao_web','ultima_atualizacao_noticia'));
    }

    public function getEstatisticas($id)
    {
        $noticia = NoticiaWeb::find($id);
        return view('noticia-web/estatisticas',compact('noticia'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $noticia = NoticiaWeb::create($request->all());

            if($noticia){
                $request->merge(['id_noticia_web' => $noticia->id]);
                ConteudoNoticiaWeb::create($request->all());
            }

            DB::commit();
            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {
            DB::rollback();
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('noticia/web')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('noticia/web/cadastrar')->withInput();
        }
    }
}