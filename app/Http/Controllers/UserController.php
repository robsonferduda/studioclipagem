<?php

namespace App\Http\Controllers;

use DB;
use Hash;
use App\User;
use App\Role;
use App\Audits;
use App\Models\RoleUser;
use App\Utils;
use App\Models\Cliente;
use App\Models\Pessoa;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','usuarios');
    }

    public function index()
    {
        $usuarios = User::orderBy("name")->get();
        $perfis = Role::orderBy("display_name")->get();

        return view('usuarios/index', compact('usuarios','perfis'));
    }

    public function show(User $user, $id)
    {
        $user = User::find($id);
        return view('usuarios/perfil', compact('user'));
    }

    public function online()
    {
        Session::put('url','online');

        $online = Audits::where(function ($query) {
                $query->where('event', 'login')
                    ->orWhere('event', 'logout');
            })
            ->orderBy('user_id')
            ->orderByDesc('created_at')
            ->get()
            ->unique('user_id') // Garante que só pegamos o último evento por usuário
            ->filter(function ($log) {
                return $log->event === 'login';
            });

        $timeout = now()->subMinutes(30); // Timeout de 30 minutos

        $online = User::where('last_active_at', '>=', $timeout)->get();
        
        $recentActivities = Audits::where('created_at', '>=', now()->subHours(1))
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        return view('usuarios/online', compact('online','recentActivities'));
    }

    public function perfil()
    {
        return view('usuarios/perfil');
    }

    public function create()
    {
        $perfis = Role::orderBy("display_name")->get();

        return view('usuarios/novo',compact('perfis'));
    }

    public function edit($id)
    {
        $perfis = Role::orderBy("display_name")->get();
        $user = User::find($id);

        return view('usuarios/editar',compact('user','perfis'));
    }

    public function insereClientes()
    {
        $clientes = Cliente::all();

        foreach ($clientes as $key => $cliente) {

            if($cliente->usuario_tmp){

                $user = array('name' => $cliente->nome,
                            'email' => $cliente->usuario_tmp,
                            'password' => \Hash::make($cliente->senha_tmp),
                            'client_id' => $cliente->id,
                            'is_active' => true);

                $user = User::create($user);

                if($user){

                    $user_role = array('role_id' => 2, 'user_id' => $user->id);

                    RoleUser::create($user_role);
                }
            }

        }

        
    }

    public function store(UserRequest $request)
    {
        $flag = $request->is_active == true ? true : false;

        DB::beginTransaction();
        try {

            $user = array('name' => $request->name,
                          'email' => $request->email,
                          'password' => \Hash::make($request->password),
                          'is_active' => $flag);

            $user = User::create($user);

            if($user){

                $user_role = array('role_id' => $request->role, 'user_id' => $user->id);

                RoleUser::create($user_role);
            }

            DB::commit();

            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {

            DB::rollback();
            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {
            
            DB::rollback();
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

            if($user->role){

                $role_user = RoleUser::where('role_id', $user->role->role_id)->where('user_id', $user->id)->first();

                $role_user->role_id = $request->role;
                $role_user->save();
                
            }else{

                $user_role = array('role_id' => $request->role, 'user_id' => $user->id);

                RoleUser::create($user_role);

            }

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

        return redirect('usuarios')->withInput();
    }
}