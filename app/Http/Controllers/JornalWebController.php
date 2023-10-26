<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Fonte;
use App\Models\FonteWeb;
use App\Models\JornalWeb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class JornalWebController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','jornal-web');
    }

    public function index(Request $request)
    {
        $fontes = FonteWeb::orderBy('nome')->get();
        $total_sites = FonteWeb::count();
        $ultima_atualizacao_web = FonteWeb::max('created_at');
        $ultima_atualizacao_noticia = JornalWeb::max('created_at');

        if($request->isMethod('POST')){

            $carbon = new Carbon();
            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
            $termo = $request->termo;

            $jornais = JornalWeb::query();

            $jornais->when($termo, function ($q) use ($termo) {
                return $q->where('texto', 'ILIKE', '%'.trim($termo).'%');
            });

            $dados = $jornais->whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->orderBy('id_fonte')->orderBy('titulo')->paginate(10);

            $total_noticias = JornalWeb::whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->count();

        }

        if($request->isMethod('GET')){

            if($request->dt_inicial){
                $dt_inicial = $request->dt_inicial;
                $dt_final = $request->dt_final;

                $dados = JornalWeb::whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->orderBy('id_fonte')->paginate(10);
                $total_noticias = JornalWeb::whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->count();
            }else{
                $dt_inicial = date('d/m/Y');
                $dt_final = date('d/m/Y');
                $dados = JornalWeb::where('dt_clipagem', $this->data_atual)->orderBy('id_fonte')->paginate(10);
                $total_noticias = JornalWeb::where('dt_clipagem', $this->data_atual)->count();
            }

        }

        return view('jornal-web/index',compact('fontes','dados','dt_inicial','dt_final','total_sites','total_noticias','ultima_atualizacao_web','ultima_atualizacao_noticia'));
    }

    public function fontes()
    {
        $fontes = FonteWeb::all();
        return view('jornal-web/fontes',compact('fontes'));
    }

    public function cadastrar()
    {
        $fontes = FonteWeb::all();
        return view('jornal-web/cadastrar',compact('fontes'));
    }

    public function listar()
    {
        $sites = FonteWeb::all();
        return view('jornal-web/listar',compact('sites'));
    }

    public function detalhes($id)
    {
        $noticia = JornalWeb::find($id);
        return view('jornal-web/detalhes',compact('noticia'));
    }

    public function estatisticas($id)
    {
        $noticia = JornalWeb::find($id);
        return view('jornal-web/estatisticas',compact('noticia'));
    }
}