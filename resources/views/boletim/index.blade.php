@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-2"><i class="fa fa-file-o"></i> Boletins</h4>
                </div>
                <div class="col-md-6">
                    
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
                            <th class="center">Código</th>
                            <th class="center">Data da Criação</th>
                            <th>Cliente</th>
                            <th>Boletim</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($boletins as $boletim)
                            <tr>
                                <td class="center">{{ $boletim->id }}</td>
                                <td class="center">{{ Carbon\Carbon::parse($boletim->data)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($boletim->id_cliente == 452)
                                        Offshore
                                    @else
                                        Zurich Airport
                                    @endif
                                </td>
                                <td>{{ $boletim->titulo }}</td>
                                <td class="text-center">
                                    @if($boletim->status_envio == 'enviado')
                                        <span class="badge badge-success">Enviado</span>
                                    @else
                                        <span class="badge badge-danger">Não enviado</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('boletim', $boletim->id) }}"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>   
            </div>        
        </div>
    </div>
</div> 
@endsection