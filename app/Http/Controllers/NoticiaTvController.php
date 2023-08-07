<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Area;
use App\Models\Cidade;
use App\Utils;
use App\Models\Emissora;
use App\Models\Estado;
use App\Models\NoticiaTv;
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

    public function dashboard()
    {
        Session::put('sub-menu','radios');

        $data_final = date("Y-m-d");
        $data_inicial = Carbon::now()->subDays(7)->format('Y-m-d');

        $total_noticia_tv = NoticiaTv::whereBetween('created_at', [$this->data_atual.' 00:00:00', $this->data_atual.' 23:59:59'])->count();
        $ultima_atualizacao = NoticiaTv::max('created_at');

        $total_emissora_tv = Emissora::where('tipo_id', 2)->count();
        $ultima_atualizacao_tv = Emissora::where('tipo_id', 2)->max('created_at');

        $noticias = NoticiaTv::paginate(10);
        return view('noticia-tv/dashboard', compact('noticias','total_noticia_tv', 'total_emissora_tv', 'ultima_atualizacao','ultima_atualizacao_tv','data_final','data_inicial'));
    }


    public function estatisticas()
    {
        $dados = array();
        $totais = (new NoticiaTv())->getTotais();

        for ($i=0; $i < count($totais); $i++) { 
            $dados['label'][] = date('d/m/Y', strtotime($totais[$i]->dt_noticia));
            $dados['totais'][] = $totais[$i]->total;
        }

        return response()->json($dados);
    }
}