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
    private $carbon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','tv');
        $this->carbon = new Carbon();
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','tv-noticias');

        $fontes = ProgramaEmissoraWeb::orderBy('nome_programa')->get();
        $clientes = Cliente::orderBy('fl_ativo')->orderBy('nome')->get();

        $tipo_data = $request->tipo_data;
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $cliente_selecionado = ($request->cliente) ? $request->cliente : null;
        $fonte = ($request->fontes) ? $request->fontes : null;
        $termo = ($request->termo) ? $request->termo : null;

        if($request->fontes or Session::get('tv_filtro_fonte')){
            if($request->fontes){
                $fonte = $request->fontes;
            }elseif(Session::get('tv_filtro_fonte')){
                $fonte = Session::get('tv_filtro_fonte');
            }else{
                $fonte = null;
            }
        }else{
            $fonte = null;
            Session::forget('tv_filtro_fonte');
        }

        if($request->monitoramento or Session::get('tv_monitoramento')){
            if($request->monitoramento){
                $monitoramento = $request->monitoramento;
            }elseif(Session::get('tv_monitoramento')){
                $monitoramento = Session::get('tv_monitoramento');
            }else{
                $monitoramento = null;
            }
        }else{
            $monitoramento = null;
            Session::forget('tv_monitoramento');
        }

        if($request->isMethod('POST')){

            if($request->monitoramento){
                Session::put('tv_monitoramento', $monitoramento);
            }else{
                Session::forget('tv_monitoramento');
            }

            if($request->fontes){
                Session::put('tv_filtro_fonte', $fonte);
            }else{
                Session::forget('tv_filtro_fonte');
                $fonte = null;
            }
        }
       
        $dados = DB::table('noticia_cliente')
                    ->select('video_path',
                            'programa_emissora_web.id AS id_fonte',
                            'programa_emissora_web.nome_programa AS nome_programa',
                            'noticia_cliente.noticia_id',
                            'noticia_cliente.monitoramento_id',
                            'transcricao',
                            'nome_emissora',
                            'programa_emissora_web.tipo_programa',
                            'horario_start_gravacao',
                            'horario_end_gravacao',
                            'misc_data',
                            'expressao',
                            'nm_estado',
                            'nm_cidade',
                            'clientes.nome AS nome_cliente')
                    ->join('clientes', 'clientes.id', '=', 'noticia_cliente.cliente_id')
                    ->join('videos_programa_emissora_web', function ($join) {
                        $join->on('videos_programa_emissora_web.id', '=', 'noticia_cliente.noticia_id')->where('tipo_id',4);
                    })
                    ->join('programa_emissora_web','videos_programa_emissora_web.id_programa_emissora_web','=','programa_emissora_web.id')
                    ->join('emissora_web','emissora_web.id','=','programa_emissora_web.id_emissora')
                    ->join('monitoramento', function($join) use($monitoramento){
                        $join->on('monitoramento.id','=','noticia_cliente.monitoramento_id')
                        ->when($monitoramento, function ($q) use ($monitoramento) {
                            return $q->where('monitoramento.id', $monitoramento);
                        });
                    })
                    ->leftJoin('estado','estado.cd_estado','=','programa_emissora_web.cd_estado')
                    ->leftJoin('cidade','cidade.cd_cidade','=','programa_emissora_web.cd_cidade')
                    ->when($termo, function ($q) use ($termo) {
                        return $q->where('transcricao', 'ILIKE', '%'.trim($termo).'%');
                    })
                    ->when($cliente_selecionado, function ($q) use ($cliente_selecionado) {
                        return $q->where('noticia_cliente.cliente_id', $cliente_selecionado);
                    })
                    ->when($fonte, function ($q) use ($fonte) {
                        return $q->whereIn('programa_emissora_web.id', $fonte);
                    })
                    ->when($dt_inicial, function ($q) use ($dt_inicial, $dt_final) {
                        return $q->whereBetween('videos_programa_emissora_web.horario_start_gravacao', [$dt_inicial." 00:00:00", $dt_final." 23:59:59"]);
                    })
                    ->when($monitoramento, function ($q) use ($monitoramento) {
                        return $q->where('noticia_cliente.monitoramento_id', $monitoramento);
                    })
                    ->orderBy('horario_start_gravacao')
                    ->paginate(10);

        return view('noticia-tv/index', compact('clientes','fontes','dados','tipo_data','dt_inicial','dt_final','cliente_selecionado','fonte','termo','monitoramento'));
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

    public function getBasePath()
    {
        return storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;
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
           
            $emissora = EmissoraWeb::find($request->emissora);
           
            $dados = array('dt_noticia' => ($request->data) ? $carbon->createFromFormat('d/m/Y', $request->data)->format('Y-m-d') : date("Y-m-d"),
                           'duracao' => $request->duracao,
                           'horario' => $request->horario,
                           'emissora_id' => $request->emissora,
                           'programa_id' => $request->programa,
                           'arquivo' => $request->arquivo,
                           'sinopse' => $request->sinopse,
                           'cd_estado' => $request->cd_estado,
                           'cd_cidade' => $request->cd_cidade,
                           'link' => $request->link
                        ); 
           
            if($noticia = NoticiaTv::create($dados))
            {
               
                $tags = collect($request->tags)->mapWithKeys(function($tag){
                    return [$tag => ['tipo_id' => 4]];
                })->toArray();

                $noticia->tags()->sync($tags);

                $clientes = json_decode($request->clientes[0]);

                if($clientes){

                    for ($i=0; $i < count($clientes); $i++) { 
                        
                        $dados = array('tipo_id' => 4,
                                'noticia_id' => $noticia->id,
                                'cliente_id' => (int) $clientes[$i]->id_cliente,
                                'area' => (int) $clientes[$i]->id_area,
                                'sentimento' => (int) $clientes[$i]->id_sentimento);

                        $noticia_cliente = NoticiaCliente::create($dados);

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

            $emissora = EmissoraWeb::find($request->emissora);
            
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

    public function destacaConteudo($id_noticia, $id_monitoramento)
    {
        $sql = "SELECT ts_headline('simple', conteudo, to_tsquery('simple', t3.expressao), 'HighlightAll=true, StartSel=<mark>, StopSel=</mark>') as texto, t3.expressao 
                        FROM conteudo_noticia_web t1
                        JOIN noticia_cliente t2 ON t2.noticia_id = t1.id_noticia_web 
                        JOIN monitoramento t3 ON t3.id = t2.monitoramento_id 
                        WHERE t1.id_noticia_web = $id_noticia
                        AND t3.id = ".$id_monitoramento;
    
        $dados = DB::select($sql)[0];

        return response()->json($dados); 
    }
}