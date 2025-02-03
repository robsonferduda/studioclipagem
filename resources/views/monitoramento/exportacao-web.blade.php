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
                <div class="col-12 col-md-12 ml-3">
                    @forelse($dados as $key => $monitoramento)
                        <p style="font-family: DejaVu Sans Mono, monospace;"><i class="fa fa-clock-o fa-1x"></i> Executado em {{ \Carbon\Carbon::parse($monitoramento->created_at)->format('d/m/Y H:i:s') }} <strong>{{ ($monitoramento->fl_automatico) ? 'automaticamente' : 'manualmente' }}</strong> {!! (!$monitoramento->fl_automatico and $monitoramento->usuario) ? 'por <strong>'.$monitoramento->usuario->name.'</strong>' : '' !!} encontrou <strong>{{ $monitoramento->total_vinculado }}</strong> registros.</p>
                    @empty
                        <p class="text-danger">Nenhum notícia web coletada para o intervalo de datas especificado</p>
                    @endforelse           
                </div>          
            </div>
        </div>
    </div>
</div> 
@endsection