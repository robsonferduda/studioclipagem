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
use Yajra\DataTables\DataTables;
use App\Http\Requests\FontWebRequest;
use Illuminate\Support\Facades\Session;

class FonteWebController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','jornal-web');
    }

    public function listar(Request $request)
    {
        Session::put('sub-menu','fontes');

        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
            
        $fontes = FonteWeb::where('misc_data','=', 'mapeado')->get();
        $situacoes = (new FonteWeb())->getSituacoes();

        if($request->ajax()) {

            $situacao = ($request->situacao) ? $request->situacao : "";
            $nome = ($request->nome) ? $request->nome : "";
            $estado = ($request->estado) ? $request->estado : "";
            $cidade = ($request->cidade) ? $request->cidade : "";
    
                $fonte = FonteWeb::query();
    
                $fonte->when($situacao, function ($q) use ($situacao) {
                    return $q->where('id_situacao', $situacao);
                });

                $fonte->when($estado, function ($q) use ($estado) {
                    return $q->where('cd_estado', $estado);
                });

                $fonte->when($cidade, function ($q) use ($cidade) {
                    return $q->where('cd_cidade', $cidade);
                });
    
                $fonte->when($nome, function ($q) use ($nome) {
                    return $q->where('nome', 'ILIKE', '%'.trim($nome).'%');
                });
    
                $fontes = $fonte->get();

            return DataTables::of($fontes)
                ->addColumn('id', function ($fonte) {
                    return $fonte->id;
                })     
                ->addColumn('estado', function ($fonte) {
                    return ($fonte->estado) ? $fonte->estado->nm_estado : "Não informado";
                }) 
                ->addColumn('regional', function ($fonte) {
                    return ($fonte->cidade and $fonte->cidade->regional) ? $fonte->cidade->regional->descricao : "Não informado";
                })
                ->addColumn('cidade', function ($fonte) {
                    return ($fonte->cidade) ? $fonte->cidade->nm_cidade : "Não informado";
                })
                ->addColumn('nome', function ($fonte) {
                    return $fonte->nome;
                })  
                ->addColumn('url', function ($fonte) {
                    return $fonte->url;
                })    
                ->addColumn('acoes', function ($fonte) {
                    return '<div class="text-center">
                                <a title="Coletas" href="../fonte-web/coletas/'.$fonte->id.'" class="btn btn-info btn-link btn-icon"> <i class="fa fa-area-chart fa-2x "></i></a>
                                <a title="Estatísticas" href="../fonte-web/estatisticas/'.$fonte->id.'" class="btn btn-warning btn-link btn-icon"> <i class="fa fa-bar-chart fa-2x"></i></a>
                                <a title="Editar" href="" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                <a title="Excluir" href="" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-times fa-2x"></i></a>
                            </div>';
                })   
                ->rawColumns(['id','acoes'])         
                ->make(true);

        }

        return view('fonte-web/listar',compact('fontes','cidades','estados','situacoes'));
    }

    public function importar()
    {
        $fontes = FonteWeb::where('id_situacao', 1)->get();
        
        foreach ($fontes as $key => $fonte) {

            $noticia = (new Noticia())->getNoticiaByFonte($donte->id_knewin);

            dd($noticia);
            
        }


    }

    public function coletas($id)
    {
        $fonte = FonteWeb::where('id', $id)->first();
        return view('fonte-web/coletas', compact('fonte'));
    }

    public function listarColetas($origem, $id)
    {
        $dados = array();

        switch ($origem) {
            case 'studio':
                $dados = JornalWeb::where('id_fonte', $id)->take(10)->orderBy('dt_clipagem',"DESC")->get();
                break;
            case 'knewin':
                $dados = (new Noticia())->getNoticias($id);
                break;
            default:
            $dados = array();
                break;
        }

        return response()->json($dados);
    }

    public function estatisticas($id)
    {
        $fonte = FonteWeb::find($id);
        $hoje = $this->data_atual;
        $data_inicial = Carbon::parse($hoje)->subDays(7)->format('Y-m-d');
        $total_dia = JornalWeb::where("id_fonte", $id)->where('dt_clipagem', $hoje)->count();  

        $clientes = $fonte->getColetasByFonte($id, $data_inicial, $hoje);
        
        return view('fonte-web/estatisticas', compact('fonte','total_dia','clientes'));
    }

    public function getSemanaColetas($id_fonte)
    {
        $dados = array();
        $dt_final = Carbon::parse($this->data_atual);
        $dt_final_formatada = $dt_final->format('Y-m-d');
        $dt_inicial = $dt_final->subDays(7);

        $total_semana =  JornalWeb::select(DB::raw('dt_clipagem::date as data'), DB::raw('COUNT(dt_clipagem) as total'))
                                ->where("id_fonte", $id_fonte)
                                ->whereBetween('dt_clipagem', [$dt_inicial->format('Y-m-d'), $dt_final_formatada])
                                ->groupBy('data')
                                ->get(); 

        foreach ($total_semana as $key => $total) {
            $dados['data'][$key] = Carbon::parse($total->data)->format('d/m/Y');
            $dados['total'][$key] = $total->total;
        }

        return response()->json($dados);
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