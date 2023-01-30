<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use File;
use Carbon\Carbon;
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
    }

    public function index(Request $request)
    {
        $fontes = Fonte::where('tipo_fonte_id',1)->orderBy('ds_fonte')->get();

        if($request->isMethod('POST')){

            $carbon = new Carbon();
            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");

            $dados = JornalImpresso::whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->orderBy('id_fonte')->orderBy('nu_pagina_atual')->paginate(10);

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

        return view('jornal-impresso/index',compact('fontes','dados','dt_inicial','dt_final'));
    }

    public function listar()
    {
        $jornais = FonteImpressa::all();
        return view('jornal-impresso/listar',compact('jornais'));
    }

    public function cadastrar()
    {
        $estados = Estado::orderBy('nm_estado')->get();
        return view('jornal-impresso/novo', compact('estados'));
    }

    public function editar(int $id)
    {
        $jornal = FonteImpressa::find($id);
        $estados = Estado::orderBy('nm_estado')->get();

        $cidades = null;
        if($jornal->cd_estado) {
            $cidades = Cidade::where(['cd_estado' => $jornal->cd_estado])->orderBy('nm_cidade')->get();;
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
        try {
            FonteImpressa::create([
                'nome' => $request->nome,
                'cd_cidade' => $request->cidade,
                'cd_estado' => $request->estado,
                'codigo' => $request->codigo ?? null
            ]);

            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

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

    public function remover(int $id)
    {
        $jornal = FonteImpressa::find($id);
        if($jornal->delete())
            Flash::success('<i class="fa fa-check"></i> Jornal <strong>'.$jornal->nome.'</strong> excluído com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('jornal-impresso/listar')->withInput();
    }
}
