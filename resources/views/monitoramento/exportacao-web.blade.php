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
                <div class="col-lg-12 col-md-3 mb-12">
                    @if(count($dados))
                        <p>Foram coletadas {{ count($dados) }} notícias no período selecionado</p>
                    @endif
                    @forelse($dados as $key => $monitoramento)
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="mb-1"><a href="{{ $monitoramento->url_noticia }}" target="BLANK">{{ $monitoramento->titulo_noticia }}</a></p>
                                        <p class="mb-1"><strong>{{ $monitoramento->nome_cliente }} - {{ $monitoramento->nome_fonte }}</strong></p>
                                        <code>{{ $monitoramento->expressao }}</code>
                                        <p class="mb-1" style="font-family: DejaVu Sans Mono, monospace;">
                                            <i class="fa fa-clock-o fa-1x"></i> Coletado em {{ \Carbon\Carbon::parse($monitoramento->created_at)->format('d/m/Y H:i:s') }} 
                                        </p>  
                                        <div class="pull-right">
                                            @if($monitoramento->exported)
                                                <span class="badge badge-pill badge-success">Exportada</span>
                                            @else
                                                <span class="badge badge-pill badge-danger">Pendente</span>
                                            @endif
                                        </div>                                     
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="row">
                            <div class="col-lg-12">
                                <p class="text-danger">Nenhum notícia web coletada para o intervalo de datas especificado</p>
                            </div>
                        </div>
                    @endforelse          
                </div>           
            </div>
        </div>
    </div>
</div> 
@endsection