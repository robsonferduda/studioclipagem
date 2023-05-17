<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Utils;
use App\Models\Emissora;
use Carbon\Carbon;
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
        $emissoras = Emissora::orderBy('ds_emissora')->get();

        if($request->isMethod('POST')){
            $programas = array();
        }

        if($request->isMethod('GET')){
            $programas = array();
        }

        return view('programa/index',compact('programas','emissoras'));
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
