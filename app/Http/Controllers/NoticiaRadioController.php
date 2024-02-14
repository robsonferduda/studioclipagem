<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Area;
use App\Models\Cidade;
use App\Utils;
use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Emissora;
use App\Models\NoticiaCliente;
use App\Models\Estado;
use App\Models\NoticiaRadio;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class NoticiaRadioController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','radio');
    }

    public function getBasePath()
    {
        return public_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','radios');

        $carbon = new Carbon();
        $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $termo = $request->termo;

        if($request->isMethod('GET')){
            $noticias = NoticiaRadio::select('noticia_radio.*','noticia_cliente.cliente_id','noticia_cliente.sentimento')
                ->leftJoin('noticia_cliente', function($join){
                $join->on('noticia_cliente.noticia_id', '=', 'noticia_radio.id');
                $join->on('noticia_cliente.tipo_id','=', DB::raw(3));
                $join->whereNull('noticia_cliente.deleted_at');
            })->where('dt_noticia', $this->data_atual)->paginate(10);
        }

        if($request->isMethod('POST')){

            $noticia = NoticiaRadio::query();
            $noticia->select('noticia_radio.*','noticia_cliente.cliente_id','noticia_cliente.sentimento');
            $noticia->leftJoin('noticia_cliente', function($join){
                $join->on('noticia_cliente.noticia_id', '=', 'noticia_radio.id');
                $join->on('tipo_id','=', DB::raw(3));
                $join->whereNull('noticia_cliente.deleted_at');
            }); 

            $noticia->when($termo, function ($q) use ($termo) {
                return $q->where('sinopse', 'ILIKE', '%'.trim($termo).'%');
            });

            $noticia->when($dt_inicial, function ($q) use ($dt_inicial, $dt_final) {
                return $q->whereBetween('dt_noticia', [$dt_inicial, $dt_final]);
            });

            $noticias = $noticia->paginate(10);

        }

        return view('noticia-radio/index', compact('noticias','dt_inicial','dt_final','termo'));
    }

    public function estatisticas()
    {
        Session::put('sub-menu','radio-estatisticas');

        $data_final = date("Y-m-d");
        $data_inicial = Carbon::now()->subDays(7)->format('Y-m-d');

        $total_noticia_radio = NoticiaRadio::whereBetween('created_at', [$data_inicial.' 00:00:00', $data_final.' 23:59:59'])->count();
        $ultima_atualizacao = NoticiaRadio::max('created_at');

        $total_emissora_radio = Emissora::where('tipo_id', 1)->count();
        $ultima_atualizacao_radio = Emissora::where('tipo_id', 1)->max('created_at');

        $noticias = NoticiaRadio::paginate(10);
        return view('noticia-radio/estatisticas', compact('noticias','total_noticia_radio', 'total_emissora_radio', 'ultima_atualizacao','ultima_atualizacao_radio','data_final','data_inicial'));
    }

    public function cadastrar()
    {
        Session::put('sub-menu','radio-cadastrar');        

        $dados = new NoticiaRadio();
        $cidades = [];
        $areas = [];

        $estados = Estado::orderBy('nm_estado')->get();
        $tags = Tag::orderBy('nome')->get();
        $emissoras = Emissora::orderBy('ds_emissora')->get();

        return view('noticia-radio/form', compact('dados', 'estados', 'cidades', 'areas','tags','emissoras'));
    }

    public function editar(int $id)
    {
        $dados = NoticiaRadio::find($id);
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
           
            if($noticia = NoticiaRadio::create($dados))
            {
                if($request->cd_cliente){

                    $inserir = array('tipo_id' => 3,
                                        'noticia_id' => $noticia->id,
                                        'cliente_id' => $request->cd_cliente,
                                        'area_id' => $request->cd_area,
                                        'sentimento' => $request->cd_sentimento);
                            
                    NoticiaCliente::create($inserir);
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
                             'msg' => '<i class="fa fa-check"></i> Dados inseridos com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            dd($e);

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
                    return redirect('radios')->withInput();
                } else {
                    Flash::error($retorno['msg']);
                    return redirect('radio/noticias/cadastrar')->withInput();
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
            $noticia = NoticiaRadio::find($id);

            if(empty($noticia)) {
                throw new \Exception('Notícia não encontrada');
            }

            $emissora = Emissora::find($request->emissora);
            
            $dados = array('dt_noticia' => ($request->data) ? $carbon->createFromFormat('d/m/Y', $request->data)->format('Y-m-d') : date("Y-m-d"),
                            'duracao' => $request->duracao,
                            'emissora_id' => $request->emissora,
                            'programa_id' => $request->programa,
                            'arquivo' => ($request->arquivo) ? $request->arquivo : $noticia->arquivo,
                            'sinopse' => $request->sinopse,
                            'cd_estado' => $emissora->cd_estado,
                            'cd_cidade' => $emissora->cd_cidade,
                            'link' => $request->link
            ); 

            if($noticia->update($dados)){

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
            return redirect('radios')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('radio/noticias/'.$id.'/editar')->withInput();
        }
    }

    public function remover(int $id, $cliente = null)
    {
        $noticia = NoticiaRadio::find($id);

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

        return redirect('radios')->withInput();
    }

    public function download(int $id)
    {
        $noticia = NoticiaRadio::find($id);

        $path = $this->getBasePath() . $noticia->arquivo;
        if (file_exists($path)) {
            return response()->download($path);
        }
    }

    public function upload(Request $request)
    {
        $arquivo = $request->file('file');
        $fileInfo = $arquivo->getClientOriginalName();
        $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
        $extension = pathinfo($fileInfo, PATHINFO_EXTENSION);
        $file_name = date('Y-m-d-H-i-s').'.'.$extension;

        $audio = new \wapmorgan\Mp3Info\Mp3Info($arquivo, true);
        $duracao = gmdate("H:i:s", $audio->duration);

        $path = 'noticias-radio'.DIRECTORY_SEPARATOR.date('Y-m-d').DIRECTORY_SEPARATOR;
        $arquivo->move(public_path($path), $file_name);

        $dados = array('arquivo' => $file_name, 'duracao' => $duracao);

        return response()->json($dados);
    }

    public function getEstatisticas()
    {
        $dados = array();
        $data_final = date("Y-m-d")." 23:59:59";
        $data_inicial = Carbon::now()->subDays(7)->format('Y-m-d')." 00:00:00";;

        $totais = (new NoticiaRadio())->getTotais($data_inicial, $data_final);

        for ($i=0; $i < count($totais); $i++) { 
            $dados['label'][] = date('d/m/Y', strtotime($totais[$i]->dt_noticia));
            $dados['totais'][] = $totais[$i]->total;
        }

        return response()->json($dados);
    }
}
