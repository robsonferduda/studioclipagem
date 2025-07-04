<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use File;
use Storage;
use DateTime;
use DateInterval;
use DatePeriod;
use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Cliente;
use App\Models\JornalWeb;
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
    private $carbon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        $this->dt_inicial = session('dt_inicial');
        $this->dt_final = session('dt_final');
        Session::put('url','impresso');
        $this->carbon = new Carbon();
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','impresso-monitoramento');

        $fontes = FonteImpressa::orderBy('nome')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();

        $tipo_data = $request->tipo_data;
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $cliente_selecionado = ($request->cliente) ? $request->cliente : null;
        $fonte = ($request->fontes) ? $request->fontes : null;
        $termo = ($request->termo) ? $request->termo : null;
       
        if($request->fontes or Session::get('impresso_filtro_fonte')){
            if($request->fontes){
                $fonte = $request->fontes;
            }elseif(Session::get('impresso_filtro_fonte')){
                $fonte = Session::get('impresso_filtro_fonte');
            }else{
                $fonte = null;
            }
        }else{
            $fonte = null;
            Session::forget('impresso_filtro_fonte');
        }

        if($request->monitoramento or Session::get('impresso_monitoramento')){
            if($request->monitoramento){
                $monitoramento = $request->monitoramento;
            }elseif(Session::get('impresso_monitoramento')){
                $monitoramento = Session::get('impresso_monitoramento');
            }else{
                $monitoramento = null;
            }
        }else{
            $monitoramento = null;
            Session::forget('impresso_monitoramento');
        }

        if($request->isMethod('POST')){

            if($request->monitoramento){
                Session::put('impresso_monitoramento', $monitoramento);
            }else{
                Session::forget('impresso_monitoramento');
            }

            if($request->fontes){
                Session::put('impresso_filtro_fonte', $fonte);
            }else{
                Session::forget('impresso_filtro_fonte');
                $fonte = null;
            }
        }
        
        $dados = DB::table('noticia_cliente')
                    ->select('path_pagina_s3',
                            'jornal_online.id AS id_fonte',
                            'jornal_online.nome AS nome_fonte',
                            'edicao_jornal_online.titulo AS edicao',
                            'edicao_jornal_online.dt_pub',
                            'n_pagina',
                            'noticia_cliente.noticia_id',
                            'noticia_cliente.monitoramento_id',
                            'texto_extraido',
                            'expressao',
                            'nm_estado',
                            'nm_cidade',
                            'fl_upload',
                            'noticia_cliente.id',
                            'noticia_cliente.created_at',
                            'noticia_cliente.id_noticia_origem',
                            'noticia_cliente.id_noticia_gerada',
                            'clientes.nome AS nome_cliente',
                            'pagina_edicao_jornal_online.id AS id_pagina')
                    ->join('clientes', 'clientes.id', '=', 'noticia_cliente.cliente_id')
                    ->join('pagina_edicao_jornal_online', function ($join) {
                        $join->on('pagina_edicao_jornal_online.id', '=', 'noticia_cliente.noticia_id')->where('tipo_id',1);
                    })
                    ->join('edicao_jornal_online','edicao_jornal_online.id','=','pagina_edicao_jornal_online.id_edicao_jornal_online')
                    ->join('jornal_online','jornal_online.id','=','edicao_jornal_online.id_jornal_online')
                    ->join('monitoramento', function($join) use($monitoramento){
                        $join->on('monitoramento.id','=','noticia_cliente.monitoramento_id')
                        ->when($monitoramento, function ($q) use ($monitoramento) {
                            return $q->where('monitoramento.id', $monitoramento);
                        });
                    })
                    ->leftJoin('estado','estado.cd_estado','=','jornal_online.cd_estado')
                    ->leftJoin('cidade','cidade.cd_cidade','=','jornal_online.cd_cidade')
                    ->when($termo, function ($q) use ($termo) {
                        return $q->where('texto_extraido', 'ILIKE', '%'.trim($termo).'%');
                    })
                    ->when($cliente_selecionado, function ($q) use ($cliente_selecionado) {
                        return $q->where('noticia_cliente.cliente_id', $cliente_selecionado);
                    })
                    ->when($fonte, function ($q) use ($fonte) {
                        return $q->whereIn('jornal_online.id', $fonte);
                    })
                    ->when($dt_inicial, function ($q) use ($dt_inicial, $dt_final) {
                        return $q->whereBetween('edicao_jornal_online.dt_coleta', [$dt_inicial." 00:00:00", $dt_final." 23:59:59"]);
                    })
                    ->when($monitoramento, function ($q) use ($monitoramento) {
                        return $q->where('noticia_cliente.monitoramento_id', $monitoramento);
                    })
                    ->orderBy('pagina_edicao_jornal_online.id_edicao_jornal_online')
                    ->orderBy('n_pagina')
                    ->paginate(20);

        return view('jornal-impresso/index',compact('clientes','fontes','dados','tipo_data','dt_inicial','dt_final','cliente_selecionado','fonte','termo','monitoramento'));
    }

    public function buscar(Request $request)
    {
        Session::put('sub-menu','jornal-impresso-buscar');
        
        $dt_inicial = date('Y-m-d H:i:s');
        $dt_final = date('Y-m-d H:i:s');
        $expressao = "";
        $fonte_selecionada = null;
        $fontes = FonteImpressa::orderBy('nome')->get();
        $impressos = array();

        $jornais = PaginaJornalImpresso::query();

        if($request->isMethod('POST') or isset($_REQUEST['page'])){
            
            $tipo_data = ($request->tipo_data) ? $request->tipo_data : 'created_at';
            $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
            $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
            $fonte_selecionada = ($request->selecionadas[0]) ? array_map('intval', explode(",", $request->selecionadas[0])) : null;
            $expressao = $request->expressao;

            $jornais = PaginaJornalImpresso::query();

            $jornais->whereBetween('created_at', [$dt_inicial." 00:00:00", $dt_final." 23:59:59"]);

            $jornais->when($fonte_selecionada, function ($q) use ($fonte_selecionada) {
                $q->whereHas('edicao', function($q) use($fonte_selecionada){
                    return $q->whereIn('id_jornal_online', $fonte_selecionada);
                });
            });

            $jornais->when($expressao, function ($q) use ($expressao) {
                return $q->where('texto_extraido', 'ILIKE', '%'.trim($expressao).'%');
            });

            $impressos = $jornais->orderBy('id_edicao_jornal_online')->orderBy('n_pagina','DESC')->paginate(10);

        }

        return view('jornal-impresso/buscar', compact("impressos","fontes",'dt_inicial','dt_final','expressao','fonte_selecionada'));
    }

    public function dashboard(Request $request)
    {
        Session::put('sub-menu','impresso');

        $dt_inicial = Carbon::now()->subDays(7);
        $dt_final = date('Y-m-d');
       
        $total_fonte_impressos = FonteImpressa::count();
        $total_noticias_impressas = count(PaginaJornalImpresso::whereBetween('created_at', [$dt_final." 00:00:00", $dt_final." 23:59:59"])->get());

        return view('jornal-impresso/dashboard', compact('dt_inicial','dt_final','total_fonte_impressos','total_noticias_impressas'));

    }

    public function detalhes($id)
    {
        $noticia = JornalImpresso::find($id);
        return view('jornal-impresso/detalhes',compact('noticia'));
    }

    public function edicoes(Request $request)
    {
        Session::put('sub-menu','jornal-impresso-edicoes');

        $fontes = FonteImpressa::orderBy("nome")->get();
        $edicoes = array();

        $fonte = ($request->fonte) ? $request->fonte : null;
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");

        $edicoes = EdicaoJornalImpresso::with('fonte')
                    ->with('paginas')
                    ->whereBetween('dt_coleta', [$dt_inicial." 00:00:00", $dt_final." 23:59:59"])
                    ->when($fonte, function ($q) use ($fonte) {
                        return $q->where('id_jornal_online', $fonte);
                    })
                    ->orderBy('dt_coleta','DESC')
                    ->paginate(10);

        return view('jornal-impresso/edicoes', compact("fontes", "edicoes", 'dt_inicial','dt_final','fonte'));
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
                $dt_final = ($request->dt_final) ? $request->dt_final : date("Y-m-d"." 23:59:59");

                $paginas = PaginaJornalImpresso::whereHas('edicao', function ($q){
                                return $q->whereBetween('dt_coleta', [$dt_inicial, $dt_final])->orderBy('dt_coleta','DESC');
                           })->with('fonte')->with('paginas')->paginate(10);

            }else{
               
                $dt_inicial = ($request->dt_inicial) ? $request->dt_inicial : date("Y-m-d")." 00:00:00";
                $dt_final = ($request->dt_final) ? $request->dt_final : date("Y-m-d"." 23:59:59");

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

            $jornais->when($fonte, function ($q) use ($fonte) {
                return $q->where('id_jornal_online', $fonte);
            });

            if($termo)
            {
                $sql = "SELECT id 
                FROM (SELECT id, to_tsvector(texto_extraido) as document 
                      FROM pagina_edicao_jornal_online) p_search 
                      WHERE p_search.document @@ plainto_tsquery('$termo')";

                $resultado = DB::select($sql);

                for ($i=0; $i < count($resultado); $i++) { 
                    $ids[] = $resultado[$i]->id;
                }

                if(!count($ids)){
                    Flash::warning('<i class="fa fa-exclamation"></i> Não foram encontrados termos para essa busca');
                }

                $paginas = $jornais->whereIn('id', $ids)->paginate(10);

            }else{

                $paginas = $jornais->paginate(10);

            }
           
        }

        return view('jornal-impresso/todas-paginas', compact("fontes", "paginas", 'dt_inicial','dt_final','termo','busca_fonte'));
    }

    public function paginas($edicao)
    {
        $paginas = PaginaJornalImpresso::where('id_edicao_jornal_online', $edicao)->orderBy('n_pagina')->get();
    
        return view('jornal-impresso/paginas', compact('paginas'));
    }

    public function editar($id)
    {
        $clientes = Cliente::orderBy('nome')->get();
        $noticia = NoticiaImpresso::find($id);
        $tags = Tag::orderBy('nome')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $cidades = Cidade::where(['cd_estado' => $noticia->cd_estado])->orderBy('nm_cidade')->get();
        $fontes = FonteImpressa::orderBy("nome")->get();

        dd("dadas");
    
        return view('jornal-impresso/editar', compact('noticia','clientes','fontes','estados','cidades','tags'));
    }

    public function extrair($monitoramento, $tipo, $id)
    {
        switch ($tipo) {

            case 'impresso':

                $conteudo = PaginaJornalImpresso::find($id);

                $arquivo = Storage::disk('s3')->get($conteudo->path_pagina_s3);
                $filename = $id.".jpg";

                $nova_noticia = array("id_fonte" => $conteudo->edicao->id_jornal_online,
                                      "dt_clipagem" => ($conteudo->edicao->dt_pub) ? $conteudo->edicao->dt_pub : $conteudo->edicao->dt_coleta,
                                      "texto" => $conteudo->texto_extraido,
                                      "cd_usuario" => Auth::user()->id,
                                      "nu_paginas_total" => $conteudo->edicao->paginas->count(),
                                      "nu_pagina_atual" => $conteudo->n_pagina,
                                      "ds_caminho_img" => $filename);

                $noticia = NoticiaImpresso::create($nova_noticia);

                Storage::disk('impresso-img')->put($filename, $arquivo);

                $vinculo = NoticiaCliente::where('noticia_id', $id)->where('monitoramento_id', $monitoramento)->where('tipo_id', 1)->first();

                $dados = array('tipo_id' => 1,
                                'noticia_id' => $noticia->id,
                                'cliente_id' => (int) $vinculo->cliente_id,
                                'area' => null,
                                'sentimento' => 0);

                    $match = array('tipo_id' => 1,
                                'noticia_id' => $noticia->id,
                                'cliente_id' => (int) $vinculo->cliente_id);
                        
                    $dados = array('area' => null,
                                   'monitoramento_id' => $monitoramento,
                                   'id_noticia_origem' => $conteudo->id,
                                   'id_noticia_gerada' => $noticia->id,
                                   'sentimento' => 0);

                    $noticia_cliente = NoticiaCliente::updateOrCreate($match, $dados);

                

                $estados = Estado::orderBy('nm_estado')->get();
                $cidades = Cidade::where(['cd_estado' => $noticia->cd_estado])->orderBy('nm_cidade')->get();
                $fontes = FonteImpressa::orderBy("nome")->get();
                $clientes = Cliente::where('fl_ativo', true)->orderBy('fl_ativo')->orderBy('nome')->get();
                $tags = Tag::orderBy('nome')->get();
                $pagina = $conteudo;

                return view('jornal-impresso/editar', compact('noticia','clientes','fontes','estados','cidades','tags','pagina'));

                break;
            
            case 'impresso':
                # code...
                break;                
        }
    }
    
    public function getPdf($id)
    {
        $edicao = EdicaoJornalImpresso::find($id);

        if( Storage::disk('s3')->exists($edicao->path_s3) ) {

            $file =  Storage::disk('s3')->get($edicao->path_s3);
      
            $headers = [
              'Content-Type' => 'pdf', 
              'Content-Description' => 'File Transfer',
              'Content-Disposition' => "attachment; filename=$edicao->path_s3",
              'filename'=> $edicao->path_s3
           ];
      
            return response($file, 200, $headers);
        }
    }

    public function getImg($id)
    {
        $conteudo = PaginaJornalImpresso::find($id);

        $arquivo = Storage::disk('s3')->get($conteudo->path_pagina_s3);
        $filename = $id.".jpg";

        Storage::disk('impresso-img-original')->put($filename, $arquivo);

        $file = public_path('img/impresso-img/'.$filename);

        $headers = [
            'Content-Type' => 'application/pdf',
        ];

        return response()->download($file, $filename, $headers);


        /*
        if( Storage::disk('s3')->exists($conteudo->path_pagina_s3) ) {

            $file =  Storage::disk('s3')->get($conteudo->path_pagina_s3);
      
            $headers = [
              'Content-Type' => 'image/jpeg', 
              'Content-Description' => 'File Transfer',
              'Content-Disposition' => "attachment; filename=$conteudo->path_pagina_s3",
              'filename'=> $conteudo->path_pagina_s3
           ];
      
            return response($file, 200, $headers);
        }*/

        $headers = [
            'Content-Type'        => 'application/jpg',
            'Content-Disposition' => 'attachment; filename="'. $conteudo->path_pagina_s3 .'"',
        ];
 
        return \Response::make(Storage::disk('s3')->get($conteudo->path_pagina_s3), 200, $headers);
    }

    public function upload(Request $request)
    {
        Session::put('sub-menu','upload');

        $dt_inicial = date("Y-m-d")." 00:00:00";
        $dt_final = date("Y-m-d")." 23:59:59";
        $tipo_data = 'created_at';

        $jornais_pendentes = EdicaoJornalImpresso::where('fl_upload', true)
                                                    ->whereBetween('dt_pub', [$dt_inicial, $dt_final])
                                                    ->orderBy('fl_processado','ASC')
                                                    ->get();

        if($request->isMethod('POST')){

            $dt_inicial = $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00";
            $dt_final = $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59";
            $tipo_data = ($request->tipo_data) ? $request->tipo_data : 'created_at';

            $jornais_pendentes = EdicaoJornalImpresso::where('fl_upload', true)
                                                    ->whereBetween($tipo_data, [$dt_inicial, $dt_final])
                                                    ->orderBy('fl_processado','ASC')
                                                    ->get();
        }

        return view('jornal-impresso/upload', compact('jornais_pendentes','dt_inicial','dt_final','tipo_data'));
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

    public function estatisticas()
    {
        $dados = array();
        
        $dt_inicial = Carbon::now()->subDays(7);
        $dt_final = Carbon::now()->addDays(1);

        $begin = new DateTime($dt_inicial);
        $end = new DateTime($dt_final);
        $interval = DateInterval::createFromDateString('1 day');

        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $dados['label'][] =  $dt->format("d/m/Y");
            $dados['totais'][] = count(PaginaJornalImpresso::whereBetween('created_at', [$dt->format("Y-m-d")." 00:00:00", $dt->format("Y-m-d")." 23:59:59"])->get());
        }

        return response()->json($dados);
    }

    public function limpar()
    {
        Session::forget('impresso_filtro_estado');
        Session::forget('impresso_filtro_cidade');
        Session::forget('impresso_filtro_mapeamento');
        Session::forget('impresso_filtro_nome');

        return redirect('fonte-impresso/listar');
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
        $file_name = $filename.'-'.time().'.'.$extension;

        $partes = explode("_", $filename);
      
        $dt_arquivo = Carbon::createFromFormat('Ymd', $partes[0]);
        $id_jornal = $partes[1];

        $fonte = FonteImpressa::where('id', $id_jornal)->first();        

        if($fonte){

            $obj_s3 = $request->file('file')->storeAs('edicao', $file_name,'s3');

            $dados_edicao = array('path_s3' => $obj_s3,
                                  'dt_coleta' => date("Y-m-d"), 
                                  'id_jornal_online' => $fonte->id, 
                                  'titulo' => $fonte->nome.' '.$dt_arquivo,
                                  'fl_upload' => true,
                                  'link_pdf' => 'https://docmidia-files.s3.us-east-1.amazonaws.com/edicao/'.$file_name,
                                  'dt_pub' => $dt_arquivo);
            
            $edicao = EdicaoJornalImpresso::create($dados_edicao);

            return response()->json(['success'=>$file_name, 'msg' => 'Arquivo inserido com sucesso.']);

        }else{

            return response()->json('Fonte não existe no sistema', 401);
        }
        
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

    public function destacaConteudo($id_noticia, $id_monitoramento)
    {
        $sql = "SELECT ts_headline('portuguese', texto_extraido , to_tsquery('portuguese', t3.expressao), 'HighlightAll=true, StartSel=<mark>, StopSel=</mark>') as texto, t3.expressao 
                        FROM pagina_edicao_jornal_online t1
                        JOIN noticia_cliente t2 ON t2.noticia_id = t1.id 
                        JOIN monitoramento t3 ON t3.id = t2.monitoramento_id 
                        WHERE t1.id = $id_noticia
                        AND t3.id = ".$id_monitoramento;
    
        $dados = DB::select($sql)[0];

        return response()->json($dados); 
    }

    public function remover($id)
    {
        $edicao = EdicaoJornalImpresso::find($id);

        if($edicao->delete())
            Flash::success('<i class="fa fa-check"></i> Edição excluída com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('jornal-impresso/uploads')->withInput();
    }

    public function loadEmissoras()
    {
       
            $fonte = FonteImpressa::select('id', 'nome', 'nm_cidade as cidade', 'sg_estado as uf');
            $fonte->leftJoin('cidade', 'cidade.cd_cidade', '=', 'jornal_online.cd_cidade');
            $fonte->leftJoin('estado', 'estado.cd_estado', '=', 'jornal_online.cd_estado');
            $emissoras = $fonte->orderBy('sg_estado')->orderBy('nm_cidade')->orderBy('nome', 'asc')->get();

        return response()->json($emissoras);
    }
}