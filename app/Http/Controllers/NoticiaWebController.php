<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Storage;
use App\Utils;
use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Cliente;
use Laracasts\Flash\Flash;
use App\Models\LogAcesso;
use App\Models\Estado;
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

        $tipo_data = ($request->tipo_data) ? $request->tipo_data : 'data_insert';
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $cliente_selecionado = ($request->cliente) ? $request->cliente : null;
        $fonte = ($request->fontes) ? $request->fontes : null;
        $termo = ($request->termo) ? $request->termo : null;

        $dados = NoticiaWeb::with('fonte')
                    ->when($cliente_selecionado, function ($query) use ($cliente_selecionado) { 
                        return $query->whereHas('clientes', function($q) use ($cliente_selecionado) {
                            $q->where('noticia_cliente.cliente_id', $cliente_selecionado)->where('noticia_cliente.tipo_id', 2);
                        });
                    })
                    ->when($termo, function ($query) use ($termo) {
                        return $query->whereHas('conteudo', function($q) use ($termo) {
                            $q->where('conteudo', 'ILIKE', '%'.trim($termo).'%');
                        });
                    })
                    ->whereBetween($tipo_data, [$dt_inicial." 00:00:00", $dt_final." 23:59:59"])
                    ->where('fl_boletim', true)
                    ->orderBy('created_at', 'DESC')
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

        $estados = Estado::orderBy('nm_estado')->get();
        $cidades = array();
        $fontes = FonteWeb::orderBy('nome')->get();
        $tags = Tag::orderBy('nome')->get();
        $noticia = null;

        return view('noticia-web/form',compact('fontes','noticia','tags','estados','cidades'));
    }

    public function edit($id)
    {
        Session::put('sub-menu','web-cadastrar');

        $estados = Estado::orderBy('nm_estado')->get();
         $cidades = array();
        $fontes = FonteWeb::orderBy('nome')->get();
        $noticia = NoticiaWeb::find($id);
        $tags = Tag::orderBy('nome')->get();

        return view('noticia-web/form',compact('fontes','noticia','tags','estados','cidades'));
    }

    public function getValores($id)
    {
        $fonte = FonteWeb::find($id);

        $valor = (float) $fonte->nu_valor;

        return response()->json($valor);
    }

    public function copiaImagens()
    {

        $dados = NoticiaWeb::with('fonte')
            ->whereHas('clientes', function($query){
                $query->where('noticia_cliente.tipo_id', 2);
            })
            ->where('ds_caminho_img','=',null)
            ->orderBy('data_noticia')
            ->get();

        foreach ($dados as $key => $noticia) {

            if($noticia->path_screenshot AND $noticia->path_screenshot != 'ERROR'){

                $arquivo = Storage::disk('s3')->get($noticia->path_screenshot);
                $filename = $noticia->id.".jpg";
                Storage::disk('web-img')->put($filename, $arquivo);

                $noticia->ds_caminho_img = $filename;
                $noticia->save();
            }
        }
    }

    public function copiaImagemIndividual($id)
    {

        $noticia = NoticiaWeb::where('id', $id)->first();

        if($noticia->path_screenshot AND $noticia->path_screenshot != 'ERROR'){

            $arquivo = Storage::disk('s3')->get($noticia->path_screenshot);
            $filename = $noticia->id.".jpg";
            Storage::disk('web-img')->put($filename, $arquivo);

            $noticia->ds_caminho_img = $filename;
            $noticia->save();
        }

        return redirect()->back();
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $data_insert = ($request->data_insert) ? $this->carbon->createFromFormat('d/m/Y', $request->data_insert)->format('Y-m-d') : date("Y-m-d");
            $request->merge(['data_insert' => $data_insert]);

            $data_noticia = ($request->data_noticia) ? $this->carbon->createFromFormat('d/m/Y', $request->data_noticia)->format('Y-m-d') : date("Y-m-d");
            $request->merge(['data_noticia' => $data_noticia]);

            $ds_caminho_img = ($request->ds_caminho_img) ? ($request->ds_caminho_img) : '';
            $request->merge(['ds_caminho_img' => $ds_caminho_img]);

            $request->merge(['cd_usuario' => Auth::user()->id]);
            $request->merge(['fl_boletim' => true]);

            $noticia = NoticiaWeb::create($request->all());

            if($noticia){
                
                $request->merge(['id_noticia_web' => $noticia->id]);
                ConteudoNoticiaWeb::create($request->all());

                $tags = collect($request->tags)->mapWithKeys(function($tag){
                    return [$tag => ['tipo_id' => 2]];
                })->toArray();

                $noticia->tags()->sync($tags);

                $clientes = json_decode($request->clientes[0]);

                if($clientes){

                    for ($i=0; $i < count($clientes); $i++) { 
                        
                        $dados = array('tipo_id' => 2,
                                'noticia_id' => $noticia->id,
                                'cliente_id' => (int) $clientes[$i]->id_cliente,
                                'area' => (int) $clientes[$i]->id_area,
                                'sentimento' => (int) $clientes[$i]->id_sentimento);

                        $noticia_cliente = NoticiaCliente::create($dados);

                    }
                }
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

        switch ($request->btn_enviar) {

            case 'salvar':
                if ($retorno['flag']) {
                    Flash::success($retorno['msg']);
                    return redirect('noticia/web')->withInput();
                } else {
                    Flash::error($retorno['msg']);
                    return redirect('noticia/web/novo')->withInput();
                }
                break;

            case 'salvar_e_copiar':

                $nova_noticia = $noticia->replicate();
                $nova_noticia->save();

                $request->merge(['id_noticia_web' => $nova_noticia->id]);
                ConteudoNoticiaWeb::create($request->all());

                return redirect('noticia/web/'.$nova_noticia->id.'/editar');

            break;
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $noticia = NoticiaWeb::find($id);

        $data_insert = ($request->data_insert) ? $this->carbon->createFromFormat('d/m/Y', $request->data_insert)->format('Y-m-d') : date("Y-m-d");
        $request->merge(['data_insert' => $data_insert]);

        $data_noticia = ($request->data_noticia) ? $this->carbon->createFromFormat('d/m/Y', $request->data_noticia)->format('Y-m-d') : date("Y-m-d");
        $request->merge(['data_noticia' => $data_noticia]);

        $ds_caminho_img = ($request->ds_caminho_img) ? ($request->ds_caminho_img) : $noticia->ds_caminho_img;
        $request->merge(['ds_caminho_img' => $ds_caminho_img]);

        $request->merge(['cd_usuario' => Auth::user()->id]);

        try {

            $noticia->update($request->all());

            if($noticia){

                $conteudo = ConteudoNoticiaWeb::where('id_noticia_web', $noticia->id)->first();
                $conteudo->conteudo = $request->conteudo;
                $conteudo->save();

                $tags = collect($request->tags)->mapWithKeys(function($tag){
                    return [$tag => ['tipo_id' => 2]];
                })->toArray();

                $noticia->tags()->sync($tags);

                $clientes = json_decode($request->clientes[0]);

                if($clientes){

                    for ($i=0; $i < count($clientes); $i++) { 

                        $match = array('tipo_id' => 2,
                                    'noticia_id' => $noticia->id,
                                    'cliente_id' => (int) $clientes[$i]->id_cliente);
                            
                        $dados = array('area' => (int) $clientes[$i]->id_area,
                                       'sentimento' => (int) $clientes[$i]->id_sentimento);

                        $noticia_cliente = NoticiaCliente::updateOrCreate($match, $dados);
                    }
                }
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

        switch ($request->btn_enviar) {

            case 'salvar':
                if ($retorno['flag']) {
                    Flash::success($retorno['msg']);
                    return redirect('noticia/web')->withInput();
                } else {
                    Flash::error($retorno['msg']);
                    return redirect('noticia/web/'.$id.'/editar')->withInput();
                }
                break;

            case 'salvar_e_copiar':

                $nova_noticia = $noticia->replicate();
                $nova_noticia->save();

                $request->merge(['id_noticia_web' => $nova_noticia->id]);
                ConteudoNoticiaWeb::create($request->all());

                return redirect('noticia/web/'.$nova_noticia->id.'/editar');

            break;
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
        $cliente_selecionado = null;

        $prints = array();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        if($request->isMethod('POST')){
            $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00";
            $dt_final = $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59";
            $cliente_selecionado = ($request->cliente) ? $request->cliente : null;
        }

        $sql = "SELECT t3.id, t3.nome, count(*) as total 
                FROM noticias_web t1 
                JOIN noticia_cliente t2 ON t2.noticia_id = t1.id AND tipo_id = 2 
                JOIN fonte_web t3 On t3.id = t1.id_fonte 
                WHERE t1.path_screenshot like 'ERROR'
                AND t1.created_at BETWEEN '$dt_inicial' AND '$dt_final'";

        if($cliente_selecionado){
            $sql .= " AND t2.cliente_id = $cliente_selecionado";
        }

        $sql .= " GROUP BY t3.id, t3.nome ORDER BY total DESC";

        $resumo = DB::select($sql);

        $erros = NoticiaWeb::where('path_screenshot','ilike','ERROR')                            
                            ->whereHas('clientes', function($query) use ($cliente_selecionado) {
                                $query->where('noticia_cliente.tipo_id', 2)
                                ->when($cliente_selecionado, function ($query) use ($cliente_selecionado) { 
                                    $query->where('noticia_cliente.cliente_id', $cliente_selecionado);
                                });
                            })
                            ->whereBetween('created_at', [$dt_inicial, $dt_final])
                            ->orderBy('id_fonte')
                            ->get();

        return view('noticia-web/prints',compact('resumo','erros','dt_inicial','dt_final','clientes','cliente_selecionado'));
    }

    public function reprint($id)
    {
        $noticia = NoticiaWeb::find($id);
        $noticia->path_screenshot = null;
        $noticia->save();

        return redirect('noticia/web/prints');
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

    public function upload(Request $request)
    {
        $image = $request->file('picture');
        $fileInfo = $image->getClientOriginalName();
        $filesize = $image->getSize()/1024/1024;
        $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
        $extension = "jpeg";
        $file_name = time().'.'.$extension;
        $file_noticia = ($request->id) ? $request->id.'.'.$extension : $file_name;

       //dd($file_noticia);

        //$image->move(public_path('img/noticia-impressa/recorte'),$file_name);
        $image->move(public_path('img/noticia-web'),$file_noticia);

        if($request->id){
            $noticia = NoticiaImpresso::find($request->id);

            $noticia->ds_caminho_img = $file_noticia;
            $noticia->save();
        }

        return $file_noticia;
    }

    public function clientes($noticia)
    {
        $vinculos = array();

        $sql = "SELECT t1.cliente_id, 
                    nome, 
                    area as area_id, 
                    CASE 
                        WHEN(t3.descricao IS NOT NULL) THEN t3.descricao 
                        ELSE 'Nenhuma área selecionada'
                    END as area,
                    CASE
                        WHEN (sentimento = '-1') THEN 'Negativo' 
                        WHEN (sentimento = '0') THEN 'Neutro' 
                        WHEN (sentimento = '1') THEN 'Positivo' 
                        ELSE 'Nenhum sentimento selecionado'
                    END as sentimento,
                    sentimento AS id_sentimento
                FROM noticia_cliente t1
                JOIN clientes t2 ON t2.id = t1.cliente_id 
                LEFT JOIN area t3 On t3.id = t1.area 
                WHERE noticia_id = $noticia
                AND t1.tipo_id = 2";

        $vinculos = DB::select($sql);

        return response()->json($vinculos);
    }

    public function excluir($id)
    {
        $noticia = NoticiaWeb::find($id);

        if($noticia->delete())
            Flash::success('<i class="fa fa-check"></i> Notícia excluída com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect()->back();
    }
}