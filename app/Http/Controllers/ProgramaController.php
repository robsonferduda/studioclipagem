<?php

namespace App\Http\Controllers;

use App\Models\Programa;
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

    public function index()
    {
        $programas = array();
        return view('programa/index',compact('programas'));
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
