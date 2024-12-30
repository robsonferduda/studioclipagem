@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row ml-1">
                <div class="col-md-6">
                    <h4 class="card-title"><i class="fa fa-group"></i> Perfis</h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('perfil/novo') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-group"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>
            </div>
            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Perfil</th>
                        <th>Chave</th>
                        <th>Descrição</th>
                        <th class="center" style="width: 20%">Ações</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Perfil</th>
                        <th>Chave</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td><span class="badge" style="background: {{ $role->display_color }}; border-color: {{ $role->display_color }};">{{ $role->display_name }}</span></td>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->description }}</td>
                            <td class="center">
                                <a title="Permissões" href="{{ url('role/permissions/'.$role->id) }}" class="btn btn-warning btn-link btn-icon"><i class="fa fa-lock font-25"></i></a>
                                <a title="Editar" href="{{ route('perfis.edit',$role->id) }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                            </td>
                        </tr>  
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 
@endsection