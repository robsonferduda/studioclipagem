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
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Listar
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('impresso') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('noticia/impresso/novo') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-newspaper-o"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['noticias/impresso']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Tipo de Data</label>
                                        <select class="form-control" name="tipo_data" id="tipo_data">
                                            <option value="dt_cadastro" {{ ($tipo_data == "dt_cadastro") ? 'selected' : '' }}>Data de Cadastro</option>
                                            <option value="dt_clipagem" {{ ($tipo_data == "dt_clipagem") ? 'selected' : '' }}>Data do Clipping</option>
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

                    @foreach ($dados as $key => $noticia)
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-sm-12 mb-1">
                                        <a href="{{ url('noticia-impressa/imagem/download/'.$noticia->id) }}" target="_BLANK">
                                            <img src="{{ asset('img/noticia-impressa/'.$noticia->ds_caminho_img) }}" alt="Página {{ $noticia->n_pagina }}">
                                        </a>
                                    </div>
                                    <div class="col-lg-10 col-sm-10 mb-1"> 
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 mb-1"> 
                                                <div class="conteudo-{{ $noticia->id }}">
                                                    <p class="font-weight-bold mb-1">{{ $noticia->titulo_noticia }}</p>
                                                    <h6><a href="{{ url('fonte-impresso/'.$noticia->id_fonte.'/editar') }}" target="_BLANK">{{ ($noticia->fonte) ? $noticia->fonte->nome : '' }}</a></h6>  
                                                    <h6 style="color: #FF5722;">{{ ($noticia->cd_estado) ? $noticia->estado->nm_estado : '' }}{{ ($noticia->cd_cidade) ? "/".$noticia->cidade->nm_cidade : '' }}</h6>  
                                                    <h6 class="text-muted mb-1">{{ \Carbon\Carbon::parse($noticia->dt_pub)->format('d/m/Y') }} - {{ ($noticia->fonte) ? $noticia->fonte->nome : '' }}  {{ ($noticia->id_sessao_impresso) ? "- ".$noticia->secao->ds_sessao : '' }}</h6> 
                                                    <p class="mb-1">
                                                        @if($noticia->nu_pagina_atual)
                                                            Página <strong>{{ $noticia->nu_pagina_atual }}</strong>/<strong>{{ $noticia->nu_paginas_total }}</strong>
                                                        @else
                                                            <span class="text-danger">Página não informada</span>
                                                        @endif
                                                    </p>  
                                                    <p class="mb-1">
                                                        <strong>Retorno de Mídia: </strong>{{ ($noticia->valor_retorno) ? "R$ ".$noticia->valor_retorno : 'Não calculado' }}
                                                    </p> 
                                                    <div>
                                                        @forelse($noticia->clientes as $cliente)
                                                            <p class="mb-2">
                                                                <span>{{ $cliente->nome }}</span>
                                                                @switch($cliente->pivot->sentimento)
                                                                    @case(-1)
                                                                            <i class="fa fa-frown-o text-danger"></i>
                                                                            <a href="{{ url('noticia/'.$cliente->pivot->noticia_id.'/tipo/'.$cliente->pivot->tipo_id.'/cliente/'.$cliente->pivot->cliente_id.'/sentimento/0/atualizar') }}"><i class="fa fa-ban op-2"></i></a>
                                                                            <a href="{{ url('noticia/'.$cliente->pivot->noticia_id.'/tipo/'.$cliente->pivot->tipo_id.'/cliente/'.$cliente->pivot->cliente_id.'/sentimento/1/atualizar') }}"><i class="fa fa-smile-o op-2"></i></a>
                                                                        @break
                                                                    @case(0)
                                                                            <a href="{{ url('noticia/'.$cliente->pivot->noticia_id.'/tipo/'.$cliente->pivot->tipo_id.'/cliente/'.$cliente->pivot->cliente_id.'/sentimento/-1/atualizar') }}"><i class="fa fa-frown-o op-2"></i></a> 
                                                                            <i class="fa fa-ban text-primary"></i>
                                                                            <a href="{{ url('noticia/'.$cliente->pivot->noticia_id.'/tipo/'.$cliente->pivot->tipo_id.'/cliente/'.$cliente->pivot->cliente_id.'/sentimento/1/atualizar') }}"><i class="fa fa-smile-o op-2"></i></a>                                                
                                                                        @break
                                                                    @case(1)
                                                                            <a href="{{ url('noticia/'.$cliente->pivot->noticia_id.'/tipo/'.$cliente->pivot->tipo_id.'/cliente/'.$cliente->pivot->cliente_id.'/sentimento/-1/atualizar') }}"><i class="fa fa-frown-o op-2"></i></a>
                                                                            <a href="{{ url('noticia/'.$cliente->pivot->noticia_id.'/tipo/'.$cliente->pivot->tipo_id.'/cliente/'.$cliente->pivot->cliente_id.'/sentimento/0/atualizar') }}"><i class="fa fa-ban op-2"></i></a>
                                                                            <i class="fa fa-smile-o text-success"></i>
                                                                        @break                                            
                                                                @endswitch
                                                                <a class="text-danger btn-excluir" href="{{ url('noticia/'.$cliente->pivot->id.'/vinculo/excluir') }}">Remover Cliente</a>
                                                            </p>
                                                        @empty
                                                            <p class="text-danger mb-1">Nenhum cliente associada à notícia</p>
                                                        @endforelse
                                                    </div>
                                                    <div>
                                                        @forelse($noticia->tags as $tag)
                                                            <span>#{{ $tag->nome }}</span>
                                                        @empty
                                                            <p class="text-danger mb-1">#Nenhuma tag associada à notícia</p>
                                                        @endforelse
                                                    </div>
                                                </div> 
                                                <div class="sinopse-{{ $noticia->id }}">
                                                    {!! ($noticia->sinopse) ?  $noticia->sinopse  : '<span class="text-danger center">Notícia não possui texto</span>' !!}
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 mb-1"> 
                                                <button class="btn btn-primary btn-visualizar-noticia" data-id="{{ $noticia->id }}"><i class="fa fa-eye"></i> Visualizar</button> 
                                            </div>
                                        </div>
                                    </div>
                                </div>     
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-refresh"></i>Cadastrado por <strong>{{ ($noticia->usuario) ? $noticia->usuario->name : 'Sistema' }}</strong> em {{ \Carbon\Carbon::parse($noticia->created_at)->format('d/m/Y H:i:s') }}. Última atualização em {{ \Carbon\Carbon::parse($noticia->updated_at)->format('d/m/Y H:i:s') }}
                                    <div class="pull-right">
                                        <a title="Excluir" href="{{ url('noticia-impressa/'.$noticia->id.'/excluir') }}" class="btn btn-danger btn-fill btn-icon btn-sm btn-excluir" style="border-radius: 30px;">
                                            <i class="fa fa-times fa-3x text-white"></i>
                                        </a>
                                        <a title="Editar" href="{{ url('noticia-impressa/'.$noticia->id.'/editar') }}" class="btn btn-primary btn-fill btn-icon btn-sm" style="border-radius: 30px;">
                                            <i class="fa fa-edit fa-3x text-white"></i>
                                        </a>
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
                <div class="col-md-12 modal-conteudo"></div>
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
                var chave = ".conteudo-"+id;
                var sinopse = ".sinopse-"+id;

                $(".modal-conteudo").html($(chave).html());
              
                $(".modal-sinopse").html($(sinopse).text().replace(/\n/g, "<br />"));

                $("#showNoticia").modal("show");

            });

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