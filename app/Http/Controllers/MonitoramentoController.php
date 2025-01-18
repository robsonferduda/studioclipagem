<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Mail;
use App\Utils;
use App\Models\FonteWeb;
use App\Models\Periodo;
use App\Models\Cliente;
use App\Models\Monitoramento;
use App\Models\MonitoramentoExecucao;
use App\Models\JornalImpresso;
use App\Models\JornalWeb;
use App\Models\NoticiaWeb;
use App\Models\ConteudoNoticiaWeb;
use App\Models\Fonte;
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
        $this->middleware('auth');        
        $this->carbon = new Carbon();
        Session::put('url','monitoramento');
    }

    public function index(Request $request)
    {
        $clientes = Cliente::orderBy('nome')->get();

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

            $url = 'monitoramento';

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
        $periodos = Periodo::orderBy('ordem')->get();
        $clientes = Cliente::orderBy('nome')->get();

        return view('monitoramento/novo', compact('clientes','periodos'));
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
        $noticias = $monitoramento->noticias;
    
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

        $sql = "SELECT 
                    n.id, n.id_fonte, n.url_noticia, n.data_insert, n.data_noticia, n.titulo_noticia, fw.nome
                FROM 
                    noticias_web n
                JOIN 
                    conteudo_noticia_web cnw ON cnw.id_noticia_web = n.id
                JOIN 
                    fonte_web fw ON fw.id = n.id_fonte 
                WHERE 1=1
                    AND n.$label_data BETWEEN '$dt_inicial' AND '$dt_final' ";

        $sql .= ($request->expressao) ? "AND  cnw.conteudo_tsv @@ to_tsquery('portuguese', '$request->expressao') " : '';
        $sql .= 'ORDER BY n.'.$label_data.' DESC';

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
                    pejo.id, id_jornal_online, link_pdf, dt_coleta, dt_pub, titulo, texto_extraido
                FROM 
                    edicao_jornal_online n
                JOIN 
                    pagina_edicao_jornal_online pejo 
                    ON pejo.id_edicao_jornal_online = n.id
                WHERE 1=1
                    AND n.$label_data BETWEEN '$dt_inicial' AND '$dt_final' ";

        $sql .= ($request->expressao) ? "AND  pejo.texto_extraido_tsv @@ to_tsquery('portuguese', '$request->expressao') " : '';
        $sql .= 'ORDER BY '.$label_data.' DESC';

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

        $sql .= ($request->expressao) ? "AND  n.transcricao_tsv @@ to_tsquery('portuguese', '$request->expressao') " : '';
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
                    n.id, id_programa_emissora_web, horario_start_gravacao, horario_end_gravacao, url_video, misc_data, transcricao, nome_programa
                FROM 
                    videos_programa_emissora_web n
                JOIN 
                    programa_emissora_web pew 
                    ON pew.id = n.id_programa_emissora_web
                WHERE 1=1
                    AND n.$label_data BETWEEN '$dt_inicial' AND '$dt_final' ";

        $sql .= ($request->expressao) ? "AND  n.transcricao_tsv @@ to_tsquery('portuguese', '$request->expressao') " : '';
        $sql .= 'ORDER BY n.'.$label_data.' DESC';

        $dados = DB::select($sql);

        return response()->json($dados);
    }

    public function getConteudo(Request $request)
    {
        switch ($request->tipo) {
            case 'web':
                $sql = "SELECT ts_headline('portuguese', conteudo , to_tsquery('portuguese', '$request->expressao'), 'HighlightAll=true, StartSel=<mark>, StopSel=</mark>') as texto
                        FROM conteudo_noticia_web 
                        WHERE id_noticia_web = ".$request->id;
                break;
            
            case 'impresso':
                $sql = "SELECT ts_headline('portuguese', texto_extraido , to_tsquery('portuguese', '$request->expressao'), 'HighlightAll=true, StartSel=<mark>, StopSel=</mark>') as texto
                        FROM pagina_edicao_jornal_online 
                        WHERE id = ".$request->id;
                break;

            case 'radio':
                $sql = "SELECT ts_headline('portuguese', transcricao , to_tsquery('portuguese', '$request->expressao'), 'HighlightAll=true, StartSel=<mark>, StopSel=</mark>') as texto
                        FROM gravacao_emissora_radio 
                        WHERE id = ".$request->id;
                break;

            case 'tv':
                $sql = "SELECT ts_headline('portuguese', transcricao , to_tsquery('portuguese', '$request->expressao'), 'HighlightAll=true, StartSel=<mark>, StopSel=</mark>') as texto
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

    public function executarWeb()
    {
        $dt_inicial = (Carbon::now())->format('Y-m-d')." 00:00:00";
        $dt_final = (Carbon::now())->format('Y-m-d')." 23:59:59";
        $data_inicio = date('Y-m-d H:i:s');
        $total_vinculado = 0;
        $tipo_midia = 2;

        $monitoramentos = Monitoramento::where('fl_ativo', true)->where('fl_web', true)->get();
        
        foreach ($monitoramentos as $key => $monitoramento) {

            try{
                $sql = "SELECT 
                            n.id, n.id_fonte, n.url_noticia, n.data_insert, n.data_noticia, n.titulo_noticia, fw.nome
                        FROM 
                            noticias_web n
                        JOIN 
                            conteudo_noticia_web cnw ON cnw.id_noticia_web = n.id
                        JOIN 
                            fonte_web fw ON fw.id = n.id_fonte 
                        WHERE 1=1
                            AND n.data_noticia BETWEEN '$dt_inicial' AND '$dt_final' 
                            AND cnw.conteudo_tsv @@ to_tsquery('portuguese', '$monitoramento->expressao') 
                            ORDER BY n.data_noticia DESC";

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

    public function executarImpresso()
    {
        $dt_inicial = (Carbon::now())->format('Y-m-d')." 00:00:00";
        $dt_final = (Carbon::now())->format('Y-m-d')." 23:59:59";
        $data_inicio = date('Y-m-d H:i:s');
        $total_vinculado = 0;
        $tipo_midia = 2;

        $monitoramentos = Monitoramento::where('fl_ativo', true)->where('fl_web', true)->get();
        
        foreach ($monitoramentos as $key => $monitoramento) {

            try{
                $sql = "SELECT 
                        pejo.id, id_jornal_online, link_pdf, dt_coleta, dt_pub, titulo, texto_extraido
                    FROM 
                        edicao_jornal_online n
                    JOIN 
                        pagina_edicao_jornal_online pejo 
                        ON pejo.id_edicao_jornal_online = n.id
                    WHERE 1=1
                        AND n.dt_coleta BETWEEN '$dt_inicial' AND '$dt_final' 
                        AND pejo.texto_extraido_tsv @@ to_tsquery('portuguese', '$monitoramento->expressao')
                        ORDER BY dt_coleta DESC";

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

    public function executarRadio()
    {
        $dt_inicial = (Carbon::now())->format('Y-m-d')." 00:00:00";
        $dt_final = (Carbon::now())->format('Y-m-d')." 23:59:59";
        $data_inicio = date('Y-m-d H:i:s');
        $total_vinculado = 0;
        $tipo_midia = 3;

        $monitoramentos = Monitoramento::where('fl_ativo', true)->where('fl_web', true)->get();
        
        foreach ($monitoramentos as $key => $monitoramento) {

            try{
                $sql = "SELECT 
                            n.id, id_emissora, data_hora_inicio, data_hora_fim, path_s3, nome_emissora
                        FROM 
                            gravacao_emissora_radio n
                        JOIN 
                            emissora_radio er 
                        ON er.id = n.id_emissora
                        WHERE 1=1
                            AND n.data_hora_inicio BETWEEN '$dt_inicial' AND '$dt_final'
                            AND  n.transcricao_tsv @@ to_tsquery('portuguese', '$monitoramento->expressao')
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

        $monitoramentos = Monitoramento::where('fl_ativo', true)->where('fl_web', true)->get();
        
        foreach ($monitoramentos as $key => $monitoramento) {

            try{
                $sql = "SELECT 
                        n.id, id_programa_emissora_web, horario_start_gravacao, horario_end_gravacao, url_video, misc_data, transcricao, nome_programa
                        FROM 
                        videos_programa_emissora_web n
                        JOIN 
                        programa_emissora_web pew 
                        ON pew.id = n.id_programa_emissora_web
                        WHERE 1=1
                        AND n.horario_start_gravacao BETWEEN '$dt_inicial' AND '$dt_final'
                        AND n.transcricao_tsv @@ to_tsquery('portuguese', '$monitoramento->expressao')
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

    public function executar($id)
    {
        $dt_inicial = (Carbon::now())->format('Y-m-d')." 00:00:00";
        $dt_final = (Carbon::now())->format('Y-m-d')." 23:59:59";

        $data_inicio = date('Y-m-d H:i:s');
        $total_vinculado = 0;

        $monitoramento = Monitoramento::find($id);

        try{
        
            if($monitoramento->fl_web) {

                $tipo_midia = 2; //Web

                $sql = "SELECT 
                            n.id, n.id_fonte, n.url_noticia, n.data_insert, n.data_noticia, n.titulo_noticia, fw.nome
                        FROM 
                            noticias_web n
                        JOIN 
                            conteudo_noticia_web cnw ON cnw.id_noticia_web = n.id
                        JOIN 
                            fonte_web fw ON fw.id = n.id_fonte 
                        WHERE 1=1
                            AND n.data_noticia BETWEEN '$dt_inicial' AND '$dt_final' 
                            AND cnw.conteudo_tsv @@ to_tsquery('portuguese', '$monitoramento->expressao') 
                            ORDER BY n.data_noticia DESC";

                $dados = DB::select($sql);
                $total_associado = $this->associar($dados, $tipo_midia, $monitoramento);
                $total_vinculado += $total_associado;
            }

            if($monitoramento->fl_impresso) {

                $tipo_midia = 1; //Impresso

                $sql = "SELECT 
                        pejo.id, id_jornal_online, link_pdf, dt_coleta, dt_pub, titulo, texto_extraido
                    FROM 
                        edicao_jornal_online n
                    JOIN 
                        pagina_edicao_jornal_online pejo 
                        ON pejo.id_edicao_jornal_online = n.id
                    WHERE 1=1
                        AND n.dt_coleta BETWEEN '$dt_inicial' AND '$dt_final' 
                        AND pejo.texto_extraido_tsv @@ to_tsquery('portuguese', '$monitoramento->expressao')
                        ORDER BY dt_coleta DESC";

                $dados = DB::select($sql);

                $total_associado = $this->associar($dados, $tipo_midia, $monitoramento);
                $total_vinculado += $total_associado;

                $total_vinculado = count($dados) + $total_vinculado;
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
                        WHERE 1=1
                        AND n.data_hora_inicio BETWEEN '$dt_inicial' AND '$dt_final'
                        AND  n.transcricao_tsv @@ to_tsquery('portuguese', '$monitoramento->expressao')
                        ORDER BY n.data_hora_inicio DESC";

                $dados = DB::select($sql);
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
                        WHERE 1=1
                        AND n.horario_start_gravacao BETWEEN '$dt_inicial' AND '$dt_final'
                        AND n.transcricao_tsv @@ to_tsquery('portuguese', '$monitoramento->expressao')
                        ORDER BY n.horario_start_gravacao DESC";

                $dados = DB::select($sql);
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

            Flash::success('<i class="fa fa-check"></i> Monitoramento executado manualmente retornou <strong>'. $total_vinculado.'</strong> registros');

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

            $noticia_cliente = NoticiaCliente::where('noticia_id', $noticia->id)->where('tipo_id', $tipo)->first();
            
            if(!$noticia_cliente){

                $dados = array('cliente_id' => $monitoramento->id_cliente,
                            'tipo_id'    => $tipo,
                            'noticia_id' => $noticia->id,
                            'monitoramento_id' => $monitoramento->id);

                NoticiaCliente::create($dados);
                $total_vinculado++;

                if($tipo == 2){
                    $noticia_web = NoticiaWeb::find($noticia->id);
                    $noticia_web->screenshot = true;
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
        $clientes = Cliente::orderBy("nome")->get();
        $fontes = array();
        $cidades_selecionadas = array();

        $monitoramento = Monitoramento::find($id);

        if($monitoramento->fl_web){

            if($monitoramento->filtro_web){
                $cidades_selecionadas = explode(",", $monitoramento->filtro_web);
            }

            $fontes_disponiveis = DB::select("SELECT id, nome, t2.sg_estado FROM fonte_web t1 LEFT JOIN estado t2 ON t2.cd_estado = t1.cd_estado WHERE id_situacao = 1 ORDER BY t2.sg_estado, nome"); 
            
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
            
        }

        return view('monitoramento/editar', compact('monitoramento','clientes','periodos','fontes'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $monitoramento = Monitoramento::find($id);

        $fl_web = $request->fl_web == true ? true : false;
        $fl_tv = $request->fl_tv == true ? true : false;
        $fl_impresso = $request->fl_impresso == true ? true : false;
        $fl_radio = $request->fl_radio == true ? true : false;

        $dt_inicio = ($request->dt_inicio) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicio)->format('Y-m-d')." 00:00:00" : date("Y-m-d H:i:s");
        $dt_fim = ($request->dt_fim) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_fim)->format('Y-m-d')." 00:00:00" : date("Y-m-d H:i:s");

        $hora_inicio = ($request->hora_inicio) ? $this->carbon->createFromFormat('H:i', $request->hora_inicio)->format('H:i') : date("H:i");
        $hora_fim = ($request->hora_fim) ? $this->carbon->createFromFormat('H:i', $request->hora_fim)->format('H:i') : date("H:i");

        $request->merge(['fl_web' => $fl_web]);
        $request->merge(['fl_tv' => $fl_tv]);
        $request->merge(['fl_impresso' => $fl_impresso]);
        $request->merge(['fl_radio' => $fl_radio]);

        $request->merge(['dt_inicio' => $dt_inicio]);
        $request->merge(['dt_fim' => $dt_fim]);
        $request->merge(['hora_inicio' => $hora_inicio]);
        $request->merge(['hora_fim' => $hora_fim]);

        $filtro_fontes = ($request->fontes) ? implode(',', $request->fontes) : '';

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

        return redirect('monitoramento')->withInput();
    }
}