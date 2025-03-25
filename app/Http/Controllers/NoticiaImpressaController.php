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
use App\Models\NoticiaImpresso;
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
    private $carbon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','noticias/impresso');
        $this->carbon = new Carbon();
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','noticias/impresso');

        $fontes = FonteImpressa::orderBy('nome')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        $tipo_data = ($request->tipo_data) ? $request->tipo_data : 'dt_clipagem';
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $cliente_selecionado = ($request->cliente) ? $request->cliente : null;
        $fonte = ($request->fontes) ? $request->fontes : null;
        $termo = ($request->termo) ? $request->termo : null;

        $dados = NoticiaImpresso::with('fonte')
                    ->whereBetween($tipo_data, [$dt_inicial." 00:00:00", $dt_final." 23:59:59"])
                    ->orderBy('dt_clipagem')
                    ->orderBy('titulo')
                    ->paginate(10);

        return view('noticia-impressa/index', compact('dados','fontes','clientes','tipo_data','dt_inicial','dt_final','cliente_selecionado','fonte','termo'));
    }

    public function show(Request $request)
    {
        
    }

    public function cadastrar()
    {
        Session::put('sub-menu','noticia-impressa-cadastrar');
        $fontes = FonteImpressa::orderBy("nome")->get();
        $estados = Estado::orderBy('nm_estado')->get();
        
        return view('noticia-impressa/cadastrar', compact('fontes','estados'));
    }

    public function editar($id)
    {
        $noticia = NoticiaImpresso::find($id);

        $estados = Estado::orderBy('nm_estado')->get();
        $cidades = Cidade::where(['cd_estado' => $noticia->cd_estado])->orderBy('nm_cidade')->get();
        $fontes = FonteImpressa::orderBy("nome")->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        return view('noticia-impressa/editar', compact('noticia','clientes','fontes','estados','cidades'));
    }

    public function copiar($cliente, $id_noticia)
    {
        //Notícia original
        $noticia_original = JornalImpresso::find($id_noticia);

        //Vínculo original
        $vinculo = NoticiaCliente::where('noticia_id', $id_noticia)->where('tipo_id',1)->where('cliente_id', $cliente)->first();

        if(!$noticia_original->fl_copia){

            $noticia = $noticia_original->replicate();
            $noticia->noticia_original_id = $noticia_original->id;
            $noticia->fl_copia = true;
            $noticia->save();
    
            $vinculo->noticia_id = $noticia->id;
            $vinculo->save();
            
        }else{
            $noticia = $noticia_original;
        }

        return redirect('noticia-impressa/cliente/'.$cliente.'/editar/'.$noticia->id);
    }

   

    public function store(Request $request)
    {
        $dt_cadastro = ($request->dt_cadastro) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_cadastro)->format('Y-m-d') : date("Y-m-d");
        $request->merge(['dt_cadastro' => $dt_cadastro]);

        $dt_clipagem = ($request->dt_clipagem) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_clipagem)->format('Y-m-d') : date("Y-m-d");
        $request->merge(['dt_clipagem' => $dt_clipagem]);

        try {
            
            NoticiaImpresso::create($request->all());

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
            return redirect('noticias/impresso')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('noticia/impresso/novo')->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $noticia = NoticiaImpresso::find($id);

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
            return redirect('noticias/impresso')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('noticia-impressa/'.$id.'/editar')->withInput();
        }
    }

    //Upload do CROP da imagem
    public function upload(Request $request)
    {
        $image = $request->file('picture');
        $fileInfo = $image->getClientOriginalName();
        $filesize = $image->getSize()/1024/1024;
        $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
        $extension = "jpeg";
        $file_name= $filename.'-'.time().'.'.$extension;
        $image->move(public_path('img/noticia-impressa/recorte'),$file_name);

        //$noticia = JornalImpresso::find($request->id);
        //$noticia->print = $file_name;
        //$noticia->save();

        return $file_name;
    }

    public function getSecoes($id_fonte)
    {
        $secoes = array();
        
        $secoes = FonteImpressa::find($id_fonte)->secoes()->orderBy('ds_sessao')->get();
        
        return response()->json($secoes);
    }
}