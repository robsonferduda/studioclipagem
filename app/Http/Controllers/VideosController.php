<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use DateTime;
use DateInterval;
use DatePeriod;
use App\Utils;
use Carbon\Carbon;
use App\Models\EmissoraWeb;
use App\Models\ProgramaEmissoraWeb;
use App\Models\VideoEmissoraWeb;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Session;

class VideosController extends Controller
{
    private $data_atual;
    private $carbon;

    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','tv');
        $this->carbon = new Carbon();
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','tv-videos');
        
        $fontes = ProgramaEmissoraWeb::orderBy('nome_programa')->get();

        $tipo_data = $request->tipo_data;
        $dt_inicial = ($request->dt_inicial) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
        $dt_final = ($request->dt_final) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");
        $fonte = ($request->fontes) ? $request->fontes : null;
        $expressao = ($request->expressao) ? $request->expressao : null;

        if($request->fontes or Session::get('tv_arquivos_fonte')){
            if($request->fontes){
                $fonte = $request->fontes;
            }elseif(Session::get('tv_arquivos_fonte')){
                $fonte = Session::get('tv_arquivos_fonte');
            }else{
                $fonte = null;
            }
        }else{
            $fonte = null;
            Session::forget('tv_arquivos_fonte');
        }

        if($request->isMethod('POST')){

            if($request->fontes){
                Session::put('tv_arquivos_fonte', $fonte);
            }else{
                Session::forget('tv_arquivos_fonte');
                $fonte = null;
            }
        }

          $videos = DB::table('videos_programa_emissora_web')
                    ->select('videos_programa_emissora_web.id AS id',
                            'programa_emissora_web.id AS id_fonte',
                            'nome_programa AS nome_fonte',
                            'nm_estado',
                            'nm_cidade',
                            'horario_start_gravacao',
                            'horario_end_gravacao',
                            'transcricao',
                            'misc_data',
                            'video_path')
                    ->join('programa_emissora_web','programa_emissora_web.id','=','videos_programa_emissora_web.id_programa_emissora_web')
                    ->join('emissora_web','emissora_web.id','=','programa_emissora_web.id_emissora')
                    ->leftJoin('estado','estado.cd_estado','=','programa_emissora_web.cd_estado')
                    ->leftJoin('cidade','cidade.cd_cidade','=','programa_emissora_web.cd_cidade')
                    ->when($expressao, function ($q) use ($expressao) {
                        return $q->whereRaw("transcricao_tsv @@ to_tsquery('portuguese', '$expressao')");
                    })
                    ->when($fonte, function ($q) use ($fonte) {
                        return $q->whereIn('programa_emissora_web.id', $fonte);
                    })
                    ->when($dt_inicial, function ($q) use ($dt_inicial, $dt_final) {
                        return $q->whereBetween('videos_programa_emissora_web.horario_start_gravacao', [$dt_inicial." 00:00:00", $dt_final." 23:59:59"]);
                    })
                    ->orderBy('videos_programa_emissora_web.horario_start_gravacao','DESC')
                    ->paginate(10);

        return view('videos/videos', compact('fontes','videos','tipo_data','dt_inicial','dt_final','fonte','expressao'));
    }

    public function detalhes($id)
    {
        $video = VideoEmissoraWeb::find($id);
        return view('videos/detalhes', compact('video'));
    }

    public function getEstatisticas()
    {
        $dados = array();
        
        $dt_inicial = Carbon::now()->subDays(7);
        $dt_final = Carbon::now()->addDays(1);

        $begin = new DateTime($dt_inicial);
        $end = new DateTime($dt_final);
        $interval = DateInterval::createFromDateString('1 day');

        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $dados['label'][] =  $dt->format("d/m/Y");
            $dados['totais'][] = count(VideoEmissoraWeb::whereBetween('created_at', [$dt->format("Y-m-d")." 00:00:00", $dt->format("Y-m-d")." 23:59:59"])->get());
        }

        return response()->json($dados);
    }
}