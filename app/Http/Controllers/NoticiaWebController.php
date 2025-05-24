<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Storage;
use App\Utils;
use Carbon\Carbon;
use App\Models\Cliente;
use Laracasts\Flash\Flash;
use App\Models\LogAcesso;
use App\Models\FonteWeb;
use App\Models\NoticiaWeb;
use App\Models\NoticiaCliente;
use App\Models\ConteudoNoticiaWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NoticiaWebController extends Controller
{
    private $data_atual;
    private $carbon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','jornal-web');
        $this->carbon = new Carbon();
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','noticias-web');

        $fontes = FonteWeb::orderBy('nome')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        $tipo_data = ($request->tipo_data) ? $request->tipo_data : 'data_noticia';
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $cliente_selecionado = ($request->cliente) ? $request->cliente : null;
        $fonte = ($request->fontes) ? $request->fontes : null;
        $termo = ($request->termo) ? $request->termo : null;

        $dados = NoticiaWeb::with('fonte')
                    ->whereHas('clientes', function($query) use ($fonte) {
                        $query->where('noticia_cliente.tipo_id', 2);
                    })
                    ->whereBetween($tipo_data, [$dt_inicial." 00:00:00", $dt_final." 23:59:59"])
                    ->orderBy('data_noticia')
                    ->paginate(10);

        return view('noticia-web/index', compact('dados','fontes','clientes','tipo_data','dt_inicial','dt_final','cliente_selecionado','fonte','termo'));
    }

    public function coletas(Request $request)
    {
        Session::put('sub-menu','noticia-web-coletas');

        $fontes = FonteWeb::orderBy('nome')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        $tipo_data = ($request->tipo_data) ? $request->tipo_data : 'data_noticia';
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $fonte = ($request->fontes) ? $request->fontes : null;
        $termo = ($request->termo) ? $request->termo : null;

        $dados = NoticiaWeb::with('fonte')
                    ->whereBetween($tipo_data, [$dt_inicial." 00:00:00", $dt_final." 23:59:59"])
                    ->orderBy('data_noticia')
                    ->orderBy('id_fonte')
                    ->paginate(10);

        return view('noticia-web/coletas', compact('dados','fontes','clientes','tipo_data','dt_inicial','dt_final','fonte','termo'));
    }

    public function monitoramento(Request $request)
    {
        Session::put('sub-menu','noticia-web-monitoramento');

        $fontes = FonteWeb::orderBy('nome')->get();
        $fontes = array();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        $dados = array();

        $tipo_data = $request->tipo_data;
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $cliente_selecionado = ($request->cliente) ? $request->cliente : null;
        $fonte = ($request->fontes) ? $request->fontes : null;
        $termo = ($request->termo) ? $request->termo : null;
        $fl_print = ($request->fl_print) ? true : false;

        if($request->fontes or Session::get('web_filtro_fonte')){
            if($request->fontes){
                $fonte = $request->fontes;
            }elseif(Session::get('web_filtro_fonte')){
                $fonte = Session::get('web_filtro_fonte');
            }else{
                $fonte = null;
            }
        }else{
            $fonte = null;
            Session::forget('web_filtro_fonte');
        }

        if($request->monitoramento or Session::get('web_monitoramento')){
            if($request->monitoramento){
                $monitoramento = $request->monitoramento;
            }elseif(Session::get('web_monitoramento')){
                $monitoramento = Session::get('web_monitoramento');
            }else{
                $monitoramento = null;
            }
        }else{
            $monitoramento = null;
            Session::forget('web_monitoramento');
        }

        if($request->isMethod('POST')){

            if($request->monitoramento){
                Session::put('web_monitoramento', $monitoramento);
            }else{
                Session::forget('web_monitoramento');
            }

            if($request->fontes){
                Session::put('web_filtro_fonte', $fonte);
            }else{
                Session::forget('web_filtro_fonte');
                $fonte = null;
            }
        }

        $dados = DB::table('noticia_cliente')
                    ->select('path_screenshot',
                            'fonte_web.id AS id_fonte',
                            'fonte_web.nome AS nome_fonte',
                            'noticias_web.data_noticia',
                            'noticias_web.data_insert',
                            'noticias_web.titulo_noticia',
                            'noticia_cliente.noticia_id',
                            'noticia_cliente.monitoramento_id',
                            'conteudo',
                            'expressao',
                            'nm_estado',
                            'fl_print',
                            'nm_cidade',
                            'clientes.nome AS nome_cliente')
                    ->join('clientes', 'clientes.id', '=', 'noticia_cliente.cliente_id')
                    ->join('noticias_web', function ($join) {
                        $join->on('noticias_web.id', '=', 'noticia_cliente.noticia_id')->where('tipo_id',2);
                    })
                    ->join('conteudo_noticia_web','conteudo_noticia_web.id_noticia_web','=','noticias_web.id')
                    ->join('fonte_web','fonte_web.id','=','noticias_web.id_fonte')
                    ->join('monitoramento', function($join) use($monitoramento){
                        $join->on('monitoramento.id','=','noticia_cliente.monitoramento_id')
                        ->when($monitoramento, function ($q) use ($monitoramento) {
                            return $q->where('monitoramento.id', $monitoramento);
                        });
                    })
                    ->leftJoin('estado','estado.cd_estado','=','fonte_web.cd_estado')
                    ->leftJoin('cidade','cidade.cd_cidade','=','fonte_web.cd_cidade')
                    ->when($termo, function ($q) use ($termo) {
                        return $q->where('texto_extraido', 'ILIKE', '%'.trim($termo).'%');
                    })
                    ->when($cliente_selecionado, function ($q) use ($cliente_selecionado) {
                        return $q->where('noticia_cliente.cliente_id', $cliente_selecionado);
                    })
                    ->when($fonte, function ($q) use ($fonte) {
                        return $q->whereIn('fonte_web.id', $fonte);
                    })
                    ->when($dt_inicial, function ($q) use ($dt_inicial, $dt_final) {
                        return $q->whereBetween('noticias_web.data_noticia', [$dt_inicial." 00:00:00", $dt_final." 23:59:59"]);
                    })
                    ->when($monitoramento, function ($q) use ($monitoramento) {
                        return $q->where('noticia_cliente.monitoramento_id', $monitoramento);
                    })
                    ->when($fl_print, function ($q) use ($fl_print) {
                        return $q->where('screenshot', $fl_print);
                    })
                    ->orderBy('fonte_web.id')
                    ->orderBy('data_noticia','DESC')
                    ->paginate(10);

        return view('noticia-web/monitoramento',compact('clientes','fontes','dados','tipo_data','dt_inicial','dt_final','cliente_selecionado','fonte','termo','monitoramento','fl_print'));
    }

    public function show($id)
    {
        Session::put('sub-menu','web-cadastrar');

        $noticia = NoticiaWeb::find($id);

        return view('noticia-web/detalhes',compact('noticia'));
    }

    public function create()
    {
        Session::put('sub-menu','web-cadastrar');

        $fontes = FonteWeb::orderBy('nome')->get();
        $noticia = null;

        return view('noticia-web/form',compact('fontes','noticia'));
    }

    public function edit($id)
    {
        Session::put('sub-menu','web-cadastrar');

        $fontes = FonteWeb::orderBy('nome')->get();
        $noticia = NoticiaWeb::find($id);

        return view('noticia-web/form',compact('fontes','noticia'));
    }

    public function copiaImagens()
    {

        $dados = NoticiaWeb::with('fonte')
            ->whereHas('clientes', function($query){
                $query->where('noticia_cliente.tipo_id', 2);
            })
            ->whereBetween('data_noticia', ["2025-05-23 00:00:00", date("Y-m-d H:i:s")])
            ->orderBy('data_noticia')
            ->get();

        foreach ($dados as $key => $noticia) {
            $arquivo = Storage::disk('s3')->get($noticia->path_screenshot);
            $filename = $noticia->id.".jpg";
            Storage::disk('web-img')->put($filename, $arquivo);

            $noticia->ds_caminho_img = $filename;
            $noticia->save();
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $request->merge(['fl_boletim' => true]);

            $noticia = NoticiaWeb::create($request->all());

            if($noticia){
                $request->merge(['id_noticia_web' => $noticia->id]);
                ConteudoNoticiaWeb::create($request->all());
            }

            DB::commit();

            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {

            dd($e);

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
            return redirect('noticia/web/novo')->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $noticia = NoticiaWeb::find($id);

        try {

           
            $noticia->update($request->all());

            if($noticia){
                $conteudo = ConteudoNoticiaWeb::where('id_noticia_web', $noticia->id)->first();
                $conteudo->conteudo = $request->conteudo;
                $conteudo->save();
            }

            DB::commit();

            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {

            dd($e);

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
            return redirect('noticia/web/'.$id.'/editar')->withInput();
        }
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

    public function prints(Request $request)
    {
        Session::put('sub-menu','fonte-web-prints');
        
        $dt_inicial = date("Y-m-d")." 00:00:00";
        $dt_final = date("Y-m-d")." 23:59:59";

        $prints = array();

        if($request->isMethod('POST')){
            $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00";
            $dt_final = $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59";
        }

        $sql = "SELECT t3.id, t3.nome, count(*) as total 
                FROM noticias_web t1 
                JOIN noticia_cliente t2 ON t2.noticia_id = t1.id 
                JOIN fonte_web t3 On t3.id = t1.id_fonte 
                WHERE t1.path_screenshot like 'ERROR'
                AND t1.created_at BETWEEN '$dt_inicial' AND '$dt_final'
                GROUP BY t3.id, t3.nome 
                ORDER BY total DESC";

        $resumo = DB::select($sql);

        $erros = NoticiaWeb::where('path_screenshot','ilike','ERROR')
                            ->whereBetween('created_at', [$dt_inicial, $dt_final])
                            ->get();

        return view('noticia-web/prints',compact('resumo','erros','dt_inicial','dt_final'));
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

    public function destacaConteudo($id_noticia, $id_monitoramento)
    {
        $sql = "SELECT ts_headline('simple', conteudo, to_tsquery('simple', t3.expressao), 'HighlightAll=true, StartSel=<mark>, StopSel=</mark>') as texto, t3.expressao 
                        FROM conteudo_noticia_web t1
                        JOIN noticia_cliente t2 ON t2.noticia_id = t1.id_noticia_web 
                        JOIN monitoramento t3 ON t3.id = t2.monitoramento_id 
                        WHERE t1.id_noticia_web = $id_noticia
                        AND t3.id = ".$id_monitoramento;
    
        $dados = DB::select($sql)[0];

        return response()->json($dados); 
    }

    public function valores()
    {
        $noticias = NoticiaCliente::where('tipo_id',2)->where('created_at', '>', '2025-02-10')->where('created_at', '<', '2025-03-01')->get();

        $sql = "SELECT t1.created_at, t2.nu_valor, t2.id 
                FROM noticia_cliente t1
                JOIN noticias_web t2 ON t2.id = t1.noticia_id 
                WHERE t1.created_at > '2025-02-01'
                AND t2.nu_valor IS null";

        $dados = DB::select($sql);

        foreach($dados as $dado){

            if($dado){

                $noticia = NoticiaWeb::find($dado->id);

                if($noticia){

                    $fonte = FonteWeb::find($noticia->id_fonte);

                    if($fonte){
                        $valor = $fonte->nu_valor;

                        if($valor){
                            $noticia->nu_valor = $valor;
                            $noticia->save();
                        }
                    }else{
                        $noticia->nu_valor = 0;
                        $noticia->save();
                    }
                }
            }

        }
    }
}