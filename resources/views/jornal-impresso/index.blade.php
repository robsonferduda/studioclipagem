@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Impressos
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Monitoramento
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('impresso') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('fonte-impresso/listar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-newspaper-o"></i> Fontes de Impresso</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => '', 'url' => ['jornal-impresso/noticias']]) !!}
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
                                    <input type="hidden" name="monitoramento_id" id="monitoramento_id" value="{{ Session::get('impresso_monitoramento') }}">
                                    <div class="form-group">
                                        <select class="form-control" name="monitoramento" id="monitoramento" disabled>
                                            <option value="">Selecione um monitoramento</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <label>Fontes</label>
                                    <div class="form-group">
                                        <select multiple="multiple" size="10" name="fontes[]" class="demo1 form-control">
                                            @foreach ($fontes as $fonte)
                                                <option value="{{ $fonte->id }}">{{ $fonte->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca</span></label>
                                        <input type="text" class="form-control" name="termo" id="termo" minlength="3" placeholder="Termo" value="{{ $termo }}">
                                    </div>
                                </div>
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}

                    @if($dados->count())
                        <h6 class="px-3">Mostrando {{ $dados->count() }} de {{ $dados->total() }} Páginas</h6>
                    @endif

                    {{ $dados->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 
                                                        'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'),
                                                        'cliente' => $cliente_selecionado,
                                                        'termo' => $termo])
                                                        ->links('vendor.pagination.bootstrap-4') }}

                    @foreach ($dados as $key => $pagina)
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-sm-12 mb-1">
                                        <a href="{{ url('jornal-impresso/web/pagina/download/'.$pagina->id_pagina) }}" target="_BLANK"><img src="{{ Storage::disk('s3')->temporaryUrl($pagina->path_pagina_s3, '+2 minutes') }}" alt="Página {{ $pagina->n_pagina }}"></a>
                                    </div>
                                    <div class="col-lg-10 col-sm-10 mb-1"> 
                                        <h6><a href="{{ url('fonte-impresso/'.$pagina->id_fonte.'/editar') }}" target="_BLANK">{{ ($pagina->nome_fonte) ? $pagina->nome_fonte : '' }}</a><span class="pull-right">{{ $pagina->id }}</span></h6>  
                                        <h6 style="color: #FF5722;">{{ ($pagina->nm_estado) ? $pagina->nm_estado : '' }}{{ ($pagina->nm_cidade) ? "/".$pagina->nm_cidade : '' }}</h6>  
                                        <h6 class="text-muted mb-1">{{ \Carbon\Carbon::parse($pagina->dt_pub)->format('d/m/Y') }} - {{ ($pagina->nome_fonte) ? $pagina->nome_fonte : '' }}</h6> 
                                        <p class="mb-0">{{ ($pagina->nome_cliente) ? $pagina->nome_cliente : '' }}</p>
                                        <p class="mb-1">Página <strong>{{ $pagina->n_pagina }}</strong></p>  
                                        <div style="margin-bottom: 5px;" class="tags destaque-{{ $pagina->noticia_id }}-{{ $pagina->monitoramento_id }}" data-monitoramento="{{ $pagina->monitoramento_id }}" data-chave="{{ $pagina->noticia_id }}-{{ $pagina->monitoramento_id }}" data-noticia="{{ $pagina->noticia_id }}">
                                                
                                        </div>
                                        <code>
                                            <a href="{{ url('monitoramento/'.$pagina->monitoramento_id.'/editar') }}" target="_BLANK">{{ $pagina->expressao }}</a>
                                        </code>
                                        <div class="panel panel-success">
                                            <div class="conteudo-noticia mb-1">
                                                {!! ($pagina->texto_extraido) ?  Str::limit(nl2br($pagina->texto_extraido), 1000, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                            </div>
                                            <div class="panel-body conteudo-{{ $pagina->noticia_id }}-{{ $pagina->monitoramento_id }}">
                                                {!! ($pagina->texto_extraido) ?  nl2br(e($pagina->texto_extraido))  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                            </div>
                                            <div class="panel-heading">
                                                <h3 class="panel-title"><span class="btn-show">Mostrar Mais</span></h3>
                                            </div>
                                        </div>                
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 mb-1"> 
                                        <button class="btn btn-primary btn-visualizar-noticia" data-id="{{ $pagina->id_pagina }}"><i class="fa fa-eye"></i> Visualizar</button> 
                                        <a href="{{ url('jornal-impresso/noticia/extrair/'.$pagina->monitoramento_id.'/impresso',$pagina->id_pagina) }}" class="btn btn-success btn-extrair-noticia"><i class="fa fa-database"></i> Extrair Notícia</a>  
                                        @if($pagina->id_noticia_gerada)
                                            <a href="{{ url('noticia-impressa/'.$pagina->id_noticia_gerada.'/editar') }}" class="btn btn-warning"><i class="fa fa-edit"></i>Notícia Extraída</a> 
                                        @endif
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 mb-1"> 
                                        @if($pagina->fl_upload)
                                            <span>Arquivo enviado via <strong class="text-primary">upload</strong> em {{ \Carbon\Carbon::parse($pagina->created_at)->format('d/m/Y H:i:s') }}. A data da notícia é igual a data do arquivo.</span>
                                        @else
                                            <span>Arquivo extraído automaticamente pelo <strong class="text-success">sistema</strong> em {{ \Carbon\Carbon::parse($pagina->created_at)->format('d/m/Y H:i:s') }}. Atenção! A data da notícia poce coincidir com a data da coleta e estar incorreta!</span>
                                        @endif
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
</div>
<div class="modal fade" id="showNoticia" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header" style="padding: 15px !important;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-newspaper-o"></i><span></span> Dodos da Notícia</h6>
        </div>
        <div class="modal-body" style="padding: 15px;">
            <div class="row">
                <div class="col-md-12 modal-cabecalho">
                    <h6 class="modal-fonte mt-0 mb-1"></h6>
                    <h6 class="text-muted modal-estado mt-0 mb-1" style="color: #FF5722;"></h6>
                    <p class="modal-pagina mt-0 mb-2"></p>
                </div>
                <hr/>
                <div class="col-md-12 modal-conteudo">
                    
                </div>
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

            $(".panel-heading").click(function() {
                $(this).parent().addClass('active').find('.panel-body').slideToggle('fast');
                $(".panel-heading").not(this).parent().removeClass('active').find('.panel-body').slideUp('fast');
            });

             $(".btn-visualizar-noticia").click(function(){

                var id = $(this).data("id");
                var chave = ".conteudo-"+id;
                var pagina = ".paginas-"+id;
                var estado = ".conteudo-estado-"+id;
                var fonte = ".conteudo-fonte-"+id;

                $(".modal-fonte").html($(fonte).text());
                $(".modal-estado").html($(estado).text());
                $(".modal-pagina").html($(pagina).text());
                $(".modal-conteudo").html($(chave).text().replace(/\n/g, "<br />"));

                $("#showNoticia").modal("show");

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
                        url: host+'/monitoramento/cliente/'+cliente_selecionado+'/fl_impresso',
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
                            if(data.filtro_impresso){

                                const lista_fontes = JSON.parse("[" + data.filtro_impresso + "]");

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
                    url: host+'/jornal-impresso/conteudo/'+noticia+'/monitoramento/'+monitoramento,
                    type: 'GET',
                    beforeSend: function() {
                            
                    },
                    success: function(data) {
                        
                        $(chave_conteudo).html(data.texto.replace(/\n/g, "<br />"));

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