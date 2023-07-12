<?php

namespace App\Http\Controllers;

use DB;
use App\Utils;
use App\Models\Tag;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TagController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','tags');
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','index');
        $tags = Tag::orderBy('nome')->get();

        return view('tags/index', compact('tags'));
    }

    public function cadastrar()
    {
        return view('tags/form');
    }

    public function store(Request $request)
    {
        try {
            Tag::create($request->all());
            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {
            
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('tags')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('tags/cadastrar')->withInput();
        }
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);
        if($tag->delete())
            Flash::success('<i class="fa fa-check"></i> Tag <strong>'.$tag->nome.'</strong> excluída com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('tags')->withInput();
    }
}