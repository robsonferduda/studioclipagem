<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use File;
use Carbon\Carbon;
use App\Models\Cliente;
use App\Models\NoticiaCliente;
use App\Models\FonteImpressa;
use App\Models\FilaImpresso;
use App\Models\JornalImpresso;
use App\Models\Fonte;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Jobs\ProcessarImpressos as JobProcessarImpressos;
use App\Models\Cidade;
use App\Models\Estado;
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

    public function listar()
    {
        Session::put('sub-menu','fonte-impressa');
        $jornais = FonteImpressa::orderBy('nome')->get();
        return view('fonte-impresso/listar',compact('jornais'));
    }

    public function cadastrar()
    {
        Session::put('sub-menu','fonte-impressa');
        $estados = Estado::orderBy('nm_estado')->get();

        return view('fonte-impresso/novo', compact('estados'));
    }

    public function editar(int $id)
    {
        $jornal = FonteImpressa::find($id);
        $estados = Estado::orderBy('nm_estado')->get();

        $cidades = null;
        if($jornal->cd_estado) {
            $cidades = Cidade::where(['cd_estado' => $jornal->cd_estado])->orderBy('nm_cidade')->get();
        }

        return view('fonte-impresso/editar')->with('jornal', $jornal)->with('estados', $estados)->with('cidades', $cidades);
    }

    public function detalhes($id)
    {
        $noticia = JornalImpresso::find($id);
        return view('jornal-impresso/detalhes',compact('noticia'));
    }

    public function inserir(Request $request)
    {
        $fonte = FonteImpressa::where('codigo', $request->codigo)->first();

        if($fonte){
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-exclamation"></i> Já existe uma fonte cadastrada com esse código');

            Flash::warning($retorno['msg']);
            return redirect('fonte-impresso/cadastrar')->withInput();
        }

        try {
            FonteImpressa::create([
                'nome' => $request->nome,
                'cd_cidade' => $request->cidade,
                'cd_estado' => $request->cd_estado,
                'codigo' => $request->codigo ?? null
            ]);

            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados inseridos com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {
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
                'cd_cidade' => $request->cidade,
                'cd_estado' => $request->cd_estado
            ]);

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
            return redirect('fonte-impresso/listar')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect()->route('fonte-impresso.editar', $jornal->id)->withInput();
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