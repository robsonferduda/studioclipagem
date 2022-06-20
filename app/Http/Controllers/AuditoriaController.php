<?php

namespace App\Http\Controllers;

use Auth;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class AuditoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','auditoria');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $auditorias = Audit::with('user')
                           ->whereNotNull('user_id')->orderBy('created_at','ASC')->get();
 
            return DataTables::of($auditorias)
            ->addColumn('data', function ($auditoria) {
                return date('d/m/Y H:i', strtotime($auditoria->created_at));
            })
            ->addColumn('nivel', function ($auditoria) {
                $roles = [];
                foreach($auditoria->user->roles()->get() as $role){
                    $roles[] = ['display_name' => $role->display_name, 'display_color' => $role->display_color];
                }
                return $roles;
            })    
            ->addColumn('usuario', function ($auditoria) {
                return  ($auditoria->user) ? $auditoria->user->name : 'Usuário não identificado';
            })
            ->addColumn('operacao', function ($auditoria) {
                return  ($auditoria->user) ? $auditoria->event : 'Evento não identificado';
            })       
            ->addColumn('acoes', function ($auditoria) {
                return  '<a href="auditoria/detalhes/'.$auditoria->id.'"><i class="fa fa-eye"></i> Ver</a>';
            })
            ->rawColumns(['acoes'])
            ->make(true);
        }

        return view('auditoria/index');
    }

    public function show($id)
    {
        $auditoria = Audit::with('user')->where('id',$id)->first();
        return view('auditoria/detalhes',compact('auditoria'));
    }

}