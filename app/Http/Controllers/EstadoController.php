<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\EmailRequest;
use Illuminate\Support\Facades\Session;

class EstadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        
    }

    public function getCidades($estado)
    {
        $cidades = Cidade::where('cd_estado',$estado)->orderBy('nm_cidade')->get();
        return response()->json($cidades);
    }
}