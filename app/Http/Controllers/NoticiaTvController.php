<?php

namespace App\Http\Controllers;

use DB;
use File;
use Storage;
use App\Utils;
use Carbon\Carbon;
use App\Models\Decupagem;
use App\Models\Area;
use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\EmissoraWeb;
use App\Models\Estado;
use App\Models\NoticiaTv;
use App\Models\VideoEmissoraWeb;
use App\Models\ProgramaEmissoraWeb;
use App\Models\NoticiaCliente;
use App\Models\Tag;
use PhpOffice\PhpWord\IOFactory;
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

        $carbon = new Carbon();
        $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d "."00:00:00");
        $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d "."23:59:59");
        $termo = $request->termo;
        $noticias = array();
        $clientes = Cliente::all();
        $emissoras = EmissoraWeb::orderBy('nome_emissora')->get();

        if($request->isMethod('GET')){

            /*
            $storage = Storage::disk('s3')->allFiles();

            $arquivo = Storage::disk('s3')->url('app/files/streams/379798884_20241104_092035.mp4');
          
            $arquivo = Storage::disk('s3')->get('app/files/streams/379798884_20241104_092035.mp4');

            dd($arquivo);
            */

            $tv = NoticiaTv::query();
            $noticias = $tv->whereBetween('dt_noticia', [$dt_inicial, $dt_final])->paginate(10);
        }

        /*
        if($request->isMethod('GET')){
            $noticias = NoticiaTv::select('noticia_tv.*','noticia_cliente.cliente_id','noticia_cliente.sentimento')
                ->leftJoin('noticia_cliente', function($join){
                $join->on('noticia_cliente.noticia_id', '=', 'noticia_tv.id');
                $join->on('noticia_cliente.tipo_id','=', DB::raw(3));
                $join->whereNull('noticia_cliente.deleted_at');
            })
            ->where('dt_noticia', $this->data_atual)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
        }

        if($request->isMethod('POST')){

            $noticia = NoticiaTv::query();
            $noticia->select('noticia_tv.*','noticia_cliente.cliente_id','noticia_cliente.sentimento');
            $noticia->leftJoin('noticia_cliente', function($join){
                $join->on('noticia_cliente.noticia_id', '=', 'noticia_tv.id');
                $join->on('tipo_id','=', DB::raw(3));
                $join->whereNull('noticia_cliente.deleted_at');
            }); 

            $noticia->when($termo, function ($q) use ($termo) {
                return $q->where('sinopse', 'ILIKE', '%'.trim($termo).'%');
            });

            $noticia->when($dt_inicial, function ($q) use ($dt_inicial, $dt_final) {
                return $q->whereBetween('dt_noticia', [$dt_inicial, $dt_final]);
            });

            $noticias = $noticia->orderBy('created_at', 'DESC')->paginate(10);

        }*/

        return view('noticia-tv/index', compact('noticias','dt_inicial','dt_final','termo','clientes','emissoras'));
    }

    public function dashboard()
    {
        Session::put('sub-menu','tv-dashboard');

        $dt_inicial = Carbon::now()->subDays(7);
        $dt_final = date('Y-m-d');

        $total_emissoras = EmissoraWeb::count();
        $total_programas = ProgramaEmissoraWeb::count();
        $total_videos_tv = count(VideoEmissoraWeb::whereBetween('created_at', [$dt_final." 00:00:00", $dt_final." 23:59:59"])->get());
        $total_noticias_tv = count(NoticiaTv::whereBetween('dt_noticia', [$dt_final." 00:00:00", $dt_final." 23:59:59"])->get());

        return view('noticia-tv/dashboard', compact('dt_inicial','dt_final','total_emissoras','total_programas','total_videos_tv','total_noticias_tv'));
    }

    public function cadastrar()
    {
        Session::put('sub-menu','tv-cadastrar');

        $dados = new NoticiaTv();
        $cidades = [];
        $areas = [];
        $cliente = null;

        $estados = Estado::orderBy('nm_estado')->get();
        $tags = Tag::orderBy('nome')->get();

        return view('noticia-tv/form', compact('dados', 'estados', 'cidades', 'areas','tags','cliente'));
    }

    public function editar(int $id, int $cliente = null)
    {
        $dados = NoticiaTv::find($id);
        $cliente = NoticiaCliente::where('noticia_id', $id)->where('cliente_id', $cliente)->first();
        
        $estados = Estado::orderBy('nm_estado')->get();
        $cidades = Cidade::where(['cd_estado' => $dados->cd_estado])->orderBy('nm_cidade')->get();
        $areas = Area::select('area.id', 'area.descricao')
            ->join('area_cliente', 'area_cliente.area_id', '=', 'area.id')
            ->where(['cliente_id' => $dados->cliente_id,])
            ->where(['ativo' => true])
            ->orderBy('area.descricao')
            ->get();

        $tags = Tag::orderBy('nome')->get();

        return view('noticia-tv/form', compact('dados', 'cliente', 'estados', 'cidades', 'areas','tags'));
    }

    public function inserir(Request $request)
    {
        $carbon = new Carbon();
        try {
           
            $emissora = Emissora::find($request->emissora);
           
            $dados = array('dt_noticia' => ($request->data) ? $carbon->createFromFormat('d/m/Y', $request->data)->format('Y-m-d') : date("Y-m-d"),
                           'duracao' => $request->duracao,
                           'horario' => $request->horario,
                           'emissora_id' => $request->emissora,
                           'programa_id' => $request->programa,
                           'arquivo' => $request->arquivo,
                           'sinopse' => $request->sinopse,
                           'cd_estado' => $emissora->cd_estado,
                           'cd_cidade' => $emissora->cd_cidade,
                           'link' => $request->link
                        ); 
           
            if($noticia = NoticiaTv::create($dados))
            {
                if($request->cd_cliente){

                    $inserir = array('tipo_id' => 4,
                                        'noticia_id' => $noticia->id,
                                        'cliente_id' => $request->cd_cliente,
                                        'area' => $request->cd_area,
                                        'sentimento' => $request->cd_sentimento);
                            
                    NoticiaCliente::create($inserir);
                }

                $tags = collect($request->tags)->mapWithKeys(function($tag){
                    return [$tag => ['tipo_id' => 4]];
                })->toArray();

                $noticia->tags()->sync($tags);

                $clientes = json_decode($request->clientes[0]);
                if($clientes){

                    for ($i=0; $i < count($clientes); $i++) { 
                        
                        $dados = array('tipo_id' => 4,
                                'noticia_id' => $noticia->id,
                                'cliente_id' => $clientes[$i]->id_cliente,
                                'area' => $clientes[$i]->id_area,
                                'sentimento' => $clientes[$i]->id_sentimento);
                    
                        NoticiaCliente::create($dados);
                    }
                }
            }

            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados inseridos com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {

            $retorno = array('flag' => false,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        switch ($request->btn_enviar) {
            case 'salvar':
                if ($retorno['flag']) {
                    Flash::success($retorno['msg']);
                    return redirect('tv/noticias')->withInput();
                } else {
                    Flash::error($retorno['msg']);
                    return redirect('tv/noticias/cadastrar')->withInput();
                }
                break;

            case 'salvar_e_copiar':

                $dados = $noticia;
                $estados = Estado::orderBy('nm_estado')->get();
                $cidades = Cidade::where(['cd_estado' => $dados->cd_estado])->orderBy('nm_cidade')->get();
                $areas = Area::select('area.id', 'area.descricao')
                    ->join('area_cliente', 'area_cliente.area_id', '=', 'area.id')
                    ->where(['cliente_id' => $dados->cliente_id,])
                    ->where(['ativo' => true])
                    ->orderBy('area.descricao')
                    ->get();

                $tags = Tag::orderBy('nome')->get();
                
                return view('noticia-radio/form', compact('dados', 'estados', 'cidades', 'areas','tags'));
                break;
        }
    }

    public function atualizar(Request $request, int $id)
    {
        $carbon = new Carbon();

        try {

            $noticia = NoticiaTv::find($id);

            if(empty($noticia)) {
                throw new \Exception('Notícia não encontrada');
            }

            $emissora = Emissora::find($request->emissora);
            
            $dados = array('dt_noticia' => ($request->data) ? $carbon->createFromFormat('d/m/Y', $request->data)->format('Y-m-d') : date("Y-m-d"),
                            'duracao' => $request->duracao,
                            'horario' => $request->horario,
                            'emissora_id' => $request->emissora,
                            'programa_id' => $request->programa,
                            'arquivo' => ($request->arquivo) ? $request->arquivo : $noticia->arquivo,
                            'sinopse' => $request->sinopse,
                            'cd_estado' => $emissora->cd_estado,
                            'cd_cidade' => $emissora->cd_cidade,
                            'link' => $request->link
            ); 

            if($noticia->update($dados)){

                if($request->cd_cliente){

                    $chave = array('tipo_id' => 3,
                                    'noticia_id' => $noticia->id,
                                    'cliente_id' => $request->cd_cliente);

                    $atualizar = array('area' => $request->cd_area,
                                       'sentimento' => $request->cd_sentimento);
                            
                    NoticiaCliente::updateOrCreate($chave, $atualizar);
                }

                $tags = collect($request->tags)->mapWithKeys(function($tag){
                    return [$tag => ['tipo_id' => 3]];
                })->toArray();

                $noticia->tags()->sync($tags);

                $clientes = json_decode($request->clientes[0]);
                if($clientes){

                    for ($i=0; $i < count($clientes); $i++) { 
                        
                        $dados = array('tipo_id' => 3,
                                'noticia_id' => $noticia->id,
                                'cliente_id' => $clientes[$i]->id_cliente,
                                'area_id' => $clientes[$i]->id_area,
                                'sentimento' => $clientes[$i]->id_sentimento);
                    
                        NoticiaCliente::create($dados);
                    }
                }
            }

            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados atualizados com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {

            $retorno = array('flag' => false,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('tv/noticias')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('tv/noticias/'.$id.'/editar')->withInput();
        }
    }

    public function estatisticas()
    {
        Session::put('sub-menu','tv-estatisticas');

        $data_final = date("Y-m-d");
        $data_inicial = Carbon::now()->subDays(7)->format('Y-m-d');

        $total_noticia_tv = NoticiaTv::whereBetween('created_at', [$this->data_atual.' 00:00:00', $this->data_atual.' 23:59:59'])->count();
        $ultima_atualizacao = NoticiaTv::max('created_at');

        $total_emissora_tv = Emissora::where('tipo_id', 2)->count();
        $ultima_atualizacao_tv = Emissora::where('tipo_id', 2)->max('created_at');

        $noticias = NoticiaTv::paginate(10);
        return view('noticia-tv/dashboard', compact('noticias','total_noticia_tv', 'total_emissora_tv', 'ultima_atualizacao','ultima_atualizacao_tv','data_final','data_inicial'));
    }

    public function decupagem()
    {
        Session::put('sub-menu','tv-decupagem');
        $arquivos = Decupagem::all();

        return view('noticia-tv/decupagem', compact('arquivos'));
    }

    public function decupar()
    {
        Session::put('sub-menu','tv-decupagem');

        return view('noticia-tv/decupar');
    }

    public function listarArquivos()
    {
        $directory = 'noticias-tv/pendentes';
        $files_info = [];
        $file_ext = array('doc','docx');

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

    public function processar(Request $request)
    {
        $carbon = new Carbon();
        $arquivo = $request->arquivo;
        $data = $request->data;
        $cd_emissora = $request->emissora; 
        $programa = $request->programa;

        $directory = 'noticias-tv/decupagem/';

        $data_decupagem = array('arquivo' => $arquivo);
        $decupagem = Decupagem::create($data_decupagem);

        $phpWord = IOFactory::createReader('Word2007')->load(public_path().'/'.$directory.$arquivo);

        foreach($phpWord->getSections() as $section) {
            foreach($section->getElements() as $element) {

                switch (get_class($element)) {
                    case 'PhpOffice\PhpWord\Element\Text' :
                        //$textos[] = $element->getText();
                        break;
                    case 'PhpOffice\PhpWord\Element\TextRun':
                        $textRunElements = $element->getElements();
                        foreach ($textRunElements as $textRunElement) {
                            if(strlen($textRunElement->getText()) > 5)
                                $textos[] = trim($textRunElement->getText());
                        }
                        break;
                    case 'PhpOffice\PhpWord\Element\TextBreak':
                        //$textos[] = " ";
                        break;
                    default:
                        throw new Exception('Something went wrong...');
                }
            }
        }

        for ($i=0; $i < count($textos); $i++) { 

            $emissora = Emissora::find($cd_emissora);
            
            $dados = array('dt_noticia' => ($data) ? $carbon->createFromFormat('d/m/Y', $data)->format('Y-m-d') : date("Y-m-d"),
                'emissora_id' => $cd_emissora,
                'programa_id' => $programa,
                'sinopse' => $textos[$i],
                'cd_estado' => $emissora->cd_estado,
                'cd_cidade' => $emissora->cd_cidade,
                'decupagem_id' => $decupagem->id
            ); 

            $noticia = NoticiaTv::create($dados);

        }

        return view('noticia-tv/salvar', compact('textos'));
    }

    
    public function salvarDecugem(Request $request)
    {
        dd("teste");
    }

    public function uploadWord(Request $request)
    {
        $arquivo = $request->file('file');
        $fileInfo = $arquivo->getClientOriginalName();
        $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
        $extension = pathinfo($fileInfo, PATHINFO_EXTENSION);
        $file_name = date('Y-m-d-H-i-s').'.'.$extension;

        //$audio = new \wapmorgan\Mp3Info\Mp3Info($arquivo, true);
        //$duracao = gmdate("H:i:s", $audio->duration);

        $path = 'noticias-tv'.DIRECTORY_SEPARATOR.'decupagem'.DIRECTORY_SEPARATOR;
        $arquivo->move(public_path($path), $file_name);

        $dados = array('arquivo' => $file_name);

        return response()->json($dados);
    }

    public function upload(Request $request)
    {
        $arquivo = $request->file('file');
        $fileInfo = $arquivo->getClientOriginalName();
        $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
        $extension = pathinfo($fileInfo, PATHINFO_EXTENSION);
        $file_name = date('Y-m-d-H-i-s').'.'.$extension;

        //$audio = new \wapmorgan\Mp3Info\Mp3Info($arquivo, true);
        //$duracao = gmdate("H:i:s", $audio->duration);

        $path = 'noticias-tv'.DIRECTORY_SEPARATOR.date('Y-m-d').DIRECTORY_SEPARATOR;
        $arquivo->move(public_path($path), $file_name);

        $dados = array('arquivo' => $file_name);

        return response()->json($dados);
    }

    public function getEstatisticas()
    {
        $dados = array();
        $totais = (new NoticiaTv())->getTotais();

        for ($i=0; $i < count($totais); $i++) { 
            $dados['label'][] = date('d/m/Y', strtotime($totais[$i]->dt_noticia));
            $dados['totais'][] = $totais[$i]->total;
        }

        return response()->json($dados);
    }

    public function remover(int $id, $cliente = null)
    {
        $noticia = NoticiaTv::find($id);

        if($cliente){

            $noticia_cliente = NoticiaCliente::where('noticia_id', $id)->where('cliente_id', $cliente)->first();

            if($noticia_cliente->delete()){

                if(count($noticia->clientes) == 0){

                    if($noticia->delete())
                        Flash::success('<i class="fa fa-check"></i> Notícia <strong>'.$noticia->titulo.'</strong> excluída com sucesso');
                }
            }
            else
                Flash::error("Erro ao excluir o registro");

        }else{

            if($noticia->delete())
                Flash::success('<i class="fa fa-check"></i> Notícia <strong>'.$noticia->titulo.'</strong> excluída com sucesso');
            else
                Flash::error("Erro ao excluir o registro");
        }

        return redirect('tv/noticias')->withInput();
    }
}