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
                    <a href="{{ url('monitoramentos') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="nc-icon nc-minimal-left"></i> Voltar</a>
                    <a href="{{ url('monitoramentos') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="nc-icon nc-sound-wave ml-2"></i> Monitoramentos</a>
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
                                        <label>Período</label>
                                        <select class="form-control periodo" name="periodo" id="periodo">
                                            <option value="">Selecione um período</option>
                                            @foreach ($periodos as $periodo)
                                                <option value="{{ $periodo->slug }}">{{ $periodo->periodo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Tipo de Data</label>
                                        <select class="form-control select2" name="tipo_data" id="tipo_data">
                                            <option value="dt_publicacao" selected>Data da Publicação</option>
                                            <option value="dt_coleta">Data da Coleta</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker dt_inicial_relatorio">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker dt_final_relatorio">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="expressao" class="form-label">Expressão de Busca <span class="text-danger">Campo obrigatório</span></label>
                                        <textarea class="form-control" id="expressao" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 mt-3">
                                    <p class="mb-1">Selecione as Mídias</p>
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" name="fl_impresso" id="fl_impresso" value="true">
                                            IMPRESSO
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" name="fl_web" id="fl_web" value="true">
                                            WEB
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" name="fl_radio" id="fl_radio" value="true">
                                            RÁDIO
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" name="fl_tv" id="fl_tv" value="true">
                                            TV
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 msg-alerta">

                                </div>
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="button" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                    <button type="button" id="btn-monitorar" class="btn btn-warning mb-3"><i class="nc-icon nc-sound-wave"></i> Monitorar</button>
                                </div>
                                <div class="col-md-12">
                                    <p class="mb-1"><strong>Observações</strong></p>
                                    <p class="mt-1 mb-1"><strong>&</strong>: operador de busca equivalente ao "E"</p>
                                    <p class="mt-1 mb-1"><strong>|</strong>: operador de busca equivalente ao "OU"</p>
                                    <p class="mt-1 mb-1"><strong>!</strong>: operador de busca equivalente ao "NÃO"</p>
                                    <p class="mt-1 mb-1"><strong><-></strong>: operador de distância entre palavras, onde o - é a distância entre elas</p>
                                    <p class="mt-1 mb-1"><strong>Exemplo de busca</strong>: <span>queda<2>energia & Celesc & !Jaguaruna</span>: Todas as notícias relacionadas à queda de energia que citam a Celesc, exceto em Jaguaruna</p>
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
                            <div id="accordion_web" role="tablist" aria-multiselectable="true" class="card-collapse">
                                <div class="row cabecalho-busca cabecalho-busca-web d-none">
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
                                <div class="row cabecalho-aguardando-busca-web">
                                    <div class="col-md-6">
                                        <span class="text-info">Aguardando critérios de busca</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="panel_impresso" role="tabpanel" aria-expanded="false">
                            <div id="accordion_impresso" role="tablist" aria-multiselectable="true" class="card-collapse">
                                <div class="row cabecalho-busca cabecalho-busca-impresso d-none">
                                    <div class="col-md-6">
                                        <p class="card-title mb-0">Foram encontradas <strong class="monitoramento-total-impresso"></strong> notícias</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">
                                            <span class="badge badge-pill badge-primary">
                                                Todas as fontes
                                            </span>
                                        </div>
                                    </div>
                                </div>   
                            </div>
                        </div>
                        <div class="tab-pane" id="panel_radio" role="tabpanel" aria-expanded="false">
                            <div id="accordion_radio" role="tablist" aria-multiselectable="true" class="card-collapse">
                                <div class="row cabecalho-busca cabecalho-busca-radio d-none">
                                    <div class="col-md-6">
                                        <p class="card-title mb-0">Foram encontradas <strong class="monitoramento-total-radio"></strong> notícias</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">
                                            <span class="badge badge-pill badge-primary">
                                                Todas as fontes
                                            </span>
                                        </div>
                                    </div>
                                </div>   
                            </div>
                        </div>
                        <div class="tab-pane" id="panel_tv" role="tabpanel" aria-expanded="false">
                            <div id="accordion_tv" role="tablist" aria-multiselectable="true" class="card-collapse">
                                <div class="row cabecalho-busca cabecalho-busca-tv d-none">
                                    <div class="col-md-6">
                                        <p class="card-title mb-0">Foram encontradas <strong class="monitoramento-total-tv"></strong> notícias</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">
                                            <span class="badge badge-pill badge-primary">
                                                Todas as fontes
                                            </span>
                                        </div>
                                    </div>
                                </div>   
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
<div class="modal fade" id="modalMonitoramento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="nc-icon nc-sound-wave"></i> Monitoramento de Mídia</h6>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Nome <span class="text-danger">Obrigatório</span></label>
                        <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Cliente <span class="text-danger">Obrigatório</span></label>
                        <select class="form-control" name="cliente" id="cliente">
                            <option value="">Selecione um cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>               
        </div>
        <div class="center mb-3">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
          <button type="button" class="btn btn-success btn-salvar-monitoramento"><i class="fa fa-save"></i> Salvar</button>
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
            var tipo_data = $("#tipo_data").val();
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

                //Busca Web
                $.ajax({url: host+'/monitoramento/filtrar',
                    type: 'POST',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                            "expressao": expressao,
                            "dt_inicial": dt_inicial,
                            "dt_final": dt_final,
                            "tipo_data": tipo_data
                    },
                    beforeSend: function() {
                        $('.load-busca').loader('show');
                    },
                    success: function(data) {

                        $("#accordion_web .card").remove();

                        if(data.length == 0){

                            $(".cabecalho-busca-web").addClass("d-none");
                            $(".monitoramento-total-web").html(0);

                        }else{

                            $(".cabecalho-busca-web").removeClass("d-none");
                            $(".cabecalho-aguardando-busca-web").addClass("d-none");

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

                                $("#accordion_web").append('<div class="card card-plain">'+
                                  '<div class="card-header card-header-custom" role="tab" id="heading1">'+
                                    '<strong>'+v.nome+'</strong>'+
                                    '<a data-toggle="collapse" data-parent="#accordion_web" href="#collapse_'+v.id+'" data-tipo="web" data-chave="card-web-txt-'+k+'" data-id="'+v.id+'" aria-expanded="false" aria-controls="collapseOne" class="collapsed fts_detalhes"> '+data_formatada+' - '+v.titulo_noticia+
                                      '<i class="nc-icon nc-minimal-down"></i>'+
                                    '</a>'+
                                    '<a href="'+v.url_noticia+'" target="BLANK"><i class="fa fa-external-link" aria-hidden="true"></i></a>'+
                                  '</div>'+
                                  '<div id="collapse_'+v.id+'" class="collapse" role="tabpanel" aria-labelledby="heading1" style="">'+
                                    '<div class="box-destaque-busca destaque-card-web-txt-'+k+'"></div><div class="card-body card-busca card-web-txt-'+k+'">'+
                                    '</div>'+
                                  '</div>'+
                                '</div>');

                            });
                        }                            
                    },
                    error: function(){
                        $("#accordion_web .card").remove();
                        $(".msg-alerta").html('<span class="text-danger">Erro ao executar expressão de busca</span>');
                    },
                    complete: function(){
                        $('.load-busca').loader('hide');
                    }
                });

                //Busca Impresso
                $.ajax({url: host+'/monitoramento/filtrar/impresso',
                    type: 'POST',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                            "expressao": expressao,
                            "dt_inicial": dt_inicial,
                            "dt_final": dt_final,
                            "tipo_data": tipo_data
                    },
                    beforeSend: function() {
                        $('.load-busca').loader('show');
                    },
                    success: function(data) {

                        $("#accordion_impresso .card").remove();

                        if(data.length == 0){

                            $(".cabecalho-busca-impresso").addClass("d-none");
                            $(".monitoramento-total-impresso").html(0);

                        }else{

                            $(".cabecalho-busca-impresso").removeClass("d-none");
                            

                            $(".monitoramento-total-impresso").html(data.length);
                            $.each(data, function(k, v) {

                               const dataObj = new Date(v.dt_pub);
                               const data_formatada = dataObj.toLocaleDateString("pt-BR", {
                                    day: "2-digit",
                                    month: "2-digit",
                                    year: "numeric"
                                });

                                $("#accordion_impresso").append('<div class="card card-plain">'+
                                  '<div class="card-header card-header-custom" role="tab" id="heading1">'+
                                    '<a data-toggle="collapse" data-parent="#accordion_impresso" href="#collapse_'+v.id+'" data-tipo="impresso" data-chave="card-impresso-txt-'+k+'" data-id="'+v.id+'" aria-expanded="false" aria-controls="collapseOne" class="collapsed fts_detalhes"> '+data_formatada+' - '+v.nome+' - Página '+v.n_pagina+
                                      '<i class="nc-icon nc-minimal-down"></i>'+
                                    '</a>'+
                                  '</div>'+
                                  '<div id="collapse_'+v.id+'" class="collapse" role="tabpanel" aria-labelledby="heading1" style="">'+
                                    '<div class="box-destaque-busca destaque-card-impresso-txt-'+k+'"></div><div class="card-body card-busca card-impresso-txt-'+k+'">'+
                                    '</div>'+
                                  '</div>'+
                                '</div>');

                            });
                        }                            
                    },
                    error: function(){
                        $("#accordion_impresso .card").remove();
                        $(".msg-alerta").html('<span class="text-danger">Erro ao executar expressão de busca</span>');
                    },
                    complete: function(){
                        $('.load-busca').loader('hide');
                    }
                });

                   //Busca Rádio
                   $.ajax({url: host+'/monitoramento/filtrar/radio',
                    type: 'POST',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                            "expressao": expressao,
                            "dt_inicial": dt_inicial,
                            "dt_final": dt_final,
                            "tipo_data": tipo_data
                    },
                    beforeSend: function() {
                        $('.load-busca').loader('show');
                    },
                    success: function(data) {

                        $("#accordion_radio .card").remove();

                        if(data.length == 0){

                            $(".cabecalho-busca-radio").addClass("d-none");
                            $(".monitoramento-total-radio").html(0);

                        }else{

                            $(".cabecalho-busca-radio").removeClass("d-none");
                            
                            $(".monitoramento-total-radio").html(data.length);
                            $.each(data, function(k, v) {

                               const dataObj = new Date(v.data_hora_inicio);
                               const data_formatada = dataObj.toLocaleDateString("pt-BR", {
                                    day: "2-digit",
                                    month: "2-digit",
                                    year: "numeric"
                                });

                                $("#accordion_radio").append('<div class="card card-plain">'+
                                  '<div class="card-header card-header-custom" role="tab" id="heading1">'+
                                    '<a data-toggle="collapse" data-parent="#accordion_radio" href="#collapse_'+v.id+'" data-tipo="radio" data-chave="card-radio-txt-'+k+'" data-id="'+v.id+'" aria-expanded="false" aria-controls="collapseOne" class="collapsed fts_detalhes"> '+data_formatada+' - '+v.nome_emissora+
                                      '<i class="nc-icon nc-minimal-down"></i>'+
                                    '</a>'+
                                  '</div>'+
                                  '<div id="collapse_'+v.id+'" class="collapse" role="tabpanel" aria-labelledby="heading1" style="">'+
                                    '<div class="box-destaque-busca destaque-card-radio-txt-'+k+'"></div><div class="card-body card-busca card-radio-txt-'+k+'">'+
                                    '</div>'+
                                  '</div>'+
                                '</div>');

                            });
                        }                            
                    },
                    error: function(){
                        $("#accordion_impresso .card").remove();
                        $(".msg-alerta").html('<span class="text-danger">Erro ao executar expressão de busca</span>');
                    },
                    complete: function(){
                        $('.load-busca').loader('hide');
                    }
                });

             //Busca TV
             $.ajax({url: host+'/monitoramento/filtrar/tv',
                    type: 'POST',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                            "expressao": expressao,
                            "dt_inicial": dt_inicial,
                            "dt_final": dt_final,
                            "tipo_data": tipo_data
                    },
                    beforeSend: function() {
                        $('.load-busca').loader('show');
                    },
                    success: function(data) {

                        $("#accordion_tv .card").remove();

                        if(data.length == 0){

                            $(".cabecalho-busca-tv").addClass("d-none");
                            $(".monitoramento-total-tv").html(0);

                        }else{

                            $(".cabecalho-busca-tv").removeClass("d-none");

                            $(".monitoramento-total-tv").html(data.length);
                            $.each(data, function(k, v) {

                               const dataObj = new Date(v.horario_start_gravacao);
                               const data_formatada = dataObj.toLocaleDateString("pt-BR", {
                                    day: "2-digit",
                                    month: "2-digit",
                                    year: "numeric"
                                });

                                $("#accordion_tv").append('<div class="card card-plain">'+
                                  '<div class="card-header card-header-custom" role="tab" id="heading1">'+
                                    '<a data-toggle="collapse" data-parent="#accordion_tv" href="#collapse_'+v.id+'" data-tipo="tv" data-chave="card-tv-txt-'+k+'" data-id="'+v.id+'" aria-expanded="false" aria-controls="collapseOne" class="collapsed fts_detalhes"> '+data_formatada+' - '+v.nome_programa+
                                      '<i class="nc-icon nc-minimal-down"></i>'+
                                    '</a>'+
                                  '</div>'+
                                  '<div id="collapse_'+v.id+'" class="collapse" role="tabpanel" aria-labelledby="heading1" style="">'+
                                    '<div class="box-destaque-busca destaque-card-tv-txt-'+k+'"></div><div class="card-body card-busca card-tv-txt-'+k+'">'+
                                    '</div>'+
                                  '</div>'+
                                '</div>');

                            });
                        }                            
                    },
                    error: function(){
                        $("#accordion_impresso .card").remove();
                        $(".msg-alerta").html('<span class="text-danger">Erro ao executar expressão de busca</span>');
                    },
                    complete: function(){
                        $('.load-busca').loader('hide');
                    }
                });

            }
           
        });

        $("#btn-monitorar").click(function(){

            $("#modalMonitoramento").modal({backdrop: 'static', keyboard: false});

        });

        $('body').on('click', '.btn-salvar-monitoramento', function() {

            var fl_tv = $("#fl_tv").is(":checked");
            var fl_web = $("#fl_web").is(":checked");
            var fl_radio = $("#fl_radio").is(":checked");
            var fl_impresso = $("#fl_impresso").is(":checked");
            var cliente = $("#cliente").val();
            var expressao = $("#expressao").val();
            var nome = $("#nome").val();
            var flag = false;

            if(!nome){
                Swal.fire({
                    html: 'O campo <strong>Nome</strong> é obrigatório.',
                    type: "warning",
                    icon: "warning",
                    confirmButtonText: '<i class="fa fa-check"></i> Ok',
                });
                flag = false;
            }else{
                flag = true;
            }

            if(!expressao){
                Swal.fire({
                    html: 'O campo <strong>Expressão</strong> é obrigatório.',
                    type: "warning",
                    icon: "warning",
                    confirmButtonText: '<i class="fa fa-check"></i> Ok',
                });
                flag = false;
            }else{
                flag = true;
            }

            if(!cliente){
                Swal.fire({
                    html: 'O campo <strong>Cliente</strong> é obrigatório.',
                    type: "warning",
                    icon: "warning",
                    confirmButtonText: '<i class="fa fa-check"></i> Ok',
                });
                flag = false;
            }else{
                flag = true;
            }

            if(flag){
                $.ajax({url: host+'/monitoramento/create',
                    type: 'POST',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                            "expressao": expressao,
                            "id_cliente": cliente,
                            "nome": nome,
                            "fl_impresso": fl_impresso,
                            "fl_radio": fl_radio,
                            "fl_web": fl_web,
                            "fl_tv": fl_tv
                    },
                    beforeSend: function() {
                        $('.modal-content').loader('show');
                    },
                    success: function(data) {                      
                        $("#modalMonitoramento").modal("hide");

                        Swal.fire({
                            html: 'Monitoramento cadastrado com sucesso. Para ver seus monitoramentos, selecione a opção <strong>Monitoramentos</strong>.',
                            type: "success",
                            icon: "success",
                            confirmButtonText: '<i class="fa fa-check"></i> Ok',
                        });
                    },
                    error: function(){
                        
                    },
                    complete: function(){
                        $('.modal-content').loader('hide');
                    }
                });
            }
        });

        $('body').on('click', '.fts_detalhes', function() {

            var id = $(this).data("id");
            var tipo = $(this).data("tipo");
            var chave = "."+$(this).data("chave");
            var chave_destaque = ".destaque-"+$(this).data("chave");
            var expressao = $("#expressao").val();
            
            $.ajax({url: host+'/monitoramento/filtrar/conteudo',
                    type: 'POST',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                            "expressao": expressao,
                            "id": id,
                            "tipo": tipo
                    },
                    beforeSend: function() {
                        $(chave).loader('show');
                    },
                    success: function(data) {                      

                        $(chave).html(data[0].texto);   
                        $(chave_destaque).empty();    
                        
                        var marks = [];                 
                        
                        const divContent = document.querySelector(chave);

                        if (divContent) {
                        
                            const childElements = divContent.querySelectorAll('mark');
                            const output = document.querySelector(chave_destaque);

                            childElements.forEach(element => {

                                if(!marks.includes(element.innerHTML.trim())){
                                    marks.push(element.innerHTML.trim());

                                    $(chave_destaque).append('<span class="destaque-busca">'+element.innerHTML.trim()+'</span>');
                                }
                            });
                        } 
                    },
                    error: function(){
                        $(".msg-alerta").html('<span class="text-danger">Erro ao buscar conteúdo</span>');
                    },
                    complete: function(){
                        $(chave).loader('hide');
                    }
            });

        });
        
    });
</script>
@endsection