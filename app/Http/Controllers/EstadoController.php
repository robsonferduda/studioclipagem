<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

    }

    public function getCidades($id)
    {
        $cidades = Cidade::where('cd_estado', $id)->orderBy('nm_cidade')->get();
        return response()->json($cidades);
    }

    public function siglas()
    {
        $siglas = Estado::select('sg_estado')->orderBy('sg_estado')->get();
        return response()->json($siglas);
    }
}