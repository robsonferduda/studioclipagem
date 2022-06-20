<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use App\Term;
use App\Client;
use App\Configs;
use App\Hashtag;
use App\Media;
use App\FbPost;
use App\MediaFilteredVw;
use App\MediaRuleFilteredVw;
use App\MediaTwitter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    private $client_id;
    private $periodo_padrao;

    public function __construct()
    {
        $this->middleware('auth');

        $cliente = Client::where('id',Configs::where('key', 'cliente_padrao')->first()->value)->first();

        $clienteSession = ['id' => $cliente->id, 'nome' => $cliente->name];

        Session::put('cliente', session('cliente') ? session('cliente') : $clienteSession);

        $this->client_id = session('cliente')['id'];
        
        Session::put('url','home');

        $this->periodo_padrao = Configs::where('key', 'periodo_padrao')->first()->value;
    }

    public function index()
    {
        $totais = array();
        $users = null;
        $clientes = null;
        $periodo_relatorio = null;
        $periodo_padrao = $this->periodo_padrao;
        $data_inicial = Carbon::now()->subDays($this->periodo_padrao - 1)->format('Y-m-d');
        $data_final = Carbon::now()->format('Y-m-d');

        $ig_comments_total = DB::table('ig_comments')
                                ->join('medias','medias.id','=','ig_comments.media_id')
                                ->where('medias.client_id','=',$this->client_id)
                                ->count();

        $fb_comments_total = DB::table('fb_comments')
                                ->join('fb_posts','fb_posts.id','=','fb_comments.post_id')
                                ->where('fb_posts.client_id','=',$this->client_id)
                                ->count();

        $u = User::find(Auth::user()->id);
        $media_twitter = round((MediaTwitter::where('client_id',$this->client_id)->count())/30, 1);
        $media_instagram = round((Media::where('client_id',$this->client_id)->count() + $ig_comments_total)/30, 1);

        $fb_post_pages_total = DB::table('page_post_term')
                                        ->join('terms', 'page_post_term.term_id','=','terms.id')
                                        ->where('terms.client_id','=',$this->client_id)
                                        ->count();

        $media_facebook = round((FbPost::where('client_id',$this->client_id)->count() + $fb_comments_total + $fb_post_pages_total)/30, 1);

        $mediaModel = new MediaFilteredVw();

        $fb_total = $mediaModel::where(function($query) {
            $query->Orwhere('tipo', 'FB_COMMENT')
            ->Orwhere('tipo', 'FB_PAGE_POST')
            ->Orwhere('tipo', 'FB_PAGE_POST_COMMENT')
            ->Orwhere('tipo', 'FB_POSTS');
        })
        ->where('client_id', $this->client_id)
        ->whereBetween('date', [$data_inicial.' 00:00:00',$data_final.' 23:23:59'])
        ->select('id')
        ->count();

        $twitter_total = $mediaModel::where('tipo', 'TWEETS')
        ->where('client_id', $this->client_id)
        ->whereBetween('date', [$data_inicial.' 00:00:00',$data_final.' 23:23:59'])
        ->select('id')
        ->count();

        $ig_total = $mediaModel::where(function($query) {
            $query->Orwhere('tipo', 'IG_POSTS')
            ->Orwhere('tipo', 'IG_COMMENT');
        })
        ->where('client_id', $this->client_id)
        ->whereBetween('date', [$data_inicial.' 00:00:00',$data_final.' 23:23:59'])
        ->select('id')
        ->count();

        if($u->hasRole('administradores')){

            $users = User::whereNull('client_id')->count();
            $clientes = Client::count();
           
            $hashtags = Hashtag::where('client_id', $this->client_id)->where('is_active',true)->orderBy('hashtag')->get();
            $terms = Term::with('mediasTwitter')->with('medias')->where('client_id', $this->client_id)->where('is_active',true)->orderBy('term')->get();

            return view('index', compact('users','clientes','totais','hashtags','terms','periodo_relatorio','media_twitter','media_instagram','media_facebook'));

        }else{
            
            $periodo_relatorio = array('data_inicial' => Carbon::now()->subDays(7)->format('d/m/Y'),
                                    'data_final'   => Carbon::now()->format('d/m/Y'));

            $hashtags = Hashtag::where('client_id', $this->client_id)->where('is_active',true)->orderBy('hashtag')->get();
            $terms = Term::with('mediasTwitter')->with('medias')->where('client_id', $this->client_id)->where('is_active',true)->orderBy('term')->get();

            $totais = array('total_insta' => $ig_total, 
                            'total_face' => $fb_total,
                            'total_twitter' => $twitter_total);

            return view('dashboard_cliente', compact('users','clientes','totais','hashtags','terms','periodo_relatorio','media_twitter','periodo_padrao'));
                
        }     

    }
}