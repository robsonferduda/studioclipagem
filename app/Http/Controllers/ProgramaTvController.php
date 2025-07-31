<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Utils;
use Carbon\Carbon;
use App\Models\Pais;
use App\Models\Cidade;
use App\Models\Estado;
use App\Models\EmissoraWeb;
use App\Models\ProgramaEmissoraWeb;
use App\Models\EmissoraWebHorario;
use App\Models\TipoProgramaEmissoraWeb;
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
        Session::put('url', 'tv');
        Session::put('sub-menu','programas-tv');

        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $tipos = TipoProgramaEmissoraWeb::orderBy('nome')->get();
        $emissoras = EmissoraWeb::orderBy("nome_emissora")->get();

        if($request->fl_gravacao){
            $gravar = ($request->fl_gravacao == 'gravando') ? 1 : 2;
        }else{
            $gravar = null;
        }

        $descricao = ($request->descricao) ? $request->descricao : null;  
        $cd_cidade = ($request->cd_cidade) ? $request->cd_cidade : null;    
        $cd_estado = ($request->cd_estado) ? $request->cd_estado : null;   
        $tipo_programa = ($request->tipo_programa) ? $request->tipo_programa : null;
        $id_emissora = ($request->id_emissora) ? $request->id_emissora : null;

        Session::put('filtro_estado', $cd_estado);
        Session::put('filtro_cidade', $cd_cidade);
        Session::put('filtro_gravar', $gravar);
        Session::put('filtro_nome', $descricao);
        Session::put('filtro_tipo', $tipo_programa);
        Session::put('filtro_emissora', $id_emissora);

        $programa = ProgramaEmissoraWeb::query();

        $programa->when($gravar, function ($q) use ($gravar) {

            $flag = (Session::get('filtro_gravar') == 1) ? true : false;

            return $q->where('gravar', $flag);
        });

        $programa->when($cd_cidade, function ($q) use ($cd_cidade) {
            return $q->where('cd_cidade', Session::get('filtro_cidade'));
        });

        $programa->when($cd_estado, function ($q) use ($cd_estado) {
            return $q->where('cd_estado', Session::get('filtro_estado'));
        });

        $programa->when($tipo_programa, function ($q) use ($tipo_programa) {
            return $q->where('tipo_programa', Session::get('filtro_tipo'));
        });

        $programa->when($id_emissora, function ($q) use ($id_emissora) {
            return $q->where('id_emissora', Session::get('filtro_emissora'));
        });

        $programa->when($descricao, function ($q) use ($descricao) {
            return $q->where('nome_programa','ilike','%'.$descricao.'%');
        });

        $programas = $programa->orderBY("id_situacao","DESC")->orderBy('nome_programa')->paginate(10);
        
        return view('programa-tv/index', compact('programas','descricao','emissoras','cidades','estados','cd_estado','cd_cidade','gravar','tipos'));
    }

    public function limpar()
    {
        Session::forget('filtro_estado');
        Session::forget('filtro_cidade');
        Session::forget('filtro_gravar');
        Session::forget('filtro_nome');
        Session::forget('filtro_tipo');
        Session::forget('filtro_emissora');

        return redirect('tv/emissoras/programas');
    }

    public function novo()
    {
        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $paises = Pais::orderBy('nu_ordem','DESC')->orderBY('ds_pais')->get();
        $tipos = TipoProgramaEmissoraWeb::orderBy("nome")->get();
        $emissoras = EmissoraWeb::orderBy("nome_emissora")->get();
        $programa = null;

        return view('programa-tv/form', compact('paises','estados','cidades','tipos','emissoras','programa'));
    }

    public function editar($id)
    {
        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $paises = Pais::orderBy('nu_ordem','DESC')->orderBY('ds_pais')->get();
        $tipos = TipoProgramaEmissoraWeb::orderBy("nome")->get();
        $emissoras = EmissoraWeb::orderBy("nome_emissora")->get();

        $programa = ProgramaEmissoraWeb::find($id);

        return view('programa-tv/form', compact('paises','estados','cidades','tipos','emissoras','programa'));
    }

    public function horarios($id_programa)
    {
        $programa = ProgramaEmissoraWeb::find($id_programa);
        $horarios = $programa->horarios->sortBy('horario_start');
        $id_emissora = ($programa->emissora) ? $programa->emissora->id : null;

        return view('programa-tv/horarios',compact('programa','horarios','id_programa','id_emissora'));
    }

    public function atualizaGravacao($id){

        $emissora = ProgramaEmissoraWeb::find($id);
        $emissora->gravar = !$emissora->gravar;
        $emissora->save();

        Flash::success('<i class="fa fa-check"></i> Gravação do programa atualizada com sucesso');

        return redirect()->back()->withInput();
    }

    public function adicionarHorarios(Request $request)
    {
        $emissora = $request->id_emissora;
        $programa = $request->id_programa;
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
                                'id_programa' => $programa,
                                'horario_start' => $hora_inicial,
                                'horario_end' => $hora_final,
                                'dias_da_semana' => $dias_da_semana);

        try {
            EmissoraWebHorario::create($dados_insercao);
            $retorno = array('flag' => true,
            'msg' => "Dados inseridos com sucesso");
        } catch (\Throwable $th) {

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
       
        return redirect('tv/emissora/programas/'.$request->id_programa.'/horarios')->withInput();
    }

    public function atualizarHorarios(Request $request)
    {
        $emissora = EmissoraWebHorario::find($request->horario);

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

    public function excluirHorario($id)
    {
        $horario = EmissoraWebHorario::find($id);

        if($horario->delete())
            Flash::success('<i class="fa fa-check"></i> Horário excluído com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('tv/emissora/programas/'.$horario->id_programa.'/horarios')->withInput();
    }

    public function adicionar(Request $request)
    {
        try {
            ProgramaEmissoraWeb::create($request->all());
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
            return redirect('tv/emissoras/programas')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('tv/emissoras/programas/novo')->withInput();
        }
    }

    public function atualizar(Request $request)
    {
        $programa = ProgramaEmissoraWeb::where('id', $request->id)->first();

        $valor_segundo = $request->input('valor_segundo'); // Ex: "1.234,56"
        $valor_segundo = str_replace('.', '', $valor_segundo);     // Remove pontos (milhar)
        $valor_segundo = str_replace(',', '.', $valor_segundo);    // Troca vírgula por ponto
        $valor_segundo = floatval($valor_segundo);

        $request->merge(['valor_segundo' => $valor_segundo]); // Atualiza o valor no request

        try{
                        
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

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('tv/emissoras/programas')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('tv/emissoras/programas/editar/'.$request->id)->withInput();
        }
    }
}