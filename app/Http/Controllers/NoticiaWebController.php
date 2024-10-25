<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\LogAcesso;
use App\Models\FonteWeb;
use App\Models\NoticiaWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NoticiaWebController extends Controller
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
        Session::put('sub-menu','jornal-web');

        $dt_inicial = date('Y-m-d')." 00:00:00";
        $dt_final = date('Y-m-d')." 23:59:59";

        $fontes = FonteWeb::orderBy('nome')->get();

        if($request->isMethod('POST')){

            $carbon = new Carbon();
            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d")." 00:00:00";
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d")." 23:59:59";
            $termo = $request->termo;
            $fonte = $request->fonte;

            Session::put('busca_termo', $termo);
            Session::put('busca_fonte', $fonte);

            $jornais = NoticiaWeb::query();

            $jornais->when($fonte, function ($q) use ($fonte) {
                return $q->where('id_fonte', $fonte);
            });

            $jornais->when($termo, function ($q) use ($termo) {
                $q->whereHas('conteudo', function($q) use($termo){
                    return $q->where('conteudo', 'ILIKE', '%'.trim($termo).'%')->orWhere('titulo_noticia','ilike','%'.trim($termo).'%');
                });
            });

            $dados = $jornais->whereBetween('data_insert', [$dt_inicial, $dt_final])->orderBy('id_fonte')->orderBy('titulo_noticia')->paginate(10);

        }

        if($request->isMethod('GET')){

            Session::forget('busca_termo');
            Session::forget('burca_fonte');

            if($request->dt_inicial){

                $carbon = new Carbon();
                $dt_inicial = ($request->dt_inicial) ? $request->dt_inicial : date("Y-m-d")." 00:00:00";
                $dt_final = ($request->dt_final) ? $request->dt_final : date("Y-m-d")." 23:59:59";

                $dados = NoticiaWeb::whereBetween('data_insert', [$dt_inicial, $dt_final])->orderBy('data_noticia','DESC')->orderBy('id_fonte')->paginate(10);

            }else{

                $dt_inicial = date('Y-m-d')." 00:00:00";
                $dt_final = date('Y-m-d')." 23:59:59";

                $dados = NoticiaWeb::whereBetween('data_insert', [$dt_inicial, $dt_final])->orderBy('data_noticia','DESC')->orderBy('id_fonte')->paginate(10);
            }

        }

        $total_noticias = count($dados);

        return view('noticia-web/index',compact('fontes','dados','dt_inicial','dt_final'));
    }

    public function detalhes($id)
    {
        $noticia = NoticiaWeb::find($id);

        $acesso = array('tipo' => 'web',
                        'usuario' => Auth::user()->id,
                        'id_noticia' => $noticia->id);

        LogAcesso::create($acesso);

        return view('noticia-web/detalhes',compact('noticia'));
    }

    public function fontes()
    {
        Session::put('sub-menu','fonte-web');

        $fontes = FonteWeb::all();
        return view('jornal-web/fontes',compact('fontes'));
    }

    public function cadastrar()
    {
        Session::put('sub-menu','web-cadastrar');

        $fontes = FonteWeb::all();
        return view('jornal-web/cadastrar',compact('fontes'));
    }

    public function listar()
    {
        $sites = FonteWeb::all();
        return view('jornal-web/listar',compact('sites'));
    }   

    public function estatisticas()
    {
        Session::put('sub-menu','web-estatisticas');

        $total_sites = FonteWeb::count();
        $ultima_atualizacao_web = FonteWeb::max('created_at');
        $ultima_atualizacao_noticia = JornalWeb::max('created_at');
        $fontes = FonteWeb::orderBy('nome')->get();
        $data_final = date("Y-m-d");
        $data_inicial = Carbon::now()->subDays(7)->format('Y-m-d');

        $total_noticias = JornalWeb::whereBetween('created_at', [$data_inicial.' 00:00:00', $data_final.' 23:59:59'])->count();

        return view('jornal-web/dashboard',compact('fontes','total_sites', 'total_noticias','ultima_atualizacao_web','ultima_atualizacao_noticia'));
    }

    public function getEstatisticas($id)
    {
        $noticia = NoticiaWeb::find($id);
        return view('noticia-web/estatisticas',compact('noticia'));
    }
}