@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="nc-icon nc-sound-wave ml-2"></i> Monitoramento 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Controle de Qualidade 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url()->previous() }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="nc-icon nc-minimal-left"></i> Voltar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                            <thead>
                                <tr>
                                <th>ID</th><th>Nome</th><th>Última execução</th><th>Horas desde a última</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($atrasados as $m)
                                <tr>
                                <td>{{ $m->id }}</td>
                                <td>{{ $m->nome }}</td>
                                <td>{{ $m->ultima_execucao ?? '—' }}</td>
                                <td>{{ $m->tempo_desde_ultima }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection