<?php

namespace App\Http\Controllers;

use Mail;
use Carbon\Carbon;
use App\Models\PostInstagram;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class InstagramController extends Controller
{
    private $carbon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->carbon = new Carbon();
        Session::put('url','instagram');
    }

    public function index()
    {
        
    }

    public function coletas()
    {
        Session::put('sub-menu','instagram-coletas');

        return view('instagram.coletas');
    }

    public function postagens()
    {
        Session::put('sub-menu','instagram-postagens');
    }

    public function listarPostsAjax(Request $request)
    {
        $query = PostInstagram::orderBy('timestamp', 'desc');

        if ($request->filled('texto')) {
            $texto = $request->input('texto');
            $query->where('caption', 'ilike', '%' . $texto . '%');
        }

        if ($request->filled('data')) {
            $data = $request->input('data');
            $inicio = $data . ' 00:00:00';
            $fim = $data . ' 23:59:59';
            $query->whereBetween('timestamp', [$inicio, $fim]);
        }else{
            $hoje = $this->carbon->now()->format('Y-m-d');
            $inicio = $hoje . ' 00:00:00';
            $fim = $hoje . ' 23:59:59';
            $query->whereBetween('timestamp', [$inicio, $fim]);
        }

        $posts = $query->paginate(10);
        return response()->json($posts);
    }
}