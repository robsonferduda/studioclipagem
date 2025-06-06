<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Cliente;
use App\Models\ClienteArea;
use App\Models\EnderecoEletronico;
use App\Models\Pessoa;
use App\Utils;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

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

        $fl_tv = $request->fl_tv == true ? true : false;
        $fl_impresso = $request->fl_impresso == true ? true : false;
        $fl_web = $request->fl_web == true ? true : false;
        $fl_radio = $request->fl_radio == true ? true : false;

        $fl_relatorio_consolidado = $request->fl_relatorio_consolidado == true ? true : false;
        $fl_relatorio_completo = $request->fl_relatorio_completo == true ? true : false;
        $fl_link_relatorio = $request->fl_link_relatorio == true ? true : false;
        $fl_area_restrita = $request->fl_area_restrita == true ? true : false;      
        
        try {

            $request->merge(['fl_ativo' => $fl_ativo]);
            $request->merge(['fl_print' => $fl_print]);

            $request->merge(['fl_tv' => $fl_tv]);
            $request->merge(['fl_impresso' => $fl_impresso]);
            $request->merge(['fl_web' => $fl_web]);
            $request->merge(['fl_radio' => $fl_radio]);

            $request->merge(['fl_relatorio_consolidado' => $fl_relatorio_consolidado]);
            $request->merge(['fl_relatorio_completo' => $fl_relatorio_completo]);
            $request->merge(['fl_link_relatorio' => $fl_link_relatorio]);
            $request->merge(['fl_area_restrita' => $fl_area_restrita]);

            $cliente = Cliente::create([
                'fl_ativo' => $request->fl_ativo,
                'fl_print' => $request->fl_print,
                'fl_tv' => $request->fl_tv,
                'fl_impresso' => $request->fl_impresso,
                'fl_web' => $request->fl_web,
                'fl_radio' => $request->fl_radio,
                'fl_relatorio_consolidado' => $request->fl_relatorio_consolidado,
                'fl_relatorio_completo' => $request->fl_relatorio_completo,
                'fl_link_relatorio' => $request->fl_link_relatorio,
                'fl_area_restrita' => $request->fl_area_restrita,
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
        $dados = array();
      
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d")." 00:00:00";
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d")." 23:59:59";

        $dt_inicial_formatada = ($request->dt_inicial) ? $request->dt_inicial : date("d/m/Y");
        $dt_final_formatada = ($request->dt_final) ? $request->dt_final : date("d/m/Y");

        $fl_web = $request->fl_web == true ? true : false;
        $fl_tv = $request->fl_tv == true ? true : false;
        $fl_impresso = $request->fl_impresso == true ? true : false;
        $fl_radio = $request->fl_radio == true ? true : false;

        return view('cliente/noticias', compact('dados','dt_inicial','dt_final','fl_web','fl_tv','fl_radio','fl_impresso'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $fl_ativo = $request->fl_ativo == true ? true : false;
        $fl_print = $request->fl_print == true ? true : false;

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

    public function adicionarArea(Request $request)
    {
        $area = Area::where('descricao', 'ILIKE', '%'.trim($request->ds_area).'%')->first();

        if(!$area){
            $area = Area::create(['descricao' => $request->ds_area]);
        }

        $cliente_area = ClienteArea::where('cliente_id', $request->id_cliente)->where('area_id', $area->id)->first();

        if(!$cliente_area){
            $created = ClienteArea::create([
                'cliente_id' => $request->id_cliente,
                'area_id' => $area->id,
                'ativo' => true
            ]);
        }
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
}
