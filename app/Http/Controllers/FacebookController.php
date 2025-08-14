<?php

namespace App\Http\Controllers;

use Mail;
use Carbon\Carbon;
use App\Models\PostFacebook;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FacebookController extends Controller
{
    private $carbon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->carbon = new Carbon();
        Session::put('url','facebook');
    }

    public function index()
    {
        
    }

    public function coletas()
    {
        Session::put('sub-menu','facebook-coletas');

        return view('facebook.coletas');
    }

    public function postagens()
    {
        Session::put('sub-menu','facebook-postagens');
    }

    public function listarPostsAjax(Request $request)
    {
        $query = PostFacebook::with('pagina')->orderBy('data_postagem', 'desc');

        if ($request->filled('data')) {
            $data = $request->input('data');
            $inicio = $data . ' 00:00:00';
            $fim = $data . ' 23:59:59';
            $query->whereBetween('data_postagem', [$inicio, $fim]);
        }else{
            $hoje = $this->carbon->now()->format('Y-m-d');
            $inicio = $hoje . ' 00:00:00';
            $fim = $hoje . ' 23:59:59';
            $query->whereBetween('data_postagem', [$inicio, $fim]);
        }

        $posts = $query->paginate(10);
        return response()->json($posts);
    }
}