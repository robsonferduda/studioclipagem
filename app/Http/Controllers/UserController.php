<?php

namespace App\Http\Controllers;

use Hash;
use App\User;
use App\Utils;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        Session::put('url','usuarios');
    }

    public function index()
    {
       // $usuarios = User::whereNull('client_id')->orderBy('name')->get();
        $usuarios = User::all();
        return view('usuarios/index', compact('usuarios'));
    }

    public function show(User $user, $id)
    {
        $user = User::find($id);
        return view('usuarios/perfil', compact('user'));
    }

    public function perfil()
    {
        return view('usuarios/perfil');
    }

    public function create()
    {
        return view('usuarios/novo');
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('usuarios/editar',compact('user'));
    }

    public function store(UserRequest $request)
    {
        try {
            $request->merge(['password' => \Hash::make($request->password)]);
            User::create($request->all());
            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {
            
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('usuarios')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('usuario/create')->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $flag = $request->is_active == true ? true : false;
        $flag_senha = $request->is_password == true ? true : false;
        $request->merge(['is_active' => $flag]);

        if($flag_senha)
            $request->merge(['password' => Hash::make($request->password)]);
        else
            unset($request['password']);
    
        try {
        
            $user->update($request->all());
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
            return redirect('usuarios')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect()->route('usuario.edit', $user->id)->withInput();
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if($user->delete())
            Flash::success('<i class="fa fa-check"></i> Usuário <strong>'.$user->name.'</strong> excluído com sucesso');
        else
            Flash::error("Erro ao excluir o registro");

        return redirect('usuarios')->withInput();;
    }
}