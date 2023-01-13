<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Fonte;
use App\Models\JornalWeb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class JornalWebController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
    }

    public function index(Request $request)
    {
        $fontes = Fonte::where('tipo_fonte_id',1)->get();

        if($request->isMethod('POST')){

            $carbon = new Carbon();
            $dt_inicial = ($request->dt_inicial) ? $carbon->createFromFormat('d/m/Y', $request->dt_inicial)->format('Y-m-d') : date("Y-m-d");
            $dt_final = ($request->dt_final) ? $carbon->createFromFormat('d/m/Y', $request->dt_final)->format('Y-m-d') : date("Y-m-d");

            $dados = JornalWeb::whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->orderBy('id_fonte')->paginate(10);

        }

        if($request->isMethod('GET')){

            if($request->dt_inicial){
                $dt_inicial = $request->dt_inicial;
                $dt_final = $request->dt_final;

                $dados = JornalWeb::whereBetween('dt_clipagem', [$dt_inicial, $dt_final])->orderBy('id_fonte')->paginate(10);
            }else{
                $dt_inicial = date('d/m/Y');
                $dt_final = date('d/m/Y');
                $dados = JornalWeb::where('dt_clipagem', $this->data_atual)->orderBy('id_fonte')->paginate(10);
            }

        }

        return view('jornal-web/index',compact('fontes','dados','dt_inicial','dt_final'));
    }
}