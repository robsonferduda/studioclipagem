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
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Cliente</th>
                                <th>Nome</th>
                                <th>Última execução</th>
                                <th d class="center">Horas desde a última</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($atrasados as $m)
                                <tr>
                                <td>
                                    <a title="Editar" class="text-primary" target="_BLANK" href="{{ url('monitoramento/'.$m->id.'/editar') }}" class="" style="">{{ $m->id }}</a>
                                </td>
                                <td>
                                    @if ($m->fl_web) <span class="badge badge-danger">Web</span> @endif
                                    @if ($m->fl_impresso) <span class="badge badge-success">Impresso</span> @endif
                                    @if ($m->fl_radio) <span class="badge badge-primary">Rádio</span> @endif
                                    @if ($m->fl_tv) <span class="badge badge-warning">TV</span> @endif
                                </td>
                                <td>{{ $m->cliente_nome }}</td>
                                <td>{{ $m->nome }}</td>
                                <td>{{ \Carbon\Carbon::parse($m->ultima_execucao)->format('d/m/Y H:i:s') ?? '—' }}</td>
                                <td class="center">{{ $m->tempo_desde_ultima }}</td>
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