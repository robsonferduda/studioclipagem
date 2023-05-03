<?php

namespace App\Http\Controllers;

use App\Utils;
use Carbon\Carbon;
use App\Models\Cidade;
use App\Models\Estado;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class NoticiaRadioController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
    }

    public function index(Request $request)
    {
        return view('noticia-radio/index');
    }

    public function create()
    {
        return view('noticia-radio/create');
    }
}