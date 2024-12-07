<?php

namespace App\Http\Controllers;

use App\Utils;
use Carbon\Carbon;
use App\Models\Emissora;
use App\Models\Programa;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProgramaController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','radio');
    }

    public function index(Request $request, $tipo)
    {
        $tipo = ($tipo == 'tv') ? 'tv' : 'radio';
        $id_tipo = ($tipo == 'tv') ? 2 : 1;

        Session::put('url', $tipo);
        Session::put('sub-menu', "programas-".$tipo);

        $emissora = $request->emissora_id;
        $programa = $request->nome;

        $emissoras = Emissora::orderBy('nome_emissora')->get();

        $prog = Programa::query();

        $prog->when($emissora, function ($q) use ($emissora) {
            return $q->where('emissora_id', $emissora);
        });

        $prog->when($programa, function ($q) use ($programa) {
            return $q->where('nome','ilike','%'.$programa.'%');
        });

        $programas = $prog->orderBy('nome')->paginate(10);

        return view('programa/index', compact('programas','emissoras','emissora','programa','tipo'));
    }

    public function novo($tipo)
    {
        $id_tipo = ($tipo == 'tv') ? 2 : 1;
        $emissoras = Emissora::where('tipo_id', $id_tipo)->orderBy('ds_emissora')->get();

        return view('programa/novo',compact('emissoras','tipo'));
    }

    public function store(Request $request)
    {
        $tipo = $request->tipo;

        try {

            Programa::create($request->all());
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados inseridos com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {

            $retorno = array('flag' => false,
                             'msg' => '<i class="fa fa-times"></i> Ocorreu um erro ao inserir o registro');
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
        } else {
            Flash::error($retorno['msg']);
        }

        return redirect('programas/'.$tipo)->withInput();
    }

    public function edit($id)
    {
        $programa = Programa::find($id);
        $id_tipo = $programa->emissora->tipo_id;

        $emissoras = Emissora::where('tipo_id', $id_tipo)->orderBy('ds_emissora')->get();

        return view('programa/editar',compact('programa','emissoras'));
    }

    public function update(Request $request, $id)
    {
        $programa = Programa::find($id);
        $id_tipo = $programa->emissora->tipo_id;
        $tipo = ($id_tipo == 1) ? 'radio' : 'tv';

        try {        
            $programa->update($request->all());
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados atualizados com sucesso');
        } catch (\Illuminate\Database\QueryException $e) {
            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));
        } catch (Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

        if($retorno['flag']) {
            Flash::success($retorno['msg']);
        }else{
            Flash::error($retorno['msg']);
        }

        return redirect('programas/'.$tipo)->withInput();
    }

    public function destroy($id)
    {
        $programa = Programa::find($id);
        if($programa->delete())
            Flash::success('<i class="fa fa-check"></i> Programa <strong>'.$programa->nome.'</strong> excluído com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('emissoras/programas')->withInput();
    }

    public function buscarProgramas(Request $request)
    {
        $programas = Programa::select('id', 'nome as text');
        if(!empty($request->query('q'))) {
            $replace = preg_replace('!\s+!', ' ', $request->query('q'));
            $busca = str_replace(' ', '%', $replace);
            $programas->whereRaw('nome ILIKE ?', ['%' . strtolower($busca) . '%']);
        }
        if(!empty($request->query('emissora'))) {
            $programas->where('emissora_id', $request->query('emissora'));
        }

        $result = $programas->orderBy('nome', 'asc')->paginate(30);
        return response()->json($result);
    }

    public function buscarProgramasEmissora(Request $request)
    {
        $emissora = $request->emissora;

        $programas = Programa::select('id', 'nome as text');
        $result = $programas->where('emissora_id', $emissora)->orderBy('nome', 'asc')->get();
        return response()->json($result);
    }

    public function buscarProgramasHorario(Request $request)
    {
        $horario = $request->horario;

        $programas = Programa::select('id', 'nome as text');
        $result = $programas->where('hora_inicio', '<=', $horario)->where('hora_fim', '>=', $horario)->orderBy('nome', 'asc')->get();
        return response()->json($result);
    }
}