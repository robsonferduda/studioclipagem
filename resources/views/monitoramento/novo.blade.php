@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card load-busca">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="nc-icon nc-sound-wave ml-2"></i> Monitoramento 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Novo 
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
            <div class="row mr-1">
                <div class="col-sm-12 col-md-6 col-lg-6">
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
                                        <label for="expressao" class="form-label">Expressão de Busca <span class="text-danger">Campo obrigatório</span></label>
                                        <textarea class="form-control" id="expressao" rows="3"></textarea>
                                    </div>
                                    <p class="mb-1"><strong>Observações</strong></p>
                                    <p class="mt-1 mb-1"><strong>&</strong>: operador de busca equivalente ao "E"</p>
                                    <p class="mt-1 mb-1"><strong>|</strong>: operador de busca equivalente ao "OU"</p>
                                    <p class="mt-1 mb-1"><strong>!</strong>: operador de busca equivalente ao "NÃO"</p>
                                    <p class="mt-1 mb-1"><strong><-></strong>: operador de distância entre palavras, onde o - é a distância entre elas</p>
                                    <p class="mt-1 mb-1"><strong>Exemplo de busca</strong>: <span>queda<2>energia & Celesc & !Jaguaruna</span>: Todas as notícias relacionadas à queda de energia que citam a Celesc, exceto em Jaguaruna</p>
                                </div>
                                <div class="col-md-12 msg-alerta">

                                </div>
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="button" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!}
                </div>   
                <div class="col-lg-6 col-md-6">
                    <div class="nav-tabs-navigation">
                        <div class="nav-tabs-wrapper">
                        <ul id="tabs" class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#panel_web" role="tab" aria-expanded="true" aria-selected="false"><i class="fa fa-globe"></i> Web <span class="monitoramento-total monitoramento-total-web">0</span></a>
                            </li>
                            <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#panel_impresso" role="tab" aria-expanded="false"><i class="fa fa-newspaper-o"></i> Impressos <span class="monitoramento-total monitoramento-total-impresso">0</span></a>
                            </li>
                            <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#panel_radio" role="tab" aria-expanded="false" aria-selected="true"><i class="fa fa-volume-up"></i> Rádio <span class="monitoramento-total monitoramento-total-radio">0</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#panel_tv" role="tab" aria-expanded="false" aria-selected="true"><i class="fa fa-volume-up"></i> TV <span class="monitoramento-total monitoramento-total-tv">0</span></a>
                            </li>
                        </ul>
                        </div>
                    </div>
                    <div id="my-tab-content" class="tab-content">
                        <div class="tab-pane active" id="panel_web" role="tabpanel" aria-expanded="true">
                            <div id="accordion" role="tablist" aria-multiselectable="true" class="card-collapse">
                                <div class="row cabecalho-busca">
                                    <div class="col-md-6">
                                        <p class="card-title mb-0">Foram encontradas <strong class="monitoramento-total-web"></strong> notícias</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">
                                            <span class="badge badge-pill badge-primary">
                                                Todas as fontes
                                            </span>
                                        </div>
                                    </div>
                                </div>   
                                <div class="row cabecalho-aguardando-busca">
                                    <div class="col-md-6">
                                        <span class="text-info">Aguardando critérios de busca</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="panel_impresso" role="tabpanel" aria-expanded="false">
                        
                        </div>
                        <div class="tab-pane" id="panel_radio" role="tabpanel" aria-expanded="false">
                        
                        </div>
                        <div class="tab-pane" id="panel_tv" role="tabpanel" aria-expanded="false">
                            
                        </div>
                    </div>
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
            var dt_inicial = $(".dt_inicial_relatorio").val();
            var dt_final = $(".dt_final_relatorio").val();
            var flag = false;

            $(".msg-alerta").empty(); //Limpa as mensagens de erro

            if(!dt_inicial & !dt_final){
                flag = false;
                $(".msg-alerta").append('<p class="text-danger mb-0">Obrigatório selecionar uma data de início e uma data de fim. </p>');
            }else{
                flag = true;
            }

            if(!expressao){
                flag = false;
                $(".msg-alerta").append('<p class="text-danger mb-0">Obrigatório informar pelo menos uma expressão de busca. </p>');
            }else{
                flag = true;
            }

            if(flag){

                $.ajax({url: host+'/monitoramento/filtrar',
                    type: 'POST',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                            "expressao": expressao,
                            "dt_inicial": dt_inicial,
                            "dt_final": dt_final
                    },
                    beforeSend: function() {
                        $('.load-busca').loader('show');
                    },
                    success: function(data) {

                        $("#accordion .card").remove();

                        if(data.length == 0){

                            $(".monitoramento-total-web").html(0);

                        }else{

                            $(".monitoramento-total-web").html(data.length);
                            $.each(data, function(k, v) {
                               // $(".resultados").append('<p><a href="'+v.url_noticia+'" target="BLANK">'+v.titulo_noticia+'</a></p>');
                               // $(".resultados").append('<div><p class="fts_detalhes" style="font-weight: 600;" data-chave="card-txt-'+k+'" data-id="'+v.id+'">'+v.titulo_noticia+'</p><div id="txt-'+k+'"></div></div>');

                               const dataObj = new Date(v.data_noticia);
                               const data_formatada = dataObj.toLocaleDateString("pt-BR", {
                                    day: "2-digit",
                                    month: "2-digit",
                                    year: "numeric"
                                });

                                $("#accordion").append('<div class="card card-plain">'+
                                  '<div class="card-header" role="tab" id="heading1">'+
                                    '<a data-toggle="collapse" data-parent="#accordion" href="#collapse_'+v.id+'" data-chave="card-txt-'+k+'" data-id="'+v.id+'" aria-expanded="false" aria-controls="collapseOne" class="collapsed fts_detalhes"> '+data_formatada+' - '+v.titulo_noticia+
                                      '<i class="nc-icon nc-minimal-down"></i>'+
                                    '</a>'+
                                  '</div>'+
                                  '<div id="collapse_'+v.id+'" class="collapse" role="tabpanel" aria-labelledby="heading1" style="">'+
                                    '<div class="card-body card-txt-'+k+'" style="padding: 0px;">'+
                                    '</div>'+
                                  '</div>'+
                                '</div>');

                            });
                        }                            
                    },
                    error: function(){
                        $("#accordion .card").remove();
                        $(".msg-alerta").html('<span class="text-danger">Erro ao executar expressão de busca</span>');
                    },
                    complete: function(){
                        $('.load-busca').loader('hide');
                    }
                });

            }
           
        });

        $('body').on('click', '.fts_detalhes', function() {

            var id = $(this).data("id");
            var chave = "."+$(this).data("chave");
            var expressao = $("#expressao").val();
            
            $.ajax({url: host+'/monitoramento/filtrar/conteudo',
                    type: 'POST',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                            "expressao": expressao,
                            "id": id
                    },
                    beforeSend: function() {
                        
                    },
                    success: function(data) {
                        $(chave).html(data[0].texto);                                         
                    },
                    error: function(){
                        $(".msg-alerta").html('<span class="text-danger">Erro ao buscar conteúdo</span>');
                    },
                    complete: function(){
                        
                    }
            });

        });
        
    });
</script>
@endsection