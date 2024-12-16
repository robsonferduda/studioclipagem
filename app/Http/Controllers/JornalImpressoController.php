<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use File;
use Storage;
use Carbon\Carbon;
use App\Models\Cliente;
use App\Models\NoticiaImpresso;
use App\Models\EdicaoJornalImpresso;
use App\Models\NoticiaCliente;
use App\Models\PaginaJornalImpresso;
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

class JornalImpressoController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        $this->dt_inicial = session('dt_inicial');
        $this->dt_final = session('dt_final');
        Session::put('url','impresso');
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','impresso');

        $carbon = new Carbon();
        $fontes = FonteImpressa::orderBy('nome')->get();
        $busca_fonte = "";
        $termo = "";

        if($request->isMethod('GET')){

            if($request->dt_inicial){
                $dt_inicial = $request->dt_inicial;
                $dt_final = $request->dt_final;

                $dados = JornalImpresso::whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->orderBy('id_fonte')->orderBy('nu_pagina_atual')->paginate(10);
            }else{
                $dt_inicial = date('d/m/Y');
                $dt_final = date('d/m/Y');
                $dados = JornalImpresso::where('dt_clipagem', $this->data_atual)->orderBy('id_fonte')->orderBy('nu_pagina_atual')->paginate(10);
            }

        }
      
        if($request->isMethod('POST')){

            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
            $busca_fonte = $request->fonte;
            $termo = $request->termo;
            
            $jornais = JornalImpresso::query();

            $jornais->when($termo, function ($q) use ($termo) {
                return $q->where('texto', 'ILIKE', '%'.trim($termo).'%');
            });

            $dados = $jornais->whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->orderBy('id_fonte')->orderBy('nu_pagina_atual')->paginate(10);

        }

        return view('jornal-impresso/index',compact('fontes','dados','dt_inicial','dt_final','termo','busca_fonte'));
    }

    public function detalhes($id)
    {
        $noticia = JornalImpresso::find($id);
        return view('jornal-impresso/detalhes',compact('noticia'));
    }

    public function web(Request $request)
    {
        Session::put('sub-menu','arquivos-web');

        $fonte = ($request->fonte) ? $request->fonte : null;

        $carbon = new Carbon();
        $fontes = FonteImpressa::where('tipo', 1)->orderBy("nome")->get();
        $dados = array();
        $busca_fonte = "";
        $termo = "";

        if($request->isMethod('GET')){

            if($request->dt_inicial){
               
                $dt_inicial = ($request->dt_inicial) ? $request->dt_inicial : date("Y-m-d")." 00:00:00";
                $dt_final = ($request->dt_final) ? $request->dt_final : date("Y-m-d H:i:s");

                $dados = EdicaoJornalImpresso::with('fonte')->with('paginas')->whereBetween('dt_coleta', [$dt_inicial, $dt_final])->orderBy('dt_coleta','DESC')->paginate(10);

            }else{
               
                $dt_inicial = "2024-11-01 00:00:00";
                $dt_final = date('Y-m-d H:i:s');
                $dados = EdicaoJornalImpresso::with('fonte')->with('paginas')->whereBetween('dt_coleta', [$dt_inicial, $dt_final])->orderBy('dt_coleta','DESC')->paginate(10);
            }

        }

        if($request->isMethod('POST')){
            
            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d")." 00:00:00";
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d H:i:s");
            $busca_fonte = $request->fonte;
            $termo = $request->termo;

            $jornais = EdicaoJornalImpresso::query();

            $jornais->when($fonte, function ($q) use ($fonte) {
                return $q->where('id_jornal_online', $fonte);
            });

            $dados = $jornais->whereBetween('dt_coleta', [$dt_inicial, $dt_final])->orderBy('dt_coleta','DESC')->paginate(10);
        }

        return view('jornal-impresso/web', compact("fontes", "dados", 'dt_inicial','dt_final','termo','busca_fonte'));
    }

    public function todasPaginas(Request $request)
    {
        Session::put('sub-menu','arquivos-paginas');

        $fonte = ($request->fonte) ? $request->fonte : null;

        $carbon = new Carbon();
        $fontes = FonteImpressa::where('tipo', 1)->orderBy("nome")->get();
        $dados = array();
        $busca_fonte = "";
        $termo = "";
        $paginas = array();

        if($request->isMethod('GET')){

            if($request->dt_inicial){
               
                $dt_inicial = ($request->dt_inicial) ? $request->dt_inicial : date("Y-m-d")." 00:00:00";
                $dt_final = ($request->dt_final) ? $request->dt_final : date("Y-m-d H:i:s");

                $paginas = PaginaJornalImpresso::whereHas('edicao', function ($q){
                                return $q->whereBetween('dt_coleta', [$dt_inicial, $dt_final])->orderBy('dt_coleta','DESC');
                            })->with('fonte')->with('paginas')->paginate(10);

            }else{
               
                $dt_inicial = "2024-11-01 00:00:00";
                $dt_final = date('Y-m-d H:i:s');

                $paginas =  PaginaJornalImpresso::whereHas('edicao', function ($q) use($dt_inicial, $dt_final){
                    return $q->whereBetween('dt_coleta', [$dt_inicial, $dt_final])->orderBy('dt_coleta','DESC');
                })->paginate(10);
            }

        }

        if($request->isMethod('POST')){
            
            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d")." 00:00:00";
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d H:i:s");
            $busca_fonte = $request->fonte;
            $termo = $request->termo;
            $ids = [];

            $jornais = PaginaJornalImpresso::query();

            $sql = "SELECT id 
							FROM (SELECT id, to_tsvector(texto_extraido) as document 
								  FROM pagina_edicao_jornal_online) p_search 
								  WHERE p_search.document @@ plainto_tsquery('$termo')";

            $resultado = DB::select($sql);

            for ($i=0; $i < count($resultado); $i++) { 
                $ids[] = $resultado[$i]->id;
            }

            $jornais->when($fonte, function ($q) use ($fonte) {
                return $q->where('id_jornal_online', $fonte);
            });

            $paginas = $jornais->whereIn('id', $ids)->paginate(10);
        }


        return view('jornal-impresso/todas-paginas', compact("fontes", "paginas", 'dt_inicial','dt_final','termo','busca_fonte'));
    }

    public function paginas($edicao)
    {
        $paginas = PaginaJornalImpresso::where('id_edicao_jornal_online', $edicao)->get();
    
        return view('jornal-impresso/paginas', compact('paginas'));
    }

    public function editar($id)
    {
        $clientes = Cliente::orderBy('nome')->get();
        $noticia = NoticiaImpresso::find($id);
    
        return view('jornal-impresso/editar', compact('noticia','clientes'));
    }

    public function extrair($tipo, $id)
    {
        switch ($tipo) {
            case 'web':
                $conteudo = PaginaJornalImpresso::find($id);

                $arquivo = Storage::disk('s3')->get($conteudo->path_pagina_s3);
                $filename = $id.".jpg";

                $nova_noticia = array("id_fonte" => $conteudo->edicao->id_jornal_online,
                                      "dt_clipagem" => $conteudo->edicao->dt_coleta,
                                      "texto" => $conteudo->texto_extraido,
                                      "nu_paginas_total" => $conteudo->edicao->paginas->count(),
                                      "nu_pagina_atual" => $conteudo->n_pagina,
                                      "ds_caminho_img" => $filename);

                $noticia = NoticiaImpresso::create($nova_noticia);

                Storage::disk('impresso-img')->put($filename, $arquivo);

                return redirect('jornal-impresso/noticia/editar/'.$noticia->id);

                break;
            
            case 'impresso':
                # code...
                break;                
        }
    }

    public function getImage($path)
    {
        return response()->make(
            Storage::disk('s3')->get($path),
            200,
            ['Content-Type' => 'image/jpeg']
        );
    }

    public function upload()
    {
        Session::put('sub-menu','upload');

        return view('jornal-impresso/upload');
    }

    public function processamento(Request $request)
    {
        $fontes = FonteImpressa::all();

        if($request->isMethod('GET')){

            $dt_inicial = date('Y-m-d H:i:s');
            $dt_final = date('Y-m-d H:i:s');

            $fila = FilaImpresso::whereBetween('dt_arquivo', [$dt_inicial, $dt_final])->get();
        }

        if($request->isMethod('POST')){

            $carbon = new Carbon();
            $dt_envio = $request->dt_envio;
            $dt_inicial = $request->dt_inicial;
            $dt_final = $request->dt_final;
            $dt_arquivo = $request->dt_arquivo;

            $fila = FilaImpresso::query();

            $fila->when($dt_envio, function ($q) use ($dt_envio, $carbon) {
                $dt_envio_inicio = $carbon->createFromFormat('d/m/Y H:i:s', $dt_envio." 00:00:00")->format('Y-m-d H:i:s');
                $dt_envio_final = $carbon->createFromFormat('d/m/Y H:i:s', $dt_envio." 23:59:59")->format('Y-m-d H:i:s');

                return $q->whereBetween('created_at', [$dt_envio_inicio, $dt_envio_final]);
            });

            $fila->when($dt_inicial, function ($q) use ($dt_inicial, $carbon) {
                $dt_inicial_inicio = $carbon->createFromFormat('d/m/Y', $dt_inicial)->format('Y-m-d H:i:s');
                $dt_inicial_final = $carbon->createFromFormat('d/m/Y', $dt_inicial)->format('Y-m-d H:i:s');

                return $q->whereBetween('start_at', [$dt_inicial_inicio, $dt_inicial_final]);
            });

            $fila->when($dt_final, function ($q) use ($dt_final, $carbon) {
                $dt_final_inicio = $carbon->createFromFormat('d/m/Y', $dt_final)->format('Y-m-d H:i:s');
                $dt_final_final = $carbon->createFromFormat('d/m/Y', $dt_final)->format('Y-m-d H:i:s');

                return $q->whereBetween('updated_at', [$dt_final_inicio, $dt_final_final]);
            });

            $fila->when($dt_arquivo, function ($q) use ($dt_arquivo, $carbon) {
                return $q->where('dt_arquivo', $dt_arquivo);
            });

            $fila = $fila->orderBy('id_fonte')->get();
        }

        return view('jornal-impresso/processamento', compact('fila','fontes'));
    }

    public function monitoramento(Request $request)
    {
        Session::put('sub-menu','monitoramento');
        
        $cliente = session('cliente_monitoramento') ? session('cliente_monitoramento') : 0;

        $clientes = Cliente::orderBy('nome')->get();

        $noticias = NoticiaCliente::where('tipo_id', 1)->where('cliente_id', $cliente)->whereBetween('created_at', [date('Y-m-d')." 00:00:00", date('Y-m-d')." 23:59:59"])->get();
        $noticias = NoticiaCliente::where('tipo_id', 1)->where('cliente_id', $cliente)->orderBy('id')->get();

        if($request->isMethod('POST')){

            $cliente = ($request->cliente) ? $request->cliente : 0;
            Session::put('cliente_monitoramento', $cliente);
        }

        return view('jornal-impresso/monitoramento', compact('clientes','noticias'));
    }

    public function listarMonitoramento($id)
    {
        dd($id);
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
        $cod_fonte = $partes[1];

        $fonte = FonteImpressa::where('codigo', $cod_fonte)->first();

        if(!$fonte){

            $dados = array('codigo' => $cod_fonte);
            $fonte = FonteImpressa::create($dados);
        }

        $dados = array('dt_arquivo' => $dt_arquivo->format('Y-m-d'),
                       'ds_arquivo' => $file_name,
                       'id_fonte' => $fonte->id,
                       'tamanho' => $filesize);

        FilaImpresso::create($dados);

        //JobProcessarImpressos::dispatch(); Chamada para processar impressos

        return response()->json(['success'=>$file_name, 'msg' => 'Arquivo inserido com sucesso.']);
    }

    public function processar()
    {
        JobProcessarImpressos::dispatch();

        /*
        $command = escapeshellcmd("python3 ".base_path()."/read-pdf-convert-to-jpg.py");
        $output = shell_exec($command);

        dd($output);
        
        $cmd = "python3 ".base_path()."/read-pdf-convert-to-jpg.py";

        $result = exec($cmd, $output, $return);

        dd($return);
        

        dd($process->getErrorOutput());

        $process->run(function ($type, $buffer){

            if (Process::ERR === $type) {

                $data['dados'] = $buffer;

                Mail::send('notificacoes.impressos.processamento', $data, function($message){
                    $message->to("robsonferduda@gmail.com")
                            ->subject('Erro - Processamento de Jornais Impresso');
                    $message->from('boletins@clipagens.com.br','Studio Clipagem');
                }); 
              
            }else{
                //Quando corre tudo bem

                dd("asdasdasd");
            }

            dd("asdasdasd");

        });

        /*

        try {
            $process->start();



            $process->waitUntil(function ($type, $output) {
                return $output === 'Ready. Waiting for commands...';
            });

        } catch (ProcessFailedException $exception) {
            echo $exception->getMessage();
        }



        /*
        $process->run(function ($type, $buffer){

            if (Process::ERR === $type) {

                dd($buffer);

            }else{

                dd($buffer);

                dd('Começou');

            }

        });
        */
        Flash::success('<i class="fa fa-check"></i> Fila de processamento iniciada com sucesso');
        return redirect()->back();
    }

    public function listarPendentes()
    {

        $directory = 'jornal-impresso/pendentes';
        $files_info = [];
        $file_ext = array('png','jpg','jpeg','pdf');

        // Read files
        foreach (File::allFiles(public_path($directory)) as $file) {
           $extension = strtolower($file->getExtension());

            if(in_array($extension,$file_ext)){ // Check file extension
                $filename = $file->getFilename();
                $size = $file->getSize(); // Bytes
                $sizeinMB = round($size / (1000 * 1024), 2);// MB

                $files_info[] = array(
                    "name" => $filename,
                    "size" => $size,
                    "path" => url($directory.'/'.$filename)
                );
            }
        }
        return response()->json($files_info);
    }
}