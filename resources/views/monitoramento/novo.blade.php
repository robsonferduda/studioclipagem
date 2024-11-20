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

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="expressao" class="form-label">Expressão de Busca</label>
                                        <textarea class="form-control" id="expressao" rows="3"></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="button" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!}
                </div>   
            </div>
            <div class="row">
                <div class="col col-sm-12 col-md-12 col-lg-12">
                    <h6 class="m-3">Resultados da Busca</h6>
                    <div class="resultados m-3"></div>
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

        $("#btn-find").click(function(){

            var expressao = $("#expressao").val();

            $.ajax({url: host+'/monitoramento/filtrar',
                    type: 'POST',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                            "expressao": expressao
                    },
                    beforeSend: function() {
                        
                    },
                    success: function(data) {

                        $(".resultados").empty();

                        if(data.length == 0){

                            $(".resultados").append('<span class="text-danger">Nenhum resultado encontrado</span>');

                        }else{
                            $.each(data, function(k, v) {
                                $(".resultados").append('<p>'+v.id+'</p>');
                            });
                        }                            
                    },
                    complete: function(){
                        
                    }
            });

        });

    });
</script>
@endsection