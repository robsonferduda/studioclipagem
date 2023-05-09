<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Cidade;
use App\Utils;
use App\Models\Emissora;
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
    }

    public function getBasePath()
    {
        return storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $noticias = NoticiaRadio::all();
        return view('noticia-radio/index', compact('noticias'));
    }

    public function cadastrar()
    {
        $dados = new NoticiaRadio();
        $cidades = [];
        $areas = [];

        $estados = Estado::orderBy('nm_estado')->get();
        return view('noticia-radio/form', compact('dados', 'estados', 'cidades', 'areas'));
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

        return view('noticia-radio/form', compact('dados', 'estados', 'cidades', 'areas'));
    }

    public function inserir(Request $request)
    {
        try {
            $filename = $this->uploadFiles($request);
            $emissora = Emissora::find($request->emissora);
            NoticiaRadio::create([
                'cliente_id'    => $request->cliente,
                'area_id'       => $request->area,
                'emissora_id'   => $request->emissora,
                'programa_id'   => $request->programa,
                'cd_estado'     => $emissora->cd_estado,
                'cd_cidade'     => $emissora->cd_cidade,
                'titulo'        => $request->titulo,
                'dt_noticia'    => $request->data,
                'arquivo'       => $filename,
                'fl_boletim'    => $request->boletim == 'S'
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
            return redirect('radio/noticias')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('radio/noticias/cadastrar')->withInput();
        }
    }

    public function atualizar(Request $request, int $id)
    {
        try {
            $noticia = NoticiaRadio::find($id);

            if(empty($noticia)) {
                throw new \Exception('Notícia não encontrada');
            }

            $emissora = Emissora::find($request->emissora);
            if($request->remover == "true") {
                $baseUrl = $this->getBasePath();
                @unlink($baseUrl.$noticia->arquivo);
            }
            $filename = $this->uploadFiles($request);

            $noticia->update([
                'cliente_id'    => $request->cliente,
                'area_id'       => $request->area,
                'emissora_id'   => $request->emissora,
                'programa_id'   => $request->programa,
                'cd_estado'     => $emissora->cd_estado,
                'cd_cidade'     => $emissora->cd_cidade,
                'titulo'        => $request->titulo,
                'dt_noticia'    => $request->data,
                'arquivo'       => $filename,
                'fl_boletim'    => $request->boletim == 'S'
            ]);

            $retorno = array('flag' => true,
                             'msg' => "Dados atualizados com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {
            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (\Exception $e) {
            $retorno = array('flag' => false,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('radio/noticias')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('radio/noticias/' . $id . '/editar')->withInput();
        }
    }

    public function remover(int $id)
    {
        $noticia = NoticiaRadio::find($id);
        if($noticia->delete())
            Flash::success('<i class="fa fa-check"></i> Notícia <strong>'.$noticia->titulo.'</strong> excluída com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('radio/noticias')->withInput();
    }

    public function download(int $id)
    {
        $noticia = NoticiaRadio::find($id);

        $baseUrl =
        $path = $this->getBasePath() . $noticia->arquivo;
        if (file_exists($path)) {
            return response()->download($path);
        }
    }

    private function uploadFiles(Request $request)
    {
        if(empty($request->file('arquivo'))) {
            return null;
        }
        $arquivo = $request->file('arquivo');
        $fileInfo = $arquivo->getClientOriginalName();
        $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
        $extension = pathinfo($fileInfo, PATHINFO_EXTENSION);
        $file_name= $filename.'-'.time().'.'.$extension;

        $path = 'noticias-radio'.DIRECTORY_SEPARATOR.$request->cliente.DIRECTORY_SEPARATOR.date('Ym').DIRECTORY_SEPARATOR;
        $arquivo->move($this->getBasePath().$path,$file_name);

        return $path.$file_name;
    }
}
