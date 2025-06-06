@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-volume-up ml-3"></i> Rádio
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Monitoramento
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('radio/dashboard') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('radio/noticias/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['noticia/radio/monitoramento']]) !!}
                    <div class="form-group m-3 w-70">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tipo de Data</label>
                                    <select class="form-control select2" name="tipo_data" id="tipo_data">
                                        <option value="created_at" {{ ($tipo_data == "created_at") ? 'selected' : '' }}>Data de Cadastro</option>
                                        <option value="dt_pub" {{ ($tipo_data == "dt_pub") ? 'selected' : '' }}>Data do Clipping</option>
                                    </select>
                                </div>
                            </div>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <select class="form-control select2" name="cliente" id="cliente">
                                        <option value="">Selecione um cliente</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}" {{ ($cliente_selecionado == $cliente->id) ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Monitoramento</label>
                                <input type="hidden" name="monitoramento_id" id="monitoramento_id" value="{{ Session::get('radio_monitoramento') }}">
                                <div class="form-group">
                                    <select class="form-control" name="monitoramento" id="monitoramento" disabled>
                                        <option value="">Selecione um monitoramento</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Emissora <span class="text-danger">Obrigatório</span></label>
                                        <select class="form-control select2" name="emissora" id="emissora">
                                            <option value="">Selecione uma emissora</option>
                                            @foreach ($emissoras as $emissora)
                                                <option value="{{ $emissora->id }}" {!! ($emissora_search == $emissora->id) ? "selected" : '' !!}>
                                                    {{ $emissora->nome_emissora }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Programa</label>
                                        <select class="form-control selector-select2" name="programa" id="programa" disabled>
                                            <option value="">Selecione um programa</option>
                                        </select>
                                    </div>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca</span></label>
                                    <textarea class="form-control" name="termo" id="termo" rows="3">{{ $termo }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12 checkbox-radios mb-0">
                                <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}

                @if($dados->count())
                    <h6 class="px-3">Mostrando {{ $dados->count() }} de {{ $dados->total() }} áudios</h6>
                @endif

                {{ $dados->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 
                                                    'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'),
                                                    'cliente' => $cliente_selecionado,
                                                    'termo' => $termo])
                                                    ->links('vendor.pagination.bootstrap-4') }}
           
                @foreach ($dados as $key => $audio)
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 mb-1 audio-{{ $audio->id_audio }}">                                    
                                    @if(Storage::disk('s3')->temporaryUrl($audio->path_s3, '+30 minutes'))
                                        <audio width="100%" controls style="width: 100%;">
                                            <source src="{{ Storage::disk('s3')->temporaryUrl($audio->path_s3, '+30 minutes') }}" type="audio/mpeg">
                                            Seu navegador não suporta a execução de áudios, faça o download para poder ouvir.
                                        </audio>
                                    @else

                                    @endif
                                </div>
                                <div class="col-lg-12 col-sm-12 mb-1"> 
                                    <div class="conteudo-{{ $audio->id_audio }}">
                                        <h6><a href="{{ url('emissora/'.$audio->id_fonte.'/edit') }}" target="_BLANK">{{ ($audio->nome_fonte) ? $audio->nome_fonte : '' }}</a></h6>  
                                        <h6 style="color: #FF5722;">{{ ($audio->nm_estado) ? $audio->nm_estado : '' }}{{ ($audio->nm_cidade) ? "/".$audio->nm_cidade : '' }}</h6>  
                                        <h6 class="text-muted mb-1">
                                            {{ ($audio->nome_fonte) ? $audio->nome_fonte : '' }} - 
                                            {{ \Carbon\Carbon::parse($audio->data_hora_inicio)->format('d/m/Y') }} - 
                                            De {{ \Carbon\Carbon::parse($audio->data_hora_inicio)->format('H:i:s') }} às {{ \Carbon\Carbon::parse($audio->data_hora_fim)->format('H:i:s') }}
                                        </h6> 
                                        <p class="mb-1"><i class="nc-icon nc-briefcase-24"></i> {{ ($audio->nome_cliente) ? $audio->nome_cliente : '' }}</p>
                                        
                                        <div style="margin-bottom: 5px;" class="tags destaque-{{ $audio->noticia_id }}-{{ $audio->monitoramento_id }}" data-monitoramento="{{ $audio->monitoramento_id }}" data-chave="{{ $audio->noticia_id }}-{{ $audio->monitoramento_id }}" data-noticia="{{ $audio->noticia_id }}">
                                                
                                        </div>
                                        <code>
                                            <a href="{{ url('monitoramento/'.$audio->monitoramento_id.'/editar') }}" target="_BLANK">{{ $audio->expressao }}</a>
                                        </code>
                                    </div>
                                    <div class="panel panel-success">
                                        <div class="conteudo-noticia mb-1 sinopse-{{ $audio->id_audio }}">
                                            {!! ($audio->transcricao) ?  Str::limit($audio->transcricao, 1000, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                        </div>
                                        <div class="panel-body conteudo-{{ $audio->noticia_id }}-{{ $audio->monitoramento_id }}">
                                            {!! ($audio->transcricao) ?  $audio->transcricao  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                        </div>
                                        <!--
                                            <div class="panel-heading">
                                                <h3 class="panel-title"><span class="btn-show">Mostrar Mais</span></h3>
                                            </div>
                                        -->
                                    </div> 
                                    <div>
                                        <a href="{{ url('noticia/radio/'.$audio->monitoramento_id .'/extrair',$audio->id_audio) }}" target="BLANK" class="btn btn-warning btn-sm"><i class="fa fa-database"></i> Extrair Notícia</a> 
                                        <button class="btn btn-primary btn-sm btn-visualizar-noticia" data-id="{{ $audio->id_audio }}" data-monitoramento="{{ $audio->monitoramento_id }}"><i class="fa fa fa-eye"></i> Visualizar</button> 
                                        @if($audio)
                                            
                                        @endif
                                    </div>               
                                </div>
                            </div>     
                        </div>
                    </div>
                @endforeach

                {{ $dados->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 
                                                    'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'),
                                                    'cliente' => $cliente_selecionado,
                                                    'termo' => $termo])
                                                    ->links('vendor.pagination.bootstrap-4') }}
            </div>            
        </div>
    </div>
