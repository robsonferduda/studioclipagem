<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Cliente;
use App\Models\ClienteArea;
use App\Models\EnderecoEletronico;
use App\Models\Pessoa;
use App\Utils;
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
    }

    public function index(): View
    {
        // $clientes = Cliente::all(); //Lista todos os clientes
        $clientes = Cliente::with('pessoa')->get();

        // $pessoa = Cliente::find(4)->pessoa; //Lista a pessoa
        // $nome = Cliente::find(4)->pessoa->nome; //Mostra nome da pessoa
        // $emails = Cliente::find(4)->pessoa->enderecoEletronico; //Mostra os endereços da pessoa

        return view('cliente/index',compact('clientes'));
    }

    public function create(): View
    {
        $areas  =Area::all();
        return view('cliente/novo', compact('areas'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {

            $pessoa = Pessoa::create([
                'nome' => $request->nome,
                'cpf_cnpj' => preg_replace('/\D/', '', $request->cpf_cnpj)
            ]);

            $cliente = Cliente::create([
                'ativo' => $request->ativo,
                'pessoa_id' => $pessoa->id
            ]);

            $this->cadastrarEnderecoEletronico($request, $cliente);
            $this->gerenciaClienteArea($request, $cliente);

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
        $cliente = Cliente::with(['pessoa', 'clienteArea'])->find($id);
        $areas  = Area::all();
        $emails = EnderecoEletronico::where('pessoa_id', $cliente->pessoa->id)->get();

        //$emails = json_decode($emails);

        return view('cliente/editar',compact('cliente', 'emails', 'areas'));
    }

    public function update(Request $request, int $id):RedirectResponse
    {
        $flag = $request->ativo == true ? true : false;

        $cliente = Cliente::find($id);
        try {

            $cliente->ativo = $flag;
            $cliente->update();

            $cliente->pessoa->update([
                'nome' => $request->nome,
                'cpf_cnpj' => preg_replace('/\D/', '', $request->cpf_cnpj)
            ]);

            EnderecoEletronico::where('pessoa_id', $cliente->pessoa->id)->delete();

            $this->cadastrarEnderecoEletronico($request, $cliente);
            $this->gerenciaClienteArea($request, $cliente);

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
                            'expressao' => $request->expressao[$key],
                            'ativo' => $request->status[$key] == "true"
                        ]);
                        continue;
                    }

                    if(empty($request->expressao[$key])) {
                        continue;
                    }

                    $created = ClienteArea::create([
                        'cliente_id' => $cliente->id,
                        'area_id' => $area,
                        'expressao' => $request->expressao[$key],
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
