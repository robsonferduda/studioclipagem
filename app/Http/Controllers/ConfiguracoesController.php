<?php

namespace App\Http\Controllers;

use App\Client;
use App\Configs;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ConfiguracoesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','configuracoes');
    }

    public function index()
    {
        $cliente = Client::find(Configs::where('key','cliente_padrao')->first()->value);
        $periodo = Configs::where('key','periodo_padrao')->first()->value;
        $flag_regras = Configs::where('key','flag_regras')->first()->value;

        return view('configuracoes/index',compact('cliente','periodo','flag_regras'));
    }

    public function selecionarCliente(Request $request)
    {
        $config = Configs::where('key', 'cliente_padrao')->first();
        $config->value = $request->cliente;
        if($config->save()){
            $cliente = Client::find($request->cliente);
            $cliente_session = array('id' => $cliente->id, 'nome' => $cliente->name);
            Session::put('cliente', $cliente_session);

            Flash::success('<i class="fa fa-check"></i> Cliente atualizado com sucesso');
        }else{
            Flash::error("Erro ao atualizar valor");
        }
    }

    public function selecionarPeriodo(Request $request)
    {
        $config = Configs::where('key', 'periodo_padrao')->first();
        $config->value = $request->periodo;
        if($config->save())
            Flash::success('<i class="fa fa-check"></i> Cliente atualizado com sucesso');
        else
            Flash::error("Erro ao atualizar valor");
    }

    public function atualizarFlag(Request $request)
    {
        $config = Configs::where('key', 'flag_regras')->first();
        $config->value = !$request->valor;

        if($config->save()){
            Session::put('flag_regras', !$request->valor);
            Flash::success('<i class="fa fa-check"></i> Indicador de filtro atualizado com sucesso');
        }else{
            Flash::error("Erro ao atualizar indicador");
        }
    }
}