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
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['monitoramento/exportacao/web']]) !!}
                        <div class="form-group m-3">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" required="true" value="{{ \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ \Carbon\Carbon::parse($dt_final)->format('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control select2" name="cliente" id="cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cli)
                                                <option value="{{ $cli->id }}" {{ ($cli->id == $cliente) ? 'selected' : '' }}>{{ $cli->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Exportação</label>
                                        <select class="form-control select2" name="exportacao" id="exportacao">
                                            <option value="">Selecione uma situação</option>
                                            <option value="0" {{ ($exportacao == 0) ? 'selected' : '' }}>Todas</option>
                                            <option value="1" {{ ($exportacao == 1) ? 'selected' : '' }}>Exportadas</option>
                                            <option value="2" {{ ($exportacao == 2) ? 'selected' : '' }}>Pendentes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3 w-100" style="margin-top: 25px;"><i class="fa fa-search"></i> </button>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-check mt-0">
                                        <div class="form-check">
                                            <label class="form-check-label" style="margin-top: 15px;">
                                                <input class="form-check-input" {{ ($fl_dia) ? 'checked' : '' }} type="checkbox" name="fl_dia" value="true">
                                                SOMENTE NOTÍCIAS DO DIA
                                                <span class="form-check-sign"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
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
                                        <p class="mb-1 text-muted"><strong>ID {{ $monitoramento->noticia_id }} - {{ $monitoramento->nome_cliente }} - {{ $monitoramento->nome_fonte }}</strong></p>
                                        <code>{{ $monitoramento->expressao }}</code>
                                        <p class="mb-1 mt-1" style="font-family: DejaVu Sans Mono, monospace;">
                                            <i class="fa fa-clock-o fa-1x"></i> Notícia de {{ \Carbon\Carbon::parse($monitoramento->data_noticia)->format('d/m/Y H:i:s') }} coletada em {{ \Carbon\Carbon::parse($monitoramento->created_at)->format('d/m/Y H:i:s') }} 
                                        </p>  
                                        <div class="pull-right">
                                            @if($monitoramento->exported)
                                                <span class="badge badge-pill badge-success">Exportada</span>
                                            @else
                                                <span class="badge badge-pill badge-danger">Exportação Pendente</span>
                                            @endif

                                            @if($monitoramento->path_screenshot)
                                                <span class="badge badge-pill badge-success">Print Capturado</span>
                                            @else
                                                <span class="badge badge-pill badge-danger">Aguardando Print</span>
                                            @endif
                                        </div>                                     
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-lg-12">
                            <p class="text-danger">Nenhum notícia web coletada para o intervalo de datas especificado</p>
                        </div>
                    @endforelse          
                </div>           
            </div>
        </div>
    </div>
</div> 
@endsection