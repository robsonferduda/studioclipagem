<?php

namespace App\Http\Controllers;

use App\Models\Area;
use DB;
use Auth;
use App\Models\NoticiaCliente;
use App\Models\ClienteArea;
use App\Models\JornalWeb;
use App\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','areas');
    }

    public function index()
    {
        $areas = Area::orderBy('descricao')->get();
        return view('area/index',compact('areas'));
    }

    public function cadastrar()
    {
        return view('area/novo');
    }

    public function editar(int $id)
    {
        $area = Area::find($id);
        return view('area/editar', compact('area'));
    }

    public function inserir(Request $request)
    {
        try {
            Area::create(['descricao' => $request->descricao]);

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
            return redirect('areas')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('areas/cadastrar')->withInput();
        }
    }

    public function atualizar(Request $request, int $id)
    {
        $area = Area::find($id);

        try {
            $area->update(['descricao' => $request->descricao]);

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
            return redirect('areas')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect()->route('areas.editar', $area->id)->withInput();
        }
    }

    public function remover(int $id)
    {
        $area = Area::find($id);

        if($area->delete())
            Flash::success('<i class="fa fa-check"></i> Área <strong>'.$area->description.'</strong> excluída com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('areas')->withInput();
    }

    public function cadastrarAreaCliente(Request $request)
    {
        $chave = array('cliente_id' => $request->cliente, 'area_id' => $request->area);

        $dados = array('expressao' => $request->expressao,
                        'ativo' => $request->situacao);

        ClienteArea::updateOrCreate($chave, $dados);
    }

    public function executarWeb()
    {
        $sql = "SELECT id, tipo, area, cliente_id
                FROM(SELECT t2.id, t1.area, 'web' as tipo, t3.expressao, t1.cliente_id, t4.conteudo_tsv as document 
                    FROM noticia_cliente t1
                    JOIN noticias_web t2 ON t2.id = t1.noticia_id AND t2.created_at > '2025-05-10'
                    JOIN area_cliente t3 ON t3.cliente_id = t1.cliente_id AND t3.expressao NOTNULL
                    JOIN conteudo_noticia_web t4 ON t4.id_noticia_web = t1.noticia_id) as p_search
                WHERE p_search.document @@ plainto_tsquery(expressao)";

        $dados = DB::select($sql);

        $total_associado = $this->associar($dados);
    }

    public function executarImpresso()
    {
        $sql = "SELECT id, tipo, area, cliente_id
                FROM(SELECT t2.id, t1.area, 'impresso' as tipo, t3.expressao, t3.cliente_id, to_tsvector(t2.sinopse) as document 
                    FROM noticia_cliente t1
                    JOIN noticia_impresso t2 ON t2.id = t1.noticia_id  
                    JOIN area_cliente t3 ON t3.cliente_id = t1.cliente_id AND t3.expressao NOTNULL) as p_search
                WHERE p_search.document @@ plainto_tsquery(expressao)";

        $dados = DB::select($sql);

        $total_associado = $this->associar($dados);
    }

    public function executarRadio()
    {

    }

    public function executarTV()
    {

    }

    public function associar($dados)
    {
        $tipo = null;

        foreach ($dados as $key => $match) {

            switch ($match->tipo) {
                case 'impresso':
                    $tipo = 1;
                    break;
                
                case 'web':
                    $tipo = 2;
                    break;
            }

            $vinculo = NoticiaCliente::where('cliente_id', $match->cliente_id)
                                        ->where('tipo_id', $tipo)
                                        ->where('noticia_id', $match->id)
                                        ->first();
            $vinculo->area = $match->area;
            $vinculo->save();
        }
    }
}