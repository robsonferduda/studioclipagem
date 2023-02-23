<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Estado;
use App\Models\Cidade;
use App\Models\FonteWeb;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FonteWebController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
    }

    public function listar(Request $request)
    {
        $fontes = FonteWeb::with('estado')->orderBy('nome')->get();

        if($request->isMethod('POST')){

            $carbon = new Carbon();
            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");

            $dados = FonteWeb::whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->orderBy('id_fonte')->paginate(10);

        }

        return view('fonte-web/listar',compact('fontes'));
    }

    public function create(Request $request)
    {
        return view('fonte-web/novo');
    }

    public function edit(FonteWeb $fonte, $id)
    {
        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $fonte = FonteWeb::find($id);
        return view('fonte-web/editar',compact('fonte','estados','cidades'));
    }

    public function update(Request $request, $id)
    {
        $fonte = FonteWeb::find($id);
    
        try{
        
            $fonte->update($request->all());
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
            return redirect('fonte-web/listar')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect()->route('font-web.edit', $fonte->id)->withInput();
        }
    }
}