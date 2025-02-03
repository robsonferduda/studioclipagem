@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="nc-icon nc-sound-wave ml-2"></i> Monitoramento 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Exportação Web 
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
                @forelse($dados as $key => $monitoramento)
                    <div class="col-12 col-md-12 ml-3">
                        <p>{{ $monitoramento->titulo_noticia }}</p>
                        <p><strong>{{ $monitoramento->nome_cliente }} - {{ $monitoramento->nome_fonte }}</strong></p>
                        <p style="font-family: DejaVu Sans Mono, monospace;">
                            <i class="fa fa-clock-o fa-1x"></i> Executado em {{ \Carbon\Carbon::parse($monitoramento->created_at)->format('d/m/Y H:i:s') }} 
                            <strong>{{ ($monitoramento->fl_automatico) ? 'automaticamente' : 'manualmente' }}</strong> 
                        </p>
                    </div>
                @empty
                    <div class="col-12 col-md-12 ml-3">
                        <p class="text-danger">Nenhum notícia web coletada para o intervalo de datas especificado</p>
                    </div>
                @endforelse                     
            </div>
        </div>
    </div>
</div> 
@endsection