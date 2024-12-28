<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Utils;
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

class EmissoraTvController extends Controller
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
        Session::put('sub-menu','tv-emissoras');

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
                ->addColumn('nome', function ($fonte) {
                    return $fonte->nome_emissora;
                })  
                ->addColumn('url', function ($fonte) {
                    return ($fonte->url_stream) ? $fonte->url_stream : '<span class="text-danger">Não informado</span>';
                })    
                ->addColumn('acoes', function ($fonte) {
                      
                    $acoes = '<div class="text-center">';

                    $acoes .= '<a title="Programas" href="../tv/emissoras/programas/'.$fonte->id.'" class="btn btn-warning btn-link btn-icon"><i class="fa fa-tv fa-2x"></i></a>
                                <a title="Editar" href="../tv/emissoras/editar/'.$fonte->id.'" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                <a title="Excluir" href="" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-times fa-2x"></i></a>';

                    $acoes .= '</div>';

                    return $acoes;
                })   
                ->rawColumns(['url','acoes'])         
                ->make(true);

        }

        return view('emissora-tv/index',compact('cidades','estados'));
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

    public function programas($id)
    {
        Session::put('filtro-emissora', $id);

        return redirect('tv/emissoras/programas');
    }

    public function adicionar(Request $request)
    {
        try {
            EmissoraWeb::create($request->all());
            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {
            
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('tv/emissoras')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('tv/emissoras/novo')->withInput();
        }
    }

    public function atualizar(Request $request)
    {
        $emissora = EmissoraWeb::where('id', $request->id)->first();

        try{
                        
            $emissora->update($request->all());
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados atualizados com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));
        } catch (Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('tv/emissoras')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('tv/emissoras/editar/'.$request->id)->withInput();
        }
    }
}