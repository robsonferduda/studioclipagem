<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Utils;
use App\Models\Cliente;
use App\Models\Pauta;
use App\Models\PautaNoticia;
use App\Models\NoticiaRadio;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PautaController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','pautas');
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','pautas');
        $id_cliente = ($request->cliente) ? $request->cliente : null;
        $descricao = ($request->descricao) ? $request->descricao : null;

        $clientes = Cliente::orderBy('nome')->get();

        if($request->isMethod('GET')){
            $pautas = Pauta::orderBy('created_at')->get();
        }

        if($request->isMethod('POST')){

            $pauta = Pauta::query();
                    
            $pauta->when($id_cliente, function ($q) use ($id_cliente) {
                return $q->where('cliente_id', $id_cliente);     
            });

            $pauta->when($descricao, function ($q) use ($descricao) {
                return $q->where('descricao', 'ILIKE', '%'.trim($descricao).'%');     
            });

            $pautas = $pauta->get();                
        }
                    
        return view('pauta/index', compact('pautas', 'clientes', 'descricao', 'id_cliente'));
    }

    public function cadastrar()
    {
        Session::put('sub-menu','pauta-cadastrar');

        $clientes = Cliente::orderBy('nome')->get();

        return view('pauta/cadastro', compact('clientes'));
    }

    public function vincular($id)
    {
        Session::put('sub-menu','pauta-cadastrar');
        $pauta = Pauta::find($id);

        $clientes = Cliente::orderBy('nome')->get();

        $noticias = NoticiaRadio::whereNotNull('sinopse')->paginate(14);

        return view('pauta/vincular', compact('clientes','pauta','noticias'));
    }

    public function store(Request $request)
    {
        try {
            Pauta::create($request->all());

            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados inseridos com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-times"></i> Ocorreu um erro ao inserir o registro');
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('pautas')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('pauta/cadastrar')->withInput();
        }
    }

    public function remover($id)
    {
        $pauta = Pauta::find($id);

        if($pauta->noticias){
            $pauta->delete();
            Flash::success('<i class="fa fa-check"></i> Dados excluídos com sucesso');
        }else{
            Flash::error('<i class="fa fa-check"></i> Impossível excluir a pauta, ela possui notícias vinculadas');
        }
        return redirect('pautas')->withInput();
    }

    public function vincularNoticia(Request $request)
    {
        $tipo = null;

        switch ($request->tipo_id) {
            case 'impresso':
                $tipo = 1;
                break;
            
            case 'web':
                $tipo = 2;
                break;
            
            case 'radio':
                $tipo = 3;
                break;
                
            case 'tv':
                $tipo = 4;     
                break;
        
        }

        $dados = array('noticia_id' => $request->noticia_id, 
                        'pauta_id' => $request->pauta_id, 
                        'tipo_id' => $tipo);

        PautaNoticia::create($dados);
    }

    public function desvincularNoticia(Request $request)
    {
        $tipo = null;

        switch ($request->tipo_id) {
            case 'impresso':
                $tipo = 1;
                break;
            
            case 'web':
                $tipo = 2;
                break;
            
            case 'radio':
                $tipo = 3;
                break;
                
            case 'tv':
                $tipo = 4;     
                break;
        
        }

        $vinculo = PautaNoticia::where('noticia_id',$request->noticia_id)->where('pauta_id',$request->pauta_id)->where('tipo_id',$tipo)->first();

        if($vinculo){
            $vinculo->delete();
        }
    }
}