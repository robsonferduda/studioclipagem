@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-2">
                        <i class="fa fa-file-o"></i> Boletins
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Resumo do Envio
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('boletins') }}" class="btn btn-primary pull-right"><i class="fa fa-file-o"></i> Boletins</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                <h5 class="mb-1">{{ $boletim->titulo }} | <small>{{ $boletim->cliente->nome }}</small></h5>
                <p>Total de envios: {{ count($boletim->envios) }}</p>
            </div>
            <div class="col-md-12">
                <table class="table table-hover">
                    <thead class="">
                        <tr>
                            <th>Data Envio</th>
                            <th>Email</th>
                            <th>Mensagem</th>
                            <th>Responsável</th>
                            <th class="center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($boletim->envios as $envio)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($envio->created_at)->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $envio->ds_email }}</td>
                                <td>{{ $envio->ds_mensagem }}</td>
                                <td>{{ ($envio->usuario) ? $envio->usuario->name : 'Envio Automático' }}</td>
                                <td class="text-center">
                                    @if($envio->id_situacao == 2)
                                        <span class="badge badge-success">Enviado</span>
                                    @else
                                        <span class="badge badge-danger">Não enviado</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>   
                <div class="center">
                    <a href="{{ url('boletim/'.$boletim->id.'/detalhes') }}" class="btn btn-primary"><i class="fa fa-back"></i> Voltar Para Boletim</a>
                </div>
            </div>        
        </div>
    </div>
</div> 
@endsection