<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\FilaImpresso;
use App\Models\JornalImpresso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class JornalImpressoController extends Controller
{
    private $client_id;
    private $periodo_padrao;

    public function __construct()
    {
        $this->middleware('auth');

        $cliente = null;

        $clienteSession = ['id' => 1, 'nome' => 'Teste'];

        Session::put('cliente', session('cliente') ? session('cliente') : $clienteSession);

        $this->client_id = session('cliente')['id'];
        
        Session::put('url','home');

        $this->periodo_padrao = 7;
    }

    public function index()
    {
        $dados = JornalImpresso::all();
        return view('jornal-impresso/index',compact('dados'));
    }

    public function upload()
    {
        return view('jornal-impresso/upload');
    }

    public function processamento()
    {
        $fila = FilaImpresso::all();
        return view('jornal-impresso/processamento', compact('fila'));
    }

    public function uploadFiles(Request $request)
    {
        $image = $request->file('file');
        $fileInfo = $image->getClientOriginalName();
        $filesize = $image->getSize()/1024/1024;
        $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
        $extension = pathinfo($fileInfo, PATHINFO_EXTENSION);
        $file_name= $filename.'-'.time().'.'.$extension;
        $image->move(public_path('jornal-impresso/pendentes'),$file_name);

        $partes = explode("_", $filename);
        $dt_arquivo = strtotime($partes[1]);
        $dt_arquivo = Carbon::createFromFormat('Ymd', $partes[0]); 
        $id_fonte = $partes[1];

        $dados = array('dt_arquivo' => $dt_arquivo->format('Y-m-d'),
                       'ds_arquivo' => $file_name,
                       'id_fonte' => $id_fonte,
                       'tamanho' => $filesize);
        FilaImpresso::create($dados);
        
        return response()->json(['success'=>$file_name, 'msg' => 'Arquivo inserido com sucesso.']);
    }
}