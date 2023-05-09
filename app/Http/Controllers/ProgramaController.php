<?php

namespace App\Http\Controllers;

use App\Utils;
use App\Models\Emissora;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

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
}