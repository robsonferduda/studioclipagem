<?php

namespace App\Http\Controllers;

use Auth;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class AssessoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','assessorias');
    }

    public function index(Request $request)
    {
        return view('assessoria/index');
    }

    public function clientes()
    {
        $clientes = \App\Models\Cliente::where('fl_ativo', true)
            ->orderBy('nome')
            ->get()
            ->map(function($cliente) {
                return [
                    'id' => $cliente->id,
                    'pessoa' => [
                        'nome' => $cliente->nome
                    ]
                ];
            });
        
        return response()->json($clientes);
    }

    public function show($id)
    {
        
    }
}