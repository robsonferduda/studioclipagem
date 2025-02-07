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

        if($request->fl_gravacao){
            $gravar = ($request->fl_gravacao == 'gravando') ? 1 : 2;
        }else{
            $gravar = null;
        }

        $descricao = ($request->descricao) ? $request->descricao : null;  
        $cd_cidade = ($request->cd_cidade) ? $request->cd_cidade : null;    
        $cd_estado = ($request->cd_estado) ? $request->cd_estado : null;   
        $tipo_programa = ($request->tipo_programa) ? $request->tipo_programa : null;

        Session::put('filtro_estado', $cd_estado);
        Session::put('filtro_cidade', $cd_cidade);
        Session::put('filtro_gravar', $gravar);
        Session::put('filtro_nome', $descricao);
        Session::put('filtro_tipo', $tipo_programa);

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

        $programa->when($descricao, function ($q) use ($descricao) {
            return $q->where('nome_programa','ilike','%'.$descricao.'%');
        });

        $programas = $programa->orderBY("id_situacao","DESC")->orderBy('nome_programa')->paginate(10);
        
        return view('programa-tv/index', compact('programas','descricao','cidades','estados','cd_estado','cd_cidade','gravar','tipos'));

        return view('emissora/index', compact('emissoras','descricao','estados','tipo','cd_estado','cd_cidade','gravar'));

        /*
        Session::put('sub-menu','programas-tv');

        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
            
        if($request->ajax()) {

            $situacao = ($request->situacao) ? $request->situacao : "";
            $nome = ($request->nome) ? $request->nome : "";
            $estado = ($request->estado) ? $request->estado : "";
            $cidade = ($request->cidade) ? $request->cidade : "";
    
            $programa = ProgramaEmissoraWeb::query();

            $programa->when(Session::get('filtro-emissora'), function ($q) {
                return $q->where('id_emissora', Session::get('filtro-emissora'));
            });
    
            $programa->orderBy('nome_programa');
    
            $programas = $programa->get();

            Session::forget('filtro-emissora');

            return DataTables::of($programas)  
                ->addColumn('estado', function ($programa) {
                    return ($programa->estado) ? $programa->estado->nm_estado : '<span class="text-danger">Não informado</span>';
                }) 
                ->addColumn('cidade', function ($programa) {
                    return ($programa->cidade) ? $programa->cidade->nm_cidade : '<span class="text-danger">Não informado</span>';
                })
                ->addColumn('emissora', function ($programa) {
                    return ($programa->emissora) ? $programa->emissora->nome_emissora : '<span class="text-danger">Não informado</span>';
                }) 
                ->addColumn('nome', function ($programa) {
                    return $programa->nome_programa;
                })  
                ->addColumn('tipo', function ($programa) {
                    return ($programa->tipo) ? '<span class="badge badge-primary" style="background: '.$programa->tipo->ds_color.'; border-color: '.$programa->tipo->ds_color.';">'.$programa->tipo->nome.'</span>' : '<span class="text-danger">Não informado</span>';
                })  
                ->addColumn('url', function ($programa) {
                    return ($programa->url) ? $programa->url : '<span class="text-danger">Não informado</span>';
                })    
                ->addColumn('acoes', function ($programa) {

                    $acoes = '<div class="text-center">';

                    if(count($programa->horarios))
                        $acoes .= '<a title="Horários de Coleta" href="../emissora/programas/'.$programa->id.'/horarios" class="btn btn-warning btn-link btn-icon"><i class="nc-icon nc-time-alarm font-25"></i></a>';
                    else
                        $acoes .= '<a title="Horários de Coleta" href="../emissora/programas/'.$programa->id.'/horarios" class="btn btn-default btn-link btn-icon"><i class="nc-icon nc-time-alarm font-25"></i></a>';

                    $acoes .= ' <a title="Editar" href="../emissoras/programas/editar/'.$programa->id.'" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                <a title="Excluir" href="" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-times fa-2x"></i></a>';

                    $acoes .= '</div>';

                    return $acoes;
                })   
                ->rawColumns(['estado','cidade','emissora','tipo','url','acoes'])         
                ->make(true);

        }

        return view('programa-tv/index', compact('cidades','estados'));
        */
    }

    public function limpar()
    {
        Session::forget('filtro_estado');
        Session::forget('filtro_cidade');
        Session::forget('filtro_gravar');
        Session::forget('filtro_nome');
        Session::forget('filtro_tipo');

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
        $emissora = ProgramaEmissoraWeb::find($id_programa);
        $horarios = $emissora->horarios->sortBy('horario_start');

        return view('programa-tv/horarios',compact('horarios','id_programa'));
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
        $emissora = $request->id_programa;
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

        $dados_insercao = array('id_programa' => $emissora,
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