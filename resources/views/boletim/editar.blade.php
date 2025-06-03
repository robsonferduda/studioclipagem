@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-file-o ml-3"></i> Boletim 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrar
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('boletins') }}" class="btn btn-primary pull-right mr-2"><i class="fa fa-table"></i> Boletins</a>
                    <a href="{{ url('boletim/'.$boletim->id.'/visualizar') }}" class="btn btn-warning pull-right mr-1" target="_blank"><i class="fa fa-eye"></i> Visualizar</a>
                    <a href="{{ url('boletim/cadastrar') }}" class="btn btn-primary pull-right mr-1"><i class="fa fa-plus"></i> Cadastrar Boletim</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>           
            <div class="col-lg-12 col-sm-12">
                {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['boletim', $boletim->id], 'method' => 'patch']) !!}
                    <input type="hidden" name="id_boletim" id="id_boletim" value="{{ $boletim->id }}">
                    <div class="row mt-0">
                        <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label>Título</label>
                                    <input type="text" class="form-control" name="titulo" id="titulo" required="true" value="Boletim Digital - Studio Clipagem - {{ date('d/m/Y') }}">
                                </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label>Cliente</label>
                                <select class="form-control select2" name="id_cliente" id="id_cliente" required="true">
                                    <option value="">Selecione um cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{!! $cliente->id !!}" {{ ($cliente->id == $boletim->id_cliente) ? 'selected' : ''}}>{!! $cliente->nome !!}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>  
                        <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Situação</label>
                                <select class="form-control select2" name="id_situacao" id="id_situacao" required="true">
                                    <option value="">Selecione uma situação</option>
                                    @foreach($situacoes as $situacao)
                                        <option value="{!! $situacao->id !!}" {{ ($situacao->id == $boletim->id_situacao) ? 'selected' : '' }}>{!! $situacao->ds_situacao !!}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>  
                        <div class="col-md-2">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary mt-4 w-100" name="btn_enviar" value="salvar"><i class="fa fa-save"></i> Atualizar</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!} 
                <div class="row mt-0">
                    <div class="col-md-12">
                        <h6 class="mt-3"><i class="fa fa-check" style="font-size: 20px; vertical-align: sub;"></i> Selecionar Notícias <small>Notícias presentes no boletim aparecem destacadas</small></h6>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Data Inicial</label>
                            <input type="text" class="form-control datepicker" name="dt_inicial" id="dt_inicial" required="true" value="{{ \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y') }}" placeholder="__/__/____">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Data Final</label>
                            <input type="text" class="form-control datepicker" name="dt_final" id="dt_final" required="true" value="{{ \Carbon\Carbon::parse($dt_final)->format('d/m/Y') }}" placeholder="__/__/____">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-black"><i class="fa fa-filter"></i> Filtrar por cliente</label>
                            <select class="form-control select2" name="cliente_busca" id="cliente_busca">
                                <option value="">Selecione um cliente</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 mt-3">
                        <div class="form-check mt-3">
                            <div class="form-check mt-3">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="nao-enviadas" value="true">
                                    Somente Não Enviadas
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-3">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="midia-impresso" value="true">
                                    Clipagem de Jornal
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-3">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="midia-radio" value="true">
                                    Clipagem de Rádio
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-3">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="midia-tv" value="true">
                                    Clipagem de TV
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-3">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="midia-web" value="true">
                                    Clipagem de Web
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>
            <div class="col-lg-12 col-sm-12">
                <div class="row mt-0">
                    <table class="table-noticias table table-striped">
                        <thead>
                            <th>
                                <tr>                            
                                    <td colspan="2">
                                        <div class="form-check">
                                            <label class="form-check-label"><input class="form-check-input todas" type="checkbox" name="is_active" value="true">
                                                SELECIONAR TODAS<span class="form-check-sign"></span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            </th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
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

            var cliente = $("#id_cliente").val();
            if(cliente) {
                $("#cliente_busca").val(cliente);
            }
    
            listaNoticias();

            $(".todas").change(function(){
                $(".item-noticia").not(this).prop('checked', this.checked);
            });

            function listaNoticias(){

                boletim = $("#id_boletim").val();
                cliente = $("#cliente_busca").val();
                flag_web = $("#midia-web").is(":checked");
                flag_impresso = $("#midia-impresso").is(":checked");
                flag_tv = $("#midia-tv").is(":checked");
                flag_radio = $("#midia-radio").is(":checked");
                flag_enviadas = $("#nao-enviadas").is(":checked");
                dt_inicial = $("#dt_inicial").val();
                dt_final = $("#dt_final").val();
                termo = $("#termo").val();

                //Limpas os dados
                dados = [];
                
                //Carrega os dados 
                $.ajax({
                    url: host+'/boletim/noticias',
                    type: 'POST',
                    data: {
                        "_token": token,
                        "flag_web": flag_web,
                        "flag_impresso": flag_impresso,
                        "flag_tv": flag_tv,
                        "flag_radio": flag_radio,
                        "flag_enviadas": flag_enviadas,
                        "cliente": cliente,
                        "dt_inicial": dt_inicial,
                        "id_boletim": boletim,
                        "dt_final": dt_final                   
                    },
                    beforeSend: function() {
                        $('.table-noticias').loader('show');
                    },
                    success: function(data) {
                        dados = data;
                        desenhaTabela();
                    },
                    complete: function(){
                        $('.table-noticias').loader('hide');
                    }
                });
            }

            function desenhaTabela(){

                $(".table-noticias tbody").empty();

                dados.forEach(function (noticia, indice) {    
                    
                    if(noticia.tipo == 'web') icone = '<i class="fa fa-globe"></i> Web';
                    if(noticia.tipo == 'impresso') icone = '<i class="fa fa-newspaper-o"></i> Impresso';
                    if(noticia.tipo == 'radio') icone = '<i class="fa fa-volume-up"></i> Rádio';
                    if(noticia.tipo == 'tv') icone = '<i class="fa fa-tv"></i> TV';

                    titulo = (noticia.tipo == 'web' || noticia.tipo == 'impresso') ? ((noticia.titulo) ? '<strong>'+noticia.titulo+'</strong><br/>' : '<strong>Título não informado</strong><br/>' ) : '';
                    sinopse = (noticia.sinopse) ? noticia.sinopse : 'Sinopse não informada';

                    var check = (false) ? 'checked' : '';
                    var boletim = (noticia.flag) ? '<span class="badge badge-pill badge-success"> Enviada</span>' : '';
                    var checked = (noticia.id_boletim) ? 'checked' : '';

                    $(".table-noticias tbody").append('<tr>'+
                                                        '<td><div class="form-check" style="margin-top: -20px !important;"><label class="form-check-label">'+
                                                        '<input class="form-check-input item-noticia" type="checkbox" '+checked+' name="lista_noticia[]" '+check+' value="'+noticia.id+'" data-tipo="'+noticia.tipo+'"><span class="form-check-sign"></span></label></div></td>'+
                                                        '<td>'+
                                                        titulo+'<strong style="color: #51cbce;">'+noticia.data_formatada+' - '+noticia.fonte+'</strong> - Coletada em '+noticia.data_coleta+''+
                                                        '<br/>'+sinopse+'<br/>'+icone+' '+boletim+'</td>'+
                                                       '</tr>');                   
                });
            }

            $("#cliente_busca").change(function(){
                listaNoticias();
            });

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

            $("#nao-enviadas").change(function(){
                listaNoticias();
            });

            $(document).on('keypress',function(e) {
                if(e.which == 13) {
                    listaNoticias();
                }
            });

            $(".datepicker").datetimepicker({
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
            }).on('dp.change', function (e) {
                listaNoticias() ;//your function call
            });

            $(document).on("change", ".item-noticia", function() {

                var id = $(this).val();
                var tipo = $(this).data('tipo');
                var checked = $(this).is(':checked');

                if(checked) {
                    // Adiciona a notícia ao boletim
                    $.ajax({
                        url: host+'/boletim/noticia/adicionar',
                        type: 'POST',
                        data: {
                            "_token": token,
                            "id_noticia": id,
                            "tipo": tipo,
                            "id_boletim": {{ $boletim->id }}
                        },
                        beforeSend: function() {
                            $('.table-noticias').loader('show');
                        },
                        success: function(response) {
                                                    
                            $.notify({
                              icon: "nc-icon nc-bell-55",
                              message: "<b>Operação Realizada com Sucesso</b> - a notícia foi adicionada com sucesso ao boletim."
                            }, {
                              type: 'success',
                              timer: 1000,
                              placement: {
                                from: 'top',
                                align: 'right'
                              }
                            });

                        },
                        error: function(response){

                            $(this).trigger('change');

                            $.notify({
                              icon: "fa fa-times",
                              message: "<b>Erro ao Realizar Operação</b> - a notícia não foi adicionada ao boletim. Código da notícia: <strong>"+id+"</strong>"
                            }, {
                              type: 'danger',
                              timer: 1000,
                              placement: {
                                from: 'top',
                                align: 'right'
                              }
                            });
                        },
                        complete: function(response){
                            $('.table-noticias').loader('hide');
                        }
                    });
                } else {
                    // Remove a notícia do boletim
                    $.ajax({
                        url: host+'/boletim/noticia/remover',
                        type: 'POST',
                        data: {
                            "_token": token,
                            "id_noticia": id,
                            "tipo": tipo,
                            "id_boletim": {{ $boletim->id }}
                        },
                        success: function(response) {
                            console.log(response);
                        }
                    });
                }
            });

        });
    </script>
@endsection