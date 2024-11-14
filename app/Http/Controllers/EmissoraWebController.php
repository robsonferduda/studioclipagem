<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use App\Noticia;
use App\Models\NoticiaWeb;
use App\Models\ConteudoNoticiaWeb;
use App\Models\EmissoraWeb;
use App\Models\Estado;
use App\Models\Cidade;
use App\Models\FonteWeb;
use App\Models\SituacaoFonteWeb;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\FontWebRequest;
use Illuminate\Support\Facades\Session;

class EmissoraWebController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','tv');
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','emissora-web');

        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
            
        if($request->ajax()) {

            $situacao = ($request->situacao) ? $request->situacao : "";
            $nome = ($request->nome) ? $request->nome : "";
            $estado = ($request->estado) ? $request->estado : "";
            $cidade = ($request->cidade) ? $request->cidade : "";
    
                $fonte = EmissoraWeb::query();
    
               

                $fonte->orderBy('nome_emissora');
    
                $fontes = $fonte->get();

            return DataTables::of($fontes)
                ->addColumn('id', function ($fonte) {
                    return $fonte->id;
                })     
                ->addColumn('nome', function ($fonte) {
                    return $fonte->nome_emissora;
                })  
                ->addColumn('url', function ($fonte) {
                    return $fonte->url_stream;
                })    
                ->addColumn('acoes', function ($fonte) {
                    return '<div class="text-center">
                                <a title="Coletas" href="../fonte-web/coletas/'.$fonte->id.'" class="btn btn-info btn-link btn-icon"> <i class="fa fa-area-chart fa-2x "></i></a>
                                <a title="Estatísticas" href="../fonte-web/estatisticas/'.$fonte->id.'" class="btn btn-warning btn-link btn-icon"> <i class="fa fa-bar-chart fa-2x"></i></a>
                                <a title="Editar" href="../fonte-web/editar/'.$fonte->id.'" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                <a title="Excluir" href="" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-times fa-2x"></i></a>
                            </div>';
                })   
                ->rawColumns(['id','acoes'])         
                ->make(true);

        }

        return view('emissora-web/index',compact('cidades','estados'));
    }
}