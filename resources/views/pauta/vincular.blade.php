@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-file-text-o ml-3"></i> Pautas 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Vincular Notícia
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('pautas') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa  fa-table"></i> Pautas</a>
                    <a href="{{ url('pauta/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar Pauta</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-lg-12 col-sm-12">
                <input type="hidden" name="cliente" id="cliente" value="{{ $pauta->cliente->id }}">
                <p class="mb-1"><strong>Cliente</strong>: {{ $pauta->cliente->pessoa->nome }}</p>
                <p class="mb-1"><strong>Pauta</strong>: {{ $pauta->descricao }}</p>
                <p><strong>Notícias do Cliente</strong></p>
            </div>            
            <div class="col-lg-12 col-sm-12">
                <div class="row mb-2">
                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label>Data Inicial</label>
                            <input type="text" class="form-control data-event" name="dt_inicial" id="dt_inicial" required="true" value="{{ date('d/m/Y') }}" placeholder="__/__/____">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label>Data Final</label>
                            <input type="text" class="form-control data-event" name="dt_final" id="dt_final" required="true" value="{{ date('d/m/Y') }}" placeholder="__/__/____">
                        </div>
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <div class="form-group">
                            <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca na sinopse</span></label>
                            <input type="text" class="form-control" name="termo" id="termo" minlength="3" placeholder="Termo">
                        </div>
                    </div>  
                </div>      
                <div class="row">
                    <div class="row ml-3">
                        <div class="tile">
                            <input type="checkbox" name="midia" id="midia-tv">
                            <label class="label-check" for="midia-tv">
                                <i class="fa fa-tv"></i>
                                <h6>TV</h6>
                            </label>
                        </div>
                        <div class="tile">
                            <input type="checkbox" name="midia" id="midia-radio">
                            <label class="label-check" for="midia-radio">
                                <i class="fa fa-volume-up"></i>
                                <h6>Rádio</h6>
                            </label>
                        </div>
                        <div class="tile">
                            <input type="checkbox" name="midia" id="midia-impresso">
                            <label class="label-check" for="midia-impresso">
                                <i class="fa fa-newspaper-o"></i>
                                <h6>Impresso</h6>
                            </label>
                        </div>
                        <div class="tile">
                            <input type="checkbox" name="midia" id="midia-web">
                            <label class="label-check" for="midia-web">
                                <i class="fa fa-globe"></i>
                                <h6>Web</h6>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-sm-12">
                <h6 class="mt-5">Listagem de Notícias</h6>
                <div class="form-check" style="margin-left: 6px;">
                    <label class="form-check-label"><input class="form-check-input" type="checkbox" name="is_active" value="true">
                        SELECIONAR TODAS<span class="form-check-sign"></span>
                    </label>
                </div>
                <div class="box-lista-noticias mt-3" style="position: relative; padding: 5px; padding-bottom: 20px;"></div>
            </div>           
        </div>
    </div>
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() { 

            var host  = $('meta[name="base-url"]').attr('content');
            var token = $('meta[name="csrf-token"]').attr('content');
            var dados = [];

            listaNoticias();

            function listaNoticias(){

                cliente = $("#cliente").val();
                flag_web = $("#midia-web").is(":checked");
                flag_impresso = $("#midia-impresso").is(":checked");
                flag_tv = $("#midia-tv").is(":checked");
                flag_radio = $("#midia-radio").is(":checked");
                dt_inicial = $("#dt_inicial").val();
                dt_final = $("#dt_final").val();
                termo = $("#termo").val();

                //Limpas os dados
                dados = [];
                $(".box-lista-noticias").empty();

                //Carrega os dados web
                $.ajax({
                    url: host+'/api/noticias/listar',
                    type: 'POST',
                    data: {
                        "_token": token,
                        "flag_web": flag_web,
                        "flag_impresso": flag_impresso,
                        "flag_tv": flag_tv,
                        "flag_radio": flag_radio,
                        "cliente": cliente,
                        "dt_inicial": dt_inicial,
                        "dt_final": dt_final,
                        "termo": termo
                    },
                    beforeSend: function() {
                        $('.box-lista-noticias').loader('show');
                    },
                    success: function(data) {
                        dados = data;
                        desenhaTabela();
                    },
                    complete: function(){
                        $('.box-lista-noticias').loader('hide');
                    }
                });
            }

            function desenhaTabela(){
                dados.forEach(function (noticia, indice) {    
                    
                    if(noticia.tipo == 'web') icone = '<i class="fa fa-globe"></i> Web';
                    if(noticia.tipo == 'impresso') icone = '<i class="fa fa-newspaper-o"></i> Impresso';

                    $(".box-lista-noticias").append('<div class="form-check">'+
                                                        '<label class="form-check-label">'+
                                                            '<input class="form-check-input" type="checkbox" name="is_active" value="true"><strong>'+noticia.titulo+'</strong><br/>'+noticia.dt_noticia+' '+icone+'<br/>'+noticia.texto.substring(0, 200)+'... <span class="form-check-sign"></span>'+
                                                        '</label>'+
                                                    '</div>');       
                });
            }

            $("#midia-impresso").change(function(){
                listaNoticias();
            });

            $("#midia-web").change(function(){
                listaNoticias();
            });

            $("#midia-tv").change(function(){
                listaNoticias();
            });

            $("#midia-radio").change(function(){
                listaNoticias();
            });

            $(document).on('keypress',function(e) {
                if(e.which == 13) {
                    listaNoticias();
                }
            });

            $(".data-event").datetimepicker({
                format: 'DD/MM/YYYY',
                icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-screenshot',
                clear: 'fa fa-trash',
                close: 'fa fa-remove'
                }
            }).on('dp.change', function (ev) {
                listaNoticias() ;//your function call
            });

        });
    </script>
@endsection