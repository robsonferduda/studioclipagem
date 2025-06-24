<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use File;
use Storage;
use App\Utils;
use Carbon\Carbon;
use App\User;
use App\Models\Cliente;
use App\Models\SecaoImpresso;
use App\Models\NoticiaCliente;
use App\Models\FonteImpressa;
use App\Models\FilaImpresso;
use App\Models\JornalImpresso;
use App\Models\NoticiaImpresso;
use App\Models\PaginaJornalImpresso;
use App\Models\Fonte;
use App\Models\Tag;
use App\Models\Cidade;
use App\Models\Estado;
use App\Jobs\ProcessarImpressos as JobProcessarImpressos;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
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
        Session::put('url','impresso');
        $this->carbon = new Carbon();
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','noticias-impresso');

        $fontes = FonteImpressa::orderBy('nome')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();
        $usuarios = User::whereHas('role', function($q){
                            return $q->whereIn('role_id', ['8']);
                        })
                        ->orderBy('name')
                        ->get();

        $tipo_data = ($request->tipo_data) ? $request->tipo_data : 'dt_cadastro';
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $cliente_selecionado = ($request->cliente) ? $request->cliente : null;
        $sentimento = ($request->sentimento) ? $request->sentimento : null;
        $fonte_selecionada = ($request->id_fonte) ? $request->id_fonte : null;
        $area_selecionada = ($request->cd_area) ? $request->cd_area : null;
        $termo = ($request->termo) ? $request->termo : null;
        $usuario = ($request->usuario) ? $request->usuario : null;

        $dados = NoticiaImpresso::with('fonte')
                    ->when($cliente_selecionado, function ($query) use ($cliente_selecionado, $area_selecionada) { 
                        return $query->whereHas('clientes', function($q) use ($cliente_selecionado, $area_selecionada) {
                            $q->where('noticia_cliente.cliente_id', $cliente_selecionado)
                                ->where('noticia_cliente.tipo_id', 1)
                                ->when($area_selecionada, function ($q) use ($area_selecionada) {
                                    return $q->where('noticia_cliente.area', $area_selecionada);
                                });
                            });
                    })
                    ->when($termo, function ($q) use ($termo) {
                        return $q->where('sinopse', 'ILIKE', '%'.trim($termo).'%');
                    })
                    ->when($fonte_selecionada, function ($q) use ($fonte_selecionada) {
                        return $q->where('id_fonte', $fonte_selecionada);
                    })
                    ->when($usuario, function ($q) use ($usuario) {

                        if($usuario == "S"){
                            return $q->whereNull('cd_usuario');
                        }else{
                            return $q->where('cd_usuario', $usuario);
                        }
                    })
                    ->whereBetween($tipo_data, [$dt_inicial." 00:00:00", $dt_final." 23:59:59"])
                    ->orderBy('created_at', 'DESC')
                    ->paginate(30);

        return view('noticia-impressa/index', compact('dados','fontes','clientes','tipo_data','dt_inicial','dt_final','cliente_selecionado','fonte_selecionada','termo','usuarios','usuario','sentimento','area_selecionada'));
    }

    public function limpar()
    {
        return redirect('noticias/impresso');
    }

    public function clientes($noticia)
    {
        $vinculos = array();

        $sql = "SELECT t1.cliente_id, 
                    nome, 
                    area as area_id, 
                    CASE 
                        WHEN(t3.descricao IS NOT NULL) THEN t3.descricao 
                        ELSE 'Nenhuma área selecionada'
                    END as area,
                    CASE
                        WHEN (sentimento = '-1') THEN 'Negativo' 
                        WHEN (sentimento = '0') THEN 'Neutro' 
                        WHEN (sentimento = '1') THEN 'Positivo' 
                        ELSE 'Nenhum sentimento selecionado'
                    END as sentimento,
                    sentimento AS id_sentimento
                FROM noticia_cliente t1
                JOIN clientes t2 ON t2.id = t1.cliente_id 
                LEFT JOIN area t3 On t3.id = t1.area 
                WHERE noticia_id = $noticia
                AND t1.tipo_id = 1";

        $vinculos = DB::select($sql);

        return response()->json($vinculos);
    }

    public function show(Request $request)
    {
        
    }

    public function cadastrar()
    {
        Session::put('sub-menu','noticias-impresso-cadastrar');
        $fontes = FonteImpressa::orderBy("nome")->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $tags = Tag::orderBy('nome')->get();
        
        return view('noticia-impressa/cadastrar', compact('fontes','estados','tags'));
    }

    public function editar($id)
    {
        $noticia = NoticiaImpresso::find($id);
        $tags = Tag::orderBy('nome')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $cidades = Cidade::where(['cd_estado' => $noticia->cd_estado])->orderBy('nm_cidade')->get();
        $fontes = FonteImpressa::orderBy("nome")->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        $pagina = ($noticia->origem and $noticia->origem->id_noticia_origem) ? PaginaJornalImpresso::where('id', $noticia->origem->id_noticia_origem)->first() : null;

        return view('noticia-impressa/editar', compact('noticia','clientes','fontes','estados','cidades','tags','pagina'));
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

        $request->merge(['cd_usuario' => Auth::user()->id]);

        try {
            
            $noticia = NoticiaImpresso::create($request->all());

            if($noticia)
            {
               
                $tags = collect($request->tags)->mapWithKeys(function($tag){
                    return [$tag => ['tipo_id' => 1]];
                })->toArray();

                $noticia->tags()->sync($tags);

                $clientes = json_decode($request->clientes[0]);

                if($clientes){

                    for ($i=0; $i < count($clientes); $i++) { 
                        
                        $dados = array('tipo_id' => 1,
                                'noticia_id' => $noticia->id,
                                'cliente_id' => (int) $clientes[$i]->id_cliente,
                                'area' => (int) $clientes[$i]->id_area,
                                'sentimento' => (int) $clientes[$i]->id_sentimento);

                        $noticia_cliente = NoticiaCliente::create($dados);

                    }
                }
            }

            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados inseridos com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-times"></i> Ocorreu um erro ao inserir o registro');
        }

        switch ($request->btn_enviar) {

            case 'salvar':

                if ($retorno['flag']) {
                    Flash::success($retorno['msg']);
                    return redirect('noticias/impresso')->withInput();
                } else {
                    Flash::error($retorno['msg']);
                    return redirect('noticia/impresso/novo')->withInput();
                }
                break;

            case 'salvar_e_copiar':

                $nova_noticia = $noticia->replicate();
                $nova_noticia->save();

                foreach($noticia->clientes as $cliente) {
                    
                    $match = array('tipo_id' => 1,
                                    'noticia_id' => $nova_noticia->id,
                                    'cliente_id' => (int) $cliente->id);
                            
                    $dados = array('area' => (int) $cliente->pivot_area,
                                   'sentimento' => (int) $cliente->pivot_area);

                    $noticia_cliente = NoticiaCliente::updateOrCreate($match, $dados);

                }

                return redirect('noticia-impressa/'.$nova_noticia->id.'/editar');

            break;
        }
    }

    public function update(Request $request, $id)
    {
        $noticia = NoticiaImpresso::find($id);

        try {

            $dt_cadastro = ($request->dt_cadastro) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_cadastro)->format('Y-m-d') : date("Y-m-d");
            $request->merge(['dt_cadastro' => $dt_cadastro]);

            $dt_clipagem = ($request->dt_clipagem) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_clipagem)->format('Y-m-d') : date("Y-m-d");
            $request->merge(['dt_clipagem' => $dt_clipagem]);

            $request->merge(['cd_cidade' => $request->cidade]);

            $request->merge(['ds_caminho_img' => ($request->ds_caminho_img) ? $request->ds_caminho_img : $noticia->ds_caminho_img]);

            $noticia->update($request->all());

            $tags = collect($request->tags)->mapWithKeys(function($tag){
                    return [$tag => ['tipo_id' => 1]];
                })->toArray();

            $noticia->tags()->sync($tags);

            //Atualização de clientes
            $clientes = json_decode($request->clientes[0]);

            if($clientes){
                for ($i=0; $i < count($clientes); $i++) { 

                    $match = array('tipo_id' => 1,
                                'noticia_id' => $noticia->id,
                                'cliente_id' => (int) $clientes[$i]->id_cliente);
                        
                    $dados = array('area' => (int) $clientes[$i]->id_area,
                                   'sentimento' => (int) $clientes[$i]->id_sentimento);

                    $noticia_cliente = NoticiaCliente::updateOrCreate($match, $dados);
                }
            }

            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados atualizados com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

        switch ($request->btn_enviar) {

            case 'salvar':

                if ($retorno['flag']) {
                    Flash::success($retorno['msg']);
                    return redirect('noticias/impresso')->withInput();
                } else {
                    Flash::error($retorno['msg']);
                    return redirect('noticia-impressa/'.$id.'/editar')->withInput();
                }
                break;

            case 'salvar_e_copiar':

                $nova_noticia = $noticia->replicate();
                $nova_noticia->save();

                foreach($noticia->clientes as $cliente) {
                    
                    $match = array('tipo_id' => 1,
                                    'noticia_id' => $nova_noticia->id,
                                    'cliente_id' => (int) $cliente->id);
                            
                    $dados = array('area' => (int) $cliente->pivot_area,
                                   'sentimento' => (int) $cliente->pivot_area);

                    $noticia_cliente = NoticiaCliente::updateOrCreate($match, $dados);

                }

                return redirect('noticia-impressa/'.$nova_noticia->id.'/editar');

            break;
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
        $file_name = time().'.'.$extension;
        $file_noticia = ($request->id) ? $request->id.'.'.$extension : $file_name;

       //dd($file_noticia);

        //$image->move(public_path('img/noticia-impressa/recorte'),$file_name);
        $image->move(public_path('img/noticia-impressa'),$file_noticia);

        if($request->id){
            $noticia = NoticiaImpresso::find($request->id);

            $noticia->ds_caminho_img = $file_noticia;
            $noticia->save();
        }

        return $file_noticia;
    }

    //Busca e faz o download da imagem vinculada a notícia
    public function getImagem($id)
    {
        $noticia = NoticiaImpresso::find($id);
        return response()->download(public_path('img/noticia-impressa/'.$noticia->ds_caminho_img));
    }

    //Busca a imagem vinculada a notícia para visualizacao
    public function getImagemView($id)
    {
         $noticia = NoticiaImpresso::find($id);

        if (!$noticia || !$noticia->ds_caminho_img) {
            return response()->json(['path' => null], 404);
        }

        $url = asset('img/noticia-impressa/' . $noticia->ds_caminho_img);

        return response()->json(['path' => $url]);
    }

    public function getSecoes($id_fonte)
    {
        $secoes = array();

        $secoes = SecaoImpresso::orderBy('ds_sessao')->get();
        
        //$secoes = FonteImpressa::find($id_fonte)->secoes()->orderBy('ds_sessao')->get();
        
        return response()->json($secoes);
    }

    public function excluir($id)
    {
        $noticia = NoticiaImpresso::find($id);

        if($noticia->delete())
            Flash::success('<i class="fa fa-check"></i> Notícia excluída com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('noticias/impresso')->withInput();
    }
}