<?php

namespace App\Http\Controllers;

use App\Models\Area;
use DB;
use Auth;
use App\Models\ClienteArea;
use App\Models\JornalWeb;
use App\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','areas');
    }

    public function index()
    {
        $areas = Area::orderBy('descricao')->get();
        return view('area/index',compact('areas'));
    }

    public function cadastrar()
    {
        return view('area/novo');
    }

    public function editar(int $id)
    {
        $area = Area::find($id);
        return view('area/editar', compact('area'));
    }

    public function inserir(Request $request)
    {
        try {
            Area::create(['descricao' => $request->descricao]);

            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {
            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('areas')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('areas/cadastrar')->withInput();
        }
    }

    public function atualizar(Request $request, int $id)
    {
        $area = Area::find($id);

        try {
            $area->update(['descricao' => $request->descricao]);

            $retorno = array(
                'flag' => true,
                'msg' => '<i class="fa fa-check"></i> Dados atualizados com sucesso'
            );

        } catch (\Illuminate\Database\QueryException $e) {
            $retorno = array(
                'flag' => false,
                'msg' => Utils::getDatabaseMessageByCode($e->getCode())
            );
        } catch (\Exception $e) {
            $retorno = array(
                'flag' => true,
                'msg' => "Ocorreu um erro ao atualizar o registro"
            );
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('areas')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect()->route('areas.editar', $area->id)->withInput();
        }
    }

    public function remover(int $id)
    {
        $area = Area::find($id);

        if($area->delete())
            Flash::success('<i class="fa fa-check"></i> Área <strong>'.$area->description.'</strong> excluída com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('areas')->withInput();
    }

    public function cadastrarAreaCliente(Request $request)
    {
        $chave = array('cliente_id' => $request->cliente, 'area_id' => $request->area);

        $dados = array('expressao' => $request->expressao,
                        'ativo' => $request->situacao);

        ClienteArea::updateOrCreate($chave, $dados);
    }
}