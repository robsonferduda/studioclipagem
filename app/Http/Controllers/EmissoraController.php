<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Utils;
use App\Models\Pais;
use App\Models\Emissora;
use App\Models\EmissoraHorario;
use App\Models\EmissoraGravacao;
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

    public function arquivos(Request $request)
    {
        Session::put('url', 'radio');
        Session::put('sub-menu', "radio-arquivos");

        $emissoras = Emissora::orderBy('nome_emissora')->get();

        if($request->isMethod('GET')){

            $dt_inicial = date('Y-m-d H:i:s');
            $dt_final = date('Y-m-d H:i:s');

            $fila = EmissoraGravacao::whereBetween('data_hora_inicio', [$dt_inicial, $dt_final])->paginate(10);
        }

        $emissora = EmissoraGravacao::query();

        
        $arquivos = $emissora->orderBy('data_hora_fim')->paginate(10);

        return view('emissora/arquivos', compact('arquivos','emissoras','dt_inicial','dt_final'));
    }

    public function atualizarHorarios(Request $request)
    {
        $emissora = EmissoraHorario::find($request->horario);

        $dias_da_semana = $emissora->dias_da_semana;

        if($dias_da_semana != ""){
            $dias = explode(',',trim($dias_da_semana));

            if(in_array($request->dia, $dias)){

                if (($key = array_search($request->dia, $dias)) !== false) {
                    unset($dias[$key]);
                }

            }else{
                $dias[] = $request->dia;
            }
        }else{
            $dias[] = $request->dia;
        }
   
        sort($dias);

        if(count($dias))
            $str_dias = implode(",",$dias);
        else 
            $str_dias = "";

        $emissora->dias_da_semana = $str_dias;
        $emissora->save();

    }

    public function listar(Request $request, $tipo)
    {
        Session::put('url', 'radio');
        Session::put('sub-menu', "emissoras-radio");

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

            $emissoras = $emissora->orderBy('nome_emissora')->paginate(10);
        

        return view('emissora/index', compact('emissoras','codigo','descricao','estados','tipo','cd_estado','cd_cidade'));
    }

    public function detalhes($id)
    {
        $estados = Estado::orderBy('nm_estado')->get();
        $emissora = Emissora::find($id);

        $gravacao = EmissoraGravacao::find($id);

        return view('emissora/detalhes',compact('estados','emissora','gravacao'));
    }

    public function novo($tipo)
    {
        $estados = Estado::orderBy('nm_estado')->get();
        $emissora = new Emissora();
        $paises = Pais::all();
        
        return view('emissora/form',compact('estados','emissora','tipo','paises'));
    }

    public function edit($id)
    {
        $estados = Estado::orderBy('nm_estado')->get();
        $emissora = Emissora::find($id);
        $paises = Pais::all();

        $tipo = ($emissora->tipo_id == 1) ? 'radio' : 'tv';

        return view('emissora/form',compact('estados','emissora','tipo','paises'));
    }

    public function horarios($id_emissora)
    {
        $emissora = Emissora::find($id_emissora);
        $horarios = $emissora->horarios->sortBy('horario_start');

        return view('emissora/horarios',compact('horarios','id_emissora'));
    }

    public function atualizaGravacao($id)
    {
        $emissora = Emissora::find($id);
        $emissora->gravar = !$emissora->gravar;
        $emissora->save();

        Flash::success('<i class="fa fa-check"></i> Gravação da emissora atualizada com sucesso');

        return redirect('emissoras/radio')->withInput();
    }

    public function atualizaTranscricao($id)
    {
        $emissora = Emissora::find($id);
        $emissora->fl_transcricao = !$emissora->fl_transcricao;
        $emissora->save();

        $tipo = ($emissora->tipo_id == 1) ? 'radio' : 'tv';

        Flash::success('<i class="fa fa-check"></i> Transcrição da emissora atualizada com sucesso');

        return redirect('emissoras/'.$tipo)->withInput();
    }

    public function adicionarHorarios(Request $request)
    {
        $emissora = $request->id_emissora;
        $hora_inicial = $request->hora_inicial;
        $hora_final = $request->hora_final;
        $dias_da_semana = '';

        if($request->dia_0) $dias_da_semana .= '0,';
        if($request->dia_1) $dias_da_semana .= '1,';
        if($request->dia_2) $dias_da_semana .= '2,';
        if($request->dia_3) $dias_da_semana .= '3,';
        if($request->dia_4) $dias_da_semana .= '4,';
        if($request->dia_5) $dias_da_semana .= '5,';
        if($request->dia_6) $dias_da_semana .= '6,';

        $dias_da_semana = substr($dias_da_semana, -0, -1);

        $dados_insercao = array('id_emissora' => $emissora,
                                'horario_start' => $hora_inicial,
                                'horario_end' => $hora_final,
                                'dias_da_semana' => $dias_da_semana);

        try {
            EmissoraHorario::create($dados_insercao);
            $retorno = array('flag' => true,
            'msg' => "Dados inseridos com sucesso");
        } catch (\Throwable $th) {

            dd($th);
            $retorno = array('flag' => false,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        } catch (\Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }       
        
        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
        } else {
            Flash::error($retorno['msg']);
        }
       
        return redirect('radio/emissora/'.$request->id_emissora.'/horarios')->withInput();
    }

    public function store(Request $request)
    {
        $id_tipo = ($request->tipo == 'tv') ? 2 : 1;

        try {
            $request->merge(['tipo_id' => $id_tipo]);

            Emissora::create($request->all());
            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {

            $retorno = array('flag' => false,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('emissoras/'.$request->tipo)->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('emissoras/'.$request->tipo.'/novo')->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $emissora = Emissora::find($id);
        $tipo = ($emissora->tipo_id == 1) ? 'radio' : 'tv';

        try {
            $emissora->update($request->all());
            $retorno = array('flag' => true,
                             'msg' => "Dados atualizados com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {

            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('emissoras/'.$tipo)->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('emissoras/'.$tipo.'/atualizar')->withInput();
        }
    }

    public function excluirHorario($id)
    {
        $horario = EmissoraHorario::find($id);

        if($horario->delete())
            Flash::success('<i class="fa fa-check"></i> Horário excluído com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('radio/emissora/'.$horario->id_emissora.'/horarios')->withInput();
    }

    public function destroy($id)
    {
        $emissora = Emissora::find($id);
        $tipo = ($emissora->tipo_id == 1) ? 'radio' : 'tv';

        if($emissora->delete())
            Flash::success('<i class="fa fa-check"></i> Emissora <strong>'.$emissora->nome_emissora.'</strong> excluída com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('emissoras/'.$tipo)->withInput();
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
        $result = $emissoras->orderBy('ds_emissora', 'asc')->get();

        return response()->json($result);
    }
}
