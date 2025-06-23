<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Mail;
use App\Utils;
use App\Models\Emissora;
use App\Models\ProgramaEmissoraWeb;
use App\Models\FonteWeb;
use App\Models\Periodo;
use App\Models\Cliente;
use App\Models\Monitoramento;
use App\Models\MonitoramentoExecucao;
use App\Models\JornalImpresso;
use App\Models\JornalOnline;
use App\Models\FonteImpressa;
use App\Models\EdicaoJornalOnline;
use App\Models\JornalWeb;
use App\Models\NoticiaWeb;
use App\Models\ConteudoNoticiaWeb;
use App\Models\Fonte;
use App\Models\Estado;
use App\Models\NoticiaCliente;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MonitoramentoController extends Controller
{
    private $client_id;
    private $data_atual;
    private $periodo_padrao;
    private $carbon;
    private $noticias = array();

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['executarImpresso']]);        
        $this->carbon = new Carbon();
        Session::put('url','monitoramento');
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','monitoramentos');

        $clientes = Cliente::where('fl_ativo', true)->orderBy('nome')->get();

        $cliente = ($request->cliente) ? $request->cliente : null;
        $midia = ($request->midia) ? $request->midia : null;
        
        if($request->situacao != ""){
            $situacao = $request->situacao;
            $fl_ativo = ($situacao == 1) ? true : false;
        }else{
            $fl_ativo = null;
            $situacao = null;
        }

        $monitoramento = Monitoramento::query();

        $monitoramento->when($cliente, function ($q) use ($cliente) {
            Session::put('monitoramento_cliente', $cliente);
            return $q->where('id_cliente', $cliente);
        });

        $monitoramento->when($midia, function ($q) use ($midia) {
            Session::put('monitoramento_midia', $midia);
            return $q->where($midia, true);
        });

        $monitoramento->when($request->situacao != "", function ($q) use ($fl_ativo) {
            Session::put('monitoramento_fl_ativo', $fl_ativo);
            return $q->where('fl_ativo', $fl_ativo);
        });

        $monitoramentos = $monitoramento->with('cliente')->orderBy('fl_ativo','DESC')->orderBy('id_cliente','ASC')->paginate(10);

        if($request->isMethod('POST')){

            $url = 'monitoramentos';

            $arr = array();

            if($cliente){
                $arr[] = "cliente=".$cliente;
            }

            if($midia){
                $arr[] = "midia=".$midia;
            }

            if($request->situacao != ""){
                $arr[] = "situacao=".$situacao;
            }

            if(count($arr)){
                $url .= "?".implode('&', $arr);
            }

            return redirect($url);
        }
        
        return view('monitoramento/index', compact('monitoramentos','clientes','situacao','cliente','midia'));
    }

    public function novo()
    {
        Session::put('sub-menu','monitoramento-cadastrar');

        $periodos = Periodo::orderBy('ordem')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('nome')->get();

        return view('monitoramento/novo', compact('clientes','periodos'));
    }

    public function exportacaoWeb(Request $request)
    {
        Session::put('sub-menu','monitoramento-exportar-web');
        
        $periodos = Periodo::orderBy('ordem')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('nome')->get();

        $tipo_data = $request->tipo_data;
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $cliente = ($request->cliente) ? $request->cliente : null;
        $fl_dia  = $request->fl_dia == true ? true : false;
        $exportacao = ($request->exportacao) ? $request->exportacao : false;

        $dados = DB::table('noticia_cliente')
                    ->select('path_screenshot',
                            'fonte_web.id AS id_fonte',
                            'fonte_web.nome AS nome_fonte',
                            'noticias_web.data_noticia',
                            'noticias_web.data_insert',
                            'noticias_web.titulo_noticia',
                            'noticia_cliente.noticia_id',
                            'noticia_cliente.monitoramento_id',
                            'expressao',
                            'fl_print',
                            'screenshot',
                            'url_noticia',
                            'exported',
                            'noticia_cliente.created_at',
                            'clientes.nome AS nome_cliente')
                    ->join('clientes', 'clientes.id', '=', 'noticia_cliente.cliente_id')
                    ->join('noticias_web', function ($join) {
                        $join->on('noticias_web.id', '=', 'noticia_cliente.noticia_id')->where('tipo_id',2);
                    })
                    ->join('fonte_web','fonte_web.id','=','noticias_web.id_fonte')
                    ->join('monitoramento', 'monitoramento.id','=','noticia_cliente.monitoramento_id')
                    ->when($dt_inicial, function ($q) use ($dt_inicial, $dt_final) {
                        return $q->whereBetween('noticia_cliente.created_at', [$dt_inicial." 00:00:00", $dt_final." 23:59:59"]);
                    })
                    ->when($fl_dia, function ($q) use ($fl_dia, $dt_final) {
                        return $q->whereBetween('noticias_web.data_noticia', [$dt_inicial." 00:00:00", $dt_final." 23:59:59"]);
                    })
                    ->when($exportacao, function ($q) use ($exportacao) {
                        if($exportacao == 1){
                            return $q->where('exported', true);
                        }else{
                            return $q->where('exported', false);
                        }
                    })
                    ->when($cliente, function ($q) use ($cliente) {
                        return $q->where('noticia_cliente.cliente_id', $cliente);
                    })
                    ->orderBy('noticia_cliente.created_at','DESC')
                    ->get();

        return view('monitoramento/exportacao-web', compact('clientes','periodos','dados','tipo_data','dt_inicial','dt_final','cliente','fl_dia','exportacao'));
    }

    public function noticias($id)
    {
        $execucao = MonitoramentoExecucao::find($id);
        $monitoramento = $execucao->monitoramento;
        $noticias = $monitoramento->noticias;
    
        return view('monitoramento/noticias', compact('noticias','monitoramento'));
    }

    public function noticiasMonitoramento($id)
    {
        $dt_inicial = date("Y-m-d H:i:s");
        $dt_final = date("Y-m-d H:i:s");

        $monitoramento = Monitoramento::find($id);
        $noticias = $monitoramento->noticiasWeb;
    
        return view('monitoramento/noticias', compact('noticias','monitoramento'));
    }

    public function create(Request $request)
    {
        $monitoramento = Monitoramento::create($request->all());
    }

    public function filtrar(Request $request)
    {
        $carbon = new Carbon();
        $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d "."00:00:00");
        $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d "."23:59:59");

        $tipo_data = $request->tipo_data;

        $label_data = ($tipo_data == "dt_publicacao") ? 'data_noticia' : 'created_at' ;

        $sql = "SELECT DISTINCT ON (n.titulo_noticia) 
                    n.id, n.id_fonte, n.url_noticia, n.data_insert, n.data_noticia, n.titulo_noticia, fw.nome
                FROM 
                    noticias_web n
                JOIN 
                    conteudo_noticia_web cnw ON cnw.id_noticia_web = n.id
                JOIN 
                    fonte_web fw ON fw.id = n.id_fonte 
                WHERE 1=1 ";

        if($request->fontes){

            $fontes = $request->fontes;
            $sql .= "AND fw.id IN($fontes) ";
        }
        
        $sql .= " AND n.$label_data BETWEEN '$dt_inicial' AND '$dt_final' ";

        $sql .= ($request->expressao) ? "AND  cnw.conteudo_tsv @@ to_tsquery('simple', '$request->expressao') " : '';
        //$sql .= 'ORDER BY n.'.$label_data.' DESC';

        $dados = DB::select($sql);

        return response()->json($dados);
    }

    public function filtrarImpresso(Request $request)
    {
        $carbon = new Carbon();
        $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d "."00:00:00");
        $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d "."23:59:59");

        $tipo_data = $request->tipo_data;

        $label_data = ($tipo_data == "dt_publicacao") ? 'dt_coleta' : 'dt_pub' ;

        $sql = "SELECT 
                    pejo.id, id_jornal_online, link_pdf, dt_coleta, dt_pub, titulo, texto_extraido, jo.nome, pejo.n_pagina 
                FROM 
                    edicao_jornal_online n
                JOIN 
                    pagina_edicao_jornal_online pejo ON pejo.id_edicao_jornal_online = n.id
                JOIN jornal_online jo ON jo.id = n.id_jornal_online 
                WHERE 1=1
                    AND n.$label_data BETWEEN '$dt_inicial' AND '$dt_final' ";

        $sql .= ($request->expressao) ? "AND pejo.texto_extraido_tsv @@ to_tsquery('simple', '$request->expressao') " : '';

        if($request->fontes){

            $fontes = $request->fontes;
            $sql .= "AND n.id_jornal_online IN($fontes) ";
        }

        $sql .= 'ORDER BY '.$label_data.' DESC, n_pagina';

        $dados = DB::select($sql);

        return response()->json($dados);
    }

    public function filtrarRadio(Request $request)
    {
        $carbon = new Carbon();
        $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d "."00:00:00");
        $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d "."23:59:59");

        $tipo_data = $request->tipo_data;

        $label_data = ($tipo_data == "dt_publicacao") ? 'data_hora_inicio' : 'created_at' ;

        $sql = "SELECT 
                    n.id, id_emissora, data_hora_inicio, data_hora_fim, path_s3, nome_emissora
                FROM 
                    gravacao_emissora_radio n
                JOIN 
                    emissora_radio er 
                    ON er.id = n.id_emissora
                WHERE 1=1
                    AND n.$label_data BETWEEN '$dt_inicial' AND '$dt_final' ";

        $sql .= ($request->expressao) ? "AND n.transcricao_tsv @@ to_tsquery('simple', '$request->expressao') " : '';

        if($request->fontes){

            $fontes = $request->fontes;
            $sql .= "AND er.id IN($fontes) ";
        }

        $sql .= 'ORDER BY n.'.$label_data.' DESC';

        $dados = DB::select($sql);

        return response()->json($dados);
    }

    public function filtrarTv(Request $request)
    {
        $carbon = new Carbon();
        $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d "."00:00:00");
        $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d "."23:59:59");

        $tipo_data = $request->tipo_data;

        $label_data = ($tipo_data == "dt_publicacao") ? 'horario_start_gravacao' : 'created_at' ;

        $sql = "SELECT 
                    n.id, id_programa_emissora_web, horario_start_gravacao, horario_end_gravacao, url_video, n.misc_data, transcricao, nome_programa
                FROM 
                    videos_programa_emissora_web n
                JOIN 
                    programa_emissora_web pew 
                    ON pew.id = n.id_programa_emissora_web
                WHERE 1=1
                    AND n.$label_data BETWEEN '$dt_inicial' AND '$dt_final' ";

        $sql .= ($request->expressao) ? "AND  n.transcricao_tsv @@ to_tsquery('simple', '$request->expressao') " : '';

        if($request->fontes){

            $fontes = $request->fontes;
            $sql .= "AND pew.id IN($fontes) ";
        }

        $sql .= 'ORDER BY n.'.$label_data.' DESC';

        $dados = DB::select($sql);

        return response()->json($dados);
    }

    public function getConteudo(Request $request)
    {
        switch ($request->tipo) {
            case 'web':
                $sql = "SELECT ts_headline('simple', conteudo , to_tsquery('simple', '$request->expressao'), 'HighlightAll=true, StartSel=<mark>, StopSel=</mark>') as texto
                        FROM conteudo_noticia_web 
                        WHERE id_noticia_web = ".$request->id;
                break;
            
            case 'impresso':
                $sql = "SELECT ts_headline('simple', texto_extraido , to_tsquery('simple', '$request->expressao'), 'HighlightAll=true, StartSel=<mark>, StopSel=</mark>') as texto
                        FROM pagina_edicao_jornal_online 
                        WHERE id = ".$request->id;
                break;

            case 'radio':
                $sql = "SELECT ts_headline('simple', transcricao , to_tsquery('simple', '$request->expressao'), 'HighlightAll=true, StartSel=<mark>, StopSel=</mark>') as texto
                        FROM gravacao_emissora_radio 
                        WHERE id = ".$request->id;
                break;

            case 'tv':
                $sql = "SELECT ts_headline('simple', transcricao , to_tsquery('simple', '$request->expressao'), 'HighlightAll=true, StartSel=<mark>, StopSel=</mark>') as texto
                        FROM videos_programa_emissora_web 
                        WHERE id = ".$request->id;
                break;
        }

        $dados = DB::select($sql);

        return response()->json($dados); 
    }

    public function getMonitoramentoCliente($id_cliente)
    {
        $cliente = Cliente::find($id_cliente);
        $monitoramentos = Monitoramento::with('cliente')->where('id_cliente', $id_cliente)->orderBy('id','DESC')->get();

        return view('monitoramento/detalhes', compact('monitoramentos','cliente'));
    }

    public function buscar($cliente)
    {
        $dados = Monitoramento::with('cliente')->where('id_cliente', $cliente)->orderBy('id','DESC')->get();
        
        return response()->json($dados);
    }

    public function historico($id)
    {
        $monitoramento = Monitoramento::find($id);

        $historico = $monitoramento->historico;
        
        return view('monitoramento/historico', compact('historico','monitoramento'));
    }

    public function executarWeb($grupo)
    {
        $dt_inicial = (Carbon::now())->format('Y-m-d')." 00:00:00";
        $dt_final = (Carbon::now())->format('Y-m-d')." 23:59:59";
        $data_inicio = date('Y-m-d H:i:s');
        $total_encontrado = 0;
        $total_vinculado = 0;
        $tipo_midia = 2;

        $monitoramentos = Monitoramento::where('fl_ativo', true)->where('fl_web', true)->where('grupo_execucao', $grupo)->get();
        
        foreach ($monitoramentos as $key => $monitoramento) {

            try{

                if($monitoramento->dt_inicio){
                    $dt_inicial = $monitoramento->dt_inicio;
                }

                $sql = "SELECT DISTINCT ON (n.titulo_noticia) 
                            n.id, n.id_fonte, n.url_noticia, n.data_insert, n.data_noticia, n.titulo_noticia, fw.nome
                        FROM 
                            noticias_web n
                        JOIN 
                            conteudo_noticia_web cnw ON cnw.id_noticia_web = n.id
                        JOIN 
                            fonte_web fw ON fw.id = n.id_fonte 
                        WHERE 1=1 
                        AND n.created_at >= now() - interval '3 hours' ";

                if($monitoramento->filtro_web){
                    $sql .= "AND fw.id IN($monitoramento->filtro_web) ";
                }

                $sql .= "AND n.data_noticia BETWEEN '$dt_inicial' AND '$dt_final' 
                         AND cnw.conteudo_tsv @@ to_tsquery('simple', '$monitoramento->expressao')";

                $dados = DB::select($sql);

                $total_encontrado += count($dados);

                $total_associado = $this->associar($dados, $tipo_midia, $monitoramento);

                $total_vinculado = $total_associado;
                
                $data_termino = date('Y-m-d H:i:s');

                $dado_moninoramento = array('monitoramento_id' => $monitoramento->id, 
                                            'total_vinculado' => $total_vinculado,
                                            'created_at' => $data_inicio,
                                            'updated_at' => $data_termino);

                MonitoramentoExecucao::create($dado_moninoramento);

                $monitoramento->updated_at = date("Y-m-d H:i:s");
                $monitoramento->save();

            } catch (\Illuminate\Database\QueryException $e) {

                $titulo = "Notificação de Monitoramento - Erro de Consulta - ".date("d/m/Y H:i:s"); 

                $data['dados'] = array('cliente' => $monitoramento->cliente->nome,
                                       'expressao' => $monitoramento->expressao,
                                       'id' => $monitoramento->id);

                //app('App\Http\Controllers\MonitoramentoController')->executar();
                
                Mail::send('notificacoes.monitoramento', $data, function($message) use ($titulo){
                    $message->to("robsonferduda@gmail.com")
                            ->subject($titulo);
                    $message->from('boletins@clipagens.com.br','Studio Clipagem');
                }); 

            } catch (Exception $e) {
                
            }
        }
    }

    public function clonar($id)
    {
        $monitoramento = Monitoramento::find($id);

        $novo_monitoramento = $monitoramento->replicate();
        $novo_monitoramento->nome = null;
        $novo_monitoramento->id_cliente = null;
        $novo_monitoramento->save();

        return redirect('monitoramento/'.$novo_monitoramento->id.'/editar');
    }

    public function executarImpresso()
    {
        $dt_inicial = (Carbon::now())->format('Y-m-d')." 00:00:00";
        $dt_final = (Carbon::now())->format('Y-m-d')." 23:59:59";
        $data_inicio = date('Y-m-d H:i:s');
        $total_vinculado = 0;
        $tipo_midia = 1;

        $monitoramentos = Monitoramento::where('fl_ativo', true)->where('fl_impresso', true)->get();
        
        foreach ($monitoramentos as $key => $monitoramento) {

            try{

                if($monitoramento->dt_inicio){
                    $dt_inicial = $monitoramento->dt_inicio;
                }

                if($monitoramento->filtro_impresso){

                    $sql = "SELECT
                        pejo.id, n.id_jornal_online, n.link_pdf, dt_coleta, dt_pub, titulo, texto_extraido
                        FROM 
                            edicao_jornal_online n
                        JOIN 
                            pagina_edicao_jornal_online pejo 
                            ON pejo.id_edicao_jornal_online = n.id
                        JOIN jornal_online jo ON jo.id = n.id_jornal_online 
                        WHERE 1=1 
                            AND n.dt_coleta BETWEEN '$dt_inicial' AND '$dt_final' 
                            AND pejo.texto_extraido_tsv @@ to_tsquery('simple', '$monitoramento->expressao')
                            AND jo.id IN($monitoramento->filtro_impresso)
                            ORDER BY dt_coleta DESC";

                }else{

                    $sql = "SELECT 
                        pejo.id, id_jornal_online, link_pdf, dt_coleta, dt_pub, titulo, texto_extraido
                    FROM 
                        edicao_jornal_online n
                    JOIN 
                        pagina_edicao_jornal_online pejo 
                        ON pejo.id_edicao_jornal_online = n.id
                    WHERE 1=1 
                        AND n.dt_coleta BETWEEN '$dt_inicial' AND '$dt_final' 
                        AND pejo.texto_extraido_tsv @@ to_tsquery('simple', '$monitoramento->expressao')
                        ORDER BY dt_coleta DESC";
                }
               
                $dados = DB::select($sql);

                $total_associado = $this->associar($dados, $tipo_midia, $monitoramento);

                $total_vinculado = $total_associado;
                
                $data_termino = date('Y-m-d H:i:s');

                $dado_moninoramento = array('monitoramento_id' => $monitoramento->id, 
                                            'total_vinculado' => $total_vinculado,
                                            'created_at' => $data_inicio,
                                            'updated_at' => $data_termino);

                MonitoramentoExecucao::create($dado_moninoramento);

                $monitoramento->updated_at = date("Y-m-d H:i:s");
                $monitoramento->save();

            } catch (\Illuminate\Database\QueryException $e) {

                $titulo = "Notificação de Monitoramento de Impresso - Erro de Consulta - ".date("d/m/Y H:i:s"); 

                $data['dados'] = array('cliente' => $monitoramento->cliente->nome,
                                       'expressao' => $monitoramento->expressao,
                                       'id' => $monitoramento->id);

                //app('App\Http\Controllers\MonitoramentoController')->executar();
                
                Mail::send('notificacoes.monitoramento', $data, function($message) use ($titulo){
                    $message->to("robsonferduda@gmail.com")
                            ->subject($titulo);
                    $message->from('boletins@clipagens.com.br','Studio Clipagem');
                }); 

            } catch (Exception $e) {
                
            }
        }
    }

    public function executarRadio()
    {
        $dt_inicial = (Carbon::now())->format('Y-m-d')." 00:00:00";
        $dt_final = (Carbon::now())->format('Y-m-d')." 23:59:59";
        $data_inicio = date('Y-m-d H:i:s');
        $total_vinculado = 0;
        $tipo_midia = 3;

        $monitoramentos = Monitoramento::where('fl_ativo', true)->where('fl_radio', true)->get();
        
        foreach ($monitoramentos as $key => $monitoramento) {

            try{

                if($monitoramento->dt_inicio){
                    $dt_inicial = $monitoramento->dt_inicio;
                }
                
                $sql = "SELECT 
                            n.id, id_emissora, data_hora_inicio, data_hora_fim, path_s3, nome_emissora
                        FROM 
                            gravacao_emissora_radio n
                        JOIN 
                            emissora_radio er 
                        ON er.id = n.id_emissora
                        WHERE 1=1 ";

                if($monitoramento->filtro_radio){
                    $sql .= "AND er.id IN($monitoramento->filtro_radio) ";
                }

                $sql .= "AND n.data_hora_inicio BETWEEN '$dt_inicial' AND '$dt_final'
                        AND  n.transcricao_tsv @@ to_tsquery('simple', '$monitoramento->expressao')
                        ORDER BY n.data_hora_inicio DESC";

                $dados = DB::select($sql);

                $total_associado = $this->associar($dados, $tipo_midia, $monitoramento);

                $total_vinculado = $total_associado;
                
                $data_termino = date('Y-m-d H:i:s');

                $dado_moninoramento = array('monitoramento_id' => $monitoramento->id, 
                                            'total_vinculado' => $total_vinculado,
                                            'created_at' => $data_inicio,
                                            'updated_at' => $data_termino);

                MonitoramentoExecucao::create($dado_moninoramento);

                $monitoramento->updated_at = date("Y-m-d H:i:s");
                $monitoramento->save();

            } catch (\Illuminate\Database\QueryException $e) {

                $titulo = "Notificação de Monitoramento de Rádio - Erro de Consulta - ".date("d/m/Y H:i:s"); 

                $data['dados'] = array('cliente' => $monitoramento->cliente->nome,
                                       'expressao' => $monitoramento->expressao,
                                       'id' => $monitoramento->id);

                //app('App\Http\Controllers\MonitoramentoController')->executar();
                
                Mail::send('notificacoes.monitoramento', $data, function($message) use ($titulo){
                    $message->to("robsonferduda@gmail.com")
                            ->subject($titulo);
                    $message->from('boletins@clipagens.com.br','Studio Clipagem');
                }); 

            } catch (Exception $e) {
                
            }
        }
    }

    public function executarTv()
    {
        $dt_inicial = (Carbon::now())->format('Y-m-d')." 00:00:00";
        $dt_final = (Carbon::now())->format('Y-m-d')." 23:59:59";
        $data_inicio = date('Y-m-d H:i:s');
        $total_vinculado = 0;
        $tipo_midia = 4;

        $monitoramentos = Monitoramento::where('fl_ativo', true)->where('fl_tv', true)->get();
        
        foreach ($monitoramentos as $key => $monitoramento) {

            if($monitoramento->dt_inicio){
                $dt_inicial = $monitoramento->dt_inicio;
            }

            try{
                $sql = "SELECT 
                        n.id, id_programa_emissora_web, horario_start_gravacao, horario_end_gravacao, url_video, n.misc_data, transcricao, nome_programa
                            FROM 
                        videos_programa_emissora_web n
                            JOIN 
                        programa_emissora_web pew 
                            ON pew.id = n.id_programa_emissora_web
                        WHERE 1=1 ";

                if($monitoramento->filtro_tv){
                    $sql .= "AND n.id_programa_emissora_web IN($monitoramento->filtro_tv)";
                }     
                        
                $sql .= "AND n.horario_start_gravacao BETWEEN '$dt_inicial' AND '$dt_final'
                         AND n.transcricao_tsv @@ to_tsquery('simple', '$monitoramento->expressao')
                         ORDER BY n.horario_start_gravacao DESC";

                $dados = DB::select($sql);

                $total_associado = $this->associar($dados, $tipo_midia, $monitoramento);

                $total_vinculado = $total_associado;
                
                $data_termino = date('Y-m-d H:i:s');

                $dado_moninoramento = array('monitoramento_id' => $monitoramento->id, 
                                            'total_vinculado' => $total_vinculado,
                                            'created_at' => $data_inicio,
                                            'updated_at' => $data_termino);

                MonitoramentoExecucao::create($dado_moninoramento);

                $monitoramento->updated_at = date("Y-m-d H:i:s");
                $monitoramento->save();

            } catch (\Illuminate\Database\QueryException $e) {

                $titulo = "Notificação de Monitoramento de TV - Erro de Consulta - ".date("d/m/Y H:i:s"); 

                $data['dados'] = array('cliente' => $monitoramento->cliente->nome,
                                       'expressao' => $monitoramento->expressao,
                                       'id' => $monitoramento->id);

                //app('App\Http\Controllers\MonitoramentoController')->executar();
                
                Mail::send('notificacoes.monitoramento', $data, function($message) use ($titulo){
                    $message->to("robsonferduda@gmail.com")
                            ->subject($titulo);
                    $message->from('boletins@clipagens.com.br','Studio Clipagem');
                }); 

            } catch (Exception $e) {
                
            }
        }
    }

    public function executar($id)
    {
        $dt_inicial = (Carbon::now())->format('Y-m-d')." 00:00:00";
        $dt_final = (Carbon::now())->format('Y-m-d')." 23:59:59";

        $data_inicio = date('Y-m-d H:i:s');
        $total_vinculado = 0;
        $total_encontrado = 0;

        $monitoramento = Monitoramento::find($id);

        if($monitoramento->dt_inicio){
            $dt_inicial = $monitoramento->dt_inicio;
        }

        try{
        
            if($monitoramento->fl_web) {

                $tipo_midia = 2; //Web

                $sql = "SELECT DISTINCT ON (n.titulo_noticia) 
                            n.id, n.id_fonte, n.url_noticia, n.data_insert, n.data_noticia, n.titulo_noticia, fw.nome
                        FROM 
                            noticias_web n
                        JOIN 
                            conteudo_noticia_web cnw ON cnw.id_noticia_web = n.id
                        JOIN 
                            fonte_web fw ON fw.id = n.id_fonte 
                        WHERE 1=1 
                        AND n.created_at >= now() - interval '3 hours' ";
                        

                if($monitoramento->filtro_web){
                    $sql .= "AND fw.id IN($monitoramento->filtro_web)";
                }

                $sql .= " AND n.data_noticia BETWEEN '$dt_inicial' AND '$dt_final' 
                        AND cnw.conteudo_tsv @@ to_tsquery('simple', '$monitoramento->expressao')";

                $dados = DB::select($sql);

                $total_encontrado += count($dados);

                $total_associado = $this->associar($dados, $tipo_midia, $monitoramento);
                $total_vinculado += $total_associado;
            }

            if($monitoramento->fl_impresso) {

                $tipo_midia = 1; //Impresso

                if($monitoramento->filtro_impresso){

                    $sql = "SELECT
                        pejo.id, n.id_jornal_online, n.link_pdf, dt_coleta, dt_pub, titulo, texto_extraido
                        FROM 
                            edicao_jornal_online n
                        JOIN 
                            pagina_edicao_jornal_online pejo 
                            ON pejo.id_edicao_jornal_online = n.id
                        JOIN jornal_online jo ON jo.id = n.id_jornal_online 
                        WHERE 1=1
                            AND n.dt_coleta BETWEEN '$dt_inicial' AND '$dt_final' 
                            AND pejo.texto_extraido_tsv @@ to_tsquery('simple', '$monitoramento->expressao')
                            AND jo.id IN($monitoramento->filtro_impresso)
                            ORDER BY dt_coleta DESC";

                }else{

                    $sql = "SELECT 
                        pejo.id, id_jornal_online, link_pdf, dt_coleta, dt_pub, titulo, texto_extraido
                    FROM 
                        edicao_jornal_online n
                    JOIN 
                        pagina_edicao_jornal_online pejo 
                        ON pejo.id_edicao_jornal_online = n.id
                    WHERE 1=1
                        AND n.dt_coleta BETWEEN '$dt_inicial' AND '$dt_final' 
                        AND pejo.texto_extraido_tsv @@ to_tsquery('simple', '$monitoramento->expressao')
                        ORDER BY dt_coleta DESC";
                }

                $dados = DB::select($sql);

                $total_encontrado += count($dados);

                $total_associado = $this->associar($dados, $tipo_midia, $monitoramento);
                $total_vinculado += $total_associado;
            }

            if($monitoramento->fl_radio) {

                $tipo_midia = 3; //Rádio

                 $sql = "SELECT 
                            n.id, id_emissora, data_hora_inicio, data_hora_fim, path_s3, nome_emissora
                        FROM 
                            gravacao_emissora_radio n
                        JOIN 
                            emissora_radio er 
                        ON er.id = n.id_emissora
                        WHERE 1=1 ";

                if($monitoramento->filtro_radio){
                    $sql .= "AND er.id IN($monitoramento->filtro_radio) ";
                }

                $sql .= "AND n.data_hora_inicio BETWEEN '$dt_inicial' AND '$dt_final'
                         AND  n.transcricao_tsv @@ to_tsquery('simple', '$monitoramento->expressao')
                        ORDER BY n.data_hora_inicio DESC";

                $dados = DB::select($sql);

                $total_encontrado += count($dados);


                $total_associado = $this->associar($dados, $tipo_midia, $monitoramento);
                $total_vinculado += $total_associado;
            }

            if($monitoramento->fl_tv) {

                $tipo_midia = 4; //TV

                $sql = "SELECT 
                        n.id, id_programa_emissora_web, horario_start_gravacao, horario_end_gravacao, url_video, misc_data, transcricao, nome_programa
                        FROM 
                        videos_programa_emissora_web n
                        JOIN 
                        programa_emissora_web pew 
                        ON pew.id = n.id_programa_emissora_web
                        WHERE 1=1 "; 

                if($monitoramento->filtro_tv){
                    $sql .= "AND n.id_programa_emissora_web IN($monitoramento->filtro_tv)";
                }

                $sql .= "AND n.horario_start_gravacao BETWEEN '$dt_inicial' AND '$dt_final'
                         AND n.transcricao_tsv @@ to_tsquery('simple', '$monitoramento->expressao')
                         ORDER BY n.horario_start_gravacao DESC";

                $dados = DB::select($sql);

                $total_encontrado += count($dados);

                $total_associado = $this->associar($dados, $tipo_midia, $monitoramento);
                $total_vinculado += $total_associado;
            }

            $data_termino = date('Y-m-d H:i:s');

            $dado_moninoramento = array('monitoramento_id' => $monitoramento->id, 
                                        'total_vinculado' => $total_vinculado,
                                        'created_at' => $data_inicio,
                                        'fl_automatico' => false,
                                        'id_user' => Auth::user()->id,
                                        'updated_at' => $data_termino);

            MonitoramentoExecucao::create($dado_moninoramento);

            $monitoramento->updated_at = date("Y-m-d H:i:s");
            $monitoramento->save();

            Flash::success('<i class="fa fa-check"></i> Monitoramento executado manualmente encontrou <strong>'.$total_encontrado.'</strong> registros e vinculou <strong>'. $total_vinculado.'</strong> registros');

        } catch (\Illuminate\Database\QueryException $e) {

            Flash::warning('<i class="fa fa-check"></i> Erro na execução da expressão de busca. Verifique a expressão e tente novamente.');

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

        return redirect()->back()->withInput();
    }

    public function associar($dados, $tipo, $monitoramento)
    {
        $total_vinculado = 0;

        foreach ($dados as $key => $noticia) {

            $noticia_cliente = NoticiaCliente::where('noticia_id', $noticia->id)
                                            ->where('tipo_id', $tipo)
                                            ->where('cliente_id', $monitoramento->id_cliente)
                                            ->where('monitoramento_id', $monitoramento->id)
                                            ->first();
            
            if(!$noticia_cliente){

                $dados = array('cliente_id' => $monitoramento->id_cliente,
                            'tipo_id'    => $tipo,
                            'noticia_id' => $noticia->id,
                            'monitoramento_id' => $monitoramento->id);

                NoticiaCliente::create($dados);
                $total_vinculado++;

                if($tipo == 2){

                    $valor_fonte = (FonteWeb::find($noticia->id_fonte)) ? FonteWeb::find($noticia->id_fonte)->nu_valor : 0;

                    if($valor_fonte){
                        $valor_retorno = $valor_fonte;
                    }else{
                        $valor_retorno = 0;
                    }
                       
                    $noticia_web = NoticiaWeb::find($noticia->id);
                    $noticia_web->screenshot = true;
                    $noticia_web->nu_valor = $valor_retorno;
                    $noticia_web->fl_boletim = true;
                    $noticia_web->save();
                }
            }            
        }

        return $total_vinculado;
    }

    public function atualizarStatus($id)
    {
        $monitoramento = Monitoramento::find($id);

        if($monitoramento){
            $monitoramento->fl_ativo = !$monitoramento->fl_ativo;
            if($monitoramento->save())
                Flash::success('<i class="fa fa-check"></i> Status do monitoramento atualizado com sucesso');
            else
                Flash::error('<i class="fa fa-times"></i> Erro ao atualizar status');
        }

        return redirect()->back()->withInput();
    }

    public function editar($id)
    {
        $periodos = Periodo::orderBy('ordem')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('nome')->get();
        $fontes = array();
        $cidades_selecionadas = array();
        $estados = Estado::orderBy('nm_estado')->get();

        $monitoramento = Monitoramento::find($id);

        /*
        if($monitoramento->fl_web){

            if($monitoramento->filtro_web){
                $cidades_selecionadas = explode(",", $monitoramento->filtro_web);
            }

            $fontes_disponiveis = DB::select("SELECT id, nome, t2.sg_estado 
                                                FROM fonte_web t1 
                                                LEFT JOIN estado t2 ON t2.cd_estado = t1.cd_estado 
                                                WHERE id_situacao IN(1, 2, 3) 
                                                ORDER BY t2.sg_estado, nome"); 
            
            foreach ($fontes_disponiveis as $key => $fd) {
                if(in_array($fd->id, $cidades_selecionadas)){
                    $fontes[] = array('id' => $fd->id,
                                      'estado' => ($fd->sg_estado) ? $fd->sg_estado : '',
                                      'nome' => $fd->nome,
                                      'flag' => 'selected');
                }else{
                    $fontes[] = array('id' => $fd->id,
                                    'estado' => ($fd->sg_estado) ? $fd->sg_estado : '',
                                    'nome' => $fd->nome,
                                    'flag' => '');
                }
            }
        }

        if($monitoramento->fl_radio){

            if($monitoramento->filtro_radio){
                $cidades_selecionadas = explode(",", $monitoramento->filtro_radio);
            }

            $fontes_disponiveis = DB::select("SELECT id, nome_emissora as nome, t2.sg_estado FROM emissora_radio t1 LEFT JOIN estado t2 ON t2.cd_estado = t1.cd_estado ORDER BY t2.sg_estado, nome"); 
            
            foreach ($fontes_disponiveis as $key => $fd) {
                if(in_array($fd->id, $cidades_selecionadas)){
                    $fontes[] = array('id' => $fd->id,
                                      'estado' => ($fd->sg_estado) ? $fd->sg_estado : '',
                                      'nome' => $fd->nome,
                                      'flag' => 'selected');
                }else{
                    $fontes[] = array('id' => $fd->id,
                                    'estado' => ($fd->sg_estado) ? $fd->sg_estado : '',
                                    'nome' => $fd->nome,
                                    'flag' => '');
                }
            }
            
        }

        if($monitoramento->fl_impresso){

            if($monitoramento->filtro_impresso){
                $cidades_selecionadas = explode(",", $monitoramento->filtro_impresso);
            }

            $fontes_disponiveis = DB::select("SELECT id, nome , t2.sg_estado FROM jornal_online t1 LEFT JOIN estado t2 ON t2.cd_estado = t1.cd_estado WHERE fl_ativo = true ORDER BY t2.sg_estado, nome"); 
            
            foreach ($fontes_disponiveis as $key => $fd) {
                if(in_array($fd->id, $cidades_selecionadas)){
                    $fontes[] = array('id' => $fd->id,
                                      'estado' => ($fd->sg_estado) ? $fd->sg_estado : '',
                                      'nome' => $fd->nome,
                                      'flag' => 'selected');
                }else{
                    $fontes[] = array('id' => $fd->id,
                                    'estado' => ($fd->sg_estado) ? $fd->sg_estado : '',
                                    'nome' => $fd->nome,
                                    'flag' => '');
                }
            }
            
        }

        if($monitoramento->fl_tv){

            if($monitoramento->filtro_tv){
                $cidades_selecionadas = explode(",", $monitoramento->filtro_tv);
            }

            $fontes_disponiveis = DB::select("SELECT id, nome_programa as nome, t2.sg_estado FROM programa_emissora_web t1 LEFT JOIN estado t2 ON t2.cd_estado = t1.cd_estado ORDER BY t2.sg_estado, nome"); 
            
            foreach ($fontes_disponiveis as $key => $fd) {
                if(in_array($fd->id, $cidades_selecionadas)){
                    $fontes[] = array('id' => $fd->id,
                                      'estado' => ($fd->sg_estado) ? $fd->sg_estado : '',
                                      'nome' => $fd->nome,
                                      'flag' => 'selected');
                }else{
                    $fontes[] = array('id' => $fd->id,
                                    'estado' => ($fd->sg_estado) ? $fd->sg_estado : '',
                                    'nome' => $fd->nome,
                                    'flag' => '');
                }
            }
            
        }*/

        return view('monitoramento/editar', compact('monitoramento','clientes','periodos','fontes','estados'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $monitoramento = Monitoramento::find($id);

        $fl_web = $request->fl_web == true ? true : false;
        $fl_tv = $request->fl_tv == true ? true : false;
        $fl_impresso = $request->fl_impresso == true ? true : false;
        $fl_radio = $request->fl_radio == true ? true : false;

        $dt_inicio = ($request->dt_inicio) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicio)->format('Y-m-d')." 00:00:00" : null;
        $dt_fim = ($request->dt_fim) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_fim)->format('Y-m-d')." 00:00:00" : null;

        $hora_inicio = ($request->hora_inicio) ? $this->carbon->createFromFormat('H:i', $request->hora_inicio)->format('H:i') : null;
        $hora_fim = ($request->hora_fim) ? $this->carbon->createFromFormat('H:i', $request->hora_fim)->format('H:i') : null;

        $request->merge(['fl_web' => $fl_web]);
        $request->merge(['fl_tv' => $fl_tv]);
        $request->merge(['fl_impresso' => $fl_impresso]);
        $request->merge(['fl_radio' => $fl_radio]);

        $request->merge(['dt_inicio' => $dt_inicio]);
        $request->merge(['dt_fim' => $dt_fim]);
        $request->merge(['hora_inicio' => $hora_inicio]);
        $request->merge(['hora_fim' => $hora_fim]);

        //$filtro_fontes = ($request->fontes) ? implode(',', $request->fontes) : '';

        $filtro_fontes = ($request->selecionadas[0]) ? $request->selecionadas[0] : '';

        if($fl_web){
            $request->merge(['filtro_web' => $filtro_fontes]);
        }

        if($fl_impresso){
            $request->merge(['filtro_impresso' => $filtro_fontes]);
        }

        if($fl_radio){
            $request->merge(['filtro_radio' => $filtro_fontes]);
        }

        if($fl_tv){
            $request->merge(['filtro_tv' => $filtro_fontes]);
        }

        try{
                        
            $monitoramento->update($request->all());
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados atualizados com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);

            $this->executar($id);
            
            return redirect($request->url_origem)->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('monitoramento/'.$monitoramento->id.'/editar')->withInput();
        }
    }

    public function excluir($id)
    {
        $monitoramento = Monitoramento::find($id);

        if($monitoramento->historico){
            $monitoramento->historico()->delete();
        }

        if($monitoramento->delete()){
            Flash::success('<i class="fa fa-check"></i> Monitoramento excluído com sucesso');
        }else{
            Flash::error('<i class="fa fa-times"></i> Erro ao excluir monitoramento');
        }

        return redirect('monitoramentos')->withInput();
    }

    public function getMonitoramento($cliente, $flag)
    {
        $monitoramentos = Monitoramento::where('id_cliente', $cliente)->where($flag, true)->get();

        return response()->json($monitoramentos);
    }

    public function getFontesMonitoramento($monitoramento)
    {
        $monitoramento = Monitoramento::where('id', $monitoramento)->first();

        return response()->json($monitoramento);
    }

    public function limparMonitoramento($id)
    {
        $dados = DB::table('noticia_cliente')->where('monitoramento_id', $id)->get();

        if(count($dados)){
            if(DB::table('noticia_cliente')->where('monitoramento_id', $id)->delete()){
                Flash::success('<i class="fa fa-check"></i> Limpeza de monitoramento realizada com sucesso');
            }else{
                Flash::error('<i class="fa fa-times"></i> Erro ao limpar monitoramento');
            }
        }else{
            Flash::warning('<i class="fa fa-times"></i> Monitoramento não possui dados vinculados');
        }
      

        return redirect()->back()->withInput();
    }

    public function loadEmissoras($tipo, $id_monitoramento)
    {
        $filtro = "filtro_".$tipo;

        if($tipo == 'web'){
            $fonte = FonteWeb::select('id', 'nome', 'nm_cidade as cidade', 'sg_estado as uf');
            $fonte->leftJoin('cidade', 'cidade.cd_cidade', '=', 'fonte_web.cd_cidade');
            $fonte->leftJoin('estado', 'estado.cd_estado', '=', 'fonte_web.cd_estado');
            $fonte->whereIn('id_situacao', [1, 2, 3]);
            $emissoras = $fonte->orderBy('sg_estado')->orderBy('nm_cidade')->orderBy('nome', 'asc')->get();
        }

        if($tipo == 'impresso'){
            $fonte = FonteImpressa::select('id', 'nome', 'nm_cidade as cidade', 'sg_estado as uf');
            $fonte->leftJoin('cidade', 'cidade.cd_cidade', '=', 'jornal_online.cd_cidade');
            $fonte->leftJoin('estado', 'estado.cd_estado', '=', 'jornal_online.cd_estado');
            $emissoras = $fonte->orderBy('sg_estado')->orderBy('nm_cidade')->orderBy('nome', 'asc')->get();
        }

        if($tipo == 'tv'){
            $fonte = ProgramaEmissoraWeb::select('id', 'nome_programa as nome', 'nm_cidade as cidade', 'sg_estado as uf');
            $fonte->leftJoin('cidade', 'cidade.cd_cidade', '=', 'programa_emissora_web.cd_cidade');
            $fonte->leftJoin('estado', 'estado.cd_estado', '=', 'programa_emissora_web.cd_estado');
            $emissoras = $fonte->orderBy('sg_estado')->orderBy('nm_cidade')->orderBy('nome', 'asc')->get();
        }

        if($tipo == 'radio'){
            $fonte = Emissora::select('id', 'nome_emissora as nome', 'nm_cidade as cidade', 'sg_estado as uf');
            $fonte->leftJoin('cidade', 'cidade.cd_cidade', '=', 'emissora_radio.cd_cidade');
            $fonte->leftJoin('estado', 'estado.cd_estado', '=', 'emissora_radio.cd_estado');
            $emissoras = $fonte->orderBy('sg_estado')->orderBy('nm_cidade')->orderBy('nome_emissora', 'asc')->get();
        }

        if($id_monitoramento > 0){

            $fontes = DB::table('monitoramento')->select($filtro)->where('id', $id_monitoramento)->first()->$filtro;
            $fontesArray = explode(',', $fontes);

            foreach ($emissoras as $key => $emissora) {
                $emissoras[$key]->fl_filtro = in_array($emissora->id, $fontesArray);
            }
        }        

        return response()->json($emissoras);
    }
}