<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use Storage;
use App\Utils;
use App\Mail\BoletimMail;
use Carbon\Carbon;
use App\Models\Area;
use App\Models\AreaCliente;
use App\Models\BoletimEnvio;
use App\Models\Boletim;
use App\Models\BoletimNoticias;
use App\Models\SituacaoBoletim;
use App\Models\Cliente;
use App\Models\NoticiaImpresso;
use App\Models\NoticiaWeb;
use App\Models\NoticiaRadio;
use App\Models\NoticiaTv;
use App\Models\NoticiaCliente;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Http;

class BoletimController extends Controller
{
    private $data_atual;
    private $carbon;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['detalhes','enviar','visualizar']]);
        Session::put('url','boletins');
        $this->data_atual = session('data_atual');
        $this->carbon = new Carbon();
    }

    public function index(Request $request)
    {
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $cliente_selecionado = ($request->cliente) ? $request->cliente : null;
        $flag = $request->flag;
        $situacao = $request->id_situacao;

        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        $boletim = Boletim::query();

        $boletim->when($cliente_selecionado, function ($q) use ($cliente_selecionado) {
            return $q->where('id_cliente', $cliente_selecionado);
        });

        $boletim->when($flag, function ($q) use ($flag) {
            return $q->where($flag, true);
        });

        $boletim->when($situacao, function ($q) use ($situacao) {
            return $q->where('id_situacao', $situacao);
        });

        $boletim->whereBetween('dt_boletim', [$dt_inicial." 00:00:00", $dt_final." 23:59:59"]);

        $boletins = $boletim->get();

        return view('boletim/index',compact('boletins','clientes','dt_inicial','dt_final','cliente_selecionado','flag','situacao'));
    }

    public function noticias(Request $request)
    {   
        $noticias = array();
        $flag_tv = $request->flag_tv == "true" ? true : false;
        $flag_impresso = $request->flag_impresso == "true" ? true : false;
        $flag_web = $request->flag_web == "true" ? true : false;
        $flag_radio = $request->flag_radio == "true" ? true : false;
        $flag_enviadas = $request->flag_enviadas == "true" ? true : false;
        $tipo_data = $request->tipo_data;

        switch ($tipo_data) {

            case 'dt_cadastro':

                $data_web = 'data_insert';
                $data_radio = 'dt_cadastro';
                $data_tv = 'dt_cadastro';
                $data_impresso = 'created_at';
                break;

            case 'dt_clipagem':
                
                $data_web = 'data_noticia';
                $data_radio = 'dt_clipagem';
                $data_tv = 'dt_noticia';
                $data_impresso = 'dt_clipagem';
                break;

            default:
                
                $data_web = 'created_at';
                $data_radio = 'created_at';
                $data_tv = 'created_at';
                $data_impresso = 'created_at';
                break;
        }

        //Notícias de Web
        $sql_web = "SELECT t1.id, 
                    titulo_noticia AS titulo, 
                    sinopse,
                    'web' as tipo, 
                    url_noticia AS link,
                    TO_CHAR(data_noticia, 'DD/MM/YYYY') AS data_noticia,
                    TO_CHAR(data_insert, 'DD/MM/YYYY') AS data_coleta,
                    t2.nome as fonte,
                    '' as programa,
                    t4.id_boletim as id_boletim,
                    t3.fl_enviada as flag
                FROM noticias_web t1
                JOIN fonte_web t2 ON t2.id = t1.id_fonte
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id
                LEFT JOIN boletim_noticia t4 ON t4.id_noticia = t3.noticia_id AND t4.id_tipo = 2 AND t4.id_boletim = $request->id_boletim
                WHERE 1=1
                AND t1.deleted_at IS NULL 
                AND t3.deleted_at IS NULL ";

        if ($request->has('dt_inicial') && $request->has('dt_final')) {
            $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d');
            $dt_final = $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d');
            $sql_web .= " AND $data_web BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'";
        }

        if( $flag_enviadas) {
            $sql_web .= " AND t3.fl_enviada = false";
        } 

        if ($request->has('cliente')) {
            $cliente = $request->cliente;
            $sql_web .= " AND t3.cliente_id = $cliente";
        }
       
        $sql_web .= " ORDER BY data_noticia DESC";
        
        $noticias_web = ($flag_web) ? DB::select($sql_web) : array();

        //Notícias de Impresso
        $sql_impresso = "SELECT t1.id, 
                    t1.titulo, 
                    sinopse,
                    'impresso' as tipo, 
                    '' AS link,
                    TO_CHAR(dt_clipagem, 'DD/MM/YYYY') AS data_noticia,
                    TO_CHAR(t1.created_at, 'DD/MM/YYYY') AS data_coleta,
                    t2.nome as fonte,
                    '' as programa,
                    t4.id_boletim as id_boletim,
                    t3.fl_enviada as flag
                FROM noticia_impresso t1
                JOIN jornal_online t2 ON t2.id = t1.id_fonte
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id
                LEFT JOIN boletim_noticia t4 ON t4.id_noticia = t3.noticia_id AND id_tipo = 1 AND t4.id_boletim = $request->id_boletim
                WHERE 1=1
                AND t1.deleted_at IS NULL 
                AND t3.deleted_at IS NULL ";

        if ($request->has('dt_inicial') && $request->has('dt_final')) {
            $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d');
            $dt_final = $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d');
            $sql_impresso .= " AND t1.$data_impresso BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'";
        }

        if( $flag_enviadas) {
            $sql_impresso .= " AND t3.fl_enviada = false";
        }

        if ($request->has('cliente')) {
            $cliente = $request->cliente;
            $sql_impresso .= " AND t3.cliente_id = $cliente";
        }
       
        $sql_impresso .= " ORDER BY dt_clipagem DESC";
        
        $noticias_impresso = ($flag_impresso) ? DB::select($sql_impresso) : array();

        //Notícias de Rádio

        $sql_radio = "SELECT t1.id, 
                    t1.titulo, 
                    sinopse,
                    'radio' as tipo, 
                    '' AS link,
                    TO_CHAR(dt_clipagem, 'DD/MM/YYYY') AS data_noticia,
                    TO_CHAR(dt_cadastro, 'DD/MM/YYYY') AS data_coleta,
                    t2.nome_emissora as fonte,
                    t5.nome_programa as programa,
                    t4.id_boletim as id_boletim,
                    t3.fl_enviada as flag
                FROM noticia_radio t1
                JOIN emissora_radio t2 ON t2.id = t1.emissora_id
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id
                LEFT JOIN boletim_noticia t4 ON t4.id_noticia = t3.noticia_id AND id_tipo = 3 AND t4.id_boletim = $request->id_boletim
                LEFT JOIN programa_emissora_radio t5 ON t5.id = t1.programa_id
                WHERE 1=1
                AND t1.deleted_at IS NULL 
                AND t3.deleted_at IS NULL ";

        if ($request->has('dt_inicial') && $request->has('dt_final')) {
            $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d');
            $dt_final = $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d');
            $sql_radio .= " AND t1.$data_radio BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'";
        }

        if( $flag_enviadas) {
            $sql_radio .= " AND t3.fl_enviada = false";
        }

        if ($request->has('cliente')) {
            $cliente = $request->cliente;
            $sql_radio .= " AND t3.cliente_id = $cliente";
        }
       
        $sql_radio .= " ORDER BY dt_cadastro DESC";

        $noticias_radio = ($flag_radio) ? DB::select($sql_radio) : array();

        //Notícias de TV

        $sql_tv = "SELECT t1.id, 
                    '' AS titulo, 
                    sinopse,
                    'tv' as tipo, 
                    '' AS link,
                    TO_CHAR(dt_noticia, 'DD/MM/YYYY') AS data_noticia,
                    TO_CHAR(dt_cadastro, 'DD/MM/YYYY') AS data_coleta,
                    t2.nome_emissora as fonte,
                    t5.nome_programa as programa,
                    t4.id_boletim as id_boletim,
                    t3.fl_enviada as flag
                FROM noticia_tv t1
                JOIN emissora_web t2 ON t2.id = t1.emissora_id
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id
                LEFT JOIN boletim_noticia t4 ON t4.id_noticia = t3.noticia_id AND id_tipo = 4 AND t4.id_boletim = $request->id_boletim
                LEFT JOIN programa_emissora_web t5 ON t5.id = t1.programa_id
                WHERE 1=1
                AND t1.deleted_at IS NULL 
                AND t3.deleted_at IS NULL ";

        if ($request->has('dt_inicial') && $request->has('dt_final')) {
            $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d');
            $dt_final = $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d');
            $sql_tv .= " AND $data_tv BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'";
        }

        if( $flag_enviadas) {
            $sql_tv .= " AND t3.fl_enviada = false";
        }

        if ($request->has('cliente')) {
            $cliente = $request->cliente;
            $sql_tv .= " AND t3.cliente_id = $cliente";
        }
       
        $sql_tv .= " ORDER BY dt_noticia DESC";

        $noticias_tv = ($flag_tv) ? DB::select($sql_tv) : array();

        if(!$flag_impresso && !$flag_web && !$flag_radio && !$flag_tv) {
            $noticias_tv = DB::select($sql_tv);
            $noticias_impresso = DB::select($sql_impresso);
            $noticias_web = DB::select($sql_web);
            $noticias_radio = DB::select($sql_radio);
        }
            
        $noticias = array_merge($noticias_web, $noticias_impresso, $noticias_tv, $noticias_radio);

        return response()->json($noticias);
    }

    public function adicionarNoticia(Request $request)
    {
        $tipo = null;
        $boletim = Boletim::find($request->id_boletim);

        switch ($request->tipo) {
            case 'web':
                $id_tipo = 2;
                $noticia = NoticiaWeb::find($request->id_noticia);
                break;
            case 'impresso':
                $id_tipo = 1;
                $noticia = NoticiaImpresso::find($request->id_noticia);
                break;
            case 'radio':
                $id_tipo = 3;
                $noticia = NoticiaRadio::find($request->id_noticia);
                break;
             case 'tv':
                $id_tipo = 4;
                $noticia = NoticiaTv::find($request->id_noticia);
                break;
            default:
                return response()->json(['error' => 'Tipo de notícia inválido'], 400);
        }

        $boletim_noticias = BoletimNoticias::where('id_boletim', $boletim->id)->where('id_noticia',$request->id_noticia)->first();
                
        if($boletim && $noticia) {
            // Verifica se existe boletim
            if (!$boletim_noticias) {
                $boletim_noticias = new BoletimNoticias();
                $boletim_noticias->id_boletim = $boletim->id;
                $boletim_noticias->id_tipo = $id_tipo;
                $boletim_noticias->id_noticia = $request->id_noticia;
            }else{
                // Se já existe, apenas atualiza a notícia
                if ($boletim_noticias->id_noticia != $request->id_noticia) {
                    $boletim_noticias->id_noticia = $request->id_noticia;
                    $boletim_noticias->id_tipo = $id_tipo;
                }
            }

            //Marca que a notícia já foi enviada para o cliente
            $noticia_cliente = NoticiaCliente::where('noticia_id', $request->id_noticia)->where('cliente_id', $boletim->id_cliente)->where('tipo_id', $id_tipo)->first();
            $noticia_cliente->fl_enviada =  true;
            $noticia_cliente->save();

            $boletim_noticias->save();
        }        
    }

    public function removerNoticia(Request $request)
    {
        $boletim = Boletim::find($request->id_boletim);
        $boletim_noticias = BoletimNoticias::where('id_boletim', $boletim->id)->where('id_noticia', $request->id_noticia)->withTrashed()->first();

        if($boletim_noticias) {
            // Verifica se existe boletim
            if ($boletim_noticias) {
                $boletim_noticias->forceDelete();
            }
        }        
    }

    public function cadastrar()
    {   
        $clientes = Cliente::orderBy('nome')->get();

        return view('boletim/cadastrar', compact('clientes'));
    }

    public function editar($id)
    {   
        $dt_inicial = date("Y-m-d");
        $dt_final = date("Y-m-d");

        $boletim = Boletim::find($id);
        $clientes = Cliente::orderBy('nome')->get();
        $situacoes = SituacaoBoletim::all();

        return view('boletim/editar', compact('boletim','clientes','situacoes','dt_inicial','dt_final'));
    }

    public function store(Request $request)
    {
        $dt_boletim = ($request->dt_boletim) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_boletim)->format('Y-m-d') : date("Y-m-d");
        $request->merge(['dt_boletim' => $dt_boletim]);
        $request->merge(['id_usuario' => Auth::user()->id ]);

        $fl_web = $request->fl_web == true ? true : false;
        $fl_tv = $request->fl_tv == true ? true : false;
        $fl_impresso = $request->fl_impresso == true ? true : false;
        $fl_radio = $request->fl_radio == true ? true : false;

        $request->merge(['fl_web' => $fl_web]);
        $request->merge(['fl_tv' => $fl_tv]);
        $request->merge(['fl_impresso' => $fl_impresso]);
        $request->merge(['fl_radio' => $fl_radio]);

        try {
            
            $boletim = Boletim::create($request->all());

            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados inseridos com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-times"></i> Ocorreu um erro ao inserir o registro');
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('boletim/editar/'.$boletim->id)->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('boletim/cadastrar')->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $boletim = Boletim::find($id);

        try {
            
            $boletim->update($request->all());

            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados inseridos com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-times"></i> Ocorreu um erro ao inserir o registro');
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('boletim/editar/'.$boletim->id)->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('boletim/cadastrar')->withInput();
        }
    }

    public function getDadosBoletim($id)
    {
        $dados = array();
        $boletim = Boletim::where('id', $id)->first(); 

        $noticias_impresso = array();
        $noticias_web = array(); 
        $noticias_radio = array(); 
        $noticias_tv = array();  

        foreach ($boletim->noticiasImpresso()->get() as $key => $noticia_impresso) {

            $area = (NoticiaCliente::where('noticia_id', $noticia_impresso->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 1)->first()->area) ?
                    Area::where('id', NoticiaCliente::where('noticia_id', $noticia_impresso->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 1)->first()->area)->first()->descricao :
                    '';

            $ordem = (NoticiaCliente::where('noticia_id', $noticia_impresso->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 1)->first()->area) ?
                    AreaCliente::where('cliente_id',$boletim->id_cliente)->where('area_id', NoticiaCliente::where('noticia_id', $noticia_impresso->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 1)->first()->area)->first()->ordem :
                    '';

            $noticias_impresso[] = array('id' => $noticia_impresso->id,
                                         'titulo' => $noticia_impresso->titulo,
                                         'data_noticia' => $noticia_impresso->dt_clipagem,
                                         'fonte' => ($noticia_impresso->fonte) ? $noticia_impresso->fonte->nome : 'Fonte não informada',
                                         'secao' => ($noticia_impresso->secao) ? $noticia_impresso->secao->ds_sessao : null,
                                         'area' => $area,
                                         'ordem' => $ordem,
                                         'tipo' => 'impresso',
                                         'programa' => '',
                                         'duracao' => '',
                                         'sinopse' => strip_tags(str_replace('Sinopse 1 - ', '', $noticia_impresso->sinopse)),
                                         'url_noticia' => null,
                                         'path_midia' => 'img/noticia-impressa/'.$noticia_impresso->ds_caminho_img,
                                         'erro' => '');
        }

        foreach ($boletim->noticiasWeb()->get() as $key => $noticia_web) {

            $erro = '';

            if($noticia_web->ds_caminho_img){
                $path = 'img/noticia-web/'.$noticia_web->ds_caminho_img;
            }else{

                $path = 'noticia/web/print/'.$noticia_web->id;

                /*
                try {
                    $path = Storage::disk('s3')->temporaryUrl($noticia_web->path_screenshot, '+30 minutes');
                } catch (Exception $e) {
                    $path = '';
                    $erro = 'Erro na imagem';
                } catch (\Throwable $e){
                    $path = '';
                    $erro = 'Erro na imagem';
                }*/
            }

            $area = (NoticiaCliente::where('noticia_id', $noticia_web->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 2)->first() and NoticiaCliente::where('noticia_id', $noticia_web->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 2)->first()->area) ?
                    Area::where('id', NoticiaCliente::where('noticia_id', $noticia_web->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 2)->first()->area)->first()->descricao :
                    '';

            $ordem = (NoticiaCliente::where('noticia_id', $noticia_web->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 2)->first() and NoticiaCliente::where('noticia_id', $noticia_web->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 2)->first()->area) ?
                    AreaCliente::where('cliente_id',$boletim->id_cliente)->where('area_id', NoticiaCliente::where('noticia_id', $noticia_web->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 2)->first()->area)->first()->ordem :
                    '';

            $noticias_web[] = array('id' => $noticia_web->id,
                                    'titulo' => $noticia_web->titulo_noticia,
                                    'data_noticia' => $noticia_web->data_noticia,
                                    'fonte' => ($noticia_web->fonte) ? $noticia_web->fonte->nome : 'Fonte não informada',
                                    'secao' => ($noticia_web->secao) ? $noticia_web->secao->ds_sessao : null,
                                    'area' => $area,
                                    'ordem' => $ordem,
                                    'tipo' => 'web',
                                    'programa' => '',
                                    'duracao' => '',
                                    'sinopse' => strip_tags(str_replace('Sinopse 1 - ', '', $noticia_web->sinopse)),
                                    'url_noticia' => $noticia_web->url_noticia, 
                                    'path_midia' => $path,
                                    'erro' => $erro);
        }

        foreach ($boletim->noticiasRadio()->get() as $key => $noticia_radio) {

            $area = (NoticiaCliente::where('noticia_id', $noticia_radio->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 3)->first()->area) ?
                    Area::where('id', NoticiaCliente::where('noticia_id', $noticia_radio->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 3)->first()->area)->first()->descricao :
                    '';

            $ordem = (NoticiaCliente::where('noticia_id', $noticia_radio->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 3)->first()->area) ?
                    AreaCliente::where('cliente_id',$boletim->id_cliente)->where('area_id', NoticiaCliente::where('noticia_id', $noticia_radio->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 3)->first()->area)->first()->ordem :
                    '';
            
            $noticias_radio[] = array('id' => $noticia_radio->id,
                                        'titulo' => $noticia_radio->titulo,
                                         'data_noticia' => $noticia_radio->dt_clipagem,
                                         'fonte' => ($noticia_radio->emissora) ? $noticia_radio->emissora->nome_emissora : 'Fonte não informada',
                                         'secao' => ($noticia_radio->secao) ? $noticia_radio->secao->ds_sessao : null,
                                         'area' => $area,
                                         'ordem' => $ordem,
                                         'tipo' => 'radio',
                                         'programa' => ($noticia_radio->programa) ? $noticia_radio->programa->nome_programa : null,
                                         'duracao' => ($noticia_radio->duracao) ? $noticia_radio->duracao : 'Não informado',
                                         'sinopse' => strip_tags(str_replace('Sinopse 1 - ', '', $noticia_radio->sinopse)),
                                         'url_noticia' => null,
                                         'path_midia' => 'audio/noticia-radio/'.$noticia_radio->ds_caminho_audio,
                                         'erro' => '');

        }

        foreach ($boletim->noticiasTv()->get() as $key => $noticia_tv) {

            $area = (NoticiaCliente::where('noticia_id', $noticia_tv->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 4)->first()->area) ?
                    Area::where('id', NoticiaCliente::where('noticia_id', $noticia_tv->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 4)->first()->area)->first()->descricao :
                    '';

            $ordem = (NoticiaCliente::where('noticia_id', $noticia_tv->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 4)->first()->area) ?
                    AreaCliente::where('cliente_id',$boletim->id_cliente)->where('area_id', NoticiaCliente::where('noticia_id', $noticia_tv->id)->where('cliente_id',$boletim->id_cliente)->where('tipo_id', 4)->first()->area)->first()->ordem :
                    '';
            
            $noticias_tv[] = array('id' => $noticia_tv->id,
                                    'titulo' => $noticia_tv->titulo,
                                         'data_noticia' => $noticia_tv->dt_noticia,
                                         'fonte' => ($noticia_tv->emissora) ? $noticia_tv->emissora->nome_emissora : 'Fonte não informada',
                                         'secao' => ($noticia_tv->secao) ? $noticia_tv->secao->ds_sessao : null,
                                         'area' => $area,
                                         'ordem' => $ordem,
                                         'tipo' => 'tv',
                                         'programa' => ($noticia_tv->programa) ? $noticia_tv->programa->nome_programa : null,
                                         'duracao' => ($noticia_tv->duracao) ? $noticia_tv->duracao : 'Não informado',
                                         'sinopse' => strip_tags(str_replace('Sinopse 1 - ', '', $noticia_tv->sinopse)),
                                         'url_noticia' => null,
                                         'path_midia' => 'video/noticia-tv/'.$noticia_tv->ds_caminho_video,
                                         'erro' => '');

        }

        $dados['impresso'] = $noticias_impresso;
        $dados['web'] = $noticias_web;
        $dados['radio'] = $noticias_radio;
        $dados['tv'] = $noticias_tv;
        
        return $dados;

    }

    public function detalhes($id)
    {   
        $boletim = Boletim::where('id', $id)->first();    
        $dados = $this->getDadosBoletim($id);     

        $cliente = Cliente::where('id', $boletim->id_cliente)->first();
        $fl_print = $cliente->fl_print;

        $noticias_impresso = $dados['impresso'];
        $noticias_web = $dados['web']; 
        $noticias_radio = $dados['radio']; 
        $noticias_tv = $dados['tv']; 

        $noticias = array_merge($noticias_web, $noticias_impresso, $noticias_tv, $noticias_radio);

        usort($noticias, function ($a, $b) {
            // Ordena por area
            $areaCompare = strcmp($a['ordem'] ?? '', $b['ordem'] ?? '');
            if ($areaCompare !== 0) {
                return $areaCompare;
            }

            // Depois por tipo
            $tipoCompare = strcmp($a['tipo'] ?? '', $b['tipo'] ?? '');
            if ($tipoCompare !== 0) {
                return $tipoCompare;
            }

            // Por fim, data_noticia (mais recente primeiro)
            return strtotime($b['data_noticia']) <=> strtotime($a['data_noticia']);
        });

        if($boletim->id_cliente == 307 or $boletim->id_cliente == 217){
            $dados = $noticias;
            return view('boletim/detalhes-area', compact('boletim','dados','fl_print'));
        }else{
            return view('boletim/detalhes', compact('boletim','noticias_impresso','noticias_web','noticias_radio','noticias_tv','fl_print'));
        }      
    }

    public function visualizar($id)
    {   
        $boletim = Boletim::where('id', $id)->first(); 
        $boletim->total_views = $boletim->total_views + 1;
        $boletim->save();

        $cliente = Cliente::where('id', $boletim->id_cliente)->first();
        $fl_print = $cliente->fl_print;
        $fl_texto_logo = $cliente->fl_texto_logo;

        $dados = $this->getDadosBoletim($id);

        $noticias_impresso = $dados['impresso'];
        $noticias_web = $dados['web']; 
        $noticias_radio = $dados['radio']; 
        $noticias_tv = $dados['tv']; 

        $noticias = array_merge($noticias_web, $noticias_impresso, $noticias_tv, $noticias_radio);

        usort($noticias, function ($a, $b) {
            // Ordena por area
            $areaCompare = strcmp($a['ordem'] ?? '', $b['ordem'] ?? '');
            if ($areaCompare !== 0) {
                return $areaCompare;
            }

            // Depois por tipo
            $tipoCompare = strcmp($a['tipo'] ?? '', $b['tipo'] ?? '');
            if ($tipoCompare !== 0) {
                return $tipoCompare;
            }

            // Por fim, data_noticia (mais recente primeiro)
            return strtotime($b['data_noticia']) <=> strtotime($a['data_noticia']);
        });

        if($boletim->id_cliente == 307 or $boletim->id_cliente == 217){
            $dados = $noticias;
            return view('boletim/visualizar-area', compact('boletim','dados','fl_print','fl_texto_logo'));
        }else{
            return view('boletim/visualizar', compact('dados','boletim','noticias_impresso','noticias_web','noticias_radio','noticias_tv','fl_print','fl_texto_logo'));
        }
    }

    public function outlook($id)
    {   
        $boletim = Boletim::where('id', $id)->first(); 
        $boletim->total_views_email = $boletim->total_views_email + 1;
        $boletim->save();

        $cliente = Cliente::where('id', $boletim->id_cliente)->first();
        $fl_print = $cliente->fl_print;
        $fl_texto_logo = $cliente->fl_texto_logo;

        $dados = $this->getDadosBoletim($id);

        $noticias_impresso = $dados['impresso'];
        $noticias_web = $dados['web']; 
        $noticias_radio = $dados['radio']; 
        $noticias_tv = $dados['tv'];

        $noticias = array_merge($noticias_web, $noticias_impresso, $noticias_tv, $noticias_radio);

        usort($noticias, function ($a, $b) {
            // Ordena por area
            $areaCompare = strcmp($a['ordem'] ?? '', $b['ordem'] ?? '');
            if ($areaCompare !== 0) {
                return $areaCompare;
            }

            // Depois por tipo
            $tipoCompare = strcmp($a['tipo'] ?? '', $b['tipo'] ?? '');
            if ($tipoCompare !== 0) {
                return $tipoCompare;
            }

            // Por fim, data_noticia (mais recente primeiro)
            return strtotime($b['data_noticia']) <=> strtotime($a['data_noticia']);
        });

        if($boletim->id_cliente == 307 or $boletim->id_cliente == 217){
            $dados = $noticias;
            return view('boletim/outlook-area', compact('boletim','dados','fl_print','fl_texto_logo'));
        }else{
            return view('boletim/outlook', compact('dados','boletim','noticias_impresso','noticias_web','noticias_radio','noticias_tv','fl_print','fl_texto_logo'));
        }
            
    }

    public function resumo($id)
    {   
        $boletim = Boletim::where('id', $id)->first();             
        return view('boletim/resumo', compact('boletim'));
    }

    public function enviar($id)
    {
        $boletim = Boletim::where('id', $id)->first();

        $emails = $boletim->cliente->emails;

        $lista = explode(",",$emails);

        $lista_email[] = array('email' => 'robsonferduda@gmail.com', 'fl_envio' => BoletimEnvio::where('id_boletim', $id)->where('ds_email', 'robsonferduda@gmail.com')->first());

        for ($i=0; $i < count($lista); $i++) { 
            $lista_email[] = array('email' => trim($lista[$i]), 
                                   'fl_envio' => (BoletimEnvio::where('id_boletim', $id)->where('ds_email', trim($lista[$i]))->where('id_situacao', 2)->first()) ? true : false);
        }
        
        return view('boletim/lista-envio', compact('boletim', 'lista_email'));
    }

    public function enviarLista(Request $request)
    {
        $logs = array();
        $detalhe = '';
        $boletim = Boletim::where('id', $request->id)->first();  

        $cliente = Cliente::where('id', $boletim->id_cliente)->first();
        $fl_print = $cliente->fl_print;
        $fl_texto_logo = $cliente->fl_texto_logo;

        $dados = $this->getDadosBoletim($request->id);

        $noticias_impresso = $dados['impresso'];
        $noticias_web = $dados['web']; 
        $noticias_radio = $dados['radio']; 
        $noticias_tv = $dados['tv'];   
        
        $noticias = array_merge($noticias_web, $noticias_impresso, $noticias_tv, $noticias_radio);

        usort($noticias, function ($a, $b) {
            // Ordena por area
            $areaCompare = strcmp($a['ordem'] ?? '', $b['ordem'] ?? '');
            if ($areaCompare !== 0) {
                return $areaCompare;
            }

            // Depois por tipo
            $tipoCompare = strcmp($a['tipo'] ?? '', $b['tipo'] ?? '');
            if ($tipoCompare !== 0) {
                return $tipoCompare;
            }

            // Por fim, data_noticia (mais recente primeiro)
            return strtotime($b['data_noticia']) <=> strtotime($a['data_noticia']);
        });

        if($boletim->id_cliente == 307 or $boletim->id_cliente == 217){

            $view = 'boletim.outlook-area';

            $data = array("dados" => $noticias, 
                            "fl_print" => $fl_print,
                            "fl_texto_logo" => $fl_texto_logo,
                           "boletim" => $boletim);

              $htmlContent = view('boletim.outlook-area', [
                'boletim' => $boletim,
                'dados' => $noticias,
                'fl_print' => $fl_print,
                'fl_texto_logo' => $fl_texto_logo
            ])->render();

        }else{

            $view = 'boletim.outlook';
            $data = array("noticias_impresso"=> $noticias_impresso,
                      "noticias_web" => $noticias_web,
                      "noticias_radio" => $noticias_radio,
                      "noticias_tv" => $noticias_tv, 
                      "fl_print" => $fl_print,
                      'fl_texto_logo' => $fl_texto_logo,
                      "boletim" => $boletim);

              $htmlContent = view('boletim.outlook', [
                'boletim' => $boletim,
                'noticias_impresso' => $noticias_impresso,
                'noticias_web' => $noticias_web,
                'noticias_radio' => $noticias_radio,
                'noticias_tv' => $noticias_tv,
                'fl_print' => $fl_print,
                'fl_texto_logo' => $fl_texto_logo
            ])->render();
        }             

        $emails = $request->emails;        
       

        for ($i=0; $i < count($emails); $i++) { 

            $boletim_envio = new \App\Models\BoletimEnvio();
            $boletim_envio->id_boletim = $boletim->id;  
            $boletim_envio->ds_email = $emails[$i];
            $boletim_envio->cd_usuario = Auth::user()->id;

            try{
                $mail_status = Mail::send($view, $data, function($message) use ($emails, $i) {
                $message->to($emails[$i])
                ->subject('Boletim de Clipagens');
                    $message->from('noreply@clipagem.online','Clipping de Notícias');
                });
                $msg = "Email enviado com sucesso";
                $tipo = "success";

                $msg = "Email enviado com sucesso";
                $tipo = "success";
                $boletim->id_situacao = 3;
                $boletim_envio->id_situacao = 2; // Enviado
                $boletim_envio->ds_mensagem = $msg;
            }
            catch (\Swift_TransportException $e) {
                $msg = "Erro ao enviar para o endereço especificado";
                $tipo = "error";

                $msg = "Erro ao enviar para o endereço especificado. Detalhes do erro: ".$e->getMessage();
                $tipo = "error";
                $boletim->id_situacao = 4;
                $boletim_envio->id_situacao = 1; // Pendente
                $boletim_envio->ds_mensagem = $msg;
            }

            /*
            $url = 'https://147.93.71.189:38257/mail_sys/send_mail_http.json';
    
            $data = [
                'mail_from' => 'boletins@clipagens.com.br',
                'password' => 'asdas1#@!SAD',
                'mail_to' => $emails[$i],
                'subject' => $boletim->titulo,
                'content' => $htmlContent,
                'subtype' => 'html'
            ];

            $response = Http::withoutVerifying()->asForm()->post($url, $data);
            $retorno = $response->json();
                        
            if ($response->json()['status']) {
                $msg = "Email enviado com sucesso";
                $tipo = "success";
                $boletim_envio->id_situacao = 2; // Enviado
                $boletim_envio->ds_mensagem = $msg;
                $boletim->id_situacao = 3;
            } else {
                $detalhe = $response->body();
                $msg = "Erro ao enviar para o endereço especificado";
                $tipo = "error";
                $boletim_envio->id_situacao = 1; // Pendente
                $boletim_envio->ds_mensagem = $msg; 
                $boletim->id_situacao = 4;
            } */          
                        
            $boletim_envio->save();

        }

        $boletim->save();

        return redirect('boletim/'.$request->id.'/resumo')->withInput();
    }
 
    public function destroy($id)
    {
        $boletim = Boletim::find($id);
        if($boletim->delete())
            Flash::success('<i class="fa fa-check"></i> Boletim excluído com sucesso');
        else
            Flash::error("Erro ao excluir boletim");

        return redirect('boletins')->withInput();
    }
}