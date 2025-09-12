<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Area;
use App\Models\Cliente;
use App\Models\ClienteArea;
use App\Models\EnderecoEletronico;
use App\Models\Pessoa;
use App\Models\NoticiaWeb;
use App\Utils;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Storage;
use App\Services\RelatorioService;
use App\Models\RelatorioGerado;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    private $client_id;
    private $periodo_padrao;

    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','cliente');
        $this->carbon = new Carbon();
        
        // Obtém o ID do cliente logado da sessão
        $this->client_id = session('cliente')['id'] ?? null;
    }

    public function index(Request $request): View
    {
        $nome = ($request->nome) ? $request->nome  : "";

        if($request->isMethod('GET')){

            $clientes = Cliente::orderBy('nome')->paginate(10);
        }

        if($request->isMethod('POST')){

            $cliente = Cliente::query();

            $cliente->when($nome, function ($q) use ($nome) {

                return $q->where('nome', 'ILIKE', '%'.trim($nome).'%');
            });
            
            $clientes = $cliente->orderBy('nome')->paginate(10);
        }

        return view('cliente/index',compact('clientes','nome'));
    }

    public function relatorios(Request $request): View
    {
        Session::put('url','relatorios');
        Session::put('sub-menu','cliente-relatorios');

        $clientes = Cliente::where('fl_ativo', true)->orderBy('nome')->get();

        return view('cliente/relatorios/listar');
    }

    public function flagsMidia($id)
    {
        $cliente = Cliente::find($id);
        return response()->json([
            'fl_impresso' => (bool) $cliente->fl_impresso,
            'fl_web' => (bool) $cliente->fl_web,
            'fl_radio' => (bool) $cliente->fl_radio,
            'fl_tv' => (bool) $cliente->fl_tv,
        ]);
    }

    public function configuracoes($id)
    {
        $cliente = Cliente::find($id);
        
        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente não encontrado'
            ], 404);
        }
        
        return response()->json([
            'fl_impresso' => (bool) $cliente->fl_impresso,
            'fl_web' => (bool) $cliente->fl_web,
            'fl_radio' => (bool) $cliente->fl_radio,
            'fl_tv' => (bool) $cliente->fl_tv,
            'fl_areas' => (bool) $cliente->fl_areas,
            'fl_sentimento' => (bool) $cliente->fl_sentimento,
            'fl_retorno_midia' => (bool) $cliente->fl_retorno_midia,
            'fl_print' => (bool) $cliente->fl_print,
        ]);
    }

    public function gerarRelatorios(Request $request): View
    {
        Session::put('url','relatorios');
        Session::put('sub-menu','cliente-gerar-relatorios');

        $clientes = array();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        // Buscar dados do cliente logado usando múltiplas estratégias
        $cliente_id = $this->client_id;

        if($this->client_id){
        
            // Fallback: tentar obter da sessão se não encontrar no atributo
            if (!$cliente_id) {
                $cliente_id = session('cliente')['id'] ?? null;
            }
            
            // Fallback: tentar obter do usuário autenticado
            if (!$cliente_id && Auth::check()) {
                $cliente_id = Auth::user()->client_id;
            }
            
            $cliente = null;
            if ($cliente_id) {
                $cliente = Cliente::find($cliente_id);
            }
        } else {
            $cliente = null;
        }

        $tipo_data = ($request->tipo_data) ? $request->tipo_data : 'data_cadastro';
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d")." 00:00:00";
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d")." 23:59:59";
        
        // Usar as flags do cliente em vez das do request
        $fl_web = $cliente && $cliente->fl_web ? true : false;
        $fl_tv = $cliente && $cliente->fl_tv ? true : false;
        $fl_impresso = $cliente && $cliente->fl_impresso ? true : false;
        $fl_radio = $cliente && $cliente->fl_radio ? true : false;
        
        // Flag para controlar visibilidade das áreas (se true, MOSTRA áreas)
        $fl_areas = $cliente && $cliente->fl_areas ? true : false;
        
        // Flag para controlar visibilidade do sentimento (se true, MOSTRA sentimento)
        $fl_sentimento = $cliente && $cliente->fl_sentimento ? true : false;
        
        // Flag para controlar visibilidade do retorno de mídia (se true, MOSTRA valores)
        $fl_retorno_midia = $cliente && $cliente->fl_retorno_midia ? true : false;
        
        // Flag para controlar visibilidade dos botões de relatório com imagens (se true, MOSTRA botões)
        $fl_print = $cliente && $cliente->fl_print ? true : false;
        
        // Debug simples
        if ($cliente) {
            Log::info('DEBUG FL_PRINT - Cliente: ' . $cliente->nome . ' (ID: ' . $cliente->id . ')');
            Log::info('DEBUG FL_PRINT - fl_print do banco: ' . (property_exists($cliente, 'fl_print') ? ($cliente->fl_print ? 'TRUE' : 'FALSE') : 'PROPRIEDADE NÃO EXISTE'));
            Log::info('DEBUG FL_PRINT - fl_print final: ' . ($fl_print ? 'TRUE' : 'FALSE'));
        }

        $relatorios = array();

        return view('cliente/noticias', compact('relatorios','tipo_data','dt_inicial','dt_final','fl_web','fl_tv','fl_radio','fl_impresso','fl_areas','fl_sentimento','fl_retorno_midia','fl_print','cliente','clientes'));
    }

    public function create(): View
    {
        $areas = Area::all();
        return view('cliente/novo', compact('areas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $fl_ativo = $request->fl_ativo == true ? true : false;
        $fl_print = $request->fl_print == true ? true : false;
        $fl_sentimento = $request->fl_sentimento == true ? true : false;
        $fl_retorno_midia = $request->fl_retorno_midia == true ? true : false;

        $fl_tv = $request->fl_tv == true ? true : false;
        $fl_impresso = $request->fl_impresso == true ? true : false;
        $fl_web = $request->fl_web == true ? true : false;
        $fl_radio = $request->fl_radio == true ? true : false;

        $fl_relatorio_consolidado = $request->fl_relatorio_consolidado == true ? true : false;
        $fl_relatorio_completo = $request->fl_relatorio_completo == true ? true : false;
        $fl_link_relatorio = $request->fl_link_relatorio == true ? true : false;
        $fl_area_restrita = $request->fl_area_restrita == true ? true : false;    
        $fl_texto_logo = $request->fl_texto_logo == true ? true : false;    
        
        try {

            $request->merge(['fl_print' => $fl_print]);
            $request->merge(['fl_print' => $fl_print]);
            $request->merge(['fl_sentimento' => $fl_sentimento]);
            $request->merge(['fl_retorno_midia' => $fl_retorno_midia]);
            $request->merge(['fl_texto_logo' => $fl_texto_logo]);

            $request->merge(['fl_tv' => $fl_tv]);
            $request->merge(['fl_impresso' => $fl_impresso]);
            $request->merge(['fl_web' => $fl_web]);
            $request->merge(['fl_radio' => $fl_radio]);

            $request->merge(['fl_relatorio_consolidado' => $fl_relatorio_consolidado]);
            $request->merge(['fl_relatorio_completo' => $fl_relatorio_completo]);
            $request->merge(['fl_link_relatorio' => $fl_link_relatorio]);
            $request->merge(['fl_area_restrita' => $fl_area_restrita]);

            $cliente = Cliente::create([
                'fl_ativo' => $fl_ativo,
                'fl_print' => $request->fl_print,
                'fl_texto_logo' => $request->fl_texto_logo,
                'fl_tv' => $request->fl_tv,
                'fl_impresso' => $request->fl_impresso,
                'fl_web' => $request->fl_web,
                'fl_radio' => $request->fl_radio,
                'fl_relatorio_consolidado' => $request->fl_relatorio_consolidado,
                'fl_relatorio_completo' => $request->fl_relatorio_completo,
                'fl_link_relatorio' => $request->fl_link_relatorio,
                'fl_area_restrita' => $request->fl_area_restrita,
                'fl_sentimento' => $fl_sentimento,
                'nome' => $request->nome
            ]);

            if($request->logo){
                
                $logo = $request->file('logo');
                $fileInfo = $logo->getClientOriginalName();
                $filesize = $logo->getSize()/1024/1024;
                $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
                $extension = pathinfo($fileInfo, PATHINFO_EXTENSION);
                $file_name = $cliente->id.'.'.$extension;
                $logo->move(public_path('img/clientes/logo'),$file_name);
                
                $cliente->update(['logo' => $file_name]);
            }

            if($request->logo_expandida){
                
                $logo_expandida = $request->file('logo_expandida');
                $fileInfo = $logo_expandida->getClientOriginalName();
                $filesize = $logo_expandida->getSize()/1024/1024;
                $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
                $extension = pathinfo($fileInfo, PATHINFO_EXTENSION);
                $file_name = $cliente->id.'.'.$extension;
                $logo_expandida->move(public_path('img/clientes/logo_expandida'),$file_name);
                
                $cliente->update(['logo_expandida' => $file_name]);
            }

            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

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
            return redirect('cliente')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('cliente/create')->withInput();
        }
    }

    public function edit($id): View
    {
        $cliente = Cliente::with(['areas'])->find($id);
        $areas  = Area::all();
        //$emails = EnderecoEletronico::where('pessoa_id', $cliente->pessoa->id)->get();
        $emails = array();

        //$emails = json_decode($emails);

        return view('cliente/editar',compact('cliente', 'emails', 'areas'));
    }

    public function noticias(Request $request)
    {
        Session::put('url','cliente-noticias');

        $dados = array();
        $cliente_selecionado = Auth::user()->client_id;
        $cliente = Cliente::find($cliente_selecionado);
        $tipo_data = ($request->tipo_data) ? $request->tipo_data : 'created_at';
      
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d")." 00:00:00";
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d")." 23:59:59";

        $dt_inicial_formatada = ($request->dt_inicial) ? $request->dt_inicial : date("d/m/Y");
        $dt_final_formatada = ($request->dt_final) ? $request->dt_final : date("d/m/Y");

        $fl_web = $request->fl_web == "true" ? true : false;
        $fl_tv = $request->fl_tv == "true" ? true : false;
        $fl_impresso = $request->fl_impresso == "true" ? true : false;
        $fl_radio = $request->fl_radio == "true" ? true : false;

        $termo = ($request->termo) ? $request->termo : null;
        $sentimento = ($request->sentimento) ? $request->sentimento : null;

        if(!$fl_impresso and !$fl_radio and !$fl_web and !$fl_tv){

            $dados_impresso = $this->dadosImpresso($dt_inicial, $dt_final,$cliente_selecionado, $termo);
            $dados_radio    = $this->dadosRadio($dt_inicial, $dt_final,$cliente_selecionado, $termo);
            $dados_web      = $this->dadosWeb($dt_inicial, $dt_final,$cliente_selecionado, $termo);
            $dados_tv       = $this->dadosTv($dt_inicial, $dt_final,$cliente_selecionado, $termo);

            $dados = array_merge($dados_impresso, $dados_radio, $dados_web, $dados_tv);

        }else{

            $dados_impresso = ($fl_impresso) ? $this->dadosImpresso($dt_inicial, $dt_final,$cliente_selecionado, $termo) : array();
            $dados_radio    = ($fl_radio) ? $this->dadosRadio($dt_inicial, $dt_final,$cliente_selecionado, $termo) : array();
            $dados_web      = ($fl_web) ? $this->dadosWeb($dt_inicial, $dt_final,$cliente_selecionado, $termo) : array();
            $dados_tv       = ($fl_tv) ? $this->dadosTv($dt_inicial, $dt_final,$cliente_selecionado, $termo) : array();

            $dados = array_merge($dados_impresso, $dados_radio, $dados_web, $dados_tv);

        }

        return view('cliente/noticias', compact('cliente','dados','tipo_data','dt_inicial','dt_final','fl_web','fl_tv','fl_radio','fl_impresso','termo','sentimento'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $fl_ativo = $request->fl_ativo == true ? true : false;
        $fl_print = $request->fl_print == true ? true : false;
        $fl_sentimento = $request->fl_sentimento == true ? true : false;
        $fl_retorno_midia = $request->fl_retorno_midia == true ? true : false;

        $fl_tv = $request->fl_tv == true ? true : false;
        $fl_impresso = $request->fl_impresso == true ? true : false;
        $fl_web = $request->fl_web == true ? true : false;
        $fl_radio = $request->fl_radio == true ? true : false;

        $fl_relatorio_consolidado = $request->fl_relatorio_consolidado == true ? true : false;
        $fl_relatorio_completo = $request->fl_relatorio_completo == true ? true : false;
        $fl_link_relatorio = $request->fl_link_relatorio == true ? true : false;
        $fl_area_restrita = $request->fl_area_restrita == true ? true : false;    
        $fl_texto_logo = $request->fl_texto_logo == true ? true : false;   
        
        $cliente = Cliente::find($id);

        try {

            $request->merge(['fl_ativo' => $fl_ativo]);
            $request->merge(['fl_print' => $fl_print]);
            $request->merge(['fl_sentimento' => $fl_sentimento]);
            $request->merge(['fl_retorno_midia' => $fl_retorno_midia]);
            $request->merge(['fl_texto_logo' => $fl_texto_logo]);
            
            $request->merge(['fl_tv' => $fl_tv]);
            $request->merge(['fl_impresso' => $fl_impresso]);
            $request->merge(['fl_web' => $fl_web]);
            $request->merge(['fl_radio' => $fl_radio]);

            $request->merge(['fl_relatorio_consolidado' => $fl_relatorio_consolidado]);
            $request->merge(['fl_relatorio_completo' => $fl_relatorio_completo]);
            $request->merge(['fl_link_relatorio' => $fl_link_relatorio]);
            $request->merge(['fl_area_restrita' => $fl_area_restrita]);

            $cliente->update($request->all());

            //$this->cadastrarEnderecoEletronico($request, $cliente);
            //$this->gerenciaClienteArea($request, $cliente);

            if($request->logo){
                
                $logo = $request->file('logo');
                $fileInfo = $logo->getClientOriginalName();
                $filesize = $logo->getSize()/1024/1024;
                $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
                $extension = pathinfo($fileInfo, PATHINFO_EXTENSION);
                $file_name = $cliente->id.'.'.$extension;
                $logo->move(public_path('img/clientes/logo'),$file_name);
                
                $cliente->update(['logo' => $file_name]);
            }

            if($request->logo_expandida){
                
                $logo_expandida = $request->file('logo_expandida');
                $fileInfo = $logo_expandida->getClientOriginalName();
                $filesize = $logo_expandida->getSize()/1024/1024;
                $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
                $extension = pathinfo($fileInfo, PATHINFO_EXTENSION);
                $file_name = $cliente->id.'.'.$extension;
                $logo_expandida->move(public_path('img/clientes/logo_expandida'),$file_name);
                
                $cliente->update(['logo_expandida' => $file_name]);
            }

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
                'flag' => false,
                'msg' => "Ocorreu um erro ao atualizar o registro".$e
            );
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
        } else {
            Flash::error($retorno['msg']);
        }

        return redirect()->route('cliente.edit', $cliente->id)->withInput();
    }

    public function validaCpf(Request $request):JsonResponse
    {
        $cpfCnpj = preg_replace('/\D/', '', $request->cpf_cnpj);

        try{
            $request->validate([
                'cpf_cnpj' => 'cpf_ou_cnpj'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "msg" => "CPF/CNPJ inválido"], 200);
        }

        $pessoa = Pessoa::where('cpf_cnpj', $cpfCnpj)->get();

        if(empty($pessoa->items)) {
            return response()->json(["success" => true], 200);
        }

        if(empty($request->cliente_id)) {
            return response()->json(["success" => false, "msg" => "CPF/CNPJ já cadastrado"], 200);
        }

        $pessoaCliente = Cliente::find($request->cliente_id)->pessoa;

        if($pessoaCliente->id != $pessoa->items[0]->id) {
            return response()->json(["success" => false, "msg" => "CPF/CNPJ já cadastrado"], 200);
        }

        return response()->json(["success" => true], 200);
    }

    public function buscarClientes(Request $request)
    {
        $clientes = Cliente::select('clientes.id', 'nome as text');

        $clientes->where('fl_ativo', true);

        if(!empty($request->query('q'))) {
            $replace = preg_replace('!\s+!', ' ', $request->query('q'));
            $busca = str_replace(' ', '%', $replace);
            $clientes->whereRaw('nome ILIKE ?', ['%' . strtolower($busca) . '%']);
        }

        $result = $clientes->orderBy('nome', 'asc')->get();
        return response()->json($result);
    }

    public function getAreasCliente(Request $request)
    {
        $areas = ClienteArea::where('cliente_id', $request->query('cliente'));
        $areas->join('area', 'area.id', '=', 'area_cliente.area_id');
        $areas->where(['ativo' => true]);
        $result = $areas->select('area.id', 'area.descricao')->get();

        return response()->json($result);
    }

    private function cadastrarEnderecoEletronico(Request $request, Cliente $cliente): void
    {
        if($request->email){

            foreach($request->email as $email) {
                if(empty($email)) {
                    continue;
                }

                EnderecoEletronico::create([
                    'pessoa_id' => $cliente->pessoa->id,
                    'endereco' => $email,
                    'tipo_id' => 1
                ]);
            }
        }
    }

    public function removerArea($id)
    {
        $cliente_area = ClienteArea::where('id', $id)->first();

        $cliente_area->delete();

        return redirect()->back();
    }

    public function adicionarArea(Request $request)
    {
        dd($request->all());

        $area = Area::where('descricao', 'ILIKE', '%'.trim($request->ds_area).'%')->first();

        if(!$area){
            $area = Area::create(['descricao' => $request->ds_area]);
        }

        $cliente_area = ClienteArea::where('cliente_id', $request->id_cliente)->where('area_id', $area->id)->first();

        if(!$cliente_area){
            $created = ClienteArea::create([
                'cliente_id' => $request->id_cliente,
                'area_id' => $area->id,
                'ativo' => $request->situacao
            ]);
        }
    }

    public function alteraSituacao($id){

        $cliente_area = ClienteArea::where('id', $id)->first();

        $cliente_area->ativo = !$cliente_area->ativo;
        $cliente_area->save();

        Flash::success('<i class="fa fa-check"></i> Preferência do programa atualizada com sucesso');

        return redirect()->back()->withInput();
    }

    private function gerenciaClienteArea(Request $request, Cliente $cliente): void
    {
        $id = [];

        if($request->area){
            try {
                foreach($request->area as $key => $area) {

                    if(!empty($request->id[$key])) {
                        $id[] = $request->id[$key];
                        $clienteArea = ClienteArea::find($request->id[$key]);
                        $clienteArea->update([
                            'area_id' => $area,
                            'expressao' => ($request->expressao) ? $request->expressao[$key] : '',
                            'ativo' => $request->status[$key] == "true"
                        ]);
                        continue;
                    }

                    if($request->expressao and empty($request->expressao[$key])) {
                        continue;
                    }

                    $created = ClienteArea::create([
                        'cliente_id' => $cliente->id,
                        'area_id' => $area,
                        'expressao' => ($request->expressao) ? $request->expressao[$key] : '',
                        'ativo' => $request->status[$key] == "true"
                    ]);
                    $id[] = $created->id;
                }

                $remover = ClienteArea::whereNotIn('id', $id)->where('cliente_id', $cliente->id)->get();

                foreach($remover as $excluir) {
                    $excluir->delete();
                }


            } catch(\Exception $e) {
                throw new \RuntimeException($e);
            }
        }
    }

    public function dadosImpresso($dt_inicial, $dt_final,$cliente_selecionado, $termo)
    {
        $sql = "SELECT t1.id, 
                    sg_estado,
                    nm_estado,
                    nm_cidade,
                    '' as secao,
                    nu_pagina_atual as pagina,
                    '' as programa,
                    titulo, 
                    t4.nome as cliente,
                    t3.cliente_id as id_cliente,
                    tipo_id,
                    'impresso' as tipo, 
                    TO_CHAR(dt_clipagem, 'DD/MM/YYYY') AS data_formatada,
                    t2.nome as fonte,
                    t1.sinopse,
                    t3.sentimento,
                    'imagem' as tipo_midia,
                    ds_caminho_img as midia,
                    '' as url_noticia,
                    valor_retorno as valor_retorno
                FROM noticia_impresso t1
                JOIN jornal_online t2 ON t2.id = t1.id_fonte
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND tipo_id = 1
                JOIN clientes t4 ON t4.id = t3.cliente_id
                LEFT JOIN cidade t5 ON t5.cd_cidade = t1.cd_cidade
                LEFT JOIN estado t6 ON t6.cd_estado = t1.cd_estado
                WHERE 1=1
                AND t1.deleted_at IS NULL
                AND t3.deleted_at IS NULL
                AND t1.valor_retorno IS NOT NULL
                AND t1.dt_clipagem BETWEEN '$dt_inicial' AND '$dt_final'";

        if($cliente_selecionado){
            $sql .= ' AND t3.cliente_id = '.$cliente_selecionado;
        }

        if($termo){
            $sql .= " AND t1.sinopse ilike '%$termo%'";
        }

        return $dados = DB::select($sql);
    }

    public function dadosRadio($dt_inicial, $dt_final,$cliente_selecionado, $termo)
    {
        $sql = "SELECT t1.id, 
                    sg_estado,
                    nm_estado,
                    nm_cidade,
                    '' as secao,
                    '' as pagina,
                    t7.nome_programa as programa,
                    titulo, 
                    t4.nome as cliente,
                    t3.cliente_id as id_cliente,
                    tipo_id,
                    'radio' as tipo, 
                    TO_CHAR(dt_clipagem, 'DD/MM/YYYY') AS data_formatada,
                    t2.nome_emissora as fonte,
                    t1.sinopse,
                    t3.sentimento,
                    'audio' as tipo_midia,
                    ds_caminho_audio as midia,
                     '' as url_noticia,
                    valor_retorno as valor_retorno
                FROM noticia_radio t1
                JOIN emissora_radio t2 ON t2.id = t1.emissora_id
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND tipo_id = 3
                JOIN clientes t4 ON t4.id = t3.cliente_id
                LEFT JOIN cidade t5 ON t5.cd_cidade = t1.cd_cidade
                LEFT JOIN estado t6 ON t6.cd_estado = t1.cd_estado
                LEFT JOIN programa_emissora_radio t7 ON t7.id = t1.programa_id
                WHERE 1=1
                AND t1.deleted_at IS NULL
                AND t3.deleted_at IS NULL
                AND t1.duracao IS NOT NULL
                AND t1.dt_clipagem BETWEEN '$dt_inicial' AND '$dt_final'";

        if($cliente_selecionado){
            $sql .= ' AND t3.cliente_id = '.$cliente_selecionado;
        }

        if($termo){
            $sql .= " AND t1.sinopse ilike '%$termo%'";
        }

        return $dados = DB::select($sql);
    }

    public function dadosWeb($dt_inicial, $dt_final,$cliente_selecionado, $termo)
    {
        $sql = "SELECT t1.id, 
                    sg_estado,
                    nm_estado,
                    nm_cidade,
                    '' as secao,
                    '' as pagina,
                    '' as programa,
                    titulo_noticia as titulo, 
                    t5.nome as cliente,
                    t3.cliente_id as id_cliente,
                    tipo_id,
                    'web' as tipo, 
                    TO_CHAR(data_noticia, 'DD/MM/YYYY') AS data_formatada,
                    t2.nome as fonte,
                    t4.conteudo as sinopse,
                    t3.sentimento,
                    'imagem' as tipo_midia,
                    ds_caminho_img as midia,
                    url_noticia,
                    t1.nu_valor as valor_retorno
                FROM noticias_web t1
                JOIN fonte_web t2 ON t2.id = t1.id_fonte
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND tipo_id = 2
                JOIN conteudo_noticia_web t4 ON t4.id_noticia_web = t1.id
                JOIN clientes t5 ON t5.id = t3.cliente_id
                LEFT JOIN cidade t6 ON t6.cd_cidade = t1.cd_cidade
                LEFT JOIN estado t7 ON t7.cd_estado = t1.cd_estado
                WHERE 1=1
                AND t1.deleted_at IS NULL
                AND t3.deleted_at IS NULL
                AND t1.data_noticia BETWEEN '$dt_inicial' AND '$dt_final'";

        if($cliente_selecionado){
            $sql .= ' AND t3.cliente_id = '.$cliente_selecionado;
        }

        if($termo){
            $sql .= " AND t4.conteudo ilike '%$termo%'";
        }

        $dados = DB::select($sql);

        foreach($dados as $dado){

            $noticia_web = NoticiaWeb::where('id', $dado->id)->where('ds_caminho_img','=',null)->first();

            if($noticia_web){

                if (Storage::disk('s3')->exists($noticia_web->path_screenshot)) {
                    $arquivo = Storage::disk('s3')->get($noticia_web->path_screenshot);
                    $filename = $noticia_web->id.".jpg";
                    Storage::disk('web-img')->put($filename, $arquivo);

                    $noticia_web->ds_caminho_img = $filename;
                    $noticia_web->save();
                }

            }
        }            

        return $dados;
    }

    public function dadosTv($dt_inicial, $dt_final,$cliente_selecionado, $termo)
    {
        $sql = "SELECT t1.id, 
                    sg_estado,
                    nm_estado,
                    nm_cidade,
                    '' as secao,
                    '' as pagina,
                    t7.nome_programa as programa,
                    '' as titulo, 
                    t4.nome as cliente,
                    t3.cliente_id as id_cliente,
                    tipo_id,
                    'tv' as tipo, 
                    TO_CHAR(dt_noticia, 'DD/MM/YYYY') AS data_formatada,
                    t2.nome_emissora as fonte,
                    sinopse,
                    t3.sentimento,
                    'imagem' as tipo_midia,
                    ds_caminho_video as midia,
                    '' as url_noticia,
                    valor_retorno as valor_retorno
                FROM noticia_tv t1
                JOIN emissora_web t2 ON t2.id = t1.emissora_id
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND tipo_id = 4
                JOIN clientes t4 ON t4.id = t3.cliente_id
                LEFT JOIN cidade t5 ON t5.cd_cidade = t1.cd_cidade
                LEFT JOIN estado t6 ON t6.cd_estado = t1.cd_estado
                LEFT JOIN programa_emissora_web t7 ON t7.id = t1.programa_id
                WHERE 1=1
                AND t1.deleted_at IS NULL
                AND t3.deleted_at IS NULL
                AND t1.duracao IS NOT NULL
                AND t1.dt_noticia BETWEEN '$dt_inicial' AND '$dt_final'";

        if($cliente_selecionado){
            $sql .= ' AND t3.cliente_id = '.$cliente_selecionado;
        }

        if($termo){
            $sql .= " AND t1.sinopse ilike '%$termo%'";
        }

        return $dados = DB::select($sql);
    }

    /**
     * Lista notícias por período com filtros aplicados (substitui /listar_noticias do Flask)
     */
    public function listarNoticias(Request $request): JsonResponse
    {
        // Configurar timeouts para consultas longas
        set_time_limit(300); // 5 minutos para listagem
        ini_set('max_execution_time', 300);
        
        Log::info('=== INICIANDO listarNoticias ===', [
            'request_data' => $request->all(),
            'client_id' => $this->client_id,
            'session_cliente' => session('cliente')
        ]);
        
        try {
            // Usa o cliente logado da sessão
            if(Auth::user()->hasRole('cliente')){
                $clienteId = $this->client_id;
            }else{
                $clienteId = $request->cliente;
            }

            $dataInicio = $request->input('data_inicio');
            $dataFim = $request->input('data_fim');
            $tipoFiltroData = $request->input('tipo_filtro_data', 'coleta'); // padrão: coleta
            $termo = $request->input('termo', null);
            
            Log::info('Dados extraídos da requisição:', [
                'clienteId' => $clienteId,
                'dataInicio' => $dataInicio,
                'dataFim' => $dataFim,
                'tipoFiltroData' => $tipoFiltroData,
                'termo' => $termo
            ]);
            
            // Filtros avançados
            $tiposMidia = $request->input('tipos_midia', ['web', 'tv', 'radio', 'impresso']);
            $statusFiltros = $request->input('status', ['positivo', 'negativo', 'neutro']);
            $retornoFiltro = $request->input('retorno', 'com_retorno');
            $valorFiltros = $request->input('valor', ['com_valor', 'sem_valor']);
            $areasFiltros = $request->input('areas', []);
            $semAreaFiltro = filter_var($request->input('sem_area', false), FILTER_VALIDATE_BOOLEAN);
            $tagsFiltros = $request->input('tags_filtro', []);
            
            Log::info('=== FILTROS EXTRAÍDOS DA REQUISIÇÃO ===', [
                'tiposMidia' => $tiposMidia,
                'statusFiltros' => $statusFiltros,
                'retornoFiltro' => $retornoFiltro,
                'valorFiltros' => $valorFiltros,
                'areasFiltros' => $areasFiltros,
                'areasFiltros_empty' => empty($areasFiltros),
                'areasFiltros_count' => count($areasFiltros),
                'semAreaFiltro' => $semAreaFiltro,
                'semAreaFiltro_raw' => $request->input('sem_area'),
                'semAreaFiltro_type' => gettype($semAreaFiltro),
                'tagsFiltros' => $tagsFiltros,
                'tem_tags_filtro' => !empty($tagsFiltros),
                'count_tags_filtro' => count($tagsFiltros)
            ]);
            
            // Validações
            if (!$clienteId || !$dataInicio || !$dataFim) {
                Log::warning('Validação falhou:', [
                    'clienteId' => $clienteId,
                    'dataInicio' => $dataInicio,
                    'dataFim' => $dataFim
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado ou campos obrigatórios não preenchidos'
                ], 400);
            }
            
            // Filtros de fontes/emissoras/programas
            $fontesFiltros = $request->input('fontes_filtro', []);
            
            // Monta filtros
            $filtros = [
                'tipos_midia' => $tiposMidia,
                'status' => $statusFiltros,
                'retorno' => [$retornoFiltro],
                'valor' => $valorFiltros,
                'areas' => $areasFiltros,
                'sem_area' => $semAreaFiltro,
                'tags_filtro' => $tagsFiltros,
                'fontes_filtro' => $fontesFiltros
            ];
            
            Log::info('=== FILTROS MONTADOS PARA RELATORIO SERVICE ===', [
                'filtros_completos' => $filtros,
                'tem_tags_no_filtro' => isset($filtros['tags_filtro']) && !empty($filtros['tags_filtro']),
                'sem_area_ativo' => $filtros['sem_area'] ?? false
            ]);
            
            $relatorioService = new RelatorioService();
            Log::info('RelatorioService criado com sucesso');
            
            // Verifica se cliente existe
            Log::info('Verificando se cliente existe...', ['clienteId' => $clienteId]);
            if (!$relatorioService->checkCliente($clienteId)) {
                Log::warning('Cliente não encontrado:', ['clienteId' => $clienteId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não encontrado'
                ], 404);
            }
            
            Log::info('Cliente existe, buscando notícias...');
            
            // Lista as notícias
            $noticias = $relatorioService->listarNoticiasPorPeriodoComFiltros($clienteId, $dataInicio, $dataFim, $filtros, $termo, $tipoFiltroData);
            
            Log::info('Notícias encontradas:', [
                'total_web' => count($noticias['web'] ?? []),
                'total_tv' => count($noticias['tv'] ?? []),
                'total_radio' => count($noticias['radio'] ?? []),
                'total_impresso' => count($noticias['impresso'] ?? [])
            ]);
            
            Log::info('Retornando resposta de sucesso');
            return response()->json([
                'success' => true,
                'message' => 'Notícias listadas com sucesso',
                'noticias' => $noticias,
                'filtros_aplicados' => $filtros
            ]);
            
        } catch (\Exception $e) {
            Log::error('=== ERRO EM listarNoticias ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gera relatório PDF (substitui /gerar_relatorio do Flask)
     */
    public function gerarRelatorioPDF(Request $request): JsonResponse
    {
        // Configurar timeouts para 1 hora
        set_time_limit(3600); // 1 hora
        ini_set('max_execution_time', 3600); // 1 hora
        ini_set('memory_limit', '1024M'); // 1GB de memória
        
        try {

            $dataInicio = $request->input('data_inicio');
            $dataFim = $request->input('data_fim');
            
            // IDs das notícias específicas
            $idsWeb = $request->input('ids_web', []);
            $idsImpresso = $request->input('ids_impresso', []);
            $idsTv = $request->input('ids_tv', []);
            $idsRadio = $request->input('ids_radio', []);

            // Usa o cliente logado da sessão ou parâmetro
            if(Auth::user()->hasRole('cliente')){
                $clienteId = $this->client_id;
            }else{
                $clienteId = $request->cliente;
            }
            
            // Validações
            if (!$clienteId || !$dataInicio || !$dataFim) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado ou campos obrigatórios não preenchidos'
                ], 400);
            }
            
            $relatorioService = new RelatorioService();
            
            // Verifica se cliente existe e busca suas configurações
            if (!$relatorioService->checkCliente($clienteId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não encontrado'
                ], 404);
            }
            
            // Busca configurações do cliente
            $cliente = Cliente::find($clienteId);
            $temPermissaoRetorno = $cliente && $cliente->fl_retorno_midia ? true : false;
            $temPermissaoSentimento = $cliente && $cliente->fl_sentimento ? true : false;
            
            // Extrai flag de mostrar retorno de mídia no relatório (vem do frontend)
            $mostrarRetornoRelatorio = $request->input('mostrar_retorno_relatorio', 'true') === 'true';
            
            // Extrai flag de mostrar sentimento no relatório (vem do frontend)
            $mostrarSentimentoRelatorio = $request->input('mostrar_sentimento_relatorio', 'true') === 'true';
            
            // Se o cliente não tem permissão, força as flags para false
            if (!$temPermissaoRetorno) {
                $mostrarRetornoRelatorio = false;
            }
            
            if (!$temPermissaoSentimento) {
                $mostrarSentimentoRelatorio = false;
            }
            
            // Define tipos de mídia baseado nas permissões do cliente
            $tiposMidiaPermitidos = [];
            if ($cliente && $cliente->fl_web) {
                $tiposMidiaPermitidos[] = 'web';
            }
            if ($cliente && $cliente->fl_tv) {
                $tiposMidiaPermitidos[] = 'tv';
            }
            if ($cliente && $cliente->fl_radio) {
                $tiposMidiaPermitidos[] = 'radio';
            }
            if ($cliente && $cliente->fl_impresso) {
                $tiposMidiaPermitidos[] = 'impresso';
            }
            
            Log::info('Configurações de retorno de mídia:', [
                'cliente_id' => $clienteId,
                'tem_permissao_retorno' => $temPermissaoRetorno,
                'mostrar_retorno_relatorio' => $mostrarRetornoRelatorio
            ]);
            
            Log::info('Configurações de sentimento:', [
                'cliente_id' => $clienteId,
                'tem_permissao_sentimento' => $temPermissaoSentimento,
                'mostrar_sentimento_relatorio' => $mostrarSentimentoRelatorio,
                'raw_input_mostrar_sentimento' => $request->input('mostrar_sentimento_relatorio'),
                'cliente_fl_sentimento' => $cliente ? $cliente->fl_sentimento : 'null'
            ]);
            
            Log::info('Tipos de mídia permitidos:', [
                'cliente_id' => $clienteId,
                'tipos_midia_permitidos' => $tiposMidiaPermitidos,
                'cliente_fl_web' => $cliente ? $cliente->fl_web : 'null',
                'cliente_fl_tv' => $cliente ? $cliente->fl_tv : 'null',
                'cliente_fl_radio' => $cliente ? $cliente->fl_radio : 'null',
                'cliente_fl_impresso' => $cliente ? $cliente->fl_impresso : 'null'
            ]);
            
            // Gera nome do arquivo
            $dataInicioClean = str_replace('-', '', $dataInicio);
            $dataFimClean = str_replace('-', '', $dataFim);
            $nomeArquivo = "relatorio_{$clienteId}_{$dataInicioClean}_{$dataFimClean}.pdf";
            
            // Monta filtros com IDs específicos se fornecidos
            $filtros = [
                'tipos_midia' => $tiposMidiaPermitidos,  // CORRIGIDO: usa apenas os tipos de mídia que o cliente tem permissão para não mostrar seções vazias
                'status' => ['positivo', 'negativo', 'neutro'],
                'retorno' => ['com_retorno'],
                'valor' => ['com_valor', 'sem_valor'],
                'areas' => [],
                'mostrar_retorno_relatorio' => $mostrarRetornoRelatorio,  // NOVO: controla se mostra seções de retorno
                'tem_permissao_retorno' => $temPermissaoRetorno,  // NOVO: indica se o cliente tem permissão para retorno
                'mostrar_sentimento_relatorio' => $mostrarSentimentoRelatorio,  // NOVO: controla se mostra seções de sentimento
                'tem_permissao_sentimento' => $temPermissaoSentimento  // NOVO: indica se o cliente tem permissão para sentimento
            ];
            
            if (!empty($idsWeb) || !empty($idsImpresso) || !empty($idsTv) || !empty($idsRadio)) {
                $filtros['ids_especificos'] = [
                    'web' => $idsWeb,
                    'impresso' => $idsImpresso,
                    'tv' => $idsTv,
                    'radio' => $idsRadio
                ];
            }
            
            // Chama o script Python para gerar PDF
            $filtrosJson = json_encode($filtros);
            
            // Caminho para o diretório dos scripts Python
            $pythonDir = base_path('python/relatorios');
            
            // Comando melhorado com tratamento de erros
            $escapedPythonDir = escapeshellarg($pythonDir);
            $escapedClienteId = escapeshellarg($clienteId);
            $escapedDataInicio = escapeshellarg($dataInicio);
            $escapedDataFim = escapeshellarg($dataFim);
            $escapedNomeArquivo = escapeshellarg($nomeArquivo);
            $escapedFiltros = escapeshellarg($filtrosJson);
            
            // Tenta primeiro python3, depois python
            $comando = "cd $escapedPythonDir && (python3 main.py --cliente $escapedClienteId --data_inicio $escapedDataInicio --data_fim $escapedDataFim --output $escapedNomeArquivo --filtros $escapedFiltros 2>&1 || python main.py --cliente $escapedClienteId --data_inicio $escapedDataInicio --data_fim $escapedDataFim --output $escapedNomeArquivo --filtros $escapedFiltros 2>&1)";
            
            Log::info('Executando comando Python: ' . $comando);
            
            $resultado = shell_exec($comando);
            
            Log::info('Resultado do comando Python: ' . ($resultado ?? 'null'));
            
            // Verifica se o arquivo foi gerado no diretório dos scripts Python
            $caminhoArquivoPython = $pythonDir . '/output/' . $nomeArquivo;
            $caminhoArquivoDestino = storage_path('app/public/relatorios/' . $nomeArquivo);
            
            // Cria o diretório de destino se não existir
            if (!is_dir(dirname($caminhoArquivoDestino))) {
                mkdir(dirname($caminhoArquivoDestino), 0755, true);
            }
            
            // Move o arquivo para o diretório público
            if (file_exists($caminhoArquivoPython)) {
                copy($caminhoArquivoPython, $caminhoArquivoDestino);
                unlink($caminhoArquivoPython); // Remove o arquivo temporário
            }
            
            $caminhoArquivo = $caminhoArquivoDestino;
            
            if (file_exists($caminhoArquivo)) {
                try {
                    // Upload para S3
                    $s3Path = 'relatorios/' . date('Y/m/') . $nomeArquivo;
                    $s3Upload = Storage::disk('relatorios-s3')->put($s3Path, file_get_contents($caminhoArquivo), [
                        'ContentType' => 'application/pdf',
                        'CacheControl' => 'max-age=31536000',
                    ]);
                    
                    if ($s3Upload) {
                        // Gera URL pública do S3
                        $urlS3 = 'https://relatorios-studio-clipagem.s3.amazonaws.com/' . $s3Path;
                        
                        // Calcula total de notícias e valor de retorno (estimativa)
                        $totalNoticias = count($idsWeb) + count($idsImpresso) + count($idsTv) + count($idsRadio);
                        
                        // Salva registro no banco
                        $dadosRelatorio = [
                            'titulo' => 'Relatório Completo - ' . now()->format('d/m/Y H:i'),
                            'descricao' => 'Relatório PDF gerado automaticamente pelo sistema',
                            'nome_arquivo' => $nomeArquivo,
                            'url_s3' => $urlS3,
                            'data_inicio' => $dataInicio,
                            'data_fim' => $dataFim,
                            'cliente_id' => $clienteId,
                            'tamanho_arquivo' => filesize($caminhoArquivo),
                            'total_noticias' => $totalNoticias,
                            'tipos_midia' => array_filter([
                                'web' => !empty($idsWeb),
                                'impresso' => !empty($idsImpresso),
                                'tv' => !empty($idsTv),
                                'radio' => !empty($idsRadio)
                            ]),
                            'filtros' => [
                                'tipo_filtro_data' => $request->input('tipo_filtro_data'),
                                'termo_busca' => $request->input('termo'),
                                'tags_selecionadas' => $request->input('tags', []),
                                'fontes_selecionadas' => $request->input('fontes', []),
                                'mostrar_retorno_relatorio' => $mostrarRetornoRelatorio,
                                'mostrar_sentimento_relatorio' => $mostrarSentimentoRelatorio,
                                'ids_especificos' => [
                                    'web' => $idsWeb,
                                    'impresso' => $idsImpresso,
                                    'tv' => $idsTv,
                                    'radio' => $idsRadio
                                ]
                            ]
                        ];
                        
                        $relatorioGerado = RelatorioGerado::criarRelatorioGerado($dadosRelatorio);
                        
                        Log::info('Relatório salvo no S3 e banco de dados', [
                            'arquivo' => $nomeArquivo,
                            'url_s3' => $urlS3,
                            'registro_id' => $relatorioGerado->id,
                            'cliente_id' => $clienteId
                        ]);
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Relatório gerado e salvo com sucesso',
                            'arquivo' => $nomeArquivo,
                            'cliente' => $clienteId,
                            'download_url' => url('cliente/'.$clienteId.'/relatorios/download/' . $nomeArquivo),
                            'url_s3' => $urlS3,
                            'registro_id' => $relatorioGerado->id
                        ]);
                    } else {
                        Log::error('Falha no upload para S3', ['arquivo' => $nomeArquivo]);
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Relatório gerado com sucesso, mas falha no backup S3',
                            'arquivo' => $nomeArquivo,
                            'cliente' => $clienteId,
                            'download_url' => url('cliente/'.$clienteId.'/relatorios/download/' . $nomeArquivo)
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Erro ao salvar relatório no S3/banco', [
                        'erro' => $e->getMessage(),
                        'arquivo' => $nomeArquivo
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Relatório gerado com sucesso, mas falha no backup S3',
                        'arquivo' => $nomeArquivo,
                        'cliente' => $clienteId,
                        'download_url' => url('cliente/'.$clienteId.'/relatorios/download/' . $nomeArquivo)
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar relatório PDF'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Resultado do comando Python: ' . ($resultado ?? 'null'));
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'command_result' => $resultado ?? 'null'
                ] : null
            ], 500);
        }
    }

    /**
     * Exibe a tela de relatórios salvos
     */
    public function relatoriosSalvos(Request $request): View
    {
        Session::put('url','cliente-relatorios');
        Session::put('sub-menu','cliente-relatorios');

        return view('cliente.relatorios');
    }

    /**
     * Lista relatórios salvos do cliente (API)
     */
    public function listarRelatorios(Request $request): JsonResponse
    {
        try {
            // Usa o cliente logado da sessão ou parâmetro
            if(Auth::user()->hasRole('cliente')){
                $clienteId = $this->client_id;
            } else {
                $clienteId = $request->input('cliente_id');
            }
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }
            
            // Parâmetros de paginação
            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            
            // Filtros opcionais
            $dataInicio = $request->input('data_inicio');
            $dataFim = $request->input('data_fim');
            
            // Query base
            $query = RelatorioGerado::doCliente($clienteId)
                ->orderBy('created_at', 'desc');
            
            // Aplica filtros de período se fornecidos
            if ($dataInicio && $dataFim) {
                $query->whereBetween('created_at', [$dataInicio, $dataFim]);
            }
            
            // Paginação
            $relatorios = $query->paginate($perPage, ['*'], 'page', $page);
            
            // Processa os dados para a resposta
            $relatoriosFormatados = $relatorios->getCollection()->map(function ($relatorio) {
                return [
                    'id' => $relatorio->id,
                    'titulo' => $relatorio->titulo,
                    'descricao' => $relatorio->misc_data['descricao'] ?? 'Relatório PDF',
                    'nome_arquivo' => $relatorio->nome_arquivo,
                    'url_s3' => $relatorio->url_s3,
                    'data_criacao' => $relatorio->created_at->format('d/m/Y H:i'),
                    'data_inicio' => $relatorio->misc_data['data_inicio'] ?? null,
                    'data_fim' => $relatorio->misc_data['data_fim'] ?? null,
                    'tamanho_arquivo' => $relatorio->misc_data['tamanho_arquivo'] ?? null,
                    'total_noticias' => $relatorio->misc_data['total_noticias'] ?? 0,
                    'tipos_midia' => $relatorio->misc_data['tipos_midia'] ?? [],
                    'filtros_aplicados' => [
                        'termo_busca' => $relatorio->misc_data['filtros']['termo_busca'] ?? null,
                        'tipo_filtro_data' => $relatorio->misc_data['filtros']['tipo_filtro_data'] ?? null,
                        'tags_count' => is_array($relatorio->misc_data['filtros']['tags_selecionadas'] ?? []) ? 
                                      count($relatorio->misc_data['filtros']['tags_selecionadas']) : 0,
                        'fontes_count' => is_array($relatorio->misc_data['filtros']['fontes_selecionadas'] ?? []) ? 
                                        count($relatorio->misc_data['filtros']['fontes_selecionadas']) : 0,
                    ],
                    'valor_total' => $relatorio->misc_data['valor_total_retorno'] ? 
                                   'R$ ' . number_format($relatorio->misc_data['valor_total_retorno'], 2, ',', '.') : null,
                    'situacao' => $relatorio->situacao ?? 0
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $relatoriosFormatados,
                'pagination' => [
                    'current_page' => $relatorios->currentPage(),
                    'last_page' => $relatorios->lastPage(),
                    'per_page' => $relatorios->perPage(),
                    'total' => $relatorios->total(),
                    'from' => $relatorios->firstItem(),
                    'to' => $relatorios->lastItem()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao listar relatórios: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download de relatório PDF com headers corretos
     */
    public function downloadRelatorio($cliente, $arquivo)
    {
        try {
            // Usa o cliente logado da sessão
            $clienteId = $cliente;
            
            if (!$clienteId) {
                abort(403, 'Acesso negado');
            }
            
            // Verifica se o arquivo pertence ao cliente (por segurança)
            if (!str_contains($arquivo, "relatorio_{$clienteId}_") && 
                !str_contains($arquivo, "relatorio_web_{$clienteId}_") &&
                !str_contains($arquivo, "relatorio_impresso_{$clienteId}_")) {
                abort(403, 'Acesso negado ao arquivo');
            }
            
            $caminhoArquivo = storage_path('app/public/relatorios/' . $arquivo);
            
            if (!file_exists($caminhoArquivo)) {
                abort(404, 'Arquivo não encontrado');
            }
            
            return response()->download($caminhoArquivo, $arquivo, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $arquivo . '"'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao fazer download do relatório: ' . $e->getMessage());
            abort(500, 'Erro interno do servidor');
        }
    }

    /**
     * Gera relatório PDF específico para notícias Web com imagens usando pdf_generator_web.py
     */
    public function gerarRelatorioPDFWeb(Request $request): JsonResponse
    {
        // Configurar timeouts para 1 hora
        set_time_limit(3600); // 1 hora
        ini_set('max_execution_time', 3600); // 1 hora
        ini_set('memory_limit', '1024M'); // 1GB de memória
        
        try {
        
            $dataInicio = $request->input('data_inicio');
            $dataFim = $request->input('data_fim');

            // Usa o cliente logado da sessão ou parâmetro
            if(Auth::user()->hasRole('cliente')){
                $clienteId = $this->client_id;
            }else{
                $clienteId = $request->cliente;
            }
            
            // IDs das notícias web específicas
            $idsWeb = $request->input('ids_web', []);
            
            // Validações
            if (!$clienteId || !$dataInicio || !$dataFim) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado ou campos obrigatórios não preenchidos'
                ], 400);
            }
            
            if (empty($idsWeb)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma notícia web selecionada'
                ], 400);
            }
            
            // Verifica se cliente existe
            $cliente = Cliente::find($clienteId);
            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não encontrado'
                ], 404);
            }
            
            // Verifica se cliente tem permissão para web
            if (!$cliente->fl_web) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não tem permissão para notícias web'
                ], 403);
            }
            
            // Verifica se cliente tem permissão para gerar relatórios com imagens
            if (!$cliente->fl_print) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não tem permissão para gerar relatórios com imagens'
                ], 403);
            }
            
            // Gera nome do arquivo
            $timestamp = date('YmdHis');
            $nomeArquivo = "relatorio_web_{$clienteId}_{$timestamp}.pdf";
            
            // Monta dados das notícias web para o Python
            $noticiasWebData = $this->buscarNoticiasWebParaPDF($idsWeb, $clienteId);
            
            if (empty($noticiasWebData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma notícia web encontrada com os IDs fornecidos'
                ], 404);
            }
            
            // Caminho para o arquivo de saída
            $outputDir = storage_path('app/public/relatorios');
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            $outputPath = $outputDir . '/' . $nomeArquivo;
            
            // Chama o script Python específico para web
            $pythonDir = base_path('python/relatorios');
            $scriptPath = $pythonDir . '/pdf_generator_web.py';
            
            // Dados para passar ao script Python
            $dadosRelatorio = [
                'noticias' => $noticiasWebData,
                'cliente_nome' => $cliente->nome,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'output_path' => $outputPath
            ];
            
            // Salva dados temporários em JSON
            $tempDataFile = tempnam(sys_get_temp_dir(), 'relatorio_web_data_');
            file_put_contents($tempDataFile, json_encode($dadosRelatorio, JSON_UNESCAPED_UNICODE));
            
            // Comando para executar o script Python
            $escapedScriptPath = escapeshellarg($scriptPath);
            $escapedDataFile = escapeshellarg($tempDataFile);
            $comando = "cd " . escapeshellarg($pythonDir) . " && (python3 -c \"
