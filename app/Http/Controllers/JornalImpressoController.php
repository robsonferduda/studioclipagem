<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\FilaImpresso;
use App\Models\JornalImpresso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class JornalImpressoController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
    }

    public function index(Request $request)
    {
        if($request->isMethod('POST')){

            $carbon = new Carbon();
            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");

            $dados = JornalImpresso::whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->paginate(10);
        }

        if($request->isMethod('GET')){
            $dados = JornalImpresso::where('dt_clipagem', $this->data_atual)->paginate(10);
        }

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

    public function processar()
    {
        $process = new Process(['python3', base_path().'/read-pdf-convert-to-jpg.py']);

        $process->run(function ($type, $buffer){
            if (Process::ERR === $type) {
              
            }else{

            }
        });

        return redirect()->back();        
    }
}