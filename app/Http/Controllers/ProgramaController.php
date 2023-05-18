<?php

namespace App\Http\Controllers;

use App\Utils;
use Carbon\Carbon;
use App\Models\Emissora;
use App\Models\Programa;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProgramaController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','radio');
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','emissoras-programas');

        $emissoras = Emissora::orderBy('ds_emissora')->get();

        if($request->isMethod('GET')){
            $programas = Programa::with('emissora')->orderBy('nome')->paginate(10);
        }

        if($request->isMethod('POST')){
            $programas = array();
        }

        return view('programa/index',compact('programas','emissoras'));
    }

    public function novo()
    {
        $emissoras = Emissora::orderBy('ds_emissora')->get();

        return view('programa/novo',compact('emissoras'));
    }

    public function store(Request $request)
    {
        try {

            Programa::create($request->all());
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados inseridos com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {

            $retorno = array('flag' => false,
                             'msg' => '<i class="fa fa-times"></i> Ocorreu um erro ao inserir o registro');
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
        } else {
            Flash::error($retorno['msg']);
        }

        return redirect('emissoras/programas')->withInput();
    }

    public function buscarProgramas(Request $request)
    {
        $programas = Programa::select('id', 'nome as text');
        if(!empty($request->query('q'))) {
            $replace = preg_replace('!\s+!', ' ', $request->query('q'));
            $busca = str_replace(' ', '%', $replace);
            $programas->whereRaw('nome ILIKE ?', ['%' . strtolower($busca) . '%']);
        }
        if(!empty($request->query('emissora'))) {
            $programas->where('emissora_id', $request->query('emissora'));
        }

        $result = $programas->orderBy('nome', 'asc')->paginate(30);
        return response()->json($result);
    }
}
