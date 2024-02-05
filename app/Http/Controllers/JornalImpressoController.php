<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use File;
use Carbon\Carbon;
use App\Models\Cliente;
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

class JornalImpressoController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','impresso');
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','impresso');

        $fontes = FonteImpressa::orderBy('nome')->get();
        $total_impresso = FonteImpressa::count();
        $ultima_atualizacao_impresso = FonteImpressa::max('created_at');

        $total_noticias = JornalImpresso::where('dt_clipagem', $this->data_atual)->count();
        $ultima_atualizacao_noticia = JornalImpresso::max('created_at');

        if($request->isMethod('POST')){

            $carbon = new Carbon();
            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
            $termo = $request->termo;

            $jornais = JornalImpresso::query();

            $jornais->when($termo, function ($q) use ($termo) {
                return $q->where('texto', 'ILIKE', '%'.trim($termo).'%');
            });

            $dados = $jornais->whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->orderBy('id_fonte')->orderBy('nu_pagina_atual')->paginate(10);

        }

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

        return view('jornal-impresso/index',compact('fontes','dados','dt_inicial','dt_final','total_impresso','ultima_atualizacao_impresso', 'total_noticias','ultima_atualizacao_noticia'));
    }

    public function listar()
    {
        Session::put('sub-menu','fonte-impressa');
        $jornais = FonteImpressa::orderBy('nome')->get();
        return view('fonte-impresso/listar',compact('jornais'));
    }

    public function cadastrar()
    {
        Session::put('sub-menu','fonte-impressa');
        $estados = Estado::orderBy('nm_estado')->get();

        return view('fonte-impresso/novo', compact('estados'));
    }

    public function editar(int $id)
    {
        $jornal = FonteImpressa::find($id);
        $estados = Estado::orderBy('nm_estado')->get();

        $cidades = null;
        if($jornal->cd_estado) {
            $cidades = Cidade::where(['cd_estado' => $jornal->cd_estado])->orderBy('nm_cidade')->get();
        }

        return view('jornal-impresso/editar')->with('jornal', $jornal)->with('estados', $estados)->with('cidades', $cidades);
    }

    public function detalhes($id)
    {
        $noticia = JornalImpresso::find($id);
        return view('jornal-impresso/detalhes',compact('noticia'));
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

        $clientes = Cliente::with('pessoa')
                    ->join('pessoas', 'pessoas.id', '=', 'clientes.pessoa_id')
                    ->orderBy('nome')
                    ->get();

        $noticias = NoticiaCliente::where('tipo_id', 1)->where('cliente_id', $cliente)->whereBetween('created_at', [date('Y-m-d')." 00:00:00", date('Y-m-d')." 23:59:59"])->get();
        $noticias = NoticiaCliente::where('tipo_id', 1)->where('cliente_id', $cliente)->orderBy('id')->get();

        if($request->isMethod('POST')){

            $cliente = ($request->cliente) ? $request->cliente : 0;
            Session::put('cliente_monitoramento', $cliente);
        }

        return view('jornal-impresso/monitoramento', compact('clientes','noticias'));
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

        JobProcessarImpressos::dispatch();

        return response()->json(['success'=>$file_name, 'msg' => 'Arquivo inserido com sucesso.']);
    }

    public function processar()
    {
        JobProcessarImpressos::dispatch();

        /*
        $process = new Process(['python3', base_path().'/read-pdf-convert-to-jpg.py']);

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

    public function inserir(Request $request)
    {
        $fonte = FonteImpressa::where('codigo', $request->codigo)->first();

        if($fonte){
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-exclamation"></i> Já existe um registro cadastrado com esse código');

            Flash::warning($retorno['msg']);
            return redirect('jornal-impresso/cadastrar')->withInput();
        }

        try {
            FonteImpressa::create([
                'nome' => $request->nome,
                'cd_cidade' => $request->cidade,
                'cd_estado' => $request->estado,
                'codigo' => $request->codigo ?? null
            ]);

            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados inseridos com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {
            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('jornal-impresso/listar')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('jornal-impresso/cadastrar')->withInput();
        }
    }

    public function atualizar(Request $request, int $id)
    {
        $jornal = FonteImpressa::find($id);

        try {
            $jornal->update([
                'codigo'    => $request->codigo,
                'nome'      => $request->nome,
                'cd_cidade' => $request->cidade,
                'cd_estado' => $request->estado
            ]);

            $retorno = array(
                'flag' => true,
                'msg' => '<i class="fa fa-check"></i> Dados atualizados com sucesso'
            );

        } catch (\Illuminate\Database\QueryException $e) {
            $retorno = array(
                'flag' => false,
                'msg' => Utils::getDatabaseMessageByCode($e->getCode())
            );
        } catch (\Exception $e) {
            $retorno = array(
                'flag' => true,
                'msg' => "Ocorreu um erro ao atualizar o registro"
            );
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('jornal-impresso/listar')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect()->route('jornal-impresso.editar', $jornal->id)->withInput();
        }
    }

    public function excluir(int $id)
    {
        $fonte = FonteImpressa::find($id);
        
        if($fonte->noticias){
            if($jornal->delete())
                Flash::success('<i class="fa fa-check"></i> Fonte impressa <strong>'.$jornal->nome.'</strong> excluído com sucesso');
            else
                Flash::error("Erro ao excluir o registro");
        }else{
            Flash::warning('<i class="fa fa-check"></i> Impossível excluir essa fonte, ela possui notícias associadas');
        }

        return redirect('fonte-impresso/listar')->withInput();
    }
}