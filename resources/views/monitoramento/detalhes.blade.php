@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="nc-icon nc-sound-wave ml-2"></i> Monitoramento 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Dashboard 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('/') }}" class="btn btn-primary pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <button class="btn btn-primary pull-right mr-3"><i class="fa fa-plus"></i> Novo</button>
                    <a href="{{ url('monitoramento/executar') }}" class="btn btn-warning pull-right mr-3"><i class="fa fa-bolt"></i> Executar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">              
                    @foreach ($monitoramentos as $key => $monitoramento)
                        
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card card-stats ml-3 mr-3" style="border: 1px solid #f1f1f1;">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-md-12">                                           
                                                <p>
                                                    <h6>
                                                        {{ $monitoramento->cliente->pessoa->nome }} 
                                                        <span class="pull-right">
                                                        @if($monitoramento->fl_ativo)
                                                        <i class="fa fa-circle text-success mr-1"></i><a href="{{ url('monitoramento/'.$monitoramento->id.'/atualizar-status') }}">Ativo </a>
                                                        @else
                                                            <i class="fa fa-circle text-danger mr-1"></i><a href="{{ url('monitoramento/'.$monitoramento->id.'/atualizar-status') }}">Inativo </a>
                                                        @endif
                                                        </span>
                                                    </h6>
                                                    <span class="badge badge-{{ $monitoramento->tipo->ds_tipo_color }}">
                                                        {{ $monitoramento->tipo->ds_tipo_fonte }}
                                                    </span>
                                                </p>
                                                <p class="card-title"></p>
                                                <p class="text-bold">"{{ $monitoramento->expressao }}"</p>                                            
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer ">
                                        <hr>
                                        <div class="stats">
                                            <i class="fa fa-refresh"></i>Última atualização em {{ \Carbon\Carbon::parse($monitoramento->created_at)->format('d/m/Y H:i:s') }}
                                            <div class="pull-right">
                                                <a href="{{ url('monitoramento/'.$monitoramento->id.'/execucoes') }}" class="btn btn-info btn-fill btn-icon btn-sm" style="border-radius: 30px;">
                                                    <i class="fa fa-clock-o fa-3x text-white"></i>
                                                </a>
                                                <a href="{{ url('monitoramento/'.$monitoramento->id.'/executar') }}" class="btn btn-warning btn-fill btn-icon btn-sm" style="border-radius: 30px;">
                                                    <i class="fa fa-bolt fa-3x text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    @endforeach                        
            </div>
        </div>
    </div>
</div> 
@endsection