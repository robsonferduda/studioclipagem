<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use File;
use Carbon\Carbon;
use App\Models\NoticiaCliente;
use App\Models\FonteImpressa;
use App\Models\FilaImpresso;
use App\Models\JornalImpresso;
use App\Models\Fonte;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Jobs\ProcessarImpressos as JobProcessarImpressos;
use App\Models\Cidade;
use App\Models\Estado;
use App\Utils;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class NoticiaImpressaController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
    }

    public function index(Request $request)
    {
        
    }

    public function cadastrar()
    {
        return view('noticia-impressa/cadastrar');
    }

    public function editar($cliente, $id_noticia)
    {
        $noticia_original = JornalImpresso::find($id_noticia);
        $vinculo_original = NoticiaCliente::where('noticia_id', $id_noticia)->where('tipo_id',1)->where('cliente_id', $cliente)->first();

        $nova = $noticia_original->replicate();
        $nova->titulo = "Notícia Alterada";
        $nova->save();

        $vinculo_original->noticia_id = $nova->id;
        $vinculo_original->save();

        return redirect('jornal-impresso/monitoramento');
    }
}