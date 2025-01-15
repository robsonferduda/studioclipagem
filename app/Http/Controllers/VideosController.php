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
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','tv');
    }

    public function index(Request $request)
    {
        Session::put('sub-menu','tv-videos');
        
        $emissoras = EmissoraWeb::orderBy('nome_emissora')->get();
        $programas = ProgramaEmissoraWeb::all();

        $carbon = new Carbon();
        
        $termo = $request->termo;
        $videos = array();
        $expressao = "";
        $fonte = 0;
        $programa = 0;

        $video = VideoEmissoraWeb::query();

        if($request->isMethod('GET')){

            $dt_inicial = date('Y-m-d H:i:s');
            $dt_final = date('Y-m-d H:i:s');

            if($request->page){

                $carbon = new Carbon();
                $dt_inicial = ($request->dt_inicial) ? $request->dt_inicial : date("Y-m-d")." 00:00:00";
                $dt_final = ($request->dt_final) ? $request->dt_final : date("Y-m-d")." 23:59:59";
                $expressao = $request->expressao;
                $fonte = $request->fonte;
                $programa = $request->programa;

                $video->when($fonte, function ($q) use ($fonte) {
                    $q->whereHas('programa.emissora', function($q) use($fonte){
                        return $q->where('id_emissora', $fonte);
                    });
                });
    
                $video->when($programa, function ($q) use ($programa) {
                    $q->whereHas('programa', function($q) use($programa){
                        return $q->where('id_programa_emissora_web', $programa);
                    });
                });
    
                $video->when($expressao, function ($q) use ($expressao) {
                    return $q->whereRaw('transcricao_tsv @@ to_tsquery(\'portuguese\', ?)', [$expressao]);
                });
    
                $video->whereBetween('created_at', [$dt_inicial, $dt_final]);

                try {                

                    $videos = $video->orderBy('created_at','DESC')->paginate(10);

                } catch (\Illuminate\Database\QueryException $e) {

                    $retorno = array('flag' => false,
                                    'msg' => Utils::getDatabaseMessageByCode($e->getCode()));
                    
                    Flash::warning($retorno['msg']);
        
                } catch (Exception $e) {
                    
                    $retorno = array('flag' => true,
                                    'msg' => "Ocorreu um erro ao inserir o registro");

                    Flash::warning($retorno['msg']);
                }          
            }

        }

        if($request->isMethod('POST')){

            $carbon = new Carbon();
            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d')." 00:00:00" : date("Y-m-d")." 00:00:00";
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d')." 23:59:59" : date("Y-m-d")." 23:59:59";
            $expressao = $request->expressao;
            $fonte = $request->fonte;
            $programa = $request->programa;

            $video->when($fonte, function ($q) use ($fonte) {
                $q->whereHas('programa.emissora', function($q) use($fonte){
                    return $q->where('id_emissora', $fonte);
                });
            });

            $video->when($programa, function ($q) use ($programa) {
                $q->whereHas('programa', function($q) use($programa){
                    return $q->where('id_programa_emissora_web', $programa);
                });
            });

            $video->when($expressao, function ($q) use ($expressao) {
                return $q->whereRaw('transcricao_tsv @@ to_tsquery(\'portuguese\', ?)', [$expressao]);
            });

            $video->whereBetween('created_at', [$dt_inicial, $dt_final]);

            try {                

                $videos = $video->orderBy('created_at','DESC')->paginate(10);

            } catch (\Illuminate\Database\QueryException $e) {

                $retorno = array('flag' => false,
                                 'msg' => Utils::getDatabaseMessageByCode($e->getCode()));
                
                Flash::warning($retorno['msg']);
    
            } catch (Exception $e) {
                
                $retorno = array('flag' => true,
                                 'msg' => "Ocorreu um erro ao inserir o registro");

                Flash::warning($retorno['msg']);
            }          
        }
        
        return view('videos/videos', compact('dt_inicial','dt_final','expressao','fonte','programa','emissoras','videos','programas'));
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