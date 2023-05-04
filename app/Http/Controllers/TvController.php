<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\JornalWeb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TvController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','tv');
    }

    public function index()
    {
        $dados = array();
        return view('tv/index',compact('dados'));
    }
}