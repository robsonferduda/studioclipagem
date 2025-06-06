@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-2">
                        <i class="nc-icon nc-circle-10"></i> Usuários
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Listar
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('usuario/create') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <table id="datatable_users" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                        <th class="disabled-sorting text-center">Situação</th>
                        <th class="disabled-sorting text-center">Ações</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                        <th class="disabled-sorting text-center">Situação</th>
                        <th class="disabled-sorting text-center">Ações</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($usuarios as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                                @forelse($u->roles as $role)
                                    <span class="badge" style="background: {{ $role->display_color }}; border-color: {{ $role->display_color }};">{{ $role->display_name }}</span>
                                @empty
                                    <span class="text-danger">Nenhum perfil associado</span>
                                @endforelse
                            </td>
                            <td class="disabled-sorting text-center">{!! ($u->is_active) ? '<span class="badge badge-pill badge-success">ATIVO</span>' : '<span class="badge badge-pill badge-danger">INATIVO</span>' !!}</td>
                            <td class="text-center">
                                <a title="Histórico do Usuário" href="{{ url('usuario/historico',$u->id) }}" class="btn btn-success btn-link btn-icon"><i class="fa fa-clock-o fa-2x"></i></a>
                                <a title="Dados do Usuário" href="{{ url('usuario',$u->id) }}" class="btn btn-warning btn-link btn-icon"><i class="nc-icon nc-circle-10 font-25"></i></a>
                                <a title="Editar" href="{{ route('usuario.edit',$u->id) }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                <form class="form-delete" style="display: inline;" action="{{ route('usuario.destroy',$u->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button title="Excluir" type="submit" class="btn btn-danger btn-link btn-icon button-remove" title="Delete">
                                        <i class="fa fa-times fa-2x"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() {

            var host =  $('meta[name="base-url"]').attr('content');

            $('#datatable_users').DataTable({
                "pagingType": "full_numbers",  
                "ordering": true,              
                "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "Todos"]
                ],
                responsive: true,
                language: {
                search: "_INPUT_",
                searchPlaceholder: "Filtrar",
                },
                columnDefs: [
                    { type: 'chinese-string', targets: 0 }
                ]

            });
            
        });
    </script>
@endsection