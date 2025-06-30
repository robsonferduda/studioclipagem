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

class ClienteController extends Controller
{
    private $client_id;
    private $periodo_padrao;

    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','cliente');
        $this->carbon = new Carbon();
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
        
        try {

            $request->merge(['fl_print' => $fl_print]);
            $request->merge(['fl_print' => $fl_print]);
            $request->merge(['fl_sentimento' => $fl_sentimento]);
            $request->merge(['fl_retorno_midia' => $fl_retorno_midia]);

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
        
        $cliente = Cliente::find($id);

        try {

            $request->merge(['fl_ativo' => $fl_ativo]);
            $request->merge(['fl_print' => $fl_print]);
            $request->merge(['fl_sentimento' => $fl_sentimento]);
            $request->merge(['fl_retorno_midia' => $fl_retorno_midia]);
            
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
                AND t1.dt_noticia BETWEEN '$dt_inicial' AND '$dt_final'";

        if($cliente_selecionado){
            $sql .= ' AND t3.cliente_id = '.$cliente_selecionado;
        }

        if($termo){
            $sql .= " AND t1.sinopse ilike '%$termo%'";
        }

        return $dados = DB::select($sql);
    }
}
