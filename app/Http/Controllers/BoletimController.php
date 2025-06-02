<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use Carbon\Carbon;
use App\Models\Boletim;
use App\Models\BoletimNoticias;
use App\Models\SituacaoBoletim;
use App\Models\Cliente;
use App\Models\NoticiaImpresso;
use App\Models\NoticiaWeb;
use App\Models\NoticiaRadio;
use App\Models\NoticiaTv;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


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

        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        $boletim = Boletim::query();

        $boletim->when($cliente_selecionado, function ($q) use ($cliente_selecionado) {
            return $q->where('id_cliente', $cliente_selecionado);
        });

        $boletim->whereBetween('dt_boletim', [$dt_inicial." 00:00:00", $dt_final." 23:59:59"]);

        $boletins = $boletim->get();

        return view('boletim/index',compact('boletins','clientes','dt_inicial','dt_final','cliente_selecionado'));
    }

    public function noticias(Request $request)
    {   
        $noticias = array();

        //Notícias de Web
        $sql_web = "SELECT t1.id, 
                    titulo_noticia AS titulo, 
                    'web' as tipo, 
                    TO_CHAR(data_noticia, 'DD/MM/YYYY') AS data_formatada,
                    t2.nome as fonte,
                    t4.id_boletim as id_boletim
                FROM noticias_web t1
                JOIN fonte_web t2 ON t2.id = t1.id_fonte
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id
                LEFT JOIN boletim_noticia t4 ON t4.id_noticia = t3.noticia_id AND id_tipo = 2 AND t4.id_boletim = $request->id_boletim
                WHERE 1=1";

        if ($request->has('dt_inicial') && $request->has('dt_final')) {
            $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d');
            $dt_final = $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d');
            $sql_web .= " AND data_noticia BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'";
        }

        if ($request->has('cliente')) {
            $cliente = $request->cliente;
            $sql_web .= " AND t3.cliente_id = $cliente";
        }
       
        $sql_web .= " ORDER BY data_noticia DESC";
        
        $noticias_web = DB::select($sql_web);

        //Notícias de Impresso
        $sql_impresso = "SELECT t1.id, 
                    t1.titulo, 
                    'impresso' as tipo, 
                    TO_CHAR(dt_clipagem, 'DD/MM/YYYY') AS data_formatada,
                    t2.nome as fonte,
                    t4.id_boletim as id_boletim
                FROM noticia_impresso t1
                JOIN jornal_online t2 ON t2.id = t1.id_fonte
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id
                LEFT JOIN boletim_noticia t4 ON t4.id_noticia = t3.noticia_id AND id_tipo = 1 AND t4.id_boletim = $request->id_boletim
                WHERE 1=1";

        if ($request->has('dt_inicial') && $request->has('dt_final')) {
            $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d');
            $dt_final = $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d');
            $sql_impresso .= " AND dt_clipagem BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'";
        }

        if ($request->has('cliente')) {
            $cliente = $request->cliente;
            $sql_impresso .= " AND t3.cliente_id = $cliente";
        }
       
        $sql_impresso .= " ORDER BY dt_clipagem DESC";
        
        $noticias_impresso = DB::select($sql_impresso);

        //Notícias de Rádio

        $sql_radio = "SELECT t1.id, 
                    t1.titulo, 
                    'radio' as tipo, 
                    TO_CHAR(dt_cadastro, 'DD/MM/YYYY') AS data_formatada,
                    t2.nome_emissora as fonte,
                    t4.id_boletim as id_boletim
                FROM noticia_radio t1
                JOIN emissora_radio t2 ON t2.id = t1.emissora_id
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id
                LEFT JOIN boletim_noticia t4 ON t4.id_noticia = t3.noticia_id AND id_tipo = 3 AND t4.id_boletim = $request->id_boletim
                WHERE 1=1";

        if ($request->has('dt_inicial') && $request->has('dt_final')) {
            $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d');
            $dt_final = $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d');
            $sql_radio .= " AND dt_cadastro BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'";
        }

        if ($request->has('cliente')) {
            $cliente = $request->cliente;
            $sql_radio .= " AND t3.cliente_id = $cliente";
        }
       
        $sql_radio .= " ORDER BY dt_cadastro DESC";

        $noticias_radio = DB::select($sql_radio);

        //Notícias de TV

        $sql_tv = "SELECT t1.id, 
                    '' AS titulo, 
                    'tv' as tipo, 
                    TO_CHAR(dt_noticia, 'DD/MM/YYYY') AS data_formatada,
                    t2.nome_emissora as fonte,
                    t4.id_boletim as id_boletim
                FROM noticia_tv t1
                JOIN emissora_web t2 ON t2.id = t1.emissora_id
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id
                LEFT JOIN boletim_noticia t4 ON t4.id_noticia = t3.noticia_id AND id_tipo = 4 AND t4.id_boletim = $request->id_boletim
                WHERE 1=1";

        if ($request->has('dt_inicial') && $request->has('dt_final')) {
            $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d');
            $dt_final = $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d');
            $sql_tv .= " AND dt_noticia BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'";
        }

        if ($request->has('cliente')) {
            $cliente = $request->cliente;
            $sql_tv .= " AND t3.cliente_id = $cliente";
        }
       
        $sql_tv .= " ORDER BY dt_noticia DESC";

        $noticias_tv = DB::select($sql_tv);

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

    public function detalhes($id)
    {   
        $boletim = Boletim::where('id', $id)->first();         

        $noticias_impresso = $boletim->noticiasImpresso()->get();        

        return view('boletim/detalhes', compact('boletim','noticias_impresso'));
    }

    public function visualizar($id)
    {   
        $boletim = Boletim::where('id', $id)->first(); 
        $boletim->total_views = $boletim->total_views + 1;
        $boletim->save();

        $noticias_impresso = $boletim->noticiasImpresso()->get();
        $noticias_web = $boletim->noticiasWeb()->get(); 
        $noticias_radio = $boletim->noticiasRadio()->get(); 
        $noticias_tv = $boletim->noticiasTv()->get();       

        return view('boletim/visualizar', compact('boletim','noticias_impresso','noticias_web','noticias_radio','noticias_tv'));
    }

    public function outlook($id)
    {   
        $boletim = Boletim::where('id', $id)->first();   

        $noticias_impresso = $boletim->noticiasImpresso()->get();
        $noticias_web = $boletim->noticiasWeb()->get(); 
        $noticias_radio = $boletim->noticiasRadio()->get(); 
        $noticias_tv = $boletim->noticiasTv()->get(); 
            
        return view('boletim/outlook', compact('boletim','noticias_impresso','noticias_web','noticias_radio','noticias_tv'));
    }

    public function enviar($id)
    {
        $boletim = Boletim::where('id', $id)->first();

        $emails = $boletim->cliente->emails;

        $lista = explode(",",$emails);

        $lista_email[] = 'robsonferduda@gmail.com';

        for ($i=0; $i < count($lista); $i++) { 
            $lista_email[] = trim($lista[$i]);
        }
        
        return view('boletim/lista-envio', compact('boletim', 'lista_email'));
    }

    public function enviarLista(Request $request)
    {
        $boletim = Boletim::where('id', $request->id)->first();  
        $noticias_impresso = array();
        $logs = array();
        
        $data = array("noticias_impresso"=> $noticias_impresso, "boletim" => $boletim);
        $emails = $request->emails;

        for ($i=0; $i < count($emails); $i++) { 

            try{
                $nail_status = Mail::send('boletim.outlook', $data, function($message) use ($emails, $i) {
                $message->to($emails[$i])
                ->subject('Boletim de Clipagens');
                    $message->from('boletins@clipagens.com.br','Studio Clipagem');
                });
                $msg = "Email enviado com sucesso";
                $tipo = "success";
            }
            catch (\Swift_TransportException $e) {
                $msg = "Erro ao enviar para o endereço especificado";
                $tipo = "error";
            }

            $logs[] = array('email' => $emails[$i],'tipo' => $tipo, 'msg' => $msg);
        }

        
        $boletim->save();

        return view('boletim/resumo', compact('boletim', 'logs'));
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