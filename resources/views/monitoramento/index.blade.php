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
                    <button class="btn btn-primary pull-right mr-3"><i class="fa fa-plus"></i> Novo</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['buscar-monitoramento']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Buscar Monitoramento</label>
                                        <select class="form-control select2" name="regra" id="regra">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($fontes as $fonte)
                                                <option value="{{ $fonte->id }}">{{ $fonte->ds_fonte }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!}
                    
                    <div id="accordion" role="tablist" aria-multiselectable="true" class="card-collapse ml-3 mr-3">
                        @foreach ($monitoramentos as $key => $monitoramento)
                            <div class="card card-plain">
                                <div class="card-header" role="tab" id="headingOne">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#tab{{ $monitoramento->id }}" aria-expanded="false" aria-controls="{{ $monitoramento->id }}">
                                        @if($monitoramento->fl_ativo)
                                            <i class="fa fa fa-clock-o text-success float-left"></i>{{ $monitoramento->cliente->nome }}
                                        @else
                                            <i class="fa fa fa-ban text-danger float-left"></i>{{ $monitoramento->cliente->nome }}
                                        @endif
                                        
                                        <i class="nc-icon nc-minimal-down"></i>
                                    </a>
                                </div>
                                <div id="tab{{ $monitoramento->id }}" class="collapse" role="tabpanel" aria-labelledby="headingOne">
                                    <div class="card-body">
                                        {{ $monitoramento->expressao }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection