<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Utils;
use App\Models\Emissora;
use App\Models\Estado;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EmissoraController extends Controller
{
    private $client_id;
    private $periodo_padrao;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','radio');
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','emissoras');
        $estados = Estado::orderBy('nm_estado')->get();

        $codigo = ($request->codigo) ? $request->codigo : null;
        $descricao = ($request->descricao) ? $request->descricao : null;       

            $emissora = Emissora::query();

            $emissora->when($codigo, function ($q) use ($codigo) {
                return $q->where('codigo', $codigo);
            });

            $emissora->when($descricao, function ($q) use ($descricao) {
                return $q->where('ds_emissora','ilike','%'.$descricao.'%');
            });

            $emissoras = $emissora->orderBy('ds_emissora')->paginate(10);
        

        return view('emissora/index', compact('emissoras','codigo','descricao','estados'));
    }

    public function listar(Request $request, $tipo)
    {
        $tipo = ($tipo == 'tv') ? 'tv' : 'radio';
        $id_tipo = ($tipo == 'tv') ? 2 : 1;

        Session::put('url', $tipo);
        Session::put('sub-menu', "emissoras-".$tipo);

        $estados = Estado::orderBy('nm_estado')->get();

        $codigo = ($request->codigo) ? $request->codigo : null;
        $descricao = ($request->descricao) ? $request->descricao : null;  
        $cd_cidade = ($request->cd_cidade) ? $request->cd_cidade : null;    
        $cd_estado = ($request->cd_estado) ? $request->cd_estado : null;   

            $emissora = Emissora::query();

            $emissora->when($codigo, function ($q) use ($codigo) {
                return $q->where('codigo', $codigo);
            });

            $emissora->when($cd_cidade, function ($q) use ($cd_cidade) {
                return $q->where('cd_cidade', $cd_cidade);
            });

            $emissora->when($cd_estado, function ($q) use ($cd_estado) {
                return $q->where('cd_estado', $cd_estado);
            });

            $emissora->when($descricao, function ($q) use ($descricao) {
                return $q->where('ds_emissora','ilike','%'.$descricao.'%');
            });

            $emissoras = $emissora->where('tipo_id', $id_tipo)->orderBy('ds_emissora')->paginate(10);
        

        return view('emissora/index', compact('emissoras','codigo','descricao','estados'));
    }

    public function horarios($emissora)
    {
        $id_emissora = $emissora;
        $horarios = array();
        return view('emissora/horarios',compact('horarios','id_emissora'));
    }

    public function atualizaTranscricao($id, $tipo)
    {
        $emissora = Emissora::find($id);
        $emissora->fl_transcricao = !$emissora->fl_transcricao;
        $emissora->save();

        Flash::success('<i class="fa fa-check"></i> Transcrição da emissora atualizada com sucesso');

        return redirect('emissoras/'.$tipo)->withInput();
    }

    public function novo()
    {
        $estados = Estado::orderBy('nm_estado')->get();
        return view('emissora/novo',compact('estados'));
    }

    public function adicionarHorarios(Request $request)
    {
        dd($request->all());

        $emissora = $request->id_emissora;
        $hora_inicial = $request->hora_inicial;
        $hora_final = $request->hora_final;
    }

    public function store(Request $request)
    {
        try {

            Emissora::create($request->all());
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
            return redirect('radio/emissoras')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('radio/emissoras')->withInput();
        }
    }

    public function destroy($id)
    {
        $emissora = Emissora::find($id);
        if($emissora->delete())
            Flash::success('<i class="fa fa-check"></i> Emissora <strong>'.$emissora->ds_emissora.'</strong> excluída com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('radio/emissoras')->withInput();
    }

    public function buscarEmissoras(Request $request)
    {
        $emissoras = Emissora::select('id', 'ds_emissora as text', 'nm_cidade as cidade');
        $emissoras->join('cidade', 'cidade.cd_cidade', '=', 'emissora.cd_cidade');
        $emissoras->where('tipo_id', 1);
        if(!empty($request->query('q'))) {
            $replace = preg_replace('!\s+!', ' ', $request->query('q'));
            $busca = str_replace(' ', '%', $replace);
            $emissoras->whereRaw('ds_emissora ILIKE ?', ['%' . strtolower($busca) . '%']);
        }
        if(!empty($request->query('estado'))) {
            $emissoras->where('emissora.cd_estado', $request->query('estado'));
        }
        if(!empty($request->query('cidade'))) {
            $emissoras->where('emissora.cd_cidade', $request->query('cidade'));
        }
        $result = $emissoras->orderBy('ds_emissora', 'asc')->paginate(30);
        return response()->json($result);
    }
}
