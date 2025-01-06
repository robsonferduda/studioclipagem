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
    private $noticias = array();

    public function __construct()
    {
        $this->middleware('auth');        
        Session::put('url','monitoramento');
    }

    public function index()
    {
        $clientes = Cliente::orderBy('nome')->get();
        $monitoramentos = Monitoramento::with('cliente')->orderBy('id','DESC')->paginate(10);

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
        $carbon = new Carbon();
        $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d "."00:00:00");
        $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d "."23:59:59");

        $sql = "SELECT 
                    n.id, n.id_fonte, n.url_noticia, n.data_insert, n.data_noticia, n.titulo_noticia, fw.nome
                FROM 
                    noticias_web n
                JOIN 
                    conteudo_noticia_web cnw ON cnw.id_noticia_web = n.id
                JOIN 
                    fonte_web fw ON fw.id = n.id_fonte 
                WHERE 1=1
                    AND cnw.data_noticia BETWEEN '$dt_inicial' AND '$dt_final' ";

        $sql .= ($request->expressao) ? "AND  cnw.conteudo_tsv @@ to_tsquery('portuguese', '$request->expressao') " : '';
        $sql .= 'ORDER BY data_noticia DESC';

        $dados = DB::select($sql);

        return response()->json($dados);
    }

    public function filtrarImpresso(Request $request)
    {
        $carbon = new Carbon();
        $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d "."00:00:00");
        $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d "."23:59:59");

        $sql = "SELECT 
                    pejo.id, id_jornal_online, link_pdf, dt_coleta, dt_pub, titulo, texto_extraido
                FROM 
                    edicao_jornal_online n
                JOIN 
                    pagina_edicao_jornal_online pejo 
                    ON pejo.id_edicao_jornal_online = n.id
                WHERE 1=1
                    AND pejo.created_at BETWEEN '$dt_inicial' AND '$dt_final' ";

        $sql .= ($request->expressao) ? "AND  pejo.texto_extraido_tsv @@ to_tsquery('portuguese', '$request->expressao') " : '';
        $sql .= 'ORDER BY dt_coleta DESC';

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

    public function executarIndividual($id)
    {
        Flash::success('<i class="fa fa-check"></i> Monitoramento individual realizado com sucesso');

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