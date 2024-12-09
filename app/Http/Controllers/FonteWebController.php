<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Utils;
use App\Models\FonteTemp;
use App\Noticia;
use App\Models\Pais;
use App\Models\NoticiaWeb;
use App\Models\ConteudoNoticiaWeb;
use App\Models\JornalWeb;
use App\Models\Prioridade;
use App\Models\Estado;
use App\Models\Cidade;
use App\Models\FonteWeb;
use App\Models\SituacaoFonteWeb;
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
        Session::put('sub-menu','fonte-web');

        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
            
        $situacoes = (new FonteWeb())->getSituacoes();

        if($request->ajax()) {

            $situacao = ($request->situacao) ? $request->situacao : "";
            $nome = ($request->nome) ? $request->nome : "";
            $estado = ($request->estado) ? $request->estado : "";
            $cidade = ($request->cidade) ? $request->cidade : "";
            $id = ($request->id) ? $request->id : "";
    
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

                $fonte->when($id, function ($q) use ($id) {
                    return $q->where('id', $id);
                });
    
                $fonte->when($nome, function ($q) use ($nome) {
                    return $q->where('nome', 'ILIKE', '%'.trim($nome).'%');
                });

                $fonte->whereNotIn('id_situacao', [127,112,103,137])->orderBy('id');
    
                $fontes = $fonte->get();

            return DataTables::of($fontes)
                ->addColumn('id', function ($fonte) {
                    return $fonte->id;
                })     
                ->addColumn('estado', function ($fonte) {
                    return ($fonte->estado) ? $fonte->estado->nm_estado : "Não informado";
                }) 
                ->addColumn('situacao', function ($fonte) {
                    return '<span class="badge badge-default" style="background: '.$fonte->situacao->ds_color.' !important; border-color: '.$fonte->situacao->ds_color.' !important;">'.$fonte->situacao->ds_situacao.'</span>';
                })
                ->addColumn('cidade', function ($fonte) {
                    return ($fonte->cidade) ? $fonte->cidade->nm_cidade : "Não informado";
                })
                ->addColumn('nome', function ($fonte) {

                    if($fonte->id_prioridade){
                        $prioridade = '<button data-fonte="'.$fonte->id.'" data-id="'.$fonte->prioridade->id.'" class="btn btn-'.$fonte->prioridade->ds_color.' btn-round btn-icon btn-sm btn-prioridade" style="height: 0.875rem; min-width: 0.6rem; width: 0.6rem;"></button>';
                    }else{
                        $prioridade = '<button data-fonte="'.$fonte->id.'" data-id="0" class="btn btn-default btn-round btn-icon btn-sm btn-prioridade" style="height: 0.875rem; min-width: 0.6rem; width: 0.6rem;"></button>';
                    }

                    return $fonte->nome.' '.$prioridade;
                })  
                ->addColumn('valor', function ($fonte) {
                    return number_format($fonte->nu_valor, 2, ".","");
                }) 
                ->addColumn('url', function ($fonte) {
                    return $fonte->url;
                })    
                ->addColumn('acoes', function ($fonte) {
                    return '<div class="text-center">
                                <a title="Estatísticas" href="../fonte-web/estatisticas/'.$fonte->id.'" class="btn btn-warning btn-link btn-icon"> <i class="fa fa-bar-chart fa-2x"></i></a>
                                <a title="Editar" href="../fonte-web/editar/'.$fonte->id.'" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                <a title="Excluir" href="../fonte-web/excluir/'.$fonte->id.'" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-times fa-2x"></i></a>
                            </div>';
                })   
                ->rawColumns(['id','acoes','situacao','nome'])         
                ->make(true);

        }

        return view('fonte-web/listar',compact('cidades','estados','situacoes'));
    }

    public function atualizarPrioridade(Request $request)
    {
        $prioridade = $request->prioridade;

        if($prioridade == 3){
            $prioridade = 0;
        }else{
            $prioridade += 1;
        }

        $fonte = FonteWeb::find($request->fonte);
        $fonte->id_prioridade = $prioridade;
        $fonte->save();
    }

    public function importacaoNova()
    {
        $n = 0;
        $s = 0;
        $temporarias = FonteTemp::all();

        foreach ($temporarias as $key => $temp) {
           
            $fonte = FonteWeb::where('id_knewin', $temp->id_knewin)->first();

            if($fonte){
                $s += 1;

            }else{
                $n += 1;
                $estado = Estado::where('nm_estado', $temp->estado)->first();
                
                
                $est = ($estado) ? $estado->cd_estado : null;

                $new_fonte = array('nome' => (string) $temp->titulo, 
                                    'url' => $temp->url, 
                                    'id_knewin' => $temp->id_knewin, 
                                    'id_situacao' => 0, 
                                    'id_prioridade' => 1, 
                                    'nu_valor' => $temp->nu_valor, 
                                    'cd_estado' => $est);
                
                FonteWeb::create($new_fonte);
            }

        }
        dd($n);
    }

    public function importarNoticia()
    {

    }

    public function importar()
    {
        $total_fontes = 0;
        $data_base = '2024-10-22';

        $period = CarbonPeriod::create('2023-10-01', '2023-10-30');

        //Fontes para inserção
        $fontes = (new Noticia())->getFontes('2023-10-01','2023-10-30');

        foreach ($fontes as $key => $fonte) {

            if($fonte->id_knewin){
            
                $find = FonteWeb::where('id_knewin', $fonte->id_knewin)->first();

                if(!$find){

                    $new_noticia = array('nome' => $fonte->titulo, 'url' => $fonte->dominio, 'id_knewin' => $fonte->id_knewin, 'id_situacao' => 0, 'id_prioridade' => 1);
                    FonteWeb::create($new_noticia);
                }
            }
        }


        // Iterate over the period
        foreach ($period as $date) {

            /*

            $data_base = $date->format('Y-m-d');

            //Fontes para inserção
            $fontes = (new Noticia())->getFontes($data_base);

            foreach ($fontes as $key => $fonte) {

                if($fonte->id_knewin){
                
                    $find = FonteWeb::where('id_knewin', $fonte->id_knewin)->first();

                    if(!$find){

                        $new_noticia = array('nome' => $fonte->titulo, 'url' => $fonte->dominio, 'id_knewin' => $fonte->id_knewin, 'id_situacao' => 0, 'id_prioridade' => 1);
                        FonteWeb::create($new_noticia);
                    }
                }
            }

            /*

            $fontes = FonteWeb::where('id_situacao', 0)->get();
        
            foreach ($fontes as $key => $fonte) {
    
                if($fonte->id_knewin){
                    
                    $noticia_web = NoticiaWeb::where('id_fonte', $fonte->id)->first();
    
                    if(!$noticia_web){
    
                        $noticia = (new Noticia())->getNoticiaByFonte($fonte->id_knewin, $data_base);
    
                        if($noticia){
    
                             //Insere em notícia
                            $dados_noticia = array('id_fonte' => $fonte->id,
                            'data_insert' => $noticia[0]->data_clipping,
                            'data_noticia' => $noticia[0]->data_cadastro,
                            'titulo_noticia' => $noticia[0]->titulo,
                            'url_noticia' => $noticia[0]->link);
    
                            $nova = NoticiaWeb::create($dados_noticia);
    
                            //Insere em conteúdo
                            $dados_conteudo = array('id_noticia_web' => $nova->id,
                                                'conteudo' => $noticia[0]->conteudo);
    
                            ConteudoNoticiaWeb::create($dados_conteudo);
    
                            $fonte->id_situacao = 1;
                            $fonte->id_prioridade = 1;
                            $fonte->save();
                            
                            $total_fontes++;   
                        }
                    }       
                }  
            }
                */

        }

        dd("Total de fontes novas inseridas ".$total_fontes);
    }

    public function inconsistencias()
    {
        Session::put('sub-menu','fonte-web-inconsistencias');
        
        $fonte = FonteWeb::query();
        $fonte->whereNotIn('id_situacao',[1,0])->orderBy('nome');
        $dados = $fonte->get();

        return view('fonte-web/inconsistencias',compact('dados'));
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
        $paises = Pais::all();
        $prioridades = Prioridade::orderBy('id')->get();

        return view('fonte-web/novo', compact('estados','cidades','paises','prioridades'));
    }

    public function edit(FonteWeb $fonte, $id)
    {
        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $fonte = FonteWeb::find($id);
        $paises = Pais::all();
        $prioridades = Prioridade::orderBy('id')->get();
        $flag_inconsistencia = false;

        $noticia = NoticiaWeb::where('id_fonte', $id)->orderBy('created_at')->first();

        return view('fonte-web/editar', compact('fonte','estados','cidades','flag_inconsistencia','paises','noticia','prioridades'));
    }

    public function editInconsistencia(FonteWeb $fonte, $id)
    {
        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $fonte = FonteWeb::find($id);
        $paises = Pais::all();
        $prioridades = Prioridade::orderBy('id')->get();
        $flag_inconsistencia = true;

        $noticia = NoticiaWeb::where('id_fonte', $id)->orderBy('created_at')->first();

        return view('fonte-web/editar', compact('fonte','estados','cidades','flag_inconsistencia','paises','noticia','prioridades'));
    }

    public function atualizarEstado()
    {
        $fontes = FonteWeb::whereNull('cd_estado')->get();

        $count = 0;
        foreach ($fontes as $key => $fonte) {
            
            $fontes_with_estado = (new Noticia())->getEstado($fonte->id_knewin);

            if(isset($fontes_with_estado[0]) AND $fontes_with_estado[0]->estado){

                $estado = Estado::where('nm_estado', $fontes_with_estado[0]->estado)->first();

                $fonte->cd_estado = $estado->cd_estado;
                $fonte->save();
                $count += 1;
            }

        }
        dd($count);
    }

    public function atualizarValor()
    {
        $fontes = FonteWeb::whereNull('nu_valor')->get();

        $count = 0;
        foreach ($fontes as $key => $fonte) {
            
            $fontes_with_estado = (new Noticia())->getValor($fonte->id_knewin);

            if(isset($fontes_with_estado[0]) AND $fontes_with_estado[0]->valor_cm){

                $fonte->nu_valor = $fontes_with_estado[0]->valor_cm;
                $fonte->save();
                $count += 1;
            }

        }
        dd($count);
    }

    public function editarInconsistencia(Request $request)
    {
        $fonte = FonteWeb::find($request->id);

        if($fonte){
            $fonte->url = $request->url;
            $fonte->id_situacao = 0;
            $fonte->save();
        }
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
        $carbon = new Carbon();
        $fonte = FonteWeb::find($id);

        if($fonte->id_situacao == 173 or $fonte->id_situacao == 174 or $fonte->id_situacao == 47){

            if($request->id_noticia_referencia){

                $noticia = NoticiaWeb::find($request->id_noticia_referencia);
                $noticia->data_noticia = $carbon->createFromFormat('d/m/Y', $request->data_noticia)->format('Y-m-d')." 00:00:00";
                $noticia->titulo_noticia = $request->titulo;
                $noticia->url_noticia = $request->url_noticia;
                
                $noticia->save();

                $conteudo = ConteudoNoticiaWeb::where('id_noticia_web', $noticia->id)->first();

                if($conteudo){
                    $conteudo->conteudo = $request->conteudo;
                    $conteudo->save();
                }else{
                    $dados_conteudo = array('id_noticia_web' => $noticia->id,
                    'conteudo' => $request->conteudo);

                        ConteudoNoticiaWeb::create($dados_conteudo);
                }
                

            }else{

                $dados_noticia = array('id_fonte' => $id,
                                'data_noticia' => $carbon->createFromFormat('d/m/Y', $request->data_noticia)->format('Y-m-d')." 00:00:00",
                                'titulo_noticia' => $request->titulo,
                                'url_noticia' => $request->url_noticia);
        
                                $nova = NoticiaWeb::create($dados_noticia);
        
                                //Insere em conteúdo
                                $dados_conteudo = array('id_noticia_web' => $nova->id,
                                                        'conteudo' => $request->conteudo);
        
                                ConteudoNoticiaWeb::create($dados_conteudo);

            }

            $fonte->update(['id_situacao' => 0]);
        }

        if($request->resetar_situacao){
            $request->merge(['id_situacao' => 0]);
        }
    
        try{
                        
            $fonte->update($request->all());
            $retorno = array('flag' => true,
                             'msg' => '<i class="fa fa-check"></i> Dados atualizados com sucesso');

        } catch (\Illuminate\Database\QueryException $e) {

            dd($e);

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));
        } catch (Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);

            if($request->flag_inconsistencia)
                return redirect('fonte-web/inconsistencias')->withInput();
            else
                return redirect('fonte-web/listar')->withInput();

        } else {
            Flash::error($retorno['msg']);
            return redirect('fonte-web/editar/'.$fonte->id)->withInput();
        }
    }

    public function destroy($id)
    {
        $fonte = FonteWeb::find($id);

        if($fonte->delete())
            Flash::success('<i class="fa fa-check"></i> Fonte <strong>'.$fonte->name.'</strong> excluída com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('fonte-web/listar')->withInput();
    }
}