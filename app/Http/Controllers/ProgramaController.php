<?php

namespace App\Http\Controllers;

use App\Utils;
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

    public function index()
    {
        $programas = array();
        return view('programa/index',compact('programas'));
    }
}