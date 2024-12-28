<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use App\Noticia;
use App\Models\NoticiaWeb;
use App\Models\ConteudoNoticiaWeb;
use App\Models\EmissoraWeb;
use App\Models\ProgramaEmissoraWeb;
use App\Models\Estado;
use App\Models\Cidade;
use App\Models\FonteWeb;
use App\Models\SituacaoFonteWeb;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\FontWebRequest;
use Illuminate\Support\Facades\Session;

class ProgramaTvController extends Controller
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
        Session::put('sub-menu','programas-tv');

        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
            
        if($request->ajax()) {

            $situacao = ($request->situacao) ? $request->situacao : "";
            $nome = ($request->nome) ? $request->nome : "";
            $estado = ($request->estado) ? $request->estado : "";
            $cidade = ($request->cidade) ? $request->cidade : "";
    
            $programa = ProgramaEmissoraWeb::query();
    
            $programa->orderBy('nome_programa');
    
            $programas = $programa->get();

            return DataTables::of($programas)  
                ->addColumn('emissora', function ($programa) {
                    return ($programa->emissora) ? $programa->emissora->nome_emissora : 'Não informada';
                }) 
                ->addColumn('nome', function ($programa) {
                    return $programa->nome_programa;
                })  
                ->addColumn('tipo', function ($programa) {
                    return ($programa->tipo) ? '<span class="badge badge-primary" style="background: '.$programa->tipo->ds_color.'; border-color: '.$programa->tipo->ds_color.';">'.$programa->tipo->nome.'</span>' : 'Nenhum';
                })  
                ->addColumn('url', function ($programa) {
                    return ($programa->url) ? $programa->url : '<span class="text-danger">Não informado</span>';
                })    
                ->addColumn('acoes', function ($programa) {
                    return '<div class="text-center">
                                <a title="Editar" href="../tv/emissoras/editar/'.$programa->id.'" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                <a title="Excluir" href="" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-times fa-2x"></i></a>
                            </div>';
                })   
                ->rawColumns(['tipo','url','acoes'])         
                ->make(true);

        }

        return view('programa-tv/index', compact('cidades','estados'));
    }

    public function novo()
    {
        $emissora = null;

        return view('emissora-tv/form', compact('emissora'));
    }

    public function editar($id)
    {
        $emissora = EmissoraWeb::find($id);

        return view('emissora-tv/form', compact('emissora'));
    }
}