<?php

namespace App\Http\Controllers;

use Hash;
use App\User;
use App\Client;
use App\ClientPageMonitor;
use App\FbReaction;
use App\Role;
use App\Utils;
use App\SocialMedia;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\ClientRequest;
use Illuminate\Support\Facades\Session;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','clientes');
    }

    public function index()
    {
        $clientes = Client::orderBy('name')->get();
        return view('clientes/index', compact('clientes'));
    }

    public function show(Client $client)
    {
        return view('clientes/detalhes', compact('client'));
    }

    public function emails($id)
    {
        $client = Client::with('user')->find($id);
        return view('clientes/emails', compact('client'));
    }

    public function create(Request $request)
    {
        return view('clientes/novo');
    }

    public function edit(Client $client)
    {
        $client = Client::with('user')->find($client->id);
        return view('clientes/editar',compact('client'));
    }

    public function json()
    {
        $clientes = Client::select('id','name')->orderBy('name')->get();
        return response()->json($clientes);
    }

    public function selecionar(Request $request)
    {
        $cliente = Client::find($request->cliente);
        $cliente_session = array('id' => $cliente->id, 'nome' => $cliente->name);
        Session::put('cliente', $cliente_session);
    }

    public function store(ClientRequest $request)
    {
        //Verifica se o email não é usado por um usuário ou cliente antes de inserir na base de dados
        $user = User::where('email', $request->email)->first();
        if($user or isset($user->client)){
            Flash::warning('<i class="fa fa-warning"></i> Este email já foi cadastrado no sistema.');
            return redirect('client/create')->withInput();
        }

        try {
            $request->merge(['password' => \Hash::make($request->password)]);
            $cliente = Client::create($request->all());
            if($cliente){

                $request->merge(['client_id' => $cliente->id]);
                $user = User::create($request->all());

                $role = Role::where('name','cliente')->first();

                if(!$user->hasRole($role->name))
                    $user->attachRole($role); 

                $retorno = array('flag' => true,
                                 'msg' => "Dados inseridos com sucesso");
            }

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {
            
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('clientes')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('client/create')->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $client = Client::with('user')->find($id);

        $ativo = $request->is_active == true ? true : false;
        $flag_password = $request->is_password == true ? true : false;

        //Seta os valores booleanos na requisição
        $request->merge(['is_active' => $ativo]);

        if($flag_password)
            $request->merge(['password' => Hash::make($request->password)]);
        else
            unset($request['password']); 
                
        try {
        
            $client->update($request->all());
            $client->user->update($request->all());

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
            return redirect('clientes')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect()->route('client.edit', $user->id)->withInput();
        }
    }

    public function destroy(Client $client)
    {
        if($client->delete())
            Flash::success('<i class="fa fa-check"></i> Usuário <strong>'.$client->name.'</strong> excluído com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('clientes')->withInput();
    }

    public function getFacebookAccounts($client)
    {
        $client = Client::with('fbAccount')->find($client);
        return view('clientes/conexoes', compact('client'));
    }

    public function getHashtags($client)
    {
        $client = Client::with(['hashtags' => function($query){
            $query->withCount('mediasTwitter')->withCount('medias')->withCount('pagePosts')->withCount('pagePostsComments');
        }])->find($client);

        $social_medias = SocialMedia::where('fl_hashtag',true)->orderBy('name')->get();
        return view('clientes/hashtags', compact('client','social_medias'));
    }

    public function connectedtPages($client) 
    {
        $client = Client::with(['pagesMonitor' => function($query){
            $query->with(['fbPagesPost' => function($query){
                $query->with('reactions');
            }]);
        }])->where('id', $client)->first();

        $reactions = FbReaction::all();

        return view('clientes/connected-pages', compact('client', 'reactions'));
    }

}