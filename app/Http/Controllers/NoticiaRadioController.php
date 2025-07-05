<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use DateTime;
use DateInterval;
use DatePeriod;
use Storage;
use App\Models\Area;
use App\Models\Cliente;
use App\Models\Cidade;
use App\Models\EmissoraGravacao;
use App\Utils;
use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Emissora;
use App\Models\NoticiaCliente;
use App\Models\Estado;
use App\Models\NoticiaRadio;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class NoticiaRadioController extends Controller
{
    private $data_atual;
    private $carbon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        $this->carbon = new Carbon();
        Session::put('url','radio');
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','noticia-radio');

        $emissora = Emissora::orderBy('nome_emissora')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        $tipo_data = ($request->tipo_data) ? $request->tipo_data : 'dt_cadastro';
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $cliente_selecionado = ($request->cliente) ? $request->cliente : null;
        $fonte = ($request->fontes) ? $request->fontes : null;
        $termo = ($request->termo) ? $request->termo : null;

        $dados = NoticiaRadio::with('emissora')
                    ->when($cliente_selecionado, function ($query) use ($cliente_selecionado) { 
                        return $query->whereHas('clientes', function($q) use ($cliente_selecionado) {
                            $q->where('noticia_cliente.cliente_id', $cliente_selecionado)->where('noticia_cliente.tipo_id', 3);
                        });
                    })
                    ->when($termo, function ($q) use ($termo) {
                        return $q->where('sinopse', 'ILIKE', '%'.trim($termo).'%');
                    })
                    ->whereBetween($tipo_data, [$dt_inicial." 00:00:00", $dt_final." 23:59:59"])
                    ->orderBy('created_at','DESC') 
                    ->paginate(50);

        return view('noticia-radio/index', compact('dados','emissora','clientes','tipo_data','dt_inicial','dt_final','cliente_selecionado','fonte','termo'));
    }

    public function monitoramento(Request $request)
    {
        Session::put('sub-menu','radio-monitoramento');

        $emissoras = Emissora::orderBy('nome_emissora')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        $tipo_data = $request->tipo_data;
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $cliente_selecionado = ($request->cliente) ? $request->cliente : null;
        $emissora_search = ($request->emissora) ? $request->emissora : null;
        $programa_search = ($request->programa) ? $request->programa : null;
        $termo = ($request->termo) ? $request->termo : null;
        
        if($request->fontes or Session::get('radio_filtro_fonte')){
            if($request->fontes){
                $fonte = $request->fontes;
            }elseif(Session::get('radio_filtro_fonte')){
                $fonte = Session::get('radio_filtro_fonte');
            }else{
                $fonte = null;
            }
        }else{
            $fonte = null;
            Session::forget('radio_filtro_fonte');
        }

        if($request->monitoramento or Session::get('radio_monitoramento')){
            if($request->monitoramento){
                $monitoramento = $request->monitoramento;
            }elseif(Session::get('radio_monitoramento')){
                $monitoramento = Session::get('radio_monitoramento');
            }else{
                $monitoramento = null;
            }
        }else{
            $monitoramento = null;
            Session::forget('radio_monitoramento');
        }

        if($request->isMethod('POST')){

            if($request->monitoramento){
                Session::put('radio_monitoramento', $monitoramento);
            }else{
                Session::forget('radio_monitoramento');
            }
        
            if($request->fontes){
                Session::put('radio_filtro_fonte', $fonte);
            }else{
                Session::forget('radio_filtro_fonte');
                $fonte = null;
            }
        }

        $dados = DB::table('noticia_cliente')
                    ->select('noticia_cliente.noticia_id AS id_audio',
                            'noticia_cliente.noticia_id',
                            'noticia_cliente.monitoramento_id',
                            'emissora_radio.id AS id_fonte',
                            'nome_emissora AS nome_fonte',
                            'clientes.nome AS nome_cliente',
                            'nm_estado',
                            'nm_cidade',
                            'data_hora_inicio',
                            'data_hora_fim',
                            'transcricao',
                            'expressao',
                            'id_noticia_gerada',
                            'path_s3')
                    ->join('clientes', 'clientes.id', '=', 'noticia_cliente.cliente_id')
                    ->join('gravacao_emissora_radio', function ($join) {
                        $join->on('gravacao_emissora_radio.id', '=', 'noticia_cliente.noticia_id')->where('tipo_id', 3);
                    })
                    ->join('emissora_radio','emissora_radio.id','=','gravacao_emissora_radio.id_emissora')
                    ->join('monitoramento', function($join) use($monitoramento){
                        $join->on('monitoramento.id','=','noticia_cliente.monitoramento_id')
                        ->when($monitoramento, function ($q) use ($monitoramento) {
                            return $q->where('monitoramento.id', $monitoramento);
                        });
                    })
                    ->leftJoin('estado','estado.cd_estado','=','emissora_radio.cd_estado')
                    ->leftJoin('cidade','cidade.cd_cidade','=','emissora_radio.cd_cidade')
                    ->when($termo, function ($q) use ($termo) {
                        return $q->where('transcricao', 'ILIKE', '%'.trim($termo).'%');
                    })
                    ->when($cliente_selecionado, function ($q) use ($cliente_selecionado) {
                        return $q->where('noticia_cliente.cliente_id', $cliente_selecionado);
                    })
                    ->when($fonte, function ($q) use ($fonte) {
                        return $q->whereIn('emissora_radio.id', $fonte);
                    })
                    ->when($dt_inicial, function ($q) use ($dt_inicial, $dt_final) {
                        return $q->whereBetween('gravacao_emissora_radio.data_hora_inicio', [$dt_inicial." 00:00:00", $dt_final." 23:59:59"]);
                    })
                    ->when($monitoramento, function ($q) use ($monitoramento) {
                        return $q->where('noticia_cliente.monitoramento_id', $monitoramento);
                    })
                    ->orderBy('gravacao_emissora_radio.data_hora_inicio','DESC')
                    ->paginate(10);

        return view('noticia-radio/monitoramento', compact('clientes','emissoras','dados','tipo_data','dt_inicial','dt_final','cliente_selecionado','fonte','termo','monitoramento','emissora_search','programa_search'));
    }

    public function dashboard()
    {
        Session::put('sub-menu','radio-dashboard');

        $data_final = date("Y-m-d");
        $data_inicial = Carbon::now()->subDays(7)->format('Y-m-d');

        $total_noticia_radio = EmissoraGravacao::whereBetween('created_at', [$this->data_atual.' 00:00:00', $this->data_atual.' 23:59:59'])->count();
        $ultima_atualizacao = EmissoraGravacao::max('created_at');

        $total_emissora_radio = Emissora::count();
        $ultima_atualizacao_radio = Emissora::max('created_at');

        $total_emissora_gravando = Emissora::where('gravar', true)->count();
        $ultima_atualizacao_gravando = Emissora::max('updated_at');

        return view('radio/dashboard', compact('total_noticia_radio', 'total_emissora_radio', 'ultima_atualizacao','ultima_atualizacao_radio','data_final','data_inicial','total_emissora_gravando','ultima_atualizacao_gravando'));
    }

    public function extrair($monitoramento, $id)
    {
        $conteudo = EmissoraGravacao::find($id);

        //Array de dados para inserção
        $dados = array("emissora_id" => $conteudo->id_emissora,
                       "horario" => $conteudo->data_hora_inicio,
                       "sinopse" => $conteudo->transcricao,
                       "cd_usuario" => Auth::user()->id,
                       "dt_cadastro" => $conteudo->data_hora_inicio,
                       "dt_clipagem" => $conteudo->data_hora_inicio);

        $noticia = NoticiaRadio::create($dados);

        //Inserção de arquivo
        $arquivo = Storage::disk('s3')->get($conteudo->path_s3);
        $filename = $noticia->id.".mp3";

        Storage::disk('radio-audio')->put($filename, $arquivo);

        $noticia->ds_caminho_audio = $filename;
        $noticia->save();

        //Relacionamento de clientes
        $vinculo = NoticiaCliente::where('noticia_id', $id)->where('monitoramento_id', $monitoramento)->where('tipo_id', 3)->first();

                $dados = array('tipo_id' => 3,
                                'noticia_id' => $noticia->id,
                                'cliente_id' => (int) $vinculo->cliente_id,
                                'area' => null,
                                'sentimento' => 0);

                    $match = array('tipo_id' => 3,
                                'noticia_id' => $noticia->id,
                                'cliente_id' => (int) $vinculo->cliente_id);
                        
                    $dados = array('area' => null,
                                   'monitoramento_id' => $monitoramento,
                                   'id_noticia_origem' => $conteudo->id,
                                   'id_noticia_gerada' => $noticia->id,
                                   'sentimento' => 0);

                    $noticia_cliente = NoticiaCliente::updateOrCreate($match, $dados);


        return redirect('noticia-radio/'.$noticia->id.'/editar');
    }

    public function cadastrar()
    {
        Session::put('sub-menu','radio-cadastrar');        

        $dados = new NoticiaRadio();
        $cidades = [];
        $noticia = null;

        $estados = Estado::orderBy('nm_estado')->get();
        $tags = Tag::orderBy('nome')->get();
        $emissoras = Emissora::orderBy('nome_emissora')->get();

        return view('noticia-radio/form', compact('noticia','estados', 'cidades', 'tags','emissoras'));
    }

    public function editar(int $id, int $cliente = null)
    {
        $noticia = NoticiaRadio::find($id);
        $cliente = NoticiaCliente::where('noticia_id', $id)->where('cliente_id', $cliente)->first();
        
        $estados = Estado::orderBy('nm_estado')->get();
        $cidades = Cidade::where(['cd_estado' => $noticia->cd_estado])->orderBy('nm_cidade')->get();
      
        $tags = Tag::orderBy('nome')->get();
        $emissoras = Emissora::orderBy('nome_emissora')->get();

        return view('noticia-radio/form', compact('noticia','cliente', 'estados', 'cidades','tags','emissoras'));
    }

    public function store(Request $request)
    {
        $dt_cadastro = ($request->dt_cadastro) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_cadastro)->format('Y-m-d') : date("Y-m-d");
        $request->merge(['dt_cadastro' => $dt_cadastro]);

        $dt_clipagem = ($request->dt_clipagem) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_clipagem)->format('Y-m-d') : date("Y-m-d");
        $request->merge(['dt_clipagem' => $dt_clipagem]);

        $request->merge(['cd_usuario' => Auth::user()->id]);

        try {

            $noticia = NoticiaRadio::create($request->all());
           
            if($noticia)
            {
        
                $tags = collect($request->tags)->mapWithKeys(function($tag){
                    return [$tag => ['tipo_id' => 3]];
                })->toArray();

                $noticia->tags()->sync($tags);

                $clientes = json_decode($request->clientes[0]);

                if($clientes){

                    for ($i=0; $i < count($clientes); $i++) { 
                        
                        $dados = array('tipo_id' => 3,
                                'noticia_id' => $noticia->id,
                                'cliente_id' => (int) $clientes[$i]->id_cliente,
                                'area' => (int) ($clientes[$i]->id_area) ? $clientes[$i]->id_area : null,
                                'sentimento' => (int) $clientes[$i]->id_sentimento);

                        $noticia_cliente = NoticiaCliente::create($dados);

                    }
                }
            }

            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados inseridos com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            DB::rollback();

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {

            $retorno = array('flag' => false,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        switch ($request->btn_enviar) {
            case 'salvar':
                if ($retorno['flag']) {
                    Flash::success($retorno['msg']);
                    return redirect('noticias/radio')->withInput();
                } else {
                    Flash::error($retorno['msg']);
                    return redirect('radio/noticias/cadastrar')->withInput();
                }
                break;

            case 'salvar_e_copiar':

                $dados = $noticia;
                $estados = Estado::orderBy('nm_estado')->get();
                $cidades = Cidade::where(['cd_estado' => $dados->cd_estado])->orderBy('nm_cidade')->get();
                $areas = Area::select('area.id', 'area.descricao')
                    ->join('area_cliente', 'area_cliente.area_id', '=', 'area.id')
                    ->where(['cliente_id' => $dados->cliente_id,])
                    ->where(['ativo' => true])
                    ->orderBy('area.descricao')
                    ->get();

                $tags = Tag::orderBy('nome')->get();
                
                return view('noticia-radio/form', compact('dados', 'estados', 'cidades', 'areas','tags'));
                break;
        }
    }

    public function update(Request $request, int $id)
    {
        $noticia = NoticiaRadio::find($id);

        try {

            $dt_cadastro = ($request->dt_cadastro) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_cadastro)->format('Y-m-d') : date("Y-m-d");
            $request->merge(['dt_cadastro' => $dt_cadastro]);
    
            $dt_clipagem = ($request->dt_clipagem) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_clipagem)->format('Y-m-d') : date("Y-m-d");
            $request->merge(['dt_clipagem' => $dt_clipagem]);

            $request->merge(['cd_usuario' => Auth::user()->id]);
            
            $noticia->update($request->all());

            $tags = collect($request->tags)->mapWithKeys(function($tag){
                return [$tag => ['tipo_id' => 3]];
            })->toArray();

            $noticia->tags()->sync($tags);

            //Atualização de clientes
            $clientes = json_decode($request->clientes[0]);

            if($clientes){
                for ($i=0; $i < count($clientes); $i++) { 

                    $match = array('tipo_id' => 3,
                                'noticia_id' => $noticia->id,
                                'cliente_id' => (int) $clientes[$i]->id_cliente);
                        
                    $dados = array('area' => (int) $clientes[$i]->id_area,
                                   'sentimento' => (int) $clientes[$i]->id_sentimento);

                    $noticia_cliente = NoticiaCliente::updateOrCreate($match, $dados);
                }
            }

            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados atualizados com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            dd($e);

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {

            dd($e);

            $retorno = array('flag' => false,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('noticias/radio')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('noticia-radio/'.$id.'/editar')->withInput();
        }
    }

    public function remover(int $id, $cliente = null)
    {
        $noticia = NoticiaRadio::find($id);

        if($cliente){

            $noticia_cliente = NoticiaCliente::where('noticia_id', $id)->where('cliente_id', $cliente)->first();

            if($noticia_cliente->delete()){

                if(count($noticia->clientes) == 0){

                    if($noticia->delete())
                        Flash::success('<i class="fa fa-check"></i> Notícia <strong>'.$noticia->titulo.'</strong> excluída com sucesso');
                }
            }
            else
                Flash::error("Erro ao excluir o registro");

        }else{

            if($noticia->delete())
                Flash::success('<i class="fa fa-check"></i> Notícia <strong>'.$noticia->titulo.'</strong> excluída com sucesso');
            else
                Flash::error("Erro ao excluir o registro");
        }

        return redirect('radios')->withInput();
    }

    public function download(int $id)
    {
        $noticia = NoticiaRadio::find($id);

        $path = $this->getBasePath() . $noticia->arquivo;
        if (file_exists($path)) {
            return response()->download($path);
        }
    }

    public function upload(Request $request)
    {
        $audio = $request->file('audio');
        $fileInfo = $audio->getClientOriginalName();
        $filesize = $audio->getSize()/1024/1024;
        $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
        $extension = "mp3";
        $file_name = time().'.'.$extension;
        $file_noticia = ($request->id) ? $request->id.'.'.$extension : $file_name;

        $audio_mp3 = new \wapmorgan\Mp3Info\Mp3Info($audio, true);
        $duracao = gmdate("H:i:s", $audio_mp3->duration);

        $audio->move(public_path('audio/noticia-radio'),$file_noticia);

        if($request->id){

            $noticia = NoticiaRadio::find($request->id);

            $noticia->ds_caminho_audio = $file_noticia;
            $noticia->save();
        }

        $retorno = array('duracao' => $duracao, 
                         'arquivo' => $file_noticia);

        return $retorno;
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
                AND t1.tipo_id = 3";

        $vinculos = DB::select($sql);

        return response()->json($vinculos);
    }

    public function getEstatisticas()
    {
        $dados = array();
        $data_final = date("Y-m-d")." 23:59:59";
        $data_inicial = Carbon::now()->subDays(7)->format('Y-m-d')." 00:00:00";;

        $totais = (new NoticiaRadio())->getTotais($data_inicial, $data_final);

        for ($i=0; $i < count($totais); $i++) { 
            $dados['label'][] = date('d/m/Y', strtotime($totais[$i]->dt_noticia));
            $dados['totais'][] = $totais[$i]->total;
        }

        return response()->json($dados);
    }

    public function getBasePath()
    {
        return public_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;
    }

    public function estatisticas()
    {
        $dados = array();
        
        $dt_inicial = Carbon::now()->subDays(7);
        $dt_final = Carbon::now()->addDays(1);

        $begin = new DateTime($dt_inicial);
        $end = new DateTime($dt_final);
        $interval = DateInterval::createFromDateString('1 day');

        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $dados['label'][] =  $dt->format("d/m/Y");
            $dados['totais'][] = count(EmissoraGravacao::whereBetween('created_at', [$dt->format("Y-m-d")." 00:00:00", $dt->format("Y-m-d")." 23:59:59"])->get());
        }

        return response()->json($dados);
    }

    public function destacaConteudo($id_noticia, $id_monitoramento)
    {
        $sql = "SELECT ts_headline('portuguese', t1.transcricao, to_tsquery('portuguese', t3.expressao), 'HighlightAll=true, StartSel=<mark>, StopSel=</mark>') as texto, t3.expressao 
                        FROM gravacao_emissora_radio t1
                        JOIN noticia_cliente t2 ON t2.noticia_id = t1.id 
                        JOIN monitoramento t3 ON t3.id = t2.monitoramento_id  
                        WHERE t1.id = $id_noticia
                        AND t3.id = ".$id_monitoramento;
    
        $dados = DB::select($sql)[0];

        return response()->json($dados); 
    }

    public function getDadosAudio($id, $monitoramento)
    {
        $sql = "SELECT t2.id_emissora 
                FROM noticia_cliente t1
                JOIN gravacao_emissora_radio t2 ON t2.id = t1.noticia_id
                WHERE t1.monitoramento_id = $monitoramento
                AND t1.noticia_id = $id";

        $id_emissora = DB::select($sql)[0]->id_emissora;

        $sql_prev = "SELECT transcricao, path_s3 FROM gravacao_emissora_radio WHERE id_emissora = $id_emissora AND id > $id LIMIT 1";
        $prev = DB::select($sql_prev)[0];
        $prev->path_s3 = Storage::disk('s3')->temporaryUrl($prev->path_s3, '+30 minutes');

        $sql_back = "SELECT transcricao, path_s3 FROM gravacao_emissora_radio WHERE id_emissora = $id_emissora AND id < $id ORDER BY id DESC LIMIT 1";
        $back = DB::select($sql_back)[0];
        $back->path_s3 = Storage::disk('s3')->temporaryUrl($back->path_s3, '+30 minutes');

        $dados = array('prev' => $prev, 'back' => $back);

        return response()->json($dados);
    }

    public function retorno()
    {
        Session::put('sub-menu','radio-retorno');

        $total_nulos = NoticiaRadio::whereNull('valor_retorno')->whereNotNull('emissora_id')->where('dt_clipagem', '>', '2025-05-01')->count();

        $sql = "SELECT t2.id, t2.nome_emissora, t2.nu_valor, count(*) as total 
                FROM noticia_radio t1
                LEFT JOIN emissora_radio t2 ON t2.id = t1.emissora_id 
                WHERE valor_retorno IS NULL
                AND dt_clipagem > '2025-05-01'
                AND t1.deleted_at IS NULL
                GROUP BY t2.id, t2.nome_emissora, t2.nu_valor
                ORDER BY nome_emissora";

        $inconsistencias = DB::select($sql);

        $sql = "SELECT t2.id, t2.nome_programa, t2.valor_segundo, count(*) as total 
                FROM noticia_radio t1
                LEFT JOIN programa_emissora_radio t2 ON t2.id = t1.programa_id 
                WHERE valor_retorno IS NULL
                AND dt_clipagem > '2025-05-01'
                AND t1.deleted_at IS NULL
                GROUP BY t2.id, t2.nome_programa, t2.valor_segundo
                ORDER BY nome_programa";

        $programas = DB::select($sql);

        $sql = "SELECT t1.id, t2.nome_emissora, t1.emissora_id, t2.nu_valor, valor_retorno, sinopse, duracao, dt_clipagem 
                FROM noticia_radio t1
                LEFT JOIN emissora_radio t2 ON t2.id = t1.emissora_id 
                WHERE valor_retorno IS NULL
                AND dt_clipagem > '2025-05-01'
                AND t1.deleted_at IS NULL
                ORDER BY nome_emissora";

        $noticias = DB::select($sql);

        return view('noticia-radio/retorno', compact('total_nulos','inconsistencias','programas','noticias'));
    }

    public function calcularValorRetornoRadio()
    {
        $totalAtualizadas = 0;

        $noticias = NoticiaRadio::whereNotNull('duracao')
            ->whereNull('valor_retorno')
            ->whereNotNull('emissora_id')
            ->where('dt_clipagem', '>', '2025-05-01')
            ->get();

        foreach ($noticias as $noticia) {
                    
                    $emissora = Emissora::find($noticia->emissora_id);

                    if (!$emissora || !is_numeric($emissora->nu_valor)) {
                        continue;
                    }

                    // Converte 'duracao' de TIME para segundos
                    $duracao = $noticia->duracao;
                    $duracaoEmSegundos = \Carbon\Carbon::parse($duracao)->hour * 3600
                        + \Carbon\Carbon::parse($duracao)->minute * 60
                        + \Carbon\Carbon::parse($duracao)->second;

                    $valorRetorno = $duracaoEmSegundos * $emissora->nu_valor;

                    $noticia->valor_retorno = $valorRetorno;
                    $noticia->save();

                    $totalAtualizadas++;
        }
        
        return redirect('radio/noticias/retorno')->withInput();
    }

    public function excluir($id)
    {
        $noticia = NoticiaRadio::find($id);

        if($noticia->delete())
            Flash::success('<i class="fa fa-check"></i> Notícia excluída com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('noticias/radio')->withInput();
    }
}