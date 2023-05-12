<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use File;
use App\Models\Cliente;
use Carbon\Carbon;
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

class NoticiaImpressaController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
    }

    public function index(Request $request)
    {
        
    }

    public function upload(Request $request)
    {
        dd($request->all());
    }

    public function cadastrar()
    {
        Session::put('sub-menu','cadastrar');
        
        return view('noticia-impressa/cadastrar');
    }

    public function editar($cliente, $id_noticia)
    {

        $clientes = Cliente::with('pessoa')
                    ->join('pessoas', 'pessoas.id', '=', 'clientes.pessoa_id')
                    ->orderBy('nome')
                    ->get();

        $noticia_original = JornalImpresso::find($id_noticia);
        $vinculo = NoticiaCliente::where('noticia_id', $id_noticia)->where('tipo_id',1)->where('cliente_id', $cliente)->first();

        if(!$noticia_original->fl_copia){

            $noticia = $noticia_original->replicate();
            $noticia->fl_copia = true;
            $noticia->save();
    
            $vinculo->noticia_id = $noticia->id;
            $vinculo->save();
            
        }else{
            $noticia = $noticia_original;
        }

        return view('noticia-impressa/editar', compact('noticia','clientes','vinculo'));
    }

    public function update(Request $request, $id)
    {
        $noticia = JornalImpresso::find($id);
        
        try {
        
            $noticia->update($request->all());
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
            return redirect('jornal-impresso/monitoramento')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect()->route('')->withInput();
        }
    }
}