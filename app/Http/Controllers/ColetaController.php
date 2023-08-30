<?php

namespace App\Http\Controllers;

use App\Models\ColetaWeb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ColetaController extends Controller
{
    private $client_id;
    private $data_atual;
    private $periodo_padrao;
    private $noticias = array();

    public function __construct()
    {
        $this->middleware('auth');        
        Session::put('url','coleta');
        $this->data_atual = session('data_atual');
    }

    public function index(Request $request)
    {
        $coletas = array();
        $dt_coleta = ($request->dt_coleta) ? $request->dt_coleta : date("d/m/Y");

        if($request->isMethod('GET')){
            $coletas = ColetaWeb::whereBetween('created_at', [$this->data_atual.' 00:00:00', $this->data_atual.' 23:59:59'])->get();
        }

        if($request->isMethod('POST')){
            $coletas = ColetaWeb::whereBetween('created_at', [$request->dt_coleta.' 00:00:00', $request->dt_coleta.' 23:59:59'])->get();
        }

        return view('coleta/index', compact('coletas','dt_coleta'));
    }

    public function noticias($id)
    {
       
    }

    public function executar()
    {
       
    }
}