<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use File;
use App\Models\Cliente;
use Carbon\Carbon;
use App\Models\NoticiaCliente;
use App\Models\FonteImpressa;
use App\Models\FilaImpresso;
use App\Models\JornalImpresso;
use App\Models\NoticiaImpresso;
use App\Models\Fonte;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Jobs\ProcessarImpressos as JobProcessarImpressos;
use App\Models\Cidade;
use App\Models\Estado;
use App\Utils;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class NoticiaImpressaController extends Controller
{
    private $data_atual;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data_atual = session('data_atual');
        Session::put('url','impresso');
    }

    public function index(Request $request)
    {
        
    }

    public function show(Request $request)
    {
        
    }

    public function cadastrar()
    {
        Session::put('sub-menu','noticia-impressa-cadastrar');
        $fontes = FonteImpressa::all();
        
        return view('noticia-impressa/cadastrar', compact('fontes'));
    }

    public function copiar($cliente, $id_noticia)
    {
        //Notícia original
        $noticia_original = JornalImpresso::find($id_noticia);

        //Vínculo original
        $vinculo = NoticiaCliente::where('noticia_id', $id_noticia)->where('tipo_id',1)->where('cliente_id', $cliente)->first();

        if(!$noticia_original->fl_copia){

            $noticia = $noticia_original->replicate();
            $noticia->noticia_original_id = $noticia_original->id;
            $noticia->fl_copia = true;
            $noticia->save();
    
            $vinculo->noticia_id = $noticia->id;
            $vinculo->save();
            
        }else{
            $noticia = $noticia_original;
        }

        return redirect('noticia-impressa/cliente/'.$cliente.'/editar/'.$noticia->id);
    }

    public function editar($cliente, $id_noticia)
    {
        $clientes = Cliente::with('pessoa')
                    ->join('pessoas', 'pessoas.id', '=', 'clientes.pessoa_id')
                    ->orderBy('nome')
                    ->get();

                    dd($cleintes);

        $noticia = JornalImpresso::find($id_noticia);
        $vinculo = NoticiaCliente::where('noticia_id', $id_noticia)->where('tipo_id',1)->where('cliente_id', $cliente)->first();

        return view('noticia-impressa/editar', compact('noticia','clientes','vinculo'));
    }

    public function update(Request $request, $id)
    {
        $noticia = NoticiaImpresso::find($id);

        try {

            $valor_retorno = $request->nu_altura * $request->nu_colunas * $noticia->fonte->retorno_midia;

            $request->merge(['valor_retorno' => $valor_retorno]);
            
            $noticia->update($request->all());
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
            return redirect('jornal-impresso/noticias')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect()->route('')->withInput();
        }
    }

    //Upload do CROP da imagem
    public function upload(Request $request)
    {
        $image = $request->file('picture');
        $fileInfo = $image->getClientOriginalName();
        $filesize = $image->getSize()/1024/1024;
        $filename = pathinfo($fileInfo, PATHINFO_FILENAME);
        $extension = "jpeg";
        $file_name= $filename.'-'.time().'.'.$extension;
        $image->move(public_path('img/noticia-impressa/recorte'),$file_name);

        //$noticia = JornalImpresso::find($request->id);
        //$noticia->print = $file_name;
        //$noticia->save();

        return $file_name;
    }
}