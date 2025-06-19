<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Utils;
use Carbon\Carbon;
use App\Noticia;
use App\Models\NoticiaWeb;
use App\Models\ConteudoNoticiaWeb;
use App\Models\ProgramaEmissoraWeb;
use App\Models\EmissoraWeb;
use App\Models\EmissoraWebHorario;
use App\Models\Pais;
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

        $descricao = ($request->descricao) ? $request->descricao : null;  
        $cd_cidade = ($request->cd_cidade) ? $request->cd_cidade : null;    
        $cd_estado = ($request->cd_estado) ? $request->cd_estado : null;   

        if($request->fl_gravacao){
            $gravar = ($request->fl_gravacao == 'gravando') ? 1 : 2;
        }else{
            $gravar = null;
        }

        Session::put('filtro_estado', $cd_estado);
        Session::put('filtro_cidade', $cd_cidade);
        Session::put('filtro_gravar', $gravar);
        Session::put('filtro_nome', $descricao);

        $emissora = EmissoraWeb::query();

        $emissora->when($gravar, function ($q) use ($gravar) {

            $flag = (Session::get('filtro_gravar') == 1) ? true : false;

            return $q->where('gravar', $flag);
        });

        $emissora->when($cd_cidade, function ($q) use ($cd_cidade) {
            return $q->where('cd_cidade', Session::get('filtro_cidade'));
        });

        $emissora->when($cd_estado, function ($q) use ($cd_estado) {
            return $q->where('cd_estado', Session::get('filtro_estado'));
        });

        $emissora->when($descricao, function ($q) use ($descricao) {
            return $q->where('nome_emissora','ilike','%'.$descricao.'%');
        });
        
        $emissoras = $emissora->orderBY("id_situacao","DESC")->orderBy('nome_emissora')->paginate(10);

        return view('emissora-tv/index',compact('emissoras','cidades','estados','descricao','cd_estado','cd_cidade','gravar'));
    }

    public function limpar()
    {
        Session::forget('filtro_estado');
        Session::forget('filtro_cidade');
        Session::forget('filtro_gravar');
        Session::forget('filtro_nome');
      
        return redirect('tv/emissoras');
    }

    public function novo()
    {
        $estados = Estado::orderBy('nm_estado')->get();
        $emissora = new EmissoraWeb();
        $paises = Pais::orderBy('nu_ordem','DESC')->orderBY('ds_pais')->get();

        return view('emissora-tv/form', compact('emissora','paises','estados'));
    }

    public function editar($id)
    {
        $emissora = EmissoraWeb::find($id);
        $paises = Pais::orderBy('nu_ordem','DESC')->orderBY('ds_pais')->get();
        $estados = Estado::orderBy('nm_estado')->get();

        return view('emissora-tv/form', compact('emissora','paises','estados'));
    }

    public function programas($id)
    {
        Session::put('filtro-emissora', $id);

        return redirect('tv/emissoras/programas');
    }

    public function horarios($id_emissora)
    {
        $emissora = EmissoraWeb::find($id_emissora);

        $horarios = ($emissora->horarios) ? $emissora->horarios->sortBy('horario_start') : array();

        return view('emissora-tv/horarios',compact('emissora','horarios','id_emissora'));
    }

    public function atualizaGravacao($id){

        $emissora = EmissoraWeb::find($id);
        $emissora->gravar = !$emissora->gravar;
        $emissora->save();

        Flash::success('<i class="fa fa-check"></i> Gravação da emissora atualizada com sucesso');

        return redirect()->back()->withInput();
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
       
        return redirect('tv/emissora/'.$request->id_emissora.'/horarios')->withInput();
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

    public function buscarEmissoras(Request $request)
    {
        $emissoras = EmissoraWeb::select('id', 'nome_emissora as text');
        $result = $emissoras->orderBy('nome_emissora', 'asc')->get();

        return response()->json($result);
    }

    public function buscarProgramas($id)
    {

        $programas = ProgramaEmissoraWeb::select('id', 'nome_programa as text');
        $result = $programas->orderBy('nome_programa', 'asc')->get();

        return response()->json($result);
    }
}