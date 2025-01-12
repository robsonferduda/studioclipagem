@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card load-busca">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="nc-icon nc-sound-wave ml-2"></i> Monitoramento 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Editar 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('monitoramento') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="nc-icon nc-minimal-left"></i> Voltar</a>
                    <a href="{{ url('monitoramento') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="nc-icon nc-sound-wave ml-2"></i> Monitoramentos</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row mr-1">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['monitoramento/update']]) !!}
                        <div class="form-group m-3">
                            <div class="row">
                                <input type="hidden" name="id" value="{{ $monitoramento->id }}">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control select2" name="id_cliente" id="id_cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ ($monitoramento->id_cliente == $cliente->id) ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 mt-3">
                                    <p class="mb-1">Selecione as Mídias</p>
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" {{ ($monitoramento->fl_impresso) ? 'checked' : '' }} name="fl_impresso" id="fl_impresso" value="true">
                                            IMPRESSO
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" {{ ($monitoramento->fl_web) ? 'checked' : '' }} name="fl_web" id="fl_web" value="true">
                                            WEB
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" {{ ($monitoramento->fl_radio) ? 'checked' : '' }} name="fl_radio" id="fl_radio" value="true">
                                            RÁDIO
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" {{ ($monitoramento->fl_tv) ? 'checked' : '' }} name="fl_tv" id="fl_tv" value="true">
                                            TV
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 mt-3">
                                    <div class="form-group">
                                        <label for="expressao" class="form-label">Expressão de Busca <span class="text-danger">Campo obrigatório</span></label>
                                        <textarea class="form-control" name="expressao" id="expressao" rows="3">{{ $monitoramento->expressao }}</textarea>
                                    </div>
                                </div>
                            </div>               
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                                <a href="{{ url('monitoramento') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                            </div>     
                        </div>
                    {!! Form::close() !!}
                </div>   
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    $( document ).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');
        var token = $('meta[name="csrf-token"]').attr('content');

    });
</script>
@endsection