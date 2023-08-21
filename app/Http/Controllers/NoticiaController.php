<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Utils;
use Carbon\Carbon;
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

    public function todas(Request $request)
    {
        $dados = array();
        $cliente = $request->cliente;
        $termo = $request->termo;

        $carbon = new Carbon();
        $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");

        $tipo = array();

        $sql = "SELECT dt_noticia, titulo, texto, tipo FROM (
                    SELECT titulo, texto, to_char(dt_clipagem,'DD/MM/YYYY') as dt_noticia, 'web' as tipo FROM noticia_web WHERE dt_clipagem BETWEEN '{$dt_inicial}' AND '{$dt_final}'
                    UNION
                    SELECT titulo, texto, to_char(dt_clipagem,'DD/MM/YYYY') as dt_noticia, 'impresso' as tipo FROM noticia_impresso WHERE dt_clipagem BETWEEN '{$dt_inicial}' AND '{$dt_final}'
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
}