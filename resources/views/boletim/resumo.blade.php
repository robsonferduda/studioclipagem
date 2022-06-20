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
                <table class="table table-hover">
                    <thead class="">
                        <tr>
                            <th>Email</th>
                            <th>Mensagem</th>
                            <th class="center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>{{ $log['email'] }}</td>
                                <td>{{ $log['msg'] }}</td>
                                <td class="text-center">
                                    @if($log['tipo'] == 'success')
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
                    <a href="{{ url('boletim',$boletim->id) }}" class="btn btn-primary"><i class="fa fa-back"></i> Voltar Para Boletim</a>
                </div>
            </div>        
        </div>
    </div>
</div> 
@endsection