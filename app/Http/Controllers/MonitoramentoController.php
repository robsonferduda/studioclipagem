<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Mail;
use App\Models\Periodo;
use App\Models\Cliente;
use App\Models\Monitoramento;
use App\Models\MonitoramentoExecucao;
use App\Models\JornalImpresso;
use App\Models\JornalWeb;
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
    private $noticias = array();

    public function __construct()
    {
        $this->middleware('auth');        
        Session::put('url','monitoramento');
    }

    public function index()
    {
        $clientes = Cliente::orderBy('nome')->get();
        $monitoramentos = array();
        //$fontes = Fonte::where('tipo_fonte_id',1)->orderBy('ds_fonte')->get();
        //$monitoramentos = Monitoramento::with('cliente')->orderBy('id','DESC')->paginate(10);

        return view('monitoramento/index', compact('monitoramentos','clientes'));
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
        $noticias = $monitoramento->noticias->whereBetween('created_at', [$execucao->created_at, $execucao->updated_at]);
    
        return view('monitoramento/noticias', compact('noticias','monitoramento'));
    }

    public function filtrar(Request $request)
    {
        $sql = "SELECT id, titulo_noticia 
                            FROM
                            (SELECT 
                                t1.id, 
                                t1.titulo_noticia,
                                to_tsvector(conteudo) as document 
                            FROM noticias_web t1
                            JOIN conteudo_noticia_web t2 ON t2.id_noticia_web = t1.id 
                            WHERE t1.created_at between '2024-11-01 00:00:00' AND '2024-11-01 23:59:59') as texto_busca
                            WHERE texto_busca.document @@ to_tsquery('$request->expressao')";

        $sql = "SELECT titulo_noticia 
                FROM noticias_web 
                WHERE id IN(SELECT id_noticia_web 
                            FROM
                            (SELECT id_noticia_web 
                            FROM conteudo_noticia_web 
                            WHERE documento @@ to_tsquery('portuguese','$request->expressao')
                            AND created_at between '2024-11-01 00:00:00' AND '2024-11-30 23:59:59') as dados_busca) ";

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

    public function executar()
    {
        $monitoramentos = Monitoramento::where('fl_ativo', true)->get();

        foreach ($monitoramentos as $key => $monitoramento) {
            
            $data_atual = date('Y-m-d');
            $data_inicio = date('Y-m-d H:i:s');
            $total_vinculado = 0;
            $tabela = '';

            if($monitoramento->tipo_midia == 1) $tabela = 'noticia_impresso';
            if($monitoramento->tipo_midia == 2) $tabela = 'noticia_web';

            $match = DB::select("SELECT id
                            FROM
                            (SELECT id,
                                    dt_clipagem,
                                    to_tsvector(t1.texto) AS document
                            FROM $tabela t1) search
                            WHERE search.document @@ to_tsquery('$monitoramento->expressao')
                            AND dt_clipagem = '$data_atual'");

            for ($i=0; $i < count($match); $i++) { 
                
                $id_noticia = $match[$i]->id;

                $noticia = NoticiaCliente::where('noticia_id', $id_noticia)->where('tipo_id', $monitoramento->tipo_midia)->first();

                if(!$noticia){

                    $dados = array('cliente_id' => $monitoramento->id_cliente,
                                'tipo_id'    => $monitoramento->tipo_midia,
                                'noticia_id' => $id_noticia,
                                'monitoramento_id' => $monitoramento->id);

                    NoticiaCliente::create($dados);
                    $total_vinculado++;
                }
            }

            $data_termino = date('Y-m-d H:i:s');

            $dado_moninoramento = array('monitoramento_id' => $monitoramento->id, 
                                        'total_vinculado' => $total_vinculado,
                                        'created_at' => $data_inicio,
                                        'updated_at' => $data_termino);

            MonitoramentoExecucao::create($dado_moninoramento);
            
        }

        return redirect('monitoramento');
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

        return redirect('monitoramento')->withInput();
    }
}