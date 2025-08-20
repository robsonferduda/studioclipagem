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

        // Lista de filtros que você deseja manter em sessão
        $filtros = [
            'tipo_data',
            'dt_inicial',
            'dt_final',
            'cliente',
            'sentimento',
            'id_fonte',
            'cd_area',
            'termo',
            'usuario'
        ];

        // Salva cada filtro na sessão, se vier na requisição
        foreach ($filtros as $filtro) {
            if ($request->has($filtro)) {
                Session::put('impresso_filtro_' . $filtro, $request->input($filtro));
            }
        }

        // Recupera os filtros da sessão (ou da request, se vier)
        $tipo_data = Session::get('impresso_filtro_tipo_data', $request->input('tipo_data', 'dt_cadastro'));
        $dt_inicial = Session::get('impresso_filtro_dt_inicial', $request->input('dt_inicial', date('d/m/Y')));
        $dt_final = Session::get('impresso_filtro_dt_final', $request->input('dt_final', date('d/m/Y')));
        $cliente_selecionado = Session::get('impresso_filtro_cliente', $request->input('cliente'));
        $sentimento = Session::get('impresso_filtro_sentimento', $request->input('sentimento'));
        $fonte_selecionada = Session::get('impresso_filtro_id_fonte', $request->input('id_fonte'));
        $area_selecionada = Session::get('impresso_filtro_cd_area', $request->input('cd_area'));
        $termo = Session::get('impresso_filtro_termo', $request->input('termo'));
        $usuario = Session::get('impresso_filtro_usuario', $request->input('usuario'));

        // Converta as datas para o formato do banco
        $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $dt_inicial)->format('Y-m-d');
        $dt_final = $this->carbon->createFromFormat('d/m/Y', $dt_final)->format('Y-m-d');

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
                        return $q->where('sinopse', 'ILIKE', '%'.trim($termo).'%')
                                  ->orWhere('titulo', 'ILIKE', '%'.trim($termo).'%');
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
        foreach (['tipo_data','dt_inicial','dt_final','cliente','sentimento','id_fonte','cd_area','termo','usuario'] as $filtro) {
            Session::forget('impresso_filtro_' . $filtro);
        }
        
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

        $dt_clipagem = ($request->dt_clipagem) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_clipagem)->format('Y-m-d H:i:s') : date("Y-m-d H:i:s");
        $request->merge(['dt_clipagem' => $dt_clipagem]);

        $request->merge(['cd_usuario' => Auth::user()->id]);

        // Trata o valor_retorno para salvar corretamente
        if (!empty($request->valor_retorno)) {
            // Remove pontos de milhar e troca vírgula por ponto
            $valor = str_replace('.', '', $request->valor_retorno);
            $valor = str_replace(',', '.', $valor);
            $request->merge(['valor_retorno' => (float) $valor]);
        }

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
                    return redirect('noticia/impresso/novo')->withInput();
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

            $dt_clipagem = ($request->dt_clipagem) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_clipagem)->format('Y-m-d H:i:s') : date("Y-m-d H:i:s");
            $request->merge(['dt_clipagem' => $dt_clipagem]);

            $request->merge(['cd_cidade' => $request->cidade]);

            $request->merge(['ds_caminho_img' => ($request->ds_caminho_img) ? $request->ds_caminho_img : $noticia->ds_caminho_img]);

            // Trata o valor_retorno para salvar corretamente
            if (!empty($request->valor_retorno)) {
                // Remove pontos de milhar e troca vírgula por ponto
                $valor = str_replace('.', '', $request->valor_retorno);
                $valor = str_replace(',', '.', $valor);
                $request->merge(['valor_retorno' => (float) $valor]);
            }

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
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()).'<br/>'.$e->getMessage());

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

    public function retorno()
    {
        Session::put('sub-menu','web-retorno');

        $total_nulos = NoticiaImpresso::whereNull('valor_retorno')
                        ->whereNotNull('titulo')
                        ->whereHas('clientes', function($q){
                            $q->where('noticia_cliente.tipo_id', 1);
                        })
                        ->whereHas('fonte', function($q){
                            $q->whereNotNull('jornal_online.deleted_at');
                        })
                        ->where('dt_clipagem', '>', '2025-05-01')
                        ->count();

        $sql = "SELECT t2.id, t2.nome, count(*) as total 
                FROM noticia_impresso t1
                LEFT JOIN jornal_online t2 ON t2.id = t1.id_fonte 
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND tipo_id = 1 AND t3.deleted_at IS NULL
                WHERE t1.valor_retorno IS NULL
                AND dt_clipagem > '2025-05-01'
                AND titulo IS NOT NULL
                AND t1.deleted_at IS NULL
                AND t2.deleted_at IS NULL
                AND t3.deleted_at IS NULL
                GROUP BY t2.id, t2.nome
                ORDER BY nome";

        $inconsistencias = DB::select($sql);

        $sql = "SELECT DISTINCT t1.id, t2.nome, t1.id_fonte, t2.retorno_midia, t1.valor_retorno AS valor_retorno, sinopse, dt_clipagem, titulo 
                FROM noticia_impresso t1
                LEFT JOIN jornal_online t2 ON t2.id = t1.id_fonte 
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND tipo_id = 1 AND t3.deleted_at IS NULL
                WHERE t1.valor_retorno IS NULL
                AND dt_clipagem > '2025-05-01'
                AND titulo IS NOT NULL
                AND t1.deleted_at IS NULL
                AND t2.deleted_at IS NULL
                AND t3.deleted_at IS NULL
                ORDER BY id_fonte";

        $noticias = DB::select($sql);

        return view('noticia-impressa/retorno', compact('total_nulos','inconsistencias','noticias'));
    }

    public function calcularValorRetornoImpresso()
    {
        $noticias = NoticiaImpresso::whereNotNull('nu_colunas')
            ->whereNotNull('nu_altura')
            ->whereNotNull('local_impressao')
            ->whereNotNull('id_fonte')
            ->whereNull('valor_retorno')
            ->get();

        foreach ($noticias as $noticia) {
            $colunaAlvo = $noticia->local_impressao;

            // Busca a fonte (jornal)
            $jornal = FonteImpressa::find($noticia->id_fonte);

            if (!$jornal || !isset($jornal->$colunaAlvo)) {
                continue; // pula se não encontrar valor correspondente
            }

            $valorCm = $jornal->$colunaAlvo;

            if (!is_numeric($valorCm)) {
                continue; // ignora valores inválidos
            }

            $valorRetorno = $noticia->nu_colunas * $noticia->nu_altura * $valorCm;

            // Atualiza apenas se o valor for positivo
            $noticia->valor_retorno = $valorRetorno;
            $noticia->save();
        }

        return redirect('noticia/impresso/retorno')->withInput();
    }
}