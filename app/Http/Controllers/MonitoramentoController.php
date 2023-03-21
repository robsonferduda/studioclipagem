<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Mail;
use App\Models\Monitoramento;
use App\Models\JornalImpresso;
use App\Models\JornalWeb;
use App\Models\Fonte;
use App\Models\NoticiaCliente;
use Carbon\Carbon;
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

        $cliente = null;

        $clienteSession = ['id' => 1, 'nome' => 'Teste'];

        Session::put('cliente', session('cliente') ? session('cliente') : $clienteSession);

        $this->data_atual = session('data_atual');
        
        Session::put('url','home');

        $this->periodo_padrao = 7;
    }

    public function index()
    {
        $fontes = Fonte::where('tipo_fonte_id',1)->orderBy('ds_fonte')->get();
        $monitoramentos = Monitoramento::with('cliente')->get();

        return view('monitoramento/index', compact('monitoramentos','fontes'));
    }

    public function executar()
    {
        $monitoramentos = Monitoramento::where('fl_ativo', true)->get();

        foreach ($monitoramentos as $key => $monitoramento) {
            
            $match = DB::select("SELECT id
                            FROM
                            (SELECT id,
                                    to_tsvector(t1.texto) AS document
                            FROM noticia_web t1) search
                            WHERE search.document @@ to_tsquery('$monitoramento->expressao')");

            for ($i=0; $i < count($match); $i++) { 
                
                $id_noticia = $match[$i]->id;

                $noticia = NoticiaCliente::where('noticia_id', $id_noticia)->where('tipo_id', 2)->first();

                if(!$noticia){

                    $dados = array('cliente_id' => $monitoramento->id_cliente,
                                'tipo_id'    => 2,
                                'noticia_id' => $id_noticia);

                    NoticiaCliente::create($dados);
                }
            }
        }

        return redirect('monitoramento');
    }
}