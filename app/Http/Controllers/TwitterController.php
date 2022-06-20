<?php

namespace App\Http\Controllers;

use App\Classes\IGMention;
use App\Classes\IGHashTag;
use App\Rule;
use App\Twitter\TwitterCollect;
use App\MediaTwitter;
use App\TwitterAPIExchange;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Session;
use App\Twitter\TwitterOAuth;
use App\Notifications\RuleProcessNotification;

class TwitterController extends Controller
{ 

    public function __construct()
    {
        $this->middleware('auth');
        
        define('CONSUMER_KEY', 'rHn2F4BIhJ17s7jTPJyZ0SrKU');
        define('CONSUMER_SECRET', 'URmePiavhe5NIFSMKpYuHDIaUvW007tGt2SJlDdgykyGWt5FgM');
        define('ACCESS_TOKEN', '725986202-o9342d5gnK1JTn3Rgn5VNoqPSacr6KSHCnGtnbQh');
        define('ACCESS_TOKEN_SECRET','fHb4L1jR2qbSv94A5DByuP26rC7IM4bD5YvwrMTXB4DgI');
       
        $this->conn = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
    }

    public function getTweetByUserAndSentiment($user, $sentiment)
    {
        $medias = MediaTwitter::where('user_name',$user)->where('sentiment',$sentiment)->get();

        foreach($medias as $key => $media){
            $medias[$key]->type_message = 'tweets';
            $medias[$key]->link = 'https://twitter.com/'.$media->user_screen_name.'/status/'.$media->twitter_id;
        }

        return view('twitter/postagens', compact('medias'));
    }

    public function index()
    {
       (new TwitterCollect())->pullMedias();
       Flash::success("As coletas do Twitter foram realizadas com sucesso");

       return redirect('coletas')->withInput(); 
    }
}