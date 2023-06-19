<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Cidade;
use App\Utils;
use App\Models\Emissora;
use App\Models\Estado;
use App\Models\NoticiaRadio;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class NoticiaTvController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','tv');
    }

    public function getBasePath()
    {
        return storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','tv-noticias');

        return view('noticia-tv/index');
    }
}