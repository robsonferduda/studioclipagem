@extends('layouts.app')
@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-2">
                            <i class="nc-icon nc-circle-10"></i> Usuários
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Perfil
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('usuarios') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-table"></i> Usuários</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="author">
                    <a href="{{ url('usuario', $user->id) }}">
                        <h5 class="title">{{ $user->name }}</h5>
                    </a>
                    <p class="description">
                        {{ $user->email }}
                    </p>
                    <p>
                        Cadastrado em {{  \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i:s') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection