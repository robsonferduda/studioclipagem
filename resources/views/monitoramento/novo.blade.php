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
                                        <select class="form-control select2" name="cliente" id="cliente">
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
                                        <select class="form-control periodo" name="periodo" id="periodo">
                                            <option value="">Selecione um período</option>
                                            @foreach ($periodos as $periodo)
                                                <option value="{{ $periodo->slug }}">{{ $periodo->periodo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control dt_inicial_relatorio dt_periodo">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control dt_final_relatorio dt_periodo">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="expressao" class="form-label">Expressão de Busca</label>
                                        <textarea class="form-control" id="expressao" rows="3"></textarea>
                                    </div>
                                    <p class="mb-1"><strong>Observações</strong></p>
                                    <p class="mt-1 mb-1"><strong>&</strong>: operador de busca equivalente ao "E"</p>
                                    <p class="mt-1 mb-1"><strong>|</strong>: operador de busca equivalente ao "OU"</p>
                                    <p class="mt-1 mb-1"><strong><-></strong>: operador de distância entre palavras, onde o - é a distância entre elas</p>
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
                    <h6 class="m-3 label-resultado">Resultados da Busca</h6>
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

        $(".periodo").change(function(){
            var periodo = $(this).val();
            inicializaDatas(periodo);        
        });

        function inicializaDatas(periodo)
        {
            var dataFinal = new Date();
            var dataInicial = new Date();    
                
            if(periodo != 'personalizado'){
                dataInicial.setDate(dataFinal.getDate() - (periodo - 1));
            }else{
                $(".dt_inicial_relatorio").focus();
            }

            $(".dt_inicial_relatorio").val(formataData(dataInicial));
            $(".dt_final_relatorio").val(formataData(dataFinal));

            $(".label_data_inicial").html(formataData(dataInicial));
            $(".label_data_final").html(formataData(dataFinal));   
        }

        function formataData(data)
        {
            var dia = String(data.getDate()).padStart(2, '0');
            var mes = String(data.getMonth() + 1).padStart(2, '0');
            var ano = data.getFullYear();

            return dia + '/' + mes + '/' + ano;
        }

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

                        $(".label-resultado").css("display","block");
                        $(".resultados").empty();

                        if(data.length == 0){

                            $(".resultados").append('<span class="text-danger">Nenhum resultado encontrado</span>');

                        }else{

                            $(".label-resultado").empty();
                            $(".label-resultado").append("Resultados da Busca"+" - Foram encontrados "+data.length+" registros");

                            $.each(data, function(k, v) {
                               // $(".resultados").append('<p><a href="'+v.url_noticia+'" target="BLANK">'+v.titulo_noticia+'</a></p>');
                                $(".resultados").append('<div><p class="fts_detalhes" data-chave="txt-'+k+'" data-id="'+v.id+'">'+v.titulo_noticia+'</p><div id="txt-'+k+'"></div></div>');
                            });
                        }                            
                    },
                    error: function(){
                        $(".resultados").empty();
                        $(".resultados").append('<span class="text-danger">Erro ao executar o string de busca</span>');
                    },
                    complete: function(){
                        
                    }
            });

        });

        $('body').on('click', '.fts_detalhes', function() {

            var id = $(this).data("id");
            var chave = "#"+$(this).data("chave");
            
            $.ajax({url: host+'/monitoramento/filtrar/conteudo/'+id,
                    type: 'GET',
                    contentType: "application/json",
                    dataType: "json",
                    beforeSend: function() {
                        
                    },
                    success: function(data) {
                        $(chave).html(data[0].texto);                                         
                    },
                    error: function(){
                        
                    },
                    complete: function(){
                        
                    }
            });

        });
        
    });
</script>
@endsection