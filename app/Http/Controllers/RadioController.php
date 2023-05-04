<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Emissora;
use App\Models\JornalWeb;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RadioController extends Controller
{
    private $client_id;
    private $periodo_padrao;

    public function __construct()
    {
        $this->middleware('auth');

        $cliente = null;

        $clienteSession = ['id' => 1, 'nome' => 'Teste'];

        Session::put('cliente', session('cliente') ? session('cliente') : $clienteSession);

        $this->client_id = session('cliente')['id'];
        
        Session::put('url','radio');

        $this->periodo_padrao = 7;
    }

    public function index()
    {
        $dados = array();
        return view('radio/index',compact('dados'));
    }

    public function emissoras(Request $request)
    {
        if($request->isMethod('POST')){

            $codigo = $request->codigo;
            $descricao = $request->descricao;

            $emissora = Emissora::query();

            $emissora->when(request('codigo'), function ($q) use ($codigo) {
                return $q->where('codigo', $codigo);
            });

            $emissora->when(request('descricao'), function ($q) use ($descricao) {
                return $q->where('ds_emissora','ilike','%'.$descricao.'%');
            });

            $emissoras = $emissora->orderBy('ds_emissora')->paginate(10);

        }

        if($request->isMethod('GET')){

            $emissoras = Emissora::orderBy('ds_emissora')->paginate(10);

        }

        return view('radio/emissoras', compact('emissoras'));
    }

    public function atualizaTranscricao($id)
    {
        $emissora = Emissora::find($id);
        $emissora->fl_transcricao = !$emissora->fl_transcricao;
        $emissora->save();

        Flash::success("Transcrição atualizada com sucesso");

        return redirect('radio/emissoras')->withInput();
    }
}