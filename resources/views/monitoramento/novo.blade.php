@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="nc-icon nc-sound-wave ml-2"></i> Monitoramento 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrar 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('monitoramento') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="nc-icon nc-minimal-left"></i> Voltar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['buscar-monitoramento']]) !!}
                        <div class="form-group m-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control select2" name="buscar_monitoramento" id="buscar_monitoramento">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Período</label>
                                        <select class="form-control" name="buscar_monitoramento" id="buscar_monitoramento">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($periodos as $periodo)
                                                <option value="{{ $periodo->slug }}">{{ $periodo->periodo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!}
                </div>   
            </div>
        </div>
    </div>
</div> 
@endsection