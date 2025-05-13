<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Utils;
use Carbon\Carbon;
use App\Models\NoticiaCliente;
use App\Models\JornalWeb;
use App\Models\JornalImpresso;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NoticiaController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','noticias');
    }

    public function atualizarSentimento($id, $tipo, $cliente, $sentimento)
    {
        $noticia = NoticiaCliente::where('noticia_id',$id)->where('tipo_id',$tipo)->where('cliente_id', $cliente)->first();
        $noticia->sentimento = $sentimento;
        $noticia->save();

        return redirect()->back();
    }

    public function todas(Request $request)
    {
        $dados = array();
        $cliente = $request->cliente;
        $termo = $request->termo;

        $carbon = new Carbon();
        $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");

        $tipo = array();

        $sql = "SELECT id, dt_noticia, titulo, texto, fonte, tipo, vinculo FROM (
                    SELECT t1.id, titulo, texto, to_char(dt_clipagem,'DD/MM/YYYY') as dt_noticia, t2.nome AS fonte, 'web' as tipo, t3.id AS vinculo 
                    FROM noticia_web t1
                    JOIN fonte_web t2 ON t1.id_fonte = t2.id 
                    LEFT JOIN pauta_noticia t3 ON t3.noticia_id = t1.id  
                    WHERE dt_clipagem BETWEEN '{$dt_inicial}' AND '{$dt_final}'   
                    UNION
                    SELECT t1.id, titulo, texto, to_char(dt_clipagem,'DD/MM/YYYY') as dt_noticia, t2.nome AS fonte, 'web' as tipo, t3.id AS vinculo 
                    FROM noticia_impresso t1
                    JOIN fonte_impressa t2 ON t1.id_fonte = t2.id 
                    LEFT JOIN pauta_noticia t3 ON t3.noticia_id = t1.id 
                    WHERE dt_clipagem BETWEEN '{$dt_inicial}' AND '{$dt_final}' 
                ) as noticias WHERE 1=1 ";

        if($request->flag_web == 'true'){
            $tipo[] = 'web';
        }

        if($request->flag_impresso == 'true'){
            $tipo[] = 'impresso';
        }

        if($request->flag_radio == 'true'){
            $tipo[] = 'radio';
        }

        if($request->flag_tv == 'true'){
            $tipo[] = 'tv';
        }

        if(count($tipo)){
            $aux = '';
            for ($i=0; $i < count($tipo); $i++) { 
                $aux .= "'".$tipo[$i]."'";
                if($i < (count($tipo)-1))
                    $aux .= ',';
            }

            $sql .= "AND tipo IN($aux)"; 
        }

        if($request->termo){
            $sql .= " AND (titulo ilike '%{$request->termo}%') ";
        }
        
        $dados = DB::select($sql);

        $dados = JornalWeb::where('dt_clipagem', $this->data_atual)->get();

        dd($dados);

        $dados = array_map(function ($value) {
            return (array)$value;
        }, $dados);
        
        return response()->json($dados);
    }

    public function noticiasWeb(Request $request)
    {
        $dados = JornalWeb::where('dt_clipagem', $this->data_atual)->get();

        return response()->json($dados);
    }

    public function estatisticasArea()
    {
        $areas = array();

        $sql = 'SELECT t2.descricao, count(*) as total 
                FROM noticia_cliente t1
                JOIN area t2 ON t2.id = t1.area 
                WHERE area > 0 
                GROUP BY descricao';

        $areas = DB::select($sql);

        return response()->json($areas);
    }
}