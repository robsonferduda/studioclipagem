@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="nc-icon nc-lock-circle-open"></i> Permissões 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Perfis
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('permissoes') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="nc-icon nc-lock-circle-open"></i> Permissões</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Perfil</th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse($perfis as $p)
                            <tr>
                                <td>{{ $p->display_name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td>Nenhum perfil associado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>  
            </div>         
        </div>
    </div>
</div> 
@endsection