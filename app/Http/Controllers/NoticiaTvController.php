<?php

namespace App\Http\Controllers;

use DB;
use App\Utils;
use Carbon\Carbon;
use App\Models\Area;
use App\Models\Cidade;
use App\Models\Emissora;
use App\Models\Estado;
use App\Models\NoticiaTv;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class NoticiaTvController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','tv');
    }

    public function getBasePath()
    {
        return storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','tv-noticias');

        $carbon = new Carbon();
        $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $termo = $request->termo;

        if($request->isMethod('GET')){
            $noticias = NoticiaTv::leftJoin('noticia_cliente', function($join){
                $join->on('noticia_cliente.noticia_id', '=', 'noticia_tv.id');
                $join->on('noticia_cliente.tipo_id','=', DB::raw(4));
            })->where('dt_noticia', $this->data_atual)->get();
        }

        if($request->isMethod('POST')){

            $noticia = NoticiaTv::query();
            $noticia->leftJoin('noticia_cliente', function($join){
                $join->on('noticia_cliente.noticia_id', '=', 'noticia_tv.id');
                $join->on('tipo_id','=', DB::raw(4));
            }); 

            $noticia->when($termo, function ($q) use ($termo) {
                return $q->where('sinopse', 'ILIKE', '%'.trim($termo).'%');
            });

            $noticia->when($dt_inicial, function ($q) use ($dt_inicial, $dt_final) {
                return $q->whereBetween('dt_noticia', [$dt_inicial, $dt_final]);
            });

            $noticias = $noticia->get();

        }

        return view('noticia-tv/index', compact('noticias','dt_inicial','dt_final','termo'));
    }

    public function dashboard()
    {
        Session::put('sub-menu','tvs');

        $data_final = date("Y-m-d");
        $data_inicial = Carbon::now()->subDays(7)->format('Y-m-d');

        $total_noticia_tv = NoticiaTv::whereBetween('created_at', [$this->data_atual.' 00:00:00', $this->data_atual.' 23:59:59'])->count();
        $ultima_atualizacao = NoticiaTv::max('created_at');

        $total_emissora_tv = Emissora::where('tipo_id', 2)->count();
        $ultima_atualizacao_tv = Emissora::where('tipo_id', 2)->max('created_at');

        $noticias = NoticiaTv::paginate(10);
        return view('noticia-tv/dashboard', compact('noticias','total_noticia_tv', 'total_emissora_tv', 'ultima_atualizacao','ultima_atualizacao_tv','data_final','data_inicial'));
    }


    public function estatisticas()
    {
        $dados = array();
        $totais = (new NoticiaTv())->getTotais();

        for ($i=0; $i < count($totais); $i++) { 
            $dados['label'][] = date('d/m/Y', strtotime($totais[$i]->dt_noticia));
            $dados['totais'][] = $totais[$i]->total;
        }

        return response()->json($dados);
    }
}