import sys, json
sys.path.append('.')
from pdf_generator_web import PDFGeneratorWeb

# Carrega dados
with open('$tempDataFile', 'r', encoding='utf-8') as f:
    dados = json.load(f)

# Gera relatório
generator = PDFGeneratorWeb()
success = generator.generate_web_report(
    dados['noticias'],
    dados['cliente_nome'],
    dados['data_inicio'],
    dados['data_fim'],
    dados['output_path']
)

print('SUCCESS' if success else 'ERROR')
\" 2>&1 || python -c \"
import sys, json
sys.path.append('.')
from pdf_generator_web import PDFGeneratorWeb

# Carrega dados
with open('$tempDataFile', 'r', encoding='utf-8') as f:
    dados = json.load(f)

# Gera relatório
generator = PDFGeneratorWeb()
success = generator.generate_web_report(
    dados['noticias'],
    dados['cliente_nome'],
    dados['data_inicio'],
    dados['data_fim'],
    dados['output_path']
)

print('SUCCESS' if success else 'ERROR')
\" 2>&1)";
            
            Log::info('Executando comando Python para relatório web: ' . $comando);
            
            $resultado = shell_exec($comando);
            
            // Remove arquivo temporário
            unlink($tempDataFile);
            
            Log::info('Resultado do comando Python: ' . ($resultado ?? 'null'));
            
            // Verifica se o arquivo foi gerado
            if (file_exists($outputPath) && strpos($resultado, 'SUCCESS') !== false) {
                return response()->json([
                    'success' => true,
                    'message' => 'Relatório web gerado com sucesso',
                    'arquivo' => $nomeArquivo,
                    'cliente' => $clienteId,
                    'download_url' => url('cliente/'.$clienteId.'/relatorios/download/' . $nomeArquivo)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar relatório web PDF',
                    'debug' => config('app.debug') ? [
                        'command_result' => $resultado ?? 'null',
                        'output_path' => $outputPath,
                        'file_exists' => file_exists($outputPath)
                    ] : null
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório web: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * Gera relatório PDF específico para notícias Impressas com imagens usando pdf_generator_impresso.py
     */
    public function gerarRelatorioPDFImpresso(Request $request): JsonResponse
    {
        // Configurar timeouts para 1 hora
        set_time_limit(3600); // 1 hora
        ini_set('max_execution_time', 3600); // 1 hora
        ini_set('memory_limit', '1024M'); // 1GB de memória
        
        try {
            
            $dataInicio = $request->input('data_inicio');
            $dataFim = $request->input('data_fim');

            // Usa o cliente logado da sessão ou parâmetro
            if(Auth::user()->hasRole('cliente')){
                $clienteId = $this->client_id;
            }else{
                $clienteId = $request->cliente;
            }
            
            // IDs das notícias impressas específicas
            $idsImpresso = $request->input('ids_impresso', []);
            
            // Validações
            if (!$clienteId || !$dataInicio || !$dataFim) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado ou campos obrigatórios não preenchidos'
                ], 400);
            }
            
            if (empty($idsImpresso)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma notícia impressa selecionada'
                ], 400);
            }
            
            // Verifica se cliente existe
            $cliente = Cliente::find($clienteId);
            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não encontrado'
                ], 404);
            }
            
            // Verifica se cliente tem permissão para impressos
            if (!$cliente->fl_impresso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não tem permissão para notícias impressas'
                ], 403);
            }
            
            // Verifica se cliente tem permissão para gerar relatórios com imagens
            if (!$cliente->fl_print) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não tem permissão para gerar relatórios com imagens'
                ], 403);
            }
            
            // Gera nome do arquivo
            $timestamp = date('YmdHis');
            $nomeArquivo = "relatorio_impresso_{$clienteId}_{$timestamp}.pdf";
            
            // Monta dados das notícias impressas para o Python
            $noticiasImpressoData = $this->buscarNoticiasImpressoParaPDF($idsImpresso, $clienteId);
            
            if (empty($noticiasImpressoData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma notícia impressa encontrada com os IDs fornecidos'
                ], 404);
            }
            
            // Caminho para o arquivo de saída
            $outputDir = storage_path('app/public/relatorios');
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            $outputPath = $outputDir . '/' . $nomeArquivo;
            
            // Chama o script Python específico para impressos
            $pythonDir = base_path('python/relatorios');
            $scriptPath = $pythonDir . '/pdf_generator_impresso.py';
            
            // Dados para passar ao script Python
            $dadosRelatorio = [
                'noticias' => $noticiasImpressoData,
                'cliente_nome' => $cliente->nome,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'output_path' => $outputPath
            ];
            
            // Salva dados temporários em JSON
            $tempDataFile = tempnam(sys_get_temp_dir(), 'relatorio_impresso_data_');
            file_put_contents($tempDataFile, json_encode($dadosRelatorio, JSON_UNESCAPED_UNICODE));
            
            // Comando para executar o script Python
            $escapedScriptPath = escapeshellarg($scriptPath);
            $escapedDataFile = escapeshellarg($tempDataFile);
            $comando = "cd " . escapeshellarg($pythonDir) . " && (python3 -c \"
