<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Response;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Utils;
use App\Models\FonteTemp;
use App\Noticia;
use App\Models\Pais;
use App\Models\SecaoWeb;
use App\Models\NoticiaWeb;
use App\Models\ConteudoNoticiaWeb;
use App\Models\JornalWeb;
use App\Models\Prioridade;
use App\Models\Situacao;
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

    public function index(Request $request)
    {
        Session::put('sub-menu','fonte-web');

        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $situacoes = (new FonteWeb())->getSituacoes();
        
        $fonte = FonteWeb::query();

        if($request->isMethod('POST')){

            $nome = ($request->nome) ? $request->nome : "";
            $url = ($request->url) ? $request->url : "";
            $codigo = ($request->codigo) ? $request->codigo : "";

            if($request->cd_estado != "")
                Session::put('filtro_estado', $request->cd_estado);
            else
                Session::forget('filtro_estado');

            if($request->cd_cidade != "")
                Session::put('filtro_cidade', $request->cd_cidade);
            else
                Session::forget('filtro_cidade');

            if($request->id_prioridade != "")
                Session::put('filtro_prioridade', $request->id_prioridade);
            else
                Session::forget('filtro_prioridade');

            $fonte->when(Session::get('filtro_estado'), function ($q) {
                return $q->where('cd_estado', Session::get('filtro_estado'));
            });

            $fonte->when(Session::get('filtro_cidade'), function ($q) {
                return $q->where('cd_cidade', Session::get('filtro_cidade'));
            });

            $fonte->when(Session::get('filtro_situacao'), function ($q) {
                return $q->where('id_situacao', Session::get('filtro_situacao'));
            });

            $fonte->when(Session::get('filtro_prioridade'), function ($q) {
                if(Session::get('filtro_prioridade') == 6)
                    return $q->where('id_prioridade', 0);
                else
                    return $q->where('id_prioridade', Session::get('filtro_prioridade'));
            });

            $fonte->when($nome, function ($q) use ($nome) {
                Session::put('filtro_nome', $nome);
                return $q->whereRaw("unaccent(nome) ILIKE unaccent(?)", ['%' . trim($nome) . '%']);
            });

            $fonte->when($url, function ($q) use ($url) {
                Session::put('filtro_url', $url);
                return $q->where('url', 'ILIKE', '%'.trim($url).'%');
            });

            $fonte->when($codigo, function ($q) use ($codigo) {
                Session::put('filtro_codigo', $codigo);
                return $q->where('id', $codigo);
            });
            
        }

        if($request->isMethod('GET')){

            $fonte->when(Session::get('filtro_estado'), function ($q) {
                return $q->where('cd_estado', Session::get('filtro_estado'));
            });

            $fonte->when(Session::get('filtro_cidade'), function ($q) {
                return $q->where('cd_cidade', Session::get('filtro_cidade'));
            });

            $fonte->when(Session::get('filtro_situacao'), function ($q) {
                return $q->where('id_situacao', Session::get('filtro_situacao'));
            });

            $fonte->when(Session::get('filtro_prioridade'), function ($q) {
                if(Session::get('filtro_prioridade') == 6)
                    return $q->where('id_prioridade', 0);
                else
                    return $q->where('id_prioridade', Session::get('filtro_prioridade'));
            });

            $fonte->when(Session::get('filtro_nome'), function ($q) {
                return $q->where('nome', 'ILIKE', '%'.trim(Session::get('filtro_nome')).'%');
            });

            $fonte->when(Session::get('filtro_url'), function ($q) {
                return $q->where('url', 'ILIKE', '%'.trim(Session::get('filtro_url')).'%');
            });

            $fonte->when(Session::get('filtro_codigo'), function ($q) {
                return $q->where('id', Session::get('filtro_codigo'));
            });           
            
        }

        $fonte->orderByRaw("CASE WHEN crawlead_at IS NULL THEN 1 ELSE 0 END ASC")
                ->orderBy('crawlead_at','DESC')
                ->orderBy('created_at');

        $fontes = $fonte->paginate(10);

        return view('fonte-web/listar',compact('cidades','estados','situacoes','fontes'));
    }

    public function filtrarSituacao(Request $request)
    {
        if($request->situacao and $request->situacao >= 0){
            Session::put('filtro_situacao', $request->situacao);
        }else{
            Session::forget('filtro_situacao');
        }
    }

    public function limpar()
    {
        Session::forget('filtro_estado');
        Session::forget('filtro_cidade');
        Session::forget('filtro_url');
        Session::forget('filtro_nome');
        Session::forget('filtro_codigo');
        Session::forget('filtro_prioridade');
        Session::forget('filtro_situacao');
        
        return redirect('fonte-web/listar');
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
            $url = ($request->url) ? $request->url : "";
            $estado = ($request->estado) ? $request->estado : "";
            $cidade = ($request->cidade) ? $request->cidade : "";
            $id = ($request->id) ? $request->id : "";
            $prioridade = ($request->id_prioridade) ? $request->id_prioridade : "";
            
    
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

        if($prioridade == 5){
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
        $temporarias = DB::select("SELECT * from fonte_tmp WHERE url NOT IN(SELECT url FROM fonte_web)");       

        foreach ($temporarias as $key => $temp) {

            $n += 1;
                $estado = Estado::where('nm_estado', $temp->estado)->first();                
                
                $est = ($estado) ? $estado->cd_estado : null;

                $new_fonte = array('nome' => (string) $temp->titulo, 
                                    'url' => $temp->url, 
                                    'id_knewin' => $temp->id_knewin, 
                                    'id_situacao' => 0, 
                                    'id_prioridade' => 1, 
                                    'cd_pais' => 55,
                                    'nu_valor' => $temp->valor_cm, 
                                    'cd_estado' => $est);
                
                FonteWeb::create($new_fonte);
           
            /*
            $fonte = FonteWeb::where('id_knewin', $temp->id_knewin)->orWhere('url', $temp->url)->first();

            dd($fonte);

            if($fonte){

                $s += 1;
                echo "Fonte já existe <br/>";

            }else{
                $n += 1;
                $estado = Estado::where('nm_estado', $temp->estado)->first();                
                
                $est = ($estado) ? $estado->cd_estado : null;

                $new_fonte = array('nome' => (string) $temp->titulo, 
                                    'url' => $temp->url, 
                                    'id_knewin' => $temp->id_knewin, 
                                    'id_situacao' => 0, 
                                    'id_prioridade' => 1, 
                                    'nu_valor' => $temp->valor_cm, 
                                    'cd_estado' => $est);
                
                FonteWeb::create($new_fonte);
            }*/

        }
        dd($n);
    }

    /* Importação de notícias" */
    public function importarNoticia()
    {
        $controle = 0;

        /*
        $fontes = DB::select("select t1.id, t2.id_site 
                            from fonte_web t1 
                            JOIN fonte_tmp t2 ON t2.id_knewin = t1.id_knewin 
                            where t1.id not in (select id_fonte from noticias_web group by id_fonte)");
                            */

                            $fontes = DB::select("select t1.id, t2.id_site 
                            from fonte_web t1 
                            JOIN fonte_tmp t2 ON t2.url = t1.url 
                            where t1.id not in (select id_fonte from noticias_web group by id_fonte)");
                                                        
        foreach ($fontes as $key => $fonte) {

            $noticia = (new Noticia())->getNoticiaBySite($fonte->id_site, '2022-01-01');
    
            if($noticia){   
                
                if($noticia[0]->conteudo){
                             //Insere em notícia
                            $dados_noticia = array('id_fonte' => $fonte->id,
                            'data_insert' => date('2024-12-15 00:00:00'),
                            'data_noticia' => $noticia[0]->data_cadastro,
                            'titulo_noticia' => $noticia[0]->titulo,
                            'url_noticia' => $noticia[0]->link);
    
                            $nova = NoticiaWeb::create($dados_noticia);
    
                            //Insere em conteúdo
                            $dados_conteudo = array('id_noticia_web' => $nova->id,
                                                'conteudo' => $noticia[0]->conteudo);
    
                            ConteudoNoticiaWeb::create($dados_conteudo);


                echo "Inseriu notícia da fonte ".$fonte->id."<br/>";
                }

            }else{

                echo "Não inseriu notícia da fonte ".$fonte->id."<br/>";
            }

            $controle++;

            if($controle == 100){
                dd("Parou");
            }
        }

        dd("FIM! Inseridas $controle notícias");
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
        $fonte->whereNotIn('id_situacao',[1,0])->orderBy('id_situacao')->orderBy('nome');
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

    public function buscar(Request $request)
    {
        $query = FonteWeb::query();

        if ($request->filled('nome')) {
            $query->where('nome', 'ilike', '%' . $request->nome . '%');
        }
        if ($request->filled('estado')) {
            $query->where('cd_estado', $request->estado);
        }
        if ($request->filled('cidade')) {
            $query->where('cd_cidade', $request->cidade);
        }

        $perPage = 10;

        $fontes = $query->paginate($perPage);
        
        $dados = array();

        foreach ($fontes->items() as $key => $fonte) {
            $dados[$key]['id'] = $fonte->id;
            $dados[$key]['nome'] = $fonte->nome;
            $dados[$key]['estado'] = ($fonte->estado) ? $fonte->estado->nm_estado : 'Não informado';
            $dados[$key]['cidade'] = ($fonte->cidade) ? $fonte->cidade->nm_cidade : 'Não informado';      
        }

        return response()->json([
                'data' => $dados,
                'current_page' => $fontes->currentPage(),
                'last_page' => $fontes->lastPage(),
                'total' => $fontes->total()
        ]);

    }

    public function buscarFontes()
    {
        $dados = FonteWeb::all();

        return response()->json($dados);
    }

    public function estatisticas($id)
    {
        $fonte = FonteWeb::find($id);
        $hoje = $this->data_atual;
        $data_inicial = Carbon::parse($hoje)->subDays(7)->format('Y-m-d');
        $total_dia = JornalWeb::where("id_fonte", $id)->where('data_insert', $hoje)->count();  

        $clientes = $fonte->getColetasByFonte($id, $data_inicial, $hoje);
        
        return view('fonte-web/estatisticas', compact('fonte','total_dia','clientes'));
    }

    public function getSemanaColetas($id_fonte)
    {
        $dados = array();
        $dt_final = Carbon::parse($this->data_atual);
        $dt_final_formatada = $dt_final->format('Y-m-d') . ' 23:59:59';
        $dt_inicial = $dt_final->subDays(7);

        $total_semana =  JornalWeb::select(DB::raw('dt_clipagem::date as data'), DB::raw('COUNT(dt_clipagem) as total'))
                                ->where("id_fonte", $id_fonte)
                                ->whereBetween('dt_clipagem', [$dt_inicial->format('Y-m-d') . ' 00:00:00', $dt_final_formatada])
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
        $paises = Pais::orderBy('nu_ordem','DESC')->orderBY('ds_pais')->get();
        $prioridades = Prioridade::orderBy('id')->get();

        return view('fonte-web/novo', compact('estados','cidades','paises','prioridades'));
    }

    public function edit(FonteWeb $fonte, $id)
    {
        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $fonte = FonteWeb::find($id);
        $paises = Pais::orderBy('nu_ordem','DESC')->orderBY('ds_pais')->get();
        $prioridades = Prioridade::orderBy('id')->get();
        $situacoes = SituacaoFonteWeb::orderBy('id_situacao_fonte_web')->get();
        $flag_inconsistencia = false;

        $noticia = NoticiaWeb::where('id_fonte', $id)->orderBy('created_at','DESC')->first();

        return view('fonte-web/editar', compact('fonte','estados','cidades','flag_inconsistencia','paises','noticia','prioridades','situacoes'));
    }

    public function editInconsistencia(FonteWeb $fonte, $id)
    {
        $cidades = Cidade::orderBy('nm_cidade')->get();
        $estados = Estado::orderBy('nm_estado')->get();
        $fonte = FonteWeb::find($id);
        $paises = Pais::all();
        $prioridades = Prioridade::orderBy('id')->get();
        $situacoes = SituacaoFonteWeb::orderBy('id_situacao_fonte_web')->get();
        $flag_inconsistencia = true;

        $noticia = NoticiaWeb::where('id_fonte', $id)->orderBy('created_at')->first();

        return view('fonte-web/editar', compact('fonte','estados','cidades','flag_inconsistencia','paises','noticia','prioridades','situacoes'));
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
        $carbon = new Carbon();
        $request->merge(['id_situacao' => 0]); //Inicia sempre com situação = 0 - Aguardando

        $fonte_existe = FonteWeb::where('url', $request->url)->first();

        if($fonte_existe)
        {
            Flash::warning('<i class="fa fa-exclamation"></i> Existe uma fonte cadastrada com a URL <strong>'.$fonte_existe->url.'</strong>');

            return redirect('fonte-web/create')->withInput();
        }

        try {
            
            $fonte = FonteWeb::create($request->all());

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

        if($request->resetar_situacao){
            $request->merge(['id_situacao' => 0]);
        }
    
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

    public function getTopColetas($n)
    {
        $dados = (new FonteWeb())->getTopColetas($n);

        return response()->json($dados);
    }

    public function getColetasByFonte($id)
    {
        $dados = (new FonteWeb())->getColetasByFonte($id);

        return response()->json($dados);
    }

    public function getSemColetas($n)
    {
        $dados = (new FonteWeb())->getSemColetas($n);

        return response()->json($dados);
    }

    public function secao(Request $request)
    {
        $dados_insert = array('id_fonte_web' => $request->font_id,
                              'ds_sessao' => $request->ds_sessao);

        $nova_secao = SecaoWeb::create($dados_insert);

        if($nova_secao){

            return Response::json(array(
                    'code'      =>  200,
                    'message'   =>  'Dados inseridos com sucesso'
                ), 200);

        }else{

             return Response::json(array(
                    'code'      =>  401,
                    'message'   =>  'Erro ao inserir dados'
                ), 401);

        }

    }

    public function excluirSecao($id)
    {
        $secao = SecaoWeb::find($id);
        $id_fonte_web = $secao->id_fonte_web;

        $secao->delete();

        return redirect('fonte-web/'.$id_fonte_web.'/editar')->withInput();
    }
}