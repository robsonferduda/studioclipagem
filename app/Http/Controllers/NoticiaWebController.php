<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Storage;
use App\Utils;
use Carbon\Carbon;
use App\User;
use App\Models\SecaoWeb;
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

        $estados = Estado::orderBy('nm_estado')->get();
        $fontes = FonteWeb::orderBy('nome')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();
        $usuarios = User::whereHas('role', function($q){
                            return $q->whereIn('role_id', ['5','8']);
                        })
                        ->orderBy('name')
                        ->get();

        // Lista de filtros que você deseja manter em sessão
        $filtros = [
            'tipo_data',
            'dt_inicial',
            'dt_final',
            'cliente',
            'sentimento',
            'fonte',
            'cd_area',
            'termo',
            'usuario'
        ];

        // Salva cada filtro na sessão, se vier na requisição
        foreach ($filtros as $filtro) {
            if ($request->has($filtro)) {
                Session::put('web_filtro_' . $filtro, $request->input($filtro));
            }
        }        

        // Recupera os filtros da sessão (ou da request, se vier)
        $tipo_data = Session::get('web_filtro_tipo_data', $request->input('tipo_data', 'data_insert'));
        $dt_inicial = Session::get('web_filtro_dt_inicial', $request->input('dt_inicial', date('d/m/Y')));
        $dt_final = Session::get('web_filtro_dt_final', $request->input('dt_final', date('d/m/Y')));
        $cliente_selecionado = Session::get('web_filtro_cliente', $request->input('cliente'));
        $sentimento = Session::get('web_filtro_sentimento', $request->input('sentimento'));
        $fonte_selecionada = Session::get('web_filtro_fonte', $request->input('fonte'));
        $area_selecionada = Session::get('web_filtro_cd_area', $request->input('cd_area'));
        $termo = Session::get('web_filtro_termo', $request->input('termo'));
        $usuario = Session::get('web_filtro_usuario', $request->input('usuario'));
        $fl_retorno = $request->fl_retorno == true ? true : false;

        // Converta as datas para o formato do banco
        $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $dt_inicial)->format('Y-m-d');
        $dt_final = $this->carbon->createFromFormat('d/m/Y', $dt_final)->format('Y-m-d');

        if($fonte_selecionada){
            $fonte_web = FonteWeb::find($fonte_selecionada);
        }else{
            $fonte_web = null;
        }

        $dados = NoticiaWeb::with('fonte')
                    ->whereHas('clientes', function($q) {
                            $q->where('noticia_cliente.tipo_id', 2)
                              ->whereNull('noticia_cliente.deleted_at');
                    })
                    ->when($cliente_selecionado, function ($query) use ($cliente_selecionado) { 
                        return $query->whereHas('clientes', function($q) use ($cliente_selecionado) {
                            $q->where('noticia_cliente.cliente_id', $cliente_selecionado)
                              ->where('noticia_cliente.tipo_id', 2)
                              ->whereNull('noticia_cliente.deleted_at');
                        });
                    })
                    ->when($area_selecionada, function ($query) use ($area_selecionada) { 
                        return $query->whereHas('clientes', function($q) use ($area_selecionada) {
                            $q->where('noticia_cliente.area', $area_selecionada)
                              ->where('noticia_cliente.tipo_id', 2)
                              ->whereNull('noticia_cliente.deleted_at');
                        });
                    })
                    ->when($sentimento, function ($query) use ($sentimento) { 
                        return $query->whereHas('clientes', function($q) use ($sentimento) {

                            $valor_sentimento = null;

                            switch ($sentimento) {
                                case 'negativo':
                                    $valor_sentimento = '-1';
                                    break;

                                case 'neutro':
                                    $valor_sentimento = '0';
                                    break;

                                case 'positivo':
                                    $valor_sentimento = '1';
                                    break;
                                
                                default:
                                    $valor_sentimento = null;
                                    break;
                            }

                            $q->where('noticia_cliente.sentimento', $valor_sentimento)
                              ->where('noticia_cliente.tipo_id', 2)
                              ->whereNull('noticia_cliente.deleted_at');
                        });
                    })
                    ->when($termo, function ($query) use ($termo) {
                        return $query->whereHas('conteudo', function($q) use ($termo) {
                            $q->where('conteudo', 'ILIKE', '%'.trim($termo).'%')
                              ->orWhere('titulo_noticia', 'ILIKE', '%'.trim($termo).'%');
                        });
                    })
                    ->when($fonte_selecionada, function ($q) use ($fonte_selecionada) {
                        return $q->where('id_fonte', $fonte_selecionada);
                    })
                    ->when($fl_retorno, function ($q) use ($fl_retorno) {
                        return  $q->where('nu_valor','<=',0);    
                    })
                    ->when($usuario, function ($q) use ($usuario) {

                        if($usuario == "S"){
                            return $q->whereNull('cd_usuario');
                        }else{
                            return $q->where('cd_usuario', $usuario);
                        }
                    })
                    ->whereBetween($tipo_data, [$dt_inicial." 00:00:00", $dt_final." 23:59:59"])
                    ->where('fl_boletim', true)
                    ->orderBy('created_at', 'DESC')
                    ->paginate(50);

        return view('noticia-web/index', compact('dados','fl_retorno','fonte_web','fontes','clientes','tipo_data','dt_inicial','dt_final','cliente_selecionado','sentimento','fonte_selecionada','termo','usuarios','usuario','estados','area_selecionada'));
    }

    public function limparFiltrosWeb()
    {
        foreach (['tipo_data','dt_inicial','dt_final','cliente','sentimento','fonte','cd_area','termo','usuario'] as $filtro) {
            Session::forget('web_filtro_' . $filtro);
        }
        return redirect('noticia/web');
    }

    public function coletas(Request $request)
    {
        Session::put('sub-menu','noticia-web-coletas');

        $fontes = FonteWeb::orderBy('nome')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        $tipo_data = ($request->tipo_data) ? $request->tipo_data : 'data_insert';
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
        $noticia = null;

        try {

            $data_insert = ($request->data_insert) ? $this->carbon->createFromFormat('d/m/Y', $request->data_insert)->format('Y-m-d') : date("Y-m-d");
            $request->merge(['data_insert' => $data_insert]);

            $data_noticia = ($request->data_noticia) ? $this->carbon->createFromFormat('d/m/Y', $request->data_noticia)->format('Y-m-d') : date("Y-m-d");
            $request->merge(['data_noticia' => $data_noticia]);

            $ds_caminho_img = ($request->ds_caminho_img) ? ($request->ds_caminho_img) : '';
            $request->merge(['ds_caminho_img' => $ds_caminho_img]);

            $request->merge(['cd_usuario' => Auth::user()->id]);
            $request->merge(['fl_boletim' => true]);

            if(empty($request->sinopse) and !empty($request->conteudo)){
                $request->merge(['sinopse' => Utils::getSinopse($request->conteudo,300)]);
            }

            $request->merge(['nu_valor' => Utils::getValorReal($request->nu_valor)]);

            $noticia = NoticiaWeb::create($request->all());

            $localFile = public_path('img/noticia-web/' . $noticia->ds_caminho_img);

            if (!empty($noticia->ds_caminho_img) && file_exists($localFile)) {
                $s3Key = 'screenshot/screenshot_noticia_'.$noticia->id.'.jpg';

                // Define visibilidade pública (ajuste conforme sua policy)
                Storage::disk('s3')->put($s3Key, file_get_contents($localFile));

                // Atualiza o caminho no banco para apontar ao S3
                $noticia->path_screenshot = $s3Key;
                $noticia->save();
            }

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

                if($noticia == null){
                    Flash::error($retorno['msg']);
                    return redirect('noticia/web/novo')->withInput();
                }

                $nova_noticia = $noticia->replicate();
                $nova_noticia->save();

                foreach($noticia->clientes as $cliente) {
                    
                    $match = array('tipo_id' => 2,
                                    'noticia_id' => $nova_noticia->id,
                                    'cliente_id' => (int) $cliente->id);
                            
                    $dados = array('area' => (int) $cliente->pivot->area,
                                   'sentimento' => (int) $cliente->pivot->sentimento);

                    $noticia_cliente = NoticiaCliente::updateOrCreate($match, $dados);

                }

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

        $ds_caminho_img_noticia = ($noticia and $noticia->ds_caminho_img ) ? $noticia->ds_caminho_img : '';

        $ds_caminho_img = ($request->ds_caminho_img) ? ($request->ds_caminho_img) : $ds_caminho_img_noticia;
        $request->merge(['ds_caminho_img' => $ds_caminho_img]);

        $request->merge(['cd_usuario' => Auth::user()->id]);

        $request->merge(['nu_valor' => Utils::getValorReal($request->nu_valor)]);

        try {

            $noticia->update($request->all());

            $localFile = public_path('img/noticia-web/' . $noticia->ds_caminho_img);

            if (!empty($noticia->ds_caminho_img) && file_exists($localFile)) {
                $s3Key = 'screenshot/screenshot_noticia_'.$noticia->id.'.jpg';

                // Define visibilidade pública (ajuste conforme sua policy)
                Storage::disk('s3')->put($s3Key, file_get_contents($localFile));

                // Atualiza o caminho no banco para apontar ao S3
                $noticia->path_screenshot = $s3Key;
                $noticia->save();
            }

            if($noticia){

                $conteudo = ConteudoNoticiaWeb::where('id_noticia_web', $noticia->id)->first();

                if($conteudo == null){
                    $conteudo = new ConteudoNoticiaWeb();
                    $conteudo->id_noticia_web = $noticia->id;
                    $conteudo->save();
                }else{
                    $conteudo->conteudo = $request->conteudo;
                    $conteudo->save();
                }                

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

                foreach($noticia->clientes as $cliente) {
                    
                    $match = array('tipo_id' => 2,
                                    'noticia_id' => $nova_noticia->id,
                                    'cliente_id' => (int) $cliente->id);
                            
                    $dados = array('area' => (int) $cliente->pivot->area,
                                   'sentimento' => (int) $cliente->pivot->sentimento);

                    $noticia_cliente = NoticiaCliente::updateOrCreate($match, $dados);

                }

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
                AND t1.created_at BETWEEN '$dt_inicial' AND '$dt_final'
                AND t1.deleted_at IS NULL";

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

    public function printsRecuperar(Request $request)
    {
        $total = 0;
        $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00";
        $dt_final = $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59";

        $erros = NoticiaWeb::where('path_screenshot','ilike','ERROR')
                            ->whereBetween('created_at', [$dt_inicial, $dt_final])
                            ->get();

        foreach($erros as $erro){
            $erro->path_screenshot = null;
            $erro->save();

            $total += $total;
        }

        return redirect('noticia/web/prints');
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

        $image->move(public_path('img/noticia-web'),$file_noticia);

        if($request->id){
            $noticia = NoticiaImpresso::find($request->id);

            $noticia->ds_caminho_img = $file_noticia;
            $noticia->save();
        }

        return $file_noticia;
    }

    public function recorteUpload(Request $request)
    {
        $image = $request->file('picture');
        $fileInfo = $image->getClientOriginalName();
        $filesize = $image->getSize()/1024/1024;
        $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
        $extension = "jpeg";
        $file_name = time().'.'.$extension;
        $file_noticia = ($request->id) ? $request->id.'.'.$extension : $file_name;

        $image->move(public_path('img/noticia-web'),$file_noticia);

        if($request->id){
            $noticia = NoticiaWeb::find($request->id);

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

    //Busca e faz o download da imagem vinculada a notícia
    public function getImagem($id)
    {
        $noticia = NoticiaWeb::find($id);
        return response()->download(public_path('img/noticia-web/'.$noticia->ds_caminho_img));
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

    public function excluirLote(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma notícia selecionada para exclusão.'
                ], 400);
            }

            // Valida se todos os IDs são números
            $ids = array_filter($ids, 'is_numeric');
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'IDs inválidos fornecidos.'
                ], 400);
            }

            // Busca as notícias que existem
            $noticias = NoticiaWeb::whereIn('id', $ids)->get();
            
            if ($noticias->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma notícia encontrada para exclusão.'
                ], 404);
            }

            $deletedCount = 0;
            
            // Exclui cada notícia
            foreach ($noticias as $noticia) {
                if ($noticia->delete()) {
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => $deletedCount . ' notícia(s) excluída(s) com sucesso.',
                    'deleted_count' => $deletedCount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir as notícias selecionadas.'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function retorno()
    {
        Session::put('sub-menu','web-retorno');

        return view('noticia-web/retorno');
    }

    public function getPrint($id)
    {
        $noticia = NoticiaWeb::find($id);
        return redirect(Storage::disk('s3')->temporaryUrl($noticia->path_screenshot, '+30 minutes'));
    } 

    public function getPrintS3()
    {
        $sql = "SELECT t1.id, t1.path_screenshot, ds_caminho_img 
                FROM noticias_web t1
                JOIN noticia_cliente t2 ON t2.noticia_id = t1.id AND t2.tipo_id = 2
                WHERE data_noticia > '2025-07-01'
                AND t2.cliente_id IN(2,255)";

        $noticias = DB::select($sql);

        foreach ($noticias as $noticia) {
            if ($noticia->path_screenshot && $noticia->path_screenshot != 'ERROR') {
                try {
                    // Baixa o arquivo do S3
                    $arquivo = Storage::disk('s3')->get($noticia->path_screenshot);

                    // Define o nome do arquivo local
                    $filename = $noticia->id . ".jpg";

                    // Salva na pasta local de imagens
                    Storage::disk('web-img')->put($filename, $arquivo);

                    // Atualiza o campo ds_caminho_img
                    $n = NoticiaWeb::where('id', $noticia->id)->first();
                    $n->ds_caminho_img = $filename;
                    $n->save();

                    echo "Imagem da notícia {$noticia->id} baixada com sucesso.<br>";

                } catch (\Exception $e) {
                    // Log de erro ou tratamento
                    \Log::error("Erro ao baixar imagem S3 da notícia {$noticia->id}: " . $e->getMessage());
                    echo "Erro ao baixar imagem da notícia {$noticia->id}.<br>";
                }
            }else{
                echo "Notícia {$noticia->id} não possui imagem válida.<br>";
            }
        }
    }

    public function getSecoes($id_fonte)
    {
        $secoes = array();

        $secoes = SecaoWeb::orderBy('ds_sessao')->get();

        //$secoes = FonteImpressa::find($id_fonte)->secoes()->orderBy('ds_sessao')->get();
        
        return response()->json($secoes);
    }

    public function fontesPendentesAjax()
    {
        $fontes = FonteWeb::select('fonte_web.id', 'fonte_web.nome', 'fonte_web.nu_valor')
                        ->join('noticias_web', 'noticias_web.id_fonte', '=', 'fonte_web.id')
                        ->join('noticia_cliente', function($join){
                            $join->on('noticia_cliente.noticia_id', '=', 'noticias_web.id')
                                ->where('noticia_cliente.tipo_id', 2)
                                ->whereNull('noticia_cliente.deleted_at');
                        })
                        ->where(function($q){
                            $q->whereNull('noticias_web.nu_valor')
                            ->orWhere('noticias_web.nu_valor', 0);
                        })
                        ->where('fonte_web.id', '!=', 1) //Excluir fonte SEM FONTE
                        ->where('noticias_web.data_noticia', '>', '2025-05-01')
                        ->whereNull('noticias_web.deleted_at')
                        ->whereNotIn('noticia_cliente.cliente_id', [438,442])
                        ->groupBy('fonte_web.id', 'fonte_web.nome', 'fonte_web.nu_valor')
                        ->selectRaw('count(*) as total')
                        ->orderBy('total', 'desc')
                        ->paginate(10);

        return response()->json($fontes);
    }

    public function noticiasPendentesAjax()
    {
        $noticias = \DB::table('noticias_web as t1')
                        ->select(
                            't1.id',
                            't2.nome',
                            't1.id_fonte',
                            't2.nu_valor',
                            't1.nu_valor as valor_retorno',
                            't1.sinopse',
                            't1.data_noticia',
                            't1.titulo_noticia'
                        )
                        ->join('fonte_web as t2', 't2.id', '=', 't1.id_fonte')
                        ->join('noticia_cliente as t3', function($join){
                            $join->on('t3.noticia_id', '=', 't1.id')
                                ->where('t3.tipo_id', 2)
                                ->whereNull('t3.deleted_at');
                        })
                        ->where(function($q){
                            $q->whereNull('t1.nu_valor')
                            ->orWhere('t1.nu_valor', 0);
                        })
                        ->where('t1.id_fonte', '!=', 1) //Excluir fonte SEM FONTE
                        ->where('t1.data_noticia', '>', '2025-08-01')
                        ->whereNull('t1.deleted_at')
                        ->whereNotIn('t3.cliente_id', [438,442])
                        ->orderBy('t1.id_fonte')
                        ->distinct()
                        ->paginate(10);

        return response()->json($noticias);
    }

    public function calcularValorRetornoWeb()
    {
        $totalAtualizadas = 0;

        $sql = "SELECT DISTINCT t1.id, t2.nome, t1.id_fonte, t2.nu_valor, t1.nu_valor AS valor_retorno, sinopse, data_noticia, titulo_noticia 
                FROM noticias_web t1
                JOIN fonte_web t2 ON t2.id = t1.id_fonte 
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND tipo_id = 2 AND t3.deleted_at IS NULL
                WHERE (t1.nu_valor IS NULL OR t1.nu_valor = 0)
                AND data_noticia > '2025-01-01'
                AND t1.id_fonte != 1 --Excluir fonte SEM FONTE
                AND t1.deleted_at IS NULL
                AND t3.cliente_id NOT IN (438,442)
                ORDER BY id_fonte";

        $noticias = DB::select($sql);

        foreach ($noticias as $noticia) {

            $noticia = NoticiaWeb::find($noticia->id);
            $fonte = FonteWeb::find($noticia->id_fonte);

            if($fonte and $fonte->nu_valor){
                $noticia->nu_valor = $fonte->nu_valor;
                $noticia->save();
                $totalAtualizadas++;
            }
        }
            
        return response()->json($totalAtualizadas);
    }
}