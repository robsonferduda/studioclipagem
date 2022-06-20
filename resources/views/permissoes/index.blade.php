@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-2"><i class="nc-icon nc-lock-circle-open"></i> Permissões</h4>
                </div>
                <div class="col-md-6">
                    
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Permissão</th>
                        <th>Descrição</th>
                        <th class="center">Opções</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Permissão</th>
                        <th>Descrição</th>
                        <th class="center">Opções</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($permissions as $p)
                        <tr>
                            <td>{{ $p->display_name }} ({{ $p->name }})</td>
                            <td>{{ $p->description }}</td>
                            <td class="center">
                                <a title="Usuários Habilitados" href="{{ url('permissoes/'.$p->id.'/users') }}" class="btn btn-primary btn-icon btn-link"><i class="nc-icon nc-circle-10 font-25"></i></a>
                                <a title="Perfis" href="{{ url('permissoes/'.$p->id.'/perfis') }}" class="btn btn-warning btn-icon btn-link"><i class="fa fa-group fa-2x"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 
@endsection