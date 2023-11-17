<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use App\Noticia;
use App\Models\JornalWeb;
use App\Models\Estado;
use App\Models\Cidade;
use App\Models\FonteWeb;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\FontWebRequest;
use Illuminate\Support\Facades\Session;

class FonteWebController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
    }

    public function listar(Request $request)
    {
        Session::put('sub-menu','fontes');

        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
            
        //$fontes = FonteWeb::with('estado')->orderBy('nome')->where('misc_data','=', 'mapeado')->paginate(10);

        $ids = JornalWeb::select('id_fonte')->distinct()->where('origem', 'MONITORAMENTO')->pluck('id_fonte')->toArray();

        $fontes = FonteWeb::where('misc_data','=', 'mapeado')->whereIn('id',$ids)->paginate(10);

        if($request->isMethod('POST')){

            $carbon = new Carbon();
            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");

            $dados = FonteWeb::whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->orderBy('id_fonte')->paginate(10);

        }

        return view('fonte-web/listar',compact('fontes','cidades','estados'));
    }

    public function coletas($id)
    {
        $fonte = FonteWeb::where('id', $id)->first();

        $noticias = JornalWeb::where('id_fonte', $id)->orderBy('dt_clipagem',"DESC")->paginate(30);
        $noticias_knewin = (new Noticia)->getNoticias($fonte->id_knewin);

        return view('fonte-web/coletas', compact('noticias','noticias_knewin'));
    }

    public function estatisticas($id)
    {
        $fonte = FonteWeb::find($id)->limit(10);

        return view('fonte-web/estatisticas', compact('fonte'));
    }

    public function relatorios()
    {
        Session::put('sub-menu','relatorios-web');

        return view('fonte-web/relatorios');
    }

    public function create(Request $request)
    {
        Session::put('sub-menu','fonte-web');

        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();

        return view('fonte-web/novo', compact('estados','cidades'));
    }

    public function edit(FonteWeb $fonte, $id)
    {
        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $fonte = FonteWeb::find($id);

        return view('fonte-web/editar', compact('fonte','estados','cidades'));
    }

    public function store(FontWebRequest $request)
    {
        try {
            FonteWeb::create($request->all());
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
            return redirect('fonte-web/listar')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('fonte-web/create')->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $fonte = FonteWeb::find($id);
    
        try{
        
            $fonte->update($request->all());
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados atualizados com sucesso');
        } catch (\Illuminate\Database\QueryException $e) {
            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));
        } catch (Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('fonte-web/listar')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect()->route('font-web.edit', $fonte->id)->withInput();
        }
    }
}