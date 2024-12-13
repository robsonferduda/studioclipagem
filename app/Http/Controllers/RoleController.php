<?php

namespace App\Http\Controllers;

use Auth;
use App\Utils;
use App\Role;
use App\Permission;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','perfis');
    }
    
    public function index()
    {
        $roles = Role::orderBy('name')->get();
        return view('perfis/index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::get()->pluck('name', 'name');
        return view('roles/create', compact('permissions'));
    }

    public function store(Request $request)
    {
        try {
            Role::create($request->all());

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
            return redirect('roles')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('roles/create')->withInput();
        }
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('roles/edit', compact('role'));
    }

    public function update(Request $request, $id)
    {

        $role = Role::findOrFail($id);

        try {
            $role->update($request->all());

            $retorno = array(
                'flag' => true,
                'msg' => "Dados atualizados com sucesso"
            );
        } catch (\Illuminate\Database\QueryException $e) {
            $retorno = array(
                'flag' => false,
                'msg' => Utils::getDatabaseMessageByCode($e->getCode())
            );
        } catch (Exception $e) {
            $retorno = array(
                'flag' => true,
                'msg' => "Ocorreu um erro ao atualizar o registro"
            );
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('roles')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect()->route('role.edit', $role->id)->withInput();
        }
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role and $role->delete()) {
            Flash::success("Registro excluído com sucesso");
        } else {
            Flash::error("Erro ao excluir registro");
        }

        return redirect('roles');
    }

    public function permissions($role)
    {
        $role = Role::findOrFail($role);
        $permissions = Permission::orderBy('display_name')->get();

        return view('perfis/permissions', compact('role','permissions'));
    }

    public function show($id)
    {
        $page_title = 'Início';
        $page_description = 'Painel Administrativo';

        $role = Role::findOrFail($id);

        return view('roles/detalhes', compact('page_title', 'page_description','role'));
    }

    public function addPermission(Request $request, $id)
    {
        $permissions = $request->input('permission') ? $request->input('permission') : [];

        $role = Role::findOrFail($id);
        if($role->syncPermissions($permissions))
            Flash::success('<i class="fa fa-check"></i> Permissões atualizadas com sucesso');
        else
            Flash::error('<i class="fa fa-times"></i> Erro ao atribuir permissões');
        
        return redirect('role/permissions/'.$id);
    }
}