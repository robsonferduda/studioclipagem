<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotificacaoController extends Controller
{
    private $data_atual;
    private $carbon;
    
    public function __construct()
    {
        $this->middleware('auth');        
        Session::put('url','coleta');
        $this->data_atual = session('data_atual');
    }

    public function index(Request $request)
    {
        
    }

    public function notificar()
    {
       
    }
}