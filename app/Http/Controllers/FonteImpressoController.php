<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use File;
use Carbon\Carbon;
use App\Models\SecaoImpresso;
use App\Models\Cliente;
use App\Models\NoticiaCliente;
use App\Models\FonteImpressa;
use App\Models\FilaImpresso;
use App\Models\JornalImpresso;
use App\Models\ModeloImpresso;
use App\Models\Fonte;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Jobs\ProcessarImpressos as JobProcessarImpressos;
use App\Models\Cidade;
use App\Models\Estado;
use App\Models\Pais;
use App\Utils;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class FonteImpressoController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','impresso');
    }

    public function listar(Request $request)
    {
        Session::put('sub-menu','fonte-impressa');

        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $fontes = array();

        $codigo = ($request->codigo) ? trim($request->codigo) : null;
        $cd_estado = ($request->cd_estado) ? trim($request->cd_estado) : null;
        $cd_cidade = ($request->cd_cidade) ? trim($request->cd_cidade) : null;
        $nome = ($request->nome) ? trim($request->nome) : null;

        $fonte = FonteImpressa::query();

        if($request->isMethod('GET')){
            $fontes = $fonte->orderBy('nome')->paginate(10);
        }

        if($request->isMethod('POST')){
            
            $fonte->when($codigo, function ($q) use ($codigo) {
                return $q->where('codigo', $codigo);
            });

            $fonte->when($cd_estado, function ($q) use ($cd_estado) {
                return $q->where('cd_estado', $cd_estado);
            });

            $fonte->when($cd_cidade, function ($q) use ($cd_cidade) {
                return $q->where('cd_cidade', $cd_cidade);
            });

            $fonte->when($nome, function ($q) use ($nome) {
                return $q->where('nome', 'ILIKE', '%'.trim($nome).'%');
            });

            $fontes = $fonte->orderBy('nome')->paginate(10);
        }

        return view('fonte-impresso/listar',compact('cidades','estados','fontes'));
    }

    public function cadastrar()
    {
        Session::put('sub-menu','fonte-impressa');
        $estados = Estado::orderBy('nm_estado')->get();
        $paises = Pais::all();
        $modelos = ModeloImpresso::all();

        return view('fonte-impresso/novo', compact('estados','paises','modelos'));
    }

    public function sessao(int $id)
    {
        
    }

    public function secao(Request $request)
    {
        $dados_insert = array('id_jornal_online' => $request->font_id,
                              'ds_sessao' => $request->ds_sessao);

        SecaoImpresso::create($dados_insert);
    }

    public function excluirSecao($id)
    {
        $secao = SecaoImpresso::find($id);
        $id_jornal_online = $secao->id_jornal_online;

        $secao->delete();

        return redirect('fonte-impresso/'.$id_jornal_online.'/editar')->withInput();
    }

    public function editar(int $id)
    {
        $fonte = FonteImpressa::find($id);
        $estados = Estado::orderBy('nm_estado')->get();
        $paises = Pais::all();
        $modelos = ModeloImpresso::all();

        $cidades = null;
        if($fonte->cd_estado) {
            $cidades = Cidade::where(['cd_estado' => $fonte->cd_estado])->orderBy('nm_cidade')->get();
        }

        return view('fonte-impresso/editar')->with('modelos', $modelos)->with('paises', $paises)->with('fonte', $fonte)->with('estados', $estados)->with('cidades', $cidades);
    }

    public function detalhes($id)
    {
        $noticia = JornalImpresso::find($id);
        return view('jornal-impresso/detalhes',compact('noticia'));
    }

    public function inserir(Request $request)
    {
        if($request->codigo){

            $fonte = FonteImpressa::where('codigo', $request->codigo)->first();

            if($fonte){

                $retorno = array('flag' => true,
                                'msg' => '<i class="fa fa-exclamation"></i> Já existe uma fonte cadastrada com esse código');

                Flash::warning($retorno['msg']);
                return redirect('fonte-impresso/cadastrar')->withInput();
            }
        }

        try {
            FonteImpressa::create([
                'codigo' => $request->codigo ?? null,
                'nome' => $request->nome,
                'cd_pais' => $request->pais,
                'cd_estado' => $request->cd_estado,
                'cd_cidade' => $request->cidade,
                'valor_cm_capa_semana' => $request->valor_cm_capa_semana,
                'valor_cm_capa_fim_semana' => $request->valor_cm_capa_fim_semana,
                'valor_cm_contracapa' => $request->valor_cm_contracapa,
                'valor_cm_demais_semana' => $request->valor_cm_demais_semana,
                'valor_cm_demais_fim_semana' => $request->valor_cm_demais_fim_semana,
                'tipo' => $request->tipo,
                'coleta' => $request->coleta,
                'modelo' => $request->modelo,
                'url' => $request->url                
            ]);

            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados inseridos com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            dd($e);

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('fonte-impresso/listar')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('fonte-impresso/cadastrar')->withInput();
        }
    }

    public function atualizar(Request $request, int $id)
    {
        $jornal = FonteImpressa::find($id);

        try {

            $jornal->update([
                'codigo'    => $request->codigo,
                'nome'      => $request->nome,
                'cd_estado' => $request->cd_estado,
                'cd_cidade' => $request->cidade,
                'cd_pais' => $request->pais,
                'valor_cm_capa_semana' => $request->valor_cm_capa_semana,
                'valor_cm_capa_fim_semana' => $request->valor_cm_capa_fim_semana,
                'valor_cm_contracapa' => $request->valor_cm_contracapa,
                'valor_cm_demais_semana' => $request->valor_cm_demais_semana,
                'valor_cm_demais_fim_semana' => $request->valor_cm_demais_fim_semana,
                'tipo' => $request->tipo,
                'coleta' => $request->coleta,
                'modelo' => $request->modelo,
                'url' => $request->url
            ]);

            $retorno = array(
                'flag' => true,
                'msg' => '<i class="fa fa-check"></i> Dados atualizados com sucesso'
            );

        } catch (\Illuminate\Database\QueryException $e) {

            dd($e);

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
            return redirect('fonte-impresso/listar')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('fonte-impresso/'.$jornal->id.'/editar')->withInput();
        }
    }

    public function excluir(int $id)
    {
        $fonte = FonteImpressa::find($id);
        
        if(!$fonte->noticias){
            if($fonte->delete())
                Flash::success('<i class="fa fa-check"></i> Fonte impressa <strong>'.$fonte->nome.'</strong> excluído com sucesso');
            else
                Flash::error("Erro ao excluir o registro");
        }else{
            Flash::warning('<i class="fa fa-check"></i> Impossível excluir essa fonte, ela possui notícias associadas');
        }

        return redirect('fonte-impresso/listar')->withInput();
    }
}