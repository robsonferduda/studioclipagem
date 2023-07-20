<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
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
}