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

class PautaController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','pautas');
    }

    public function index()
    {
        Session::put('sub-menu','pautas');

        return view('pauta/index');
    }

    public function cadastrar()
    {
        Session::put('sub-menu','pauta-cadastrar');

        return view('pauta/cadastro');
    }
}