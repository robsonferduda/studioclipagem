<?php

namespace App\Http\Controllers;

use App\Classes\FBFeed;
use App\Classes\FBFeedApi;
use App\Classes\FBSearchPageApi;
use App\Enums\TypeMessage;
use App\FbPageMonitor;
use App\FbPagePost;
use App\Client;
use App\Enums\FbReaction;
use App\FbPagePostComment;
use App\FbMediaNotFilteredVw;
use App\FbPagePostCommentNotFilteredVw;
use App\FbPagePostNotFilteredVw;
use App\Utils;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\PageRequest;
use Illuminate\Support\Facades\Http;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Session;
use DB;

class FbPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','facebook-paginas');
    }

    public function index(Request $request)
    {
        $quantidade = null;

        $page_term =  strtolower($request->page_term);

        $pages = FbPageMonitor::when($page_term, function($query) use ($page_term){
            $query->whereRaw(" lower(name) SIMILAR TO '%{$page_term}%' ");
        })
        ->orderby('page_post_count', 'desc')
        ->paginate(20);
        // ->when($quantidade > 0, function($query) use ($quantidade) {
        //     $query->has('fbPagesPost', '>=', $quantidade);
        // })
        // ->when($quantidade <= 0 && is_numeric($quantidade), function($query) use ($quantidade) {
        //     $query->doesntHave('fbPagesPost');
        // })

        $clients = Client::select(['id', 'name'])->get();

        return view('pages/index', compact('pages', 'clients', 'page_term'));
    }

    public function cadastrar()
    {
        return view('pages/cadastrar');
    }

    public function associar()
    {
        $clientes = Client::orderBy('name')->get();
        $paginas = FbPageMonitor::orderBy('name')->get();
        return view('pages/associar', compact('clientes','paginas'));
    }

    public function buscarPagina(Request $request)
    {

        $pages_monitor = FbPageMonitor::pluck('page_id')->toArray();

        $token_app = env('COLETA1');//getTokenApp();

        $fb_api = new FBSearchPageApi();

        $fields = $fb_api->getPageInfoFields();

        $params = [
            'q' => strtolower($request->termo),
            'access_token' => $token_app,
            'after' => $request->after,
            'before' => $request->before,
            'limit' => 10
        ];

        $pages = $fb_api->getPages($params);

        $dados = [];
        $dados['limit_exceeded'] =  false;

        if(isset($pages['headers']['x-app-usage'][0])) {
            $x_app_usage = json_decode($pages['headers']['x-app-usage'][0]);

            if((int) $x_app_usage->call_count > 50) {
                $dados['limit_exceeded'] = true;
                echo json_encode($dados);
                exit;
            }
        }

        $pages = $pages['body'];

        foreach ($pages['data'] as $page) {

            $page_id = $page['id'];

            $params = [
                'fields' => $fields,
                'access_token' => $token_app
            ];

            $infos = $fb_api->getPageInfo($page_id, $params);

            $dados['data'][] =  array(
                            'id' => $infos['id'],
                            'name' => $infos['name'],
                            'link' => $infos['link'],
                            'description' => isset($infos['description']) ? $infos['description'] : '',
                            'category' => $infos['category'],
                            'picture' => $infos['picture']['data']['url'],
                            'registered' =>  ( in_array($infos['id'], $pages_monitor) ? true : false ),
                            'location' => isset($infos['location']) ? $infos['location'] : ''
                        );
        }

        if($fb_api->hasAfter($pages) || $fb_api->hasBefore($pages) ) {
            $dados['info']['after'] = $fb_api->getAfter($pages);
            $dados['info']['before'] = $fb_api->getBefore($pages);
            $dados['info']['query'] = $request->termo;
        }

        echo json_encode($dados);

    }

    public function show($id) {
        $page = FbPageMonitor::where('id', $id)->first();

        return $page;
    }

    public function store(Request $request)
    {
        try {
            $page = FbPageMonitor::create([
                'name' => $request->name,
                'url' => $request->link,
                'page_id' => $request->id,
                'picture_url' => $request->picture
            ]);

            if($page) {
                $retorno = array('flag' => true,
                'msg' => "Dados inseridos com sucesso");
            }

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {

            $retorno = array('flag' => false,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        if ($retorno['flag']) {
            echo json_encode($retorno);
        } else {
            echo json_encode($retorno);
        }
    }

    public function update(Request $request)
    {
        try {
            $page = FbPageMonitor::where('id', $request->id)->first();

            $page->update([
                'name' => $request->name,
                'url' => 'https://paginaexemplo1.com.br' //$request->url
            ]);

            if($page) {
                $retorno = array('flag' => true,
                'msg' => "Dados atualizados com sucesso");
            }

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {

            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

       //(new FBFeed())->pullMedias();

       if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('facebook-paginas');
        } else {
            Flash::error($retorno['msg']);
            return redirect('facebook-paginas');
        }
    }

    public function destroy($id)
    {
        $page = FbPageMonitor::where('id', $id)->first();

        if($page->delete())
            Flash::success('<i class="fa fa-check"></i> Página <strong>'.$page->name.'</strong> excluída com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('facebook-paginas');
    }

    public function medias(Request $request, $page = NULL)
    {
        $term =  strtolower($request->term);

        $take = 10000;
        if($term) {
            $take = 500;
        }

        $date_limit = Carbon::now();

        $media_a = FbPagePostNotFilteredVw::when($page, function($query) use ($page){
                $query->where('page_monitor_id', $page);
            })
            ->when($term, function($query) use ($term){
                $query->whereRaw(" lower(text) SIMILAR TO '%({$term} | {$term}| {$term} )%' ");
            })->take($take)
            ->orderBy('date','DESC');
        $media_b = FbPagePostCommentNotFilteredVw::when($page, function($query) use ($page){
                $query->where('page_monitor_id', $page);
            })
            ->when($term, function($query) use ($term){
                $query->whereRaw(" lower(text) SIMILAR TO '%({$term} | {$term}| {$term} )%' ");
            })->take($take)
            ->orderBy('date','DESC');

        $medias_temp = $media_b->unionAll($media_a)->orderBy('date', 'DESC')->simplePaginate(20);

        $medias = [];

        foreach ($medias_temp as $key => $media) {

            if($media->tipo == TypeMessage::FB_PAGE_POST) {
                $name = $media->name;
                $type_message = 'facebook-page';
            } else {
                $name = '';
                $type_message = 'facebook-page-comment';
            }

            $medias[] = array('id' => $media->id,
                'text' => $media->text,
                'username' => $name,
                'created_at' => dateTimeUtcToLocal($media->date),
                'sentiment' => '',
                'type_message' => $type_message,
                'like_count' => $media->like_count,
                'comments_count' => !empty($media->comment_count) ? $media->comment_count : 0,
                'tipo' => 'facebook',
                'link' =>  $media->link,
                'share_count' => !empty($media->share_count) ? $media->share_count : 0,
                'user_profile_image_url' => $media->img_link
             );

        }

        return view('pages/medias', compact('medias', 'medias_temp', 'term'));
    }

    public function pullMedias()
    {
        //(new FBFeed())->fetchPostCount();
        (new FBFeed())->pullMedias();
    }

    public function associarCliente(PageRequest $request)
    {
        $clients = $request->clientes;
        $pages = $request->paginas;

        for ($i=0; $i < count($clients); $i++) {
            $client = Client::where('id', $clients[$i])->first();
            $result = $client->pagesMonitor()->sync($pages);
        }

        Flash::success('<i class="fa fa-check"></i> Associações de clientes e páginas atualizadas com sucesso');
        return redirect('facebook-paginas');
    }

    public function verifyPicture(){

        $pages_monitor = FbPageMonitor::get();

        foreach ($pages_monitor as $page) {


            try {

                $response = Http::get($page->picture_url);

                if($response->status() == 200){
                    continue;
                }

                $token_app = env('COLETA1');//getTokenApp();

                $fb_api = new FBSearchPageApi();

                $params = [
                    'access_token' => $token_app,
                    'redirect' => 0,
                    'fields' => 'url'
                ];

                $picture = $fb_api->getPagePicture($page->page_id, $params);

                $page->picture_url = $picture['data']['url'];

                $page->save();


            }catch(Exception $e){

                //dd($e);

            }


        }

    }

    public function updateReactions() {

        $posts = FbPagePost::get();
        $fb_feed = new FBFeedApi(999);
        $token_app = env('COLETA1');//getTokenApp();

        foreach ($posts as $post) {
            $reactions = $this->getReactions($post['post_id'], $fb_feed, $token_app);

            $post->comment_count = $reactions['qtd_comments'];
            $post->share_count  = $reactions['qtd_shares'];
            $post->save();

            $reaction_buffer = [];
            foreach ($reactions['types'] as $type => $qtd) {
                if($qtd > 0) {

                    $reaction = constant('App\Enums\FbReaction::'. $type);
                    $reaction_buffer[$reaction] = ['count' => $qtd];

                }
            }

            if (!empty($reaction_buffer)) {
                $post->reactions()->sync($reaction_buffer);
            }
        }
    }


    public function getReactions($post_id, $fb_feed, $access_token) {

        $params = [
            'fields' => $fb_feed->getFBReactionsFields($post_id),
            'access_token' => $access_token
        ];

        $post_reactions = $fb_feed->getFBPostReactions($post_id,$params);

        // if(empty($post_reactions['id'])){
        //     return [];
        // }

        $reactions = [
            'id' =>  isset($post_reactions['id']) ? $post_reactions['id'] : null,
            'qtd_shares' => isset($post_reactions['shares']['count']) ? $post_reactions['shares']['count'] : null,
            'qtd_comments' => isset($post_reactions['comments']['summary']['total_count']) ? $post_reactions['comments']['summary']['total_count'] : null,
            'types' => [
                'LIKE' => isset($post_reactions['LIKE']['summary']['total_count']) ? $post_reactions['LIKE']['summary']['total_count'] : null,
                'LOVE' => isset($post_reactions['LOVE']['summary']['total_count']) ? $post_reactions['LOVE']['summary']['total_count'] : null,
                'WOW' => isset($post_reactions['WOW']['summary']['total_count']) ? $post_reactions['WOW']['summary']['total_count'] : null,
                'HAHA' => isset($post_reactions['HAHA']['summary']['total_count']) ? $post_reactions['HAHA']['summary']['total_count'] : null,
                'SAD' => isset($post_reactions['SAD']['summary']['total_count']) ? $post_reactions['SAD']['summary']['total_count'] : null,
                'ANGRY' => isset($post_reactions['ANGRY']['summary']['total_count']) ? $post_reactions['ANGRY']['summary']['total_count'] : null,
                'THANKFUL' => isset($post_reactions['THANKFUL']['summary']['total_count']) ? $post_reactions['THANKFUL']['summary']['total_count'] : null
            ]
        ];

        return $reactions;

    }
}
