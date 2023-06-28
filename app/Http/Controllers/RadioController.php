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
        Session::put('url','radio');

    }

    public function index()
    {
        Session::put('sub-menu','radios');

        $dados = array();
        return view('radio/index',compact('dados'));
    }
}