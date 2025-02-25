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
    private $carbon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','radio');
        $this->carbon = new Carbon();
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','emissoras');

        $estados = Estado::orderBy('nm_estado')->get();

        $dt_inicial = date('Y-m-d')." 00:00:00";
        $dt_final = date('Y-m-d')." 23:59:59";
        $expressao = "";
        $fonte = 0;
        $arquivos = array();

        if($request->fl_gravacao){
            $gravar = ($request->fl_gravacao == 'gravando') ? 1 : 2;
        }else{
            $gravar = null;
        }

        $nome = ($request->nome) ? $request->nome : null;  
        $cd_cidade = ($request->cd_cidade) ? $request->cd_cidade : null;    
        $cd_estado = ($request->cd_estado) ? $request->cd_estado : null;   

        Session::put('filtro_estado', $cd_estado);
        Session::put('filtro_cidade', $cd_cidade);
        Session::put('filtro_gravar', $gravar);
        Session::put('filtro_nome', $nome);     

        $emissora = Emissora::query();

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

        dd($nome);

        $emissora->when($nome, function ($q) use ($nome) {
            dd($nome);
            return $q->where('nome_emissora','ilike','%'.$nome.'%');
        });

        $emissoras = $emissora->orderBy('ds_emissora')->paginate(10);

        return view('emissora/index', compact('emissoras','codigo','descricao','estados'));
    }

    public function arquivos(Request $request)
    {
        Session::put('url', 'radio');
        Session::put('sub-menu', "radio-arquivos");

        $fontes = Emissora::orderBy('nome_emissora')->get();

        $tipo_data = $request->tipo_data;
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $expressao = ($request->expressao) ? $request->expressao : null;
        $dados = array();

        if($request->fontes or Session::get('radio_arquivos_fonte')){
            if($request->fontes){
                $fonte = $request->fontes;
            }elseif(Session::get('radio_arquivos_fonte')){
                $fonte = Session::get('radio_arquivos_fonte');
            }else{
                $fonte = null;
            }
        }else{
            $fonte = null;
            Session::forget('radio_arquivos_fonte');
        }

        if($request->isMethod('POST')){
        
            if($request->fontes){
                Session::put('radio_arquivos_fonte', $fonte);
            }else{
                Session::forget('radio_arquivos_fonte');
                $fonte = null;
            }
        }

        $dados = DB::table('gravacao_emissora_radio')
                    ->select('gravacao_emissora_radio.id AS id',
                            'emissora_radio.id AS id_fonte',
                            'nome_emissora AS nome_fonte',
                            'nm_estado',
                            'nm_cidade',
                            'data_hora_inicio',
                            'data_hora_fim',
                            'transcricao',
                            'nu_valor as valor_retorno',
                            'path_s3')
                    ->join('emissora_radio','emissora_radio.id','=','gravacao_emissora_radio.id_emissora')
                    ->leftJoin('estado','estado.cd_estado','=','emissora_radio.cd_estado')
                    ->leftJoin('cidade','cidade.cd_cidade','=','emissora_radio.cd_cidade')
                    ->when($expressao, function ($q) use ($expressao) {
                        return $q->whereRaw("transcricao_tsv @@ to_tsquery('portuguese', '$expressao')");
                    })
                    ->when($fonte, function ($q) use ($fonte) {
                        return $q->whereIn('emissora_radio.id', $fonte);
                    })
                    ->when($dt_inicial, function ($q) use ($dt_inicial, $dt_final) {
                        return $q->whereBetween('gravacao_emissora_radio.data_hora_inicio', [$dt_inicial." 00:00:00", $dt_final." 23:59:59"]);
                    })
                    ->orderBy('gravacao_emissora_radio.data_hora_inicio','DESC')
                    ->paginate(10);
        

        return view('emissora/arquivos', compact('fontes','dados','tipo_data','dt_inicial','dt_final','fonte','expressao'));
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

        if($request->fl_gravacao){
            $gravar = ($request->fl_gravacao == 'gravando') ? 1 : 2;
        }else{
            $gravar = null;
        }

        $nome = ($request->nome) ? $request->nome : null;  
        $cd_cidade = ($request->cd_cidade) ? $request->cd_cidade : null;    
        $cd_estado = ($request->cd_estado) ? $request->cd_estado : null;   

        Session::put('filtro_estado', $cd_estado);
        Session::put('filtro_cidade', $cd_cidade);
        Session::put('filtro_gravar', $gravar);
        Session::put('filtro_nome', $nome);

        $emissora = Emissora::query();

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

        $emissora->when($nome, function ($q) use ($nome) {
            return $q->where('nome_emissora','ilike','%'.Session::get('filtro_nome').'%');
        });

        $emissoras = $emissora->orderBy('nome_emissora')->paginate(20);        

        return view('emissora/index', compact('emissoras','nome','estados','tipo','cd_estado','cd_cidade','gravar'));
    }

    public function limpar()
    {
        Session::forget('filtro_estado');
        Session::forget('filtro_cidade');
        Session::forget('filtro_gravar');
        Session::forget('filtro_nome');

        return redirect('emissoras/radio');
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
        $paises = Pais::orderBy('nu_ordem','DESC')->orderBY('ds_pais')->get();
        
        return view('emissora/form',compact('estados','emissora','tipo','paises'));
    }

    public function edit(Request $request, $id)
    {
        Session::put('url_anterior', url()->previous());

        $estados = Estado::orderBy('nm_estado')->get();
        $emissora = Emissora::find($id);
        $paises = Pais::orderBy('nu_ordem','DESC')->orderBY('ds_pais')->get();

        $tipo = ($emissora->tipo_id == 1) ? 'radio' : 'tv';

        return view('emissora/form',compact('estados','emissora','tipo','paises'));
    }

    public function horarios($id_emissora)
    {
        $emissora = Emissora::find($id_emissora);
        $horarios = $emissora->horarios->sortBy('horario_start');

        return view('emissora/horarios',compact('horarios','id_emissora','emissora'));
    }

    public function atualizaGravacao($id)
    {
        $emissora = Emissora::find($id);
        $emissora->gravar = !$emissora->gravar;
        $emissora->save();

        Flash::success('<i class="fa fa-check"></i> Gravação da emissora atualizada com sucesso');

        return redirect()->back()->withInput();
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

            if(Session::get('url_anterior')){
                return redirect(Session::get('url_anterior'))->withInput();
            }else{
                return redirect('emissoras/radio')->withInput();
            }
        } else {
            Flash::error($retorno['msg']);
            return redirect('emissoras/radio/atualizar')->withInput();
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

    public function loadEmissoras()
    {
        $emissora = Emissora::select('id', 'nome_emissora as nome', 'nm_cidade as cidade', 'sg_estado as uf');
        $emissora->leftJoin('cidade', 'cidade.cd_cidade', '=', 'emissora_radio.cd_cidade');
        $emissora->leftJoin('estado', 'estado.cd_estado', '=', 'emissora_radio.cd_estado');

        $emissoras = $emissora->orderBy('nome_emissora', 'asc')->get();

        return response()->json($emissoras);
    }
}