</div>
<div class="modal fade" id="showNoticia" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-scrollable modal-lg" role="document" style="max-width: 78% !important; position: relative; right: -8%;">
      <div class="modal-content">
        <div class="modal-header" style="padding: 15px !important;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-newspaper-o"></i><span></span> Dodos da Notícia</h6>
        </div>
        <div class="modal-body" style="padding: 15px;">
            <div class="row">
                <div class="col-md-12 modal-conteudo"></div>
                <div class="col-md-12 modal-controle"></div>
                <div class="col-md-12 modal-audio mt-1 mb-0"></div>
                <div class="col-md-12 modal-sinopse"></div>
            </div>
            <div class="center">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
            </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('script')
    <script>
        $(document).ready(function(){

            var host =  $('meta[name="base-url"]').attr('content');

            var demo2 = $('.demo1').bootstrapDualListbox({
                nonSelectedListLabel: 'Disponíveis',
                selectedListLabel: 'Selecionadas',               
            });

            $(".btn-visualizar-noticia").click(function(){

                var id = $(this).data("id");
                var monitoramento = $(this).data("monitoramento");
                var audio = ".audio-"+id;
                var chave = ".conteudo-"+id;
                var sinopse = ".conteudo-"+id+"-"+monitoramento;

                $(".modal-conteudo").html($(chave).html());
                $(".modal-controle").html('<div class="center">'+
                                            '<button title="Anterior" id="btn-back" data-id="'+id+'" data-monitoramento="'+monitoramento+'" type="button" class="btn btn-primary btn-sm"><i class="fa fa-step-backward fa-2x" aria-hidden="true"></i></button>'+
                                            '<button title="Atual" id="btn-home" data-id="'+id+'" data-monitoramento="'+monitoramento+'" type="button" class="btn btn-primary btn-sm"><i class="fa fa-home fa-2x" aria-hidden="true"></i></button>'+
                                            '<button title="Próximo" id="btn-prev" data-id="'+id+'" data-monitoramento="'+monitoramento+'" type="button" class="btn btn-primary btn-sm"><i class="fa fa-step-forward fa-2x" aria-hidden="true"></i></button>'+
                                        '</div>');
                $(".modal-audio").html($(audio).html());
                $(".modal-sinopse").html($(sinopse).html());

                $("#showNoticia").modal("show");

            });

            $(document).on('click', '#btn-back', function() {
                
                var id = $(this).data("id");
                var monitoramento = $(this).data("monitoramento");

                getDadosAudio(id, monitoramento, "back");
            });

            $(document).on('click', '#btn-home', function() {
                
                var id = $(this).data("id");
                var monitoramento = $(this).data("monitoramento");
                var audio = ".audio-"+id;
                var chave = ".conteudo-"+id;
                var sinopse = ".conteudo-"+id+"-"+monitoramento;

                $(".modal-audio").html($(audio).html());
                $(".modal-sinopse").html($(sinopse).html());

            });

            $(document).on('click', '#btn-prev', function() {

                var id = $(this).data("id");
                var monitoramento = $(this).data("monitoramento");

                getDadosAudio(id, monitoramento, "prev");   
            });

            function getDadosAudio(id, monitoramento, tipo){
                $.ajax({
                    url: host+'/radio/adjacentes/'+id+'/monitoramento/'+monitoramento,
                    type: 'GET',
                    beforeSend: function() {
                        $('.modal-audio').loader('show');     
                        $('.modal-sinopse').loader('show'); 
                    },
                    success: function(data) {
                        
                        if(tipo == "back"){ 
                            $(".modal-sinopse").html(data.back.transcricao);
                            $(".modal-audio").html('<audio width="100%" controls style="width: 100%;">'+
                                                    '<source src="'+data.back.path_s3+'" type="audio/mpeg">'+
                                                    'Seu navegador não suporta a execução de áudios, faça o download para poder ouvir.'+
                                                    '</audio>');
                        }else{
                            $(".modal-sinopse").html(data.prev.transcricao);
                            $(".modal-audio").html('<audio width="100%" controls style="width: 100%;">'+
                                                    '<source src="'+data.prev.path_s3+'" type="audio/mpeg">'+
                                                    'Seu navegador não suporta a execução de áudios, faça o download para poder ouvir.'+
                                                    '</audio>');
                        }
                    },
                    error: function(){
                       
                    },
                    complete: function(){
                        $('.modal-audio').loader('hide'); 
                        $('.modal-sinopse').loader('hide'); 
                    }
                });
            }

            $(".panel-heading").click(function() {
                $(this).parent().addClass('active').find('.panel-body').slideToggle('fast');
                $(".panel-heading").not(this).parent().removeClass('active').find('.panel-body').slideUp('fast');
            });

            $(".btn-show").click(function(){

                var texto = $(this).text();

                if(texto == 'Mostrar Mais'){

                    $(this).closest('.panel').find('.conteudo-noticia').addClass('d-none');
                    $(this).html("Mostrar Menos");

                }
                
                if(texto == 'Mostrar Menos'){
                    $(this).closest('.panel').find('.conteudo-noticia').removeClass('d-none');
                    $(this).html("Mostrar Mais");
                }

            });

            $("#cliente").change(function(){

                var cliente_selecionado = $(this).val();

                if(cliente_selecionado){

                    $.ajax({
                        url: host+'/monitoramento/cliente/'+cliente_selecionado+'/fl_radio',
                        type: 'GET',
                        beforeSend: function() {
                            $('#monitoramento').find('option').remove().end();
                            $('#monitoramento').append('<option value="">Carregando...</option>').val('');                            
                        },
                        success: function(data) {
                            $('#monitoramento').attr('disabled', false);
                            $('#monitoramento').find('option').remove().end();

                            $('#monitoramento').append('<option value="" selected>Selecione um monitoramento</option>').val(''); 
                            data.forEach(element => {

                                var nome = (element.nome) ? element.nome : 'Monitoramento sem nome';

                                let option = new Option(nome, element.id);
                                $('#monitoramento').append(option);
                            });    
                            
                            var monitoramento_selecionado = $("#monitoramento_id").val();
                            if(monitoramento_selecionado > 0){
                                if($("#monitoramento option[value="+monitoramento_selecionado+"]").length > 0)
                                    $("#monitoramento").val(monitoramento_selecionado);
                            }
                        },
                        error: function(){
                            $('#monitoramento').find('option').remove().end();
                            $('#monitoramento').append('<option value="">Erro ao carregar dados...</option>').val('');
                        },
                        complete: function(){
                                
                        }
                    }); 

                }
             
            });

            $("#monitoramento").change(function(){

                var monitoramento_selecionado = $(this).val();

                if(monitoramento_selecionado){

                    $.ajax({
                        url: host+'/monitoramento/'+monitoramento_selecionado+'/fontes',
                        type: 'GET',
                        beforeSend: function() {
                                                       
                        },
                        success: function(data) {
                            if(data.filtro_radio){

                                const lista_fontes = JSON.parse("[" + data.filtro_radio + "]");

                                console.log(lista_fontes);

                                
                                for (var i = 0; i < $('#fontes option').length; i++) {
                                    if ($('#fontes option')[i].value == 1) {
                                        
                                    }
                                }
                            }
                            
                        },
                        error: function(){
                           
                        },
                        complete: function(){
                                
                        }
                    }); 

                }

            });

            $(".tags").each(function() {
               
                var monitoramento = $(this).data("monitoramento");
                var noticia = $(this).data("noticia");
                var chave = ".destaque-"+$(this).data("chave");
                var chave_conteudo = ".conteudo-"+$(this).data("chave");

                $.ajax({
                    url: host+'/radio/conteudo/'+noticia+'/monitoramento/'+monitoramento,
                    type: 'GET',
                    beforeSend: function() {
                            
                    },
                    success: function(data) {
                        
                        $(chave_conteudo).html(data.texto);

                        var marks = [];                 
                        
                        const divContent = document.querySelector(chave_conteudo);

                        if (divContent) {
            
                            const childElements = divContent.querySelectorAll('mark');
                            const output = document.querySelector(chave);

                            childElements.forEach(element => {

                                if(!marks.includes(element.innerHTML.trim())){
                                    marks.push(element.innerHTML.trim());

                                    $(chave).append('<span class="destaque-busca">'+element.innerHTML.trim()+'</span>');
                                }
                            });
                        } 
                    },
                    complete: function(){
                            
                    }
                });
            });
        });

        $(document).ready(function(){
            $('#cliente').trigger('change');
        });
    </script>
@endsection