import sys, json
sys.path.append('.')
from pdf_generator_impresso import PDFGeneratorImpresso

# Carrega dados
with open('$tempDataFile', 'r', encoding='utf-8') as f:
    dados = json.load(f)

# Gera relatório
generator = PDFGeneratorImpresso()
success = generator.generate_impresso_report(
    dados['noticias'],
    dados['cliente_nome'],
    dados['data_inicio'],
    dados['data_fim'],
    dados['output_path']
)

print('SUCCESS' if success else 'ERROR')
\" 2>&1 || python -c \"
import sys, json
sys.path.append('.')
from pdf_generator_impresso import PDFGeneratorImpresso

# Carrega dados
with open('$tempDataFile', 'r', encoding='utf-8') as f:
    dados = json.load(f)

# Gera relatório
generator = PDFGeneratorImpresso()
success = generator.generate_impresso_report(
    dados['noticias'],
    dados['cliente_nome'],
    dados['data_inicio'],
    dados['data_fim'],
    dados['output_path']
)

print('SUCCESS' if success else 'ERROR')
\" 2>&1)";
            
            Log::info('Executando comando Python para relatório impresso: ' . $comando);
            
            $resultado = shell_exec($comando);
            
            // Remove arquivo temporário
            unlink($tempDataFile);
            
            Log::info('Resultado do comando Python: ' . ($resultado ?? 'null'));
            
            // Verifica se o arquivo foi gerado
            if (file_exists($outputPath) && strpos($resultado, 'SUCCESS') !== false) {
                return response()->json([
                    'success' => true,
                    'message' => 'Relatório impresso gerado com sucesso',
                    'arquivo' => $nomeArquivo,
                    'cliente' => $clienteId,
                    'download_url' => url('cliente/'.$clienteId.'/relatorios/download/' . $nomeArquivo)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar relatório impresso PDF',
                    'debug' => config('app.debug') ? [
                        'command_result' => $resultado ?? 'null',
                        'output_path' => $outputPath,
                        'file_exists' => file_exists($outputPath)
                    ] : null
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório impresso: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * Busca dados das notícias impressas formatados para o PDF
     */
    private function buscarNoticiasImpressoParaPDF($idsImpresso, $clienteId)
    {
        try {
            $sql = "
                SELECT 
                    t1.id,
                    t1.titulo,
                    t2.nome as veiculo,
                    t1.dt_clipagem as data,
                    TO_CHAR(t1.dt_clipagem, 'DD/MM/YYYY') as data_publicacao,
                    t1.sinopse as texto,
                    t1.ds_caminho_img,
                    t1.valor_retorno as valor,
                    t3.sentimento
                FROM noticia_impresso t1
                JOIN jornal_online t2 ON t2.id = t1.id_fonte
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND t3.tipo_id = 1
                WHERE t3.cliente_id = ?
                AND t1.id IN (" . implode(',', array_fill(0, count($idsImpresso), '?')) . ")
                ORDER BY t1.dt_clipagem ASC
            ";
            
            $params = array_merge([$clienteId], $idsImpresso);
            $noticias = DB::select($sql, $params);
            
            // Converte para array associativo
            $noticiasArray = [];
            foreach ($noticias as $noticia) {
                $noticiasArray[] = [
                    'id' => $noticia->id,
                    'titulo' => $noticia->titulo,
                    'veiculo' => $noticia->veiculo,
                    'data' => $noticia->data,
                    'data_publicacao' => $noticia->data_publicacao,
                    'texto' => $noticia->texto,
                    'ds_caminho_img' => $noticia->ds_caminho_img,
                    'valor' => $noticia->valor,
                    'sentimento' => $noticia->sentimento
                ];
            }
            
            return $noticiasArray;
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar notícias impressas para PDF: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca dados das notícias web formatados para o PDF
     */
    private function buscarNoticiasWebParaPDF($idsWeb, $clienteId)
    {
        try {
            $sql = "
                SELECT 
                    t1.id,
                    t1.titulo_noticia as titulo,
                    t2.nome as veiculo,
                    t1.data_noticia,
                    TO_CHAR(t1.data_noticia, 'DD/MM/YYYY') as data_publicacao,
                    t4.conteudo as descricao,
                    t1.url_noticia as link,
                    t1.ds_caminho_img,
                    t1.nu_valor as valor,
                    t3.sentimento
                FROM noticias_web t1
                JOIN fonte_web t2 ON t2.id = t1.id_fonte
                JOIN noticia_cliente t3 ON t3.noticia_id = t1.id AND t3.tipo_id = 2
                JOIN conteudo_noticia_web t4 ON t4.id_noticia_web = t1.id
                WHERE t3.cliente_id = ?
                AND t1.id IN (" . implode(',', array_fill(0, count($idsWeb), '?')) . ")
                ORDER BY t1.data_noticia ASC
            ";
            
            $params = array_merge([$clienteId], $idsWeb);
            $noticias = DB::select($sql, $params);
            
            // Converte para array associativo
            $noticiasArray = [];
            foreach ($noticias as $noticia) {
                $noticiasArray[] = [
                    'id' => $noticia->id,
                    'titulo' => $noticia->titulo,
                    'veiculo' => $noticia->veiculo,
                    'data_noticia' => $noticia->data_noticia,
                    'data_publicacao' => $noticia->data_publicacao,
                    'descricao' => $noticia->descricao,
                    'link' => $noticia->link,
                    'ds_caminho_img' => $noticia->ds_caminho_img,
                    'valor' => $noticia->valor,
                    'sentimento' => $noticia->sentimento
                ];
            }
            
            return $noticiasArray;
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar notícias web para PDF: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Adiciona uma nova notícia (substitui /adicionar_noticia do Flask)
     */
    public function adicionarNoticia(Request $request): JsonResponse
    {
        try {
            // Usa o cliente logado da sessão
            $clienteId = $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }
            
            $dados = [
                'tipo' => $request->input('tipo'),
                'cliente_id' => $clienteId,
                'data' => $request->input('data'),
                'titulo' => $request->input('titulo'),
                'veiculo' => $request->input('veiculo'),
                'texto' => $request->input('texto'),
                'valor' => $request->input('valor', 0),
                'tags' => $request->input('tags', '')
            ];
            
            // Campos específicos por tipo
            if ($dados['tipo'] === 'WEB') {
                $dados['link'] = $request->input('link', '');
            } elseif ($dados['tipo'] === 'TV') {
                $dados['programa'] = $request->input('programa', '');
                $dados['horario'] = $request->input('horario', '');
            } elseif ($dados['tipo'] === 'RADIO') {
                $dados['programa_radio'] = $request->input('programa_radio', '');
                $dados['horario_radio'] = $request->input('horario_radio', '');
            }
            
            $relatorioService = new RelatorioService();
            $resultado = $relatorioService->adicionarNoticia($dados);
            
            if ($resultado['success']) {
                return response()->json($resultado);
            } else {
                return response()->json($resultado, 400);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao adicionar notícia: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edita uma notícia existente (substitui /editar_noticia do Flask)
     */
    public function editarNoticia(Request $request): JsonResponse
    {
        try {
            // Usa o cliente logado da sessão
            $clienteId = $this->client_id;
            $noticiaId = $request->input('noticia_id');
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }
            
            if (!$noticiaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID da notícia é obrigatório'
                ], 400);
            }
            
            // Normalizar o tipo para minúsculo
            $tipo = strtolower($request->input('tipo', ''));
            
            // Mapear tipos se necessário
            if ($tipo === 'jornal') {
                $tipo = 'impresso';
            }
            
            $dados = [
                'tipo' => $tipo,
                'cliente_id' => $clienteId,
                'data' => $request->input('data'),
                'titulo' => $request->input('titulo'),
                'veiculo' => $request->input('veiculo'),
                'texto' => $request->input('texto'),
                'valor' => $request->input('valor') ? floatval($request->input('valor')) : 0,
                'tags' => $request->input('tags', ''),
                'sentimento' => $request->input('sentimento') !== null ? intval($request->input('sentimento')) : null
            ];
            
            // Campos específicos por tipo
            if ($tipo === 'web') {
                $dados['link'] = $request->input('link', '');
            } elseif (in_array($tipo, ['tv', 'radio'])) {
                $dados['programa'] = $request->input('programa', '');
                $dados['horario'] = $request->input('horario', '');
            }
            
            Log::info('Dados para edição:', $dados);
            
            $relatorioService = new RelatorioService();
            $resultado = $relatorioService->editarNoticia($noticiaId, $dados);
            
            if ($resultado['success']) {
                return response()->json($resultado);
            } else {
                return response()->json($resultado, 400);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao editar notícia: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exclui uma notícia (substitui /excluir_noticia do Flask)
     */
    public function excluirNoticia(Request $request): JsonResponse
    {
        try {
            $vinculoId = $request->input('vinculo_id');
            
            if (!$vinculoId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID do vínculo é obrigatório'
                ], 400);
            }
            
            $relatorioService = new RelatorioService();
            $resultado = $relatorioService->excluirNoticia($vinculoId);
            
            if ($resultado['success']) {
                return response()->json($resultado);
            } else {
                return response()->json($resultado, 404);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao excluir notícia: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aplica tags a múltiplas notícias (substitui /aplicar_tags_lote do Flask)
     */
    public function aplicarTagsLote(Request $request): JsonResponse
    {
        try {
            $noticiasIds = $request->input('noticias_ids', []);
            $tags = $request->input('tags', '');
            $acao = $request->input('acao', 'adicionar');
            
            if (empty($noticiasIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma notícia selecionada'
                ], 400);
            }
            
            if (empty($tags) && $acao !== 'remover') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tags não fornecidas'
                ], 400);
            }
            
            $relatorioService = new RelatorioService();
            $resultado = $relatorioService->aplicarTagsLote($noticiasIds, $tags, $acao);
            
            if ($resultado['success']) {
                return response()->json($resultado);
            } else {
                return response()->json($resultado, 400);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao aplicar tags em lote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vincula notícia a uma área (substitui /vincular_noticia_area do Flask)
     */
    public function vincularNoticiaArea(Request $request): JsonResponse
    {
        try {
            $noticiaId = $request->input('noticia_id');
            $tipoMidia = $request->input('tipo_midia');
            $areaId = $request->input('area_id');
            
            if (!$noticiaId || !$tipoMidia) {
                return response()->json([
                    'success' => false,
                    'message' => 'noticia_id e tipo_midia são obrigatórios'
                ], 400);
            }
            
            $relatorioService = new RelatorioService();
            $sucesso = $relatorioService->vincularNoticiaArea($noticiaId, $tipoMidia, $areaId);
            
            if ($sucesso) {
                $areaTexto = $areaId ? "área ID $areaId" : "nenhuma área";
                return response()->json([
                    'success' => true,
                    'message' => "Notícia vinculada à $areaTexto com sucesso"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao vincular notícia à área'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao vincular notícia à área: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca áreas de um cliente para relatórios (substitui /api/areas/<cliente_id> do Flask)
     */
    public function getAreasClienteRelatorio(Request $request): JsonResponse
    {
        try {
            // Usa o cliente logado da sessão
            $clienteId = $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }
            
            $relatorioService = new RelatorioService();
            $areas = $relatorioService->getAreasByCliente($clienteId);
            
            return response()->json($areas);
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar áreas do cliente: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca áreas vinculadas às notícias selecionadas
     */
    public function getAreasNoticias(Request $request): JsonResponse
    {
        try {
            // Para unificado.blade.php, o cliente vem da requisição
            $clienteId = $request->input('cliente_id');
            
            // Se não vier da requisição, tenta pegar da sessão (páginas normais)
            if (!$clienteId) {
                $clienteId = $this->client_id;
            }
            
            Log::info('getAreasNoticias - dados recebidos:', [
                'cliente_id_request' => $request->input('cliente_id'),
                'cliente_id_sessao' => $this->client_id,
                'cliente_id_final' => $clienteId,
                'todos_parametros' => $request->all()
            ]);
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }

            $idsWeb = $request->input('ids_web', []);
            $idsTv = $request->input('ids_tv', []);
            $idsRadio = $request->input('ids_radio', []);
            $idsImpresso = $request->input('ids_impresso', []);

            if (empty($idsWeb) && empty($idsTv) && empty($idsRadio) && empty($idsImpresso)) {
                return response()->json([]);
            }

            $areas = [];
            $tipoIdMap = ['web' => 2, 'impresso' => 1, 'tv' => 4, 'radio' => 3];

            // Buscar áreas para cada tipo de notícia
            foreach ([
                'web' => $idsWeb,
                'tv' => $idsTv,
                'radio' => $idsRadio,
                'impresso' => $idsImpresso
            ] as $tipo => $ids) {
                if (!empty($ids)) {
                    $tipoId = $tipoIdMap[$tipo];
                    
                    $areasEncontradas = DB::table('noticia_cliente as nc')
                        ->join('area as a', 'nc.area', '=', 'a.id')
                        ->whereIn('nc.noticia_id', $ids)
                        ->where('nc.tipo_id', $tipoId)
                        ->where('nc.cliente_id', $clienteId)
                        ->whereNotNull('nc.area')
                        ->select('a.id', 'a.descricao as nome')
                        ->distinct()
                        ->get();

                    foreach ($areasEncontradas as $area) {
                        $areaKey = $area->id;
                        if (!isset($areas[$areaKey])) {
                            $areas[$areaKey] = [
                                'id' => $area->id,
                                'nome' => $area->nome
                            ];
                        }
                    }
                }
            }

            return response()->json(array_values($areas));

        } catch (\Exception $e) {
            Log::error('Erro ao buscar áreas das notícias: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAreasClienteEspecifico($id): JsonResponse
    {
        try {
            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID do cliente não fornecido'
                ], 400);
            }
            
            $relatorioService = new RelatorioService();
            $areas = $relatorioService->getAreasByCliente($id);
            
            return response()->json($areas);
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar áreas do cliente específico: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca lista de todos os clientes (substitui /api/clientes do Flask)
     */
    public function getClientesApi(): JsonResponse
    {
        try {
            $relatorioService = new RelatorioService();
            $clientes = $relatorioService->getClientes();
            
            return response()->json($clientes);
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar clientes: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter tags disponíveis para filtro
     */
    public function getTagsDisponiveis(Request $request): JsonResponse
    {
        try {
            // Usa o cliente da request ou o cliente logado da sessão
            $clienteId = $request->get('cliente_id') ?: $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'error' => 'Cliente não identificado'
                ], 400);
            }
            
            // Buscar todas as tags utilizadas nas notícias do cliente
            $tags = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                ->whereNotNull('misc_data')
                ->get()
                ->pluck('misc_data')
                ->filter(function($miscData) {
                    return isset($miscData['tags_noticia']) && is_array($miscData['tags_noticia']);
                })
                ->map(function($miscData) {
                    return $miscData['tags_noticia'];
                })
                ->flatten()
                ->unique()
                ->values()
                ->toArray();
            
            return response()->json($tags);
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar tags disponíveis: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter tags das notícias selecionadas
     */
    public function getTagsNoticias(Request $request): JsonResponse
    {
        try {
            // Usa o cliente da request (para administradores) ou o cliente logado da sessão (para clientes)
            $clienteId = $request->get('cliente_id') ?: $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'error' => 'Cliente não identificado'
                ], 400);
            }

            $idsWeb = $request->input('ids_web', []);
            $idsTv = $request->input('ids_tv', []);
            $idsRadio = $request->input('ids_radio', []);
            $idsImpresso = $request->input('ids_impresso', []);

            $todasTags = [];

            // Buscar tags de cada tipo de notícia
            if (!empty($idsWeb)) {
                $tagsWeb = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                    ->where('tipo_id', 2) // web
                    ->whereIn('noticia_id', $idsWeb)
                    ->whereNotNull('misc_data')
                    ->get()
                    ->pluck('misc_data')
                    ->filter(function($miscData) {
                        return isset($miscData['tags_noticia']) && is_array($miscData['tags_noticia']);
                    })
                    ->map(function($miscData) {
                        return $miscData['tags_noticia'];
                    })
                    ->flatten()
                    ->toArray();
                
                $todasTags = array_merge($todasTags, $tagsWeb);
            }

            if (!empty($idsTv)) {
                $tagsTv = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                    ->where('tipo_id', 4) // tv
                    ->whereIn('noticia_id', $idsTv)
                    ->whereNotNull('misc_data')
                    ->get()
                    ->pluck('misc_data')
                    ->filter(function($miscData) {
                        return isset($miscData['tags_noticia']) && is_array($miscData['tags_noticia']);
                    })
                    ->map(function($miscData) {
                        return $miscData['tags_noticia'];
                    })
                    ->flatten()
                    ->toArray();
                
                $todasTags = array_merge($todasTags, $tagsTv);
            }

            if (!empty($idsRadio)) {
                $tagsRadio = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                    ->where('tipo_id', 3) // radio
                    ->whereIn('noticia_id', $idsRadio)
                    ->whereNotNull('misc_data')
                    ->get()
                    ->pluck('misc_data')
                    ->filter(function($miscData) {
                        return isset($miscData['tags_noticia']) && is_array($miscData['tags_noticia']);
                    })
                    ->map(function($miscData) {
                        return $miscData['tags_noticia'];
                    })
                    ->flatten()
                    ->toArray();
                
                $todasTags = array_merge($todasTags, $tagsRadio);
            }

            if (!empty($idsImpresso)) {
                $tagsImpresso = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                    ->where('tipo_id', 1) // impresso
                    ->whereIn('noticia_id', $idsImpresso)
                    ->whereNotNull('misc_data')
                    ->get()
                    ->pluck('misc_data')
                    ->filter(function($miscData) {
                        return isset($miscData['tags_noticia']) && is_array($miscData['tags_noticia']);
                    })
                    ->map(function($miscData) {
                        return $miscData['tags_noticia'];
                    })
                    ->flatten()
                    ->toArray();
                
                $todasTags = array_merge($todasTags, $tagsImpresso);
            }

            // Remover duplicatas e retornar array com valores únicos
            $tagsUnicas = array_values(array_unique($todasTags));
            
            return response()->json($tagsUnicas);
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar tags das notícias: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Adicionar tag às notícias selecionadas
     */
    public function adicionarTag(Request $request): JsonResponse
    {
        try {
            // Usa o cliente da request (para administradores) ou o cliente logado da sessão (para clientes)
            $clienteId = $request->get('cliente_id') ?: $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }

            $tag = trim($request->input('tag'));
            $idsWeb = $request->input('ids_web', []);
            $idsTv = $request->input('ids_tv', []);
            $idsRadio = $request->input('ids_radio', []);
            $idsImpresso = $request->input('ids_impresso', []);

            if (empty($tag)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nome da tag é obrigatório'
                ], 400);
            }

            $noticiasAfetadas = 0;

            // Adicionar tag para cada tipo de notícia
            if (!empty($idsWeb)) {
                $registros = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                    ->where('tipo_id', 2) // web
                    ->whereIn('noticia_id', $idsWeb)
                    ->get();
                
                foreach ($registros as $registro) {
                    $registro->addTag($tag);
                    $noticiasAfetadas++;
                }
            }

            if (!empty($idsTv)) {
                $registros = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                    ->where('tipo_id', 4) // tv
                    ->whereIn('noticia_id', $idsTv)
                    ->get();
                
                foreach ($registros as $registro) {
                    $registro->addTag($tag);
                    $noticiasAfetadas++;
                }
            }

            if (!empty($idsRadio)) {
                $registros = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                    ->where('tipo_id', 3) // radio
                    ->whereIn('noticia_id', $idsRadio)
                    ->get();
                
                foreach ($registros as $registro) {
                    $registro->addTag($tag);
                    $noticiasAfetadas++;
                }
            }

            if (!empty($idsImpresso)) {
                $registros = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                    ->where('tipo_id', 1) // impresso
                    ->whereIn('noticia_id', $idsImpresso)
                    ->get();
                
                foreach ($registros as $registro) {
                    $registro->addTag($tag);
                    $noticiasAfetadas++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Tag '$tag' adicionada com sucesso",
                'noticias_afetadas' => $noticiasAfetadas
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao adicionar tag: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover tag das notícias selecionadas
     */
    public function removerTag(Request $request): JsonResponse
    {
        try {
            // Usa o cliente da request (para administradores) ou o cliente logado da sessão (para clientes)
            $clienteId = $request->get('cliente_id') ?: $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }

            $tag = trim($request->input('tag'));
            $idsWeb = $request->input('ids_web', []);
            $idsTv = $request->input('ids_tv', []);
            $idsRadio = $request->input('ids_radio', []);
            $idsImpresso = $request->input('ids_impresso', []);

            if (empty($tag)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nome da tag é obrigatório'
                ], 400);
            }

            $noticiasAfetadas = 0;

            // Remover tag de cada tipo de notícia
            if (!empty($idsWeb)) {
                $registros = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                    ->where('tipo_id', 2) // web
                    ->whereIn('noticia_id', $idsWeb)
                    ->get();
                
                foreach ($registros as $registro) {
                    $registro->removeTag($tag);
                    $noticiasAfetadas++;
                }
            }

            if (!empty($idsTv)) {
                $registros = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                    ->where('tipo_id', 4) // tv
                    ->whereIn('noticia_id', $idsTv)
                    ->get();
                
                foreach ($registros as $registro) {
                    $registro->removeTag($tag);
                    $noticiasAfetadas++;
                }
            }

            if (!empty($idsRadio)) {
                $registros = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                    ->where('tipo_id', 3) // radio
                    ->whereIn('noticia_id', $idsRadio)
                    ->get();
                
                foreach ($registros as $registro) {
                    $registro->removeTag($tag);
                    $noticiasAfetadas++;
                }
            }

            if (!empty($idsImpresso)) {
                $registros = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                    ->where('tipo_id', 1) // impresso
                    ->whereIn('noticia_id', $idsImpresso)
                    ->get();
                
                foreach ($registros as $registro) {
                    $registro->removeTag($tag);
                    $noticiasAfetadas++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Tag '$tag' removida com sucesso",
                'noticias_afetadas' => $noticiasAfetadas
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao remover tag: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alterar sentimento de uma notícia
     */
    public function alterarSentimento(Request $request): JsonResponse
    {
        try {
            // Usa o cliente da request (para administradores) ou o cliente logado da sessão (para clientes)
            $clienteId = $request->get('cliente_id') ?: $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }

            $noticiaId = $request->input('noticia_id');
            $tipo = $request->input('tipo');
            $sentimento = $request->input('sentimento');

            // Validações
            if (empty($noticiaId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID da notícia é obrigatório'
                ], 400);
            }

            if (empty($tipo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo da notícia é obrigatório'
                ], 400);
            }

            if (!in_array($sentimento, ['1', '0', '-1'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Valor de sentimento inválido'
                ], 400);
            }

            // Mapear tipos para tipo_id
            $tipoIdMap = [
                'impresso' => 1,
                'web' => 2,
                'radio' => 3,
                'tv' => 4
            ];

            if (!isset($tipoIdMap[$tipo])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de mídia inválido'
                ], 400);
            }

            $tipoId = $tipoIdMap[$tipo];

            // Buscar e atualizar o registro na tabela noticia_cliente
            $registro = \App\Models\NoticiaCliente::where('cliente_id', $clienteId)
                ->where('tipo_id', $tipoId)
                ->where('noticia_id', $noticiaId)
                ->first();

            if (!$registro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notícia não encontrada para este cliente'
                ], 404);
            }

            // Atualizar o sentimento
            $registro->sentimento = intval($sentimento);
            $registro->save();

            // Log da alteração
            Log::info("Sentimento da notícia {$tipo} #{$noticiaId} alterado para {$sentimento} pelo cliente {$clienteId}");

            return response()->json([
                'success' => true,
                'message' => 'Sentimento alterado com sucesso',
                'noticia_id' => $noticiaId,
                'tipo' => $tipo,
                'novo_sentimento' => intval($sentimento)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao alterar sentimento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valida se um cliente existe (substitui /api/validar_cliente/<cliente_id> do Flask)
     */
    public function validarCliente(Request $request): JsonResponse
    {
        try {
            // Usa o cliente logado da sessão
            $clienteId = $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'existe' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }
            
            $relatorioService = new RelatorioService();
            $existe = $relatorioService->checkCliente($clienteId);
            
            return response()->json(['existe' => $existe]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao validar cliente: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload de imagem para notícias (substitui /upload_imagem do Flask)
     */
    public function uploadImagem(Request $request): JsonResponse
    {
        try {
            // Usa o cliente logado da sessão
            $clienteId = $this->client_id;
            $noticiaId = $request->input('noticia_id');
            $tipoMidia = $request->input('tipo_midia');
            
            if (!$clienteId || !$noticiaId || !$tipoMidia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Campos obrigatórios: noticia_id, tipo_midia (cliente identificado automaticamente)'
                ], 400);
            }
            
            if (!$request->hasFile('imagem')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo de imagem não encontrado'
                ], 400);
            }
            
            $arquivo = $request->file('imagem');
            
            // Validações
            $extensoesPermitidas = ['jpg', 'jpeg', 'png'];
            $extensao = strtolower($arquivo->getClientOriginalExtension());
            
            if (!in_array($extensao, $extensoesPermitidas)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato não suportado. Use: ' . implode(', ', $extensoesPermitidas)
                ], 400);
            }
            
            // Validar tamanho (5MB)
            if ($arquivo->getSize() > 5 * 1024 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo muito grande. Máximo: 5MB'
                ], 400);
            }
            
            // Salvar arquivo
            $nomeArquivo = "arquivo{$noticiaId}_1.{$extensao}";
            $caminhoStorage = "public/images/{$tipoMidia}/{$nomeArquivo}";
            
            $arquivo->storeAs("public/images/{$tipoMidia}", $nomeArquivo);
            
            return response()->json([
                'success' => true,
                'message' => 'Imagem enviada com sucesso',
                'arquivo' => $nomeArquivo,
                'url' => Storage::url("images/{$tipoMidia}/{$nomeArquivo}")
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro no upload de imagem: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca detalhes de uma notícia específica para visualização
     */
    public function buscarNoticia(Request $request, $id, $tipo): JsonResponse
    {
        try {
            // Usa o cliente da request (para administradores) ou o cliente logado da sessão (para clientes)
            $clienteId = $request->get('cliente_id') ?: $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }
            
            $relatorioService = new RelatorioService();
            $noticia = $relatorioService->buscarNoticia($id, $tipo, $clienteId);
            
            if ($noticia) {
                return response()->json([
                    'success' => true,
                    'noticia' => $noticia
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Notícia não encontrada'
                ], 404);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar notícia: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Seleciona um cliente na sessão (para troca de cliente)
     */
    public function selecionar(Request $request): JsonResponse
    {
        try {
            $clienteId = $request->input('cliente');
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID do cliente é obrigatório'
                ], 400);
            }
            
            $cliente = Cliente::find($clienteId);
            
            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não encontrado'
                ], 404);
            }
            
            // Atualizar sessão com novo cliente
            Session::put('cliente', ['id' => $cliente->id, 'nome' => $cliente->nome]);
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente selecionado com sucesso',
                'cliente' => ['id' => $cliente->id, 'nome' => $cliente->nome]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao selecionar cliente: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reordenarAreas(Request $request, $id)
    {
        foreach ($request->ordem as $item) {
            DB::table('area_cliente')
                ->where('id', $item['id'])
                ->update(['ordem' => $item['ordem']]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Busca fontes disponíveis para Web (apenas as que têm notícias do cliente)
     */
    public function obterFontesWeb(Request $request): JsonResponse
    {
        try {
            // Usa o cliente da request ou o cliente logado da sessão
            $clienteId = $request->get('cliente_id') ?: $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }

            // Query principal
            $fontes = DB::table('fonte_web as fw')
                ->select('fw.id', 'fw.nome', 'fw.fl_coleta', DB::raw('COUNT(DISTINCT nw.id) as total_noticias'))
                ->join('noticias_web as nw', 'fw.id', '=', 'nw.id_fonte')
                ->join('noticia_cliente as nc', function($join) use ($clienteId) {
                    $join->on('nw.id', '=', 'nc.noticia_id')
                         ->where('nc.tipo_id', '=', 2)
                         ->where('nc.cliente_id', '=', $clienteId);
                })
                ->whereNull('fw.deleted_at')
                ->whereNull('nw.deleted_at')
                ->groupBy('fw.id', 'fw.nome', 'fw.fl_coleta')
                ->orderBy('fw.nome')
                ->get();

            // Filtrar apenas as com fl_coleta se necessário
            $fontesFiltered = $fontes->where('fl_coleta', true);

            // Formatar resultado final
            $resultado = $fontesFiltered->map(function($fonte) {
                return [
                    'id' => (int) $fonte->id,
                    'nome' => (string) $fonte->nome,
                    'total_noticias' => (int) $fonte->total_noticias
                ];
            })->values();

            // Se não encontrou fontes com fl_coleta, tentar sem esse filtro
            if (count($resultado) == 0) {
                // Retornar todas as fontes que têm notícias do cliente, independente de fl_coleta
                $resultado = $fontes->map(function($fonte) {
                    return [
                        'id' => (int) $fonte->id,
                        'nome' => (string) $fonte->nome . ($fonte->fl_coleta ? '' : ' (inativa)'),
                        'total_noticias' => (int) $fonte->total_noticias
                    ];
                })->values();
            }

            return response()->json($resultado);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar fontes web: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar fontes web: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca fontes disponíveis para Impresso (apenas as que têm notícias do cliente)
     */
    public function obterFontesImpresso(Request $request): JsonResponse
    {
        try {
            // Usa o cliente da request ou o cliente logado da sessão
            $clienteId = $request->get('cliente_id') ?: $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }

            $fontes = DB::table('jornal_online as jo')
                ->select('jo.id', 'jo.nome', DB::raw('COUNT(DISTINCT ni.id) as total_noticias'))
                ->join('noticia_impresso as ni', 'jo.id', '=', 'ni.id_fonte')
                ->join('noticia_cliente as nc', function($join) use ($clienteId) {
                    $join->on('ni.id', '=', 'nc.noticia_id')
                         ->where('nc.tipo_id', '=', 1)
                         ->where('nc.cliente_id', '=', $clienteId);
                })
                ->where('jo.fl_ativo', true)
                ->whereNull('jo.deleted_at')
                ->whereNull('ni.deleted_at')
                ->groupBy('jo.id', 'jo.nome')
                ->orderBy('jo.nome')
                ->get();

            return response()->json($fontes);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar fontes impresso: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar fontes impresso'
            ], 500);
        }
    }

    /**
     * Busca emissoras e programas disponíveis para TV (apenas as que têm notícias do cliente)
     */
    public function obterFontesTv(Request $request): JsonResponse
    {
        try {
            // Usa o cliente da request ou o cliente logado da sessão
            $clienteId = $request->get('cliente_id') ?: $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }

            // Buscar emissoras que têm notícias do cliente
            $emissoras = DB::table('emissora_web as ew')
                ->select('ew.id', 'ew.nome_emissora as nome', DB::raw('COUNT(DISTINCT nt.id) as total_noticias'))
                ->join('noticia_tv as nt', 'ew.id', '=', 'nt.emissora_id')
                ->join('noticia_cliente as nc', function($join) use ($clienteId) {
                    $join->on('nt.id', '=', 'nc.noticia_id')
                         ->where('nc.tipo_id', '=', 4)
                         ->where('nc.cliente_id', '=', $clienteId);
                })
                ->whereNull('ew.deleted_at')
                ->whereNull('nt.deleted_at')
                ->groupBy('ew.id', 'ew.nome_emissora')
                ->orderBy('ew.nome_emissora')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nome' => $item->nome,
                        'tipo' => 'emissora',
                        'total_noticias' => $item->total_noticias
                    ];
                });

            // Buscar programas que têm notícias do cliente
            $programas = DB::table('programa_emissora_web as p')
                ->join('emissora_web as e', 'e.id', '=', 'p.id_emissora')
                ->join('noticia_tv as nt', 'p.id', '=', 'nt.programa_id')
                ->join('noticia_cliente as nc', function($join) use ($clienteId) {
                    $join->on('nt.id', '=', 'nc.noticia_id')
                         ->where('nc.tipo_id', '=', 4)
                         ->where('nc.cliente_id', '=', $clienteId);
                })
                ->select('p.id', 'p.nome_programa as nome', 'e.nome_emissora as emissora', DB::raw('COUNT(DISTINCT nt.id) as total_noticias'))
                ->whereNull('p.deleted_at')
                ->whereNull('e.deleted_at')
                ->whereNull('nt.deleted_at')
                ->groupBy('p.id', 'p.nome_programa', 'e.nome_emissora')
                ->orderBy('e.nome_emissora')
                ->orderBy('p.nome_programa')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nome' => $item->nome . ' (' . $item->emissora . ')',
                        'tipo' => 'programa',
                        'total_noticias' => $item->total_noticias
                    ];
                });

            $fontes = $emissoras->concat($programas);

            return response()->json($fontes);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar fontes TV: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar fontes TV'
            ], 500);
        }
    }

    /**
     * Busca emissoras e programas disponíveis para Rádio (apenas as que têm notícias do cliente)
     */
    public function obterFontesRadio(Request $request): JsonResponse
    {
        try {
            // Usa o cliente da request ou o cliente logado da sessão
            $clienteId = $request->get('cliente_id') ?: $this->client_id;
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não identificado'
                ], 400);
            }

            // Buscar emissoras que têm notícias do cliente
            $emissoras = DB::table('emissora_radio as er')
                ->select('er.id', 'er.nome_emissora as nome', DB::raw('COUNT(DISTINCT nr.id) as total_noticias'))
                ->join('noticia_radio as nr', 'er.id', '=', 'nr.emissora_id')
                ->join('noticia_cliente as nc', function($join) use ($clienteId) {
                    $join->on('nr.id', '=', 'nc.noticia_id')
                         ->where('nc.tipo_id', '=', 3)
                         ->where('nc.cliente_id', '=', $clienteId);
                })
                ->whereNull('er.deleted_at')
                ->whereNull('nr.deleted_at')
                ->groupBy('er.id', 'er.nome_emissora')
                ->orderBy('er.nome_emissora')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nome' => $item->nome,
                        'tipo' => 'emissora',
                        'total_noticias' => $item->total_noticias
                    ];
                });

            // Buscar programas que têm notícias do cliente
            $programas = DB::table('programa_emissora_radio as p')
                ->join('emissora_radio as e', 'e.id', '=', 'p.id_emissora')
                ->join('noticia_radio as nr', 'p.id', '=', 'nr.programa_id')
                ->join('noticia_cliente as nc', function($join) use ($clienteId) {
                    $join->on('nr.id', '=', 'nc.noticia_id')
                         ->where('nc.tipo_id', '=', 3)
                         ->where('nc.cliente_id', '=', $clienteId);
                })
                ->select('p.id', 'p.nome_programa as nome', 'e.nome_emissora as emissora', DB::raw('COUNT(DISTINCT nr.id) as total_noticias'))
                ->whereNull('p.deleted_at')
                ->whereNull('e.deleted_at')
                ->whereNull('nr.deleted_at')
                ->groupBy('p.id', 'p.nome_programa', 'e.nome_emissora')
                ->orderBy('e.nome_emissora')
                ->orderBy('p.nome_programa')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nome' => $item->nome . ' (' . $item->emissora . ')',
                        'tipo' => 'programa',
                        'total_noticias' => $item->total_noticias
                    ];
                });

            $fontes = $emissoras->concat($programas);

            return response()->json($fontes);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar fontes Rádio: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar fontes Rádio'
            ], 500);
        }
    }



}
