<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\EmissoraWeb;
use App\Models\VideoEmissoraWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VideosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','tv');
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','tv-videos');
        $emissoras = EmissoraWeb::orderBy('nome_emissora')->get();

        $carbon = new Carbon();
        $termo = $request->termo;
        $videos = null;
        $expressao = "";
        $fonte = 0;

        $video = VideoEmissoraWeb::query();

        if($request->isMethod('GET')){

            $video->when($dt_inicial, function ($q) use ($dt_inicial, $dt_final) {
                return $q->whereBetween('horario_start_gravacao', [$dt_inicial, $dt_final]);
            });

        }

        if($request->isMethod('POST')){

            $expressao = $request->expressao;
            $fonte = $request->fonte;

            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d")." 00:00:00";
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d")." 23:59:59";

            $video->when($dt_inicial, function ($q) use ($dt_inicial, $dt_final) {
                return $q->whereBetween('horario_start_gravacao', [$dt_inicial, $dt_final]);
            });

            $video->when($fonte, function ($q) use ($fonte) {
                return $q->where('id_fonte', $fonte);
            });

            $video->when($expressao, function ($q) use ($expressao) {
                return $q->whereRaw("transcricao  ~* '$expressao' ");
            });
        }

        $videos = $video->orderBy('created_at','DESC')->paginate(10);
        
        return view('videos/videos', compact('dt_inicial','dt_final','termo','emissoras','videos'));
    }

    public function detalhes($id)
    {
        $video = VideoEmissoraWeb::find($id);
        return view('videos/detalhes', compact('video'));
    }
}