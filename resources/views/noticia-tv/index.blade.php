@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-tv ml-3"></i> TV
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                    </h4>
                </div>
                <div class="col-md-6">
                     <a href="{{ url('tv/dashboard') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('tv/noticias/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['noticias/tv']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Tipo de Data</label>
                                        <select class="form-control" name="tipo_data" id="tipo_data">
                                            <option value="dt_cadastro" {{ ($tipo_data == "dt_cadastro") ? 'selected' : '' }}>Data de Cadastro</option>
                                            <option value="dt_noticia" {{ ($tipo_data == "dt_noticia") ? 'selected' : '' }}>Data do Clipping</option>
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Emissora</label>
                                        <select class="form-control select2" name="id_fonte" id="id_fonte">
                                            <option value="">Selecione uma emissora</option>
                                            @foreach ($emissoras as $emissora)
                                                <option value="{{ $emissora->id }}" {{ (old("id_fonte") or $emissora->id == $fonte_selecionada)  ? "selected" : "" }}>{{ $emissora->nome_emissora }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Programa</label>
                                        <select class="form-control select2" name="programa_id" id="programa_id">
                                            <option value="">Selecione um programa</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control select2 cliente" name="cliente" id="cd_cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ ($cliente_selecionado == $cliente->id) ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Área do Cliente</label>
                                        <input type="hidden" name="area_selecionada" id="area_selecionada" value="{{ ($area_selecionada) ? $area_selecionada : 0 }}">
                                        <select class="form-control area" name="cd_area" id="cd_area" disabled>
                                            <option value="0">Selecione uma área</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Sentimento</label>
                                        <select class="form-control" name="sentimento" id="sentimento">
                                            <option value="">Selecione um sentimento</option>
                                            <option value="1" {{ ($sentimento == '1') ? 'selected' : '' }}>Positivo</option>
                                            <option value="0" {{ ($sentimento == '0') ? 'selected' : '' }}>Neutro</option>
                                            <option value="-1" {{ ($sentimento == '-1') ? 'selected' : '' }}>Negativo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca</span></label>
                                        <input type="text" class="form-control" name="termo" id="termo" minlength="3" placeholder="Termo" value="{{ $termo }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Responsável pelo cadastro</label>
                                        <select class="form-control" name="usuario" id="usuario">
                                            <option value="">Selecione um usuário</option>
                                            <option value="S" {{ ($usuario == 'S') ? 'selected' : '' }}>Sistema</option>
                                            @foreach ($usuarios as $user)
                                                <option value="{{ $user->id }}" {{ ($usuario == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" id="exibir_videos" name="exibir_videos" value="true">
                                                EXIBIR VÍDEOS
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 center mb-0">
                                    <a href="{{ url('tv/noticias/limpar-filtros') }}" class="btn btn-warning btn-limpar mb-3"><i class="fa fa-refresh"></i> Limpar</a>
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
                                                        'tipo_data' =>$tipo_data,
                                                        'termo' => $termo])
                                                        ->links('vendor.pagination.bootstrap-4') }}

                    @foreach ($dados as $key => $noticia)
                        <div class="card noticia-card card-audio" id="card-audio-{{ $noticia->id }}" data-id="{{ $noticia->id }}">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-12 mb-1 video-container" style="display: none;">                                   
                                        @if($noticia->ds_caminho_video)
                                            <video width="100%" height="240" controls class="video-noticia" data-src="{{ asset('video/noticia-tv/'.$noticia->ds_caminho_video) }}">
                                                <source type="video/mp4">
                                                <source src="movie.ogg" type="video/ogg">
                                                Seu navegador não suporta a exibição de vídeos.
                                            </video>
                                        @else
                                            <h6 class="mb-1 mt-1" style="color: #ef8157;">Notícia sem vídeo vinculado</h6>
                                        @endif
                                    </div>
                                    <div class="col-lg-8 col-sm-8 mb-1 conteudo-col"> 
                                        <div class="row">
                                            <div class="col-lg-8 col-md-8 col-sm-12 mb-1"> 
                                                <div class="conteudo-{{ $noticia->id }}">
                                                    
                                                    <h6><a href="{{ url('tv/emissoras/editar/'.$noticia->emissora_id) }}" target="_BLANK">{{ ($noticia->emissora) ? $noticia->emissora->nome_emissora : '' }}</a></h6>  
                                                    <h6 style="color: #FF5722;">{{ ($noticia->cd_estado) ? $noticia->estado->nm_estado : '' }}{{ ($noticia->cd_cidade) ? "/".$noticia->cidade->nm_cidade : '' }}</h6>  
                                                    <h6 class="text-muted mb-1">
                                                        {{ \Carbon\Carbon::parse($noticia->dt_noticia)->format('d/m/Y') }} 
                                                        {{ ($noticia->horario) ? $noticia->horario : '' }}
                                                        {{ ($noticia->emissora) ? " - ".$noticia->emissora->nome_emissora : '' }}
                                                        {{ ($noticia->programa) ? "/".$noticia->programa->nome_programa : '' }}
                                                    </h6> 
                                                    <p class="mb-1">
                                                        @if($noticia->duracao)
                                                            Duração <strong>{{ $noticia->duracao }}</strong></strong>
                                                        @else
                                                            <span class="text-danger">Duração não informada</span>
                                                        @endif
                                                    </p>  
                                                    <p class="mb-1">
                                                        <strong>Retorno de Mídia: </strong>{{ ($noticia->valor_retorno) ? "R$ ".number_format($noticia->valor_retorno, 2, ',', '.') : 'Não calculado' }}
                                                    </p> 
                                                    <div class="clientes-noticia clientes-noticia-{{ $noticia->id }}" data-id="{{ $noticia->id }}" data-tipo="4">
                                                        
                                                    </div>
                                                    <div>
                                                        @forelse($noticia->tags as $tag)
                                                            <span>#{{ $tag->nome }}</span>
                                                        @empty
                                                            <p class="text-danger mb-1">#Nenhuma tag associada à notícia</p>
                                                        @endforelse
                                                    </div>
                                                </div> 
                                                <div class="panel panel-success">
                                                    <div class="conteudo-noticia mb-1">
                                                        <span class="transcricao-limitada" id="transcricao-limitada-{{ $noticia->id }}">
                                                            {!! ($noticia->sinopse) ? Str::limit($noticia->sinopse, 1000, " ...") : '<span class="text-danger">Nenhuma transcrição disponível</span>' !!}
                                                            @if($noticia->sinopse && strlen($noticia->sinopse) > 1000)
                                                                <a href="javascript:void(0);" class="text-primary ver-mais" data-id="{{ $noticia->id }}">[ver mais]</a>
                                                            @endif
                                                        </span>
                                                        <span class="transcricao-completa d-none" id="transcricao-completa-{{ $noticia->id }}">
                                                            {!! ($noticia->sinopse) ? $noticia->sinopse : '' !!}
                                                            <a href="javascript:void(0);" class="text-primary ver-menos" data-id="{{ $noticia->id }}">[ver menos]</a>
                                                        </span>
                                                    </div>
                                                </div>  
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
                                        <a title="Excluir" href="{{ url('noticia/tv/'.$noticia->id.'/excluir') }}" class="btn btn-danger btn-fill btn-icon btn-sm btn-excluir" style="border-radius: 30px;">
                                            <i class="fa fa-times fa-3x text-white"></i>
                                        </a>
                                        <a title="Editar" href="{{ url('noticia/tv/'.$noticia->id.'/editar') }}" class="btn btn-primary btn-fill btn-icon btn-sm" style="border-radius: 30px;">
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
                                                        'tipo_data' =>$tipo_data,
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
<script src="{{ asset('js/campos-cliente.js') }}"></script>
<script src="{{ asset('js/noticia_clientes.js') }}"></script>
<style>
    .noticia-card.card-destaque {
        border: 2px solid #007bff;
        background-color: #f8f9ff;
    }
</style>
    <script>

        var host = $('meta[name="base-url"]').attr('content');
        
        $(document).ready(function(){

            // Destacar card ao clicar
            $('.card-audio').click(function(){
                // Remove destaque de todos
                $('.card-audio').removeClass('card-destaque');
                // Adiciona destaque ao clicado
                $(this).addClass('card-destaque');
            });

            // Controle de exibição de vídeos
            $('#exibir_videos').change(function(){
                if($(this).is(':checked')){
                    // Exibir vídeos
                    $('.video-container').show();
                    $('.conteudo-col').removeClass('col-lg-8').addClass('col-lg-8');
                    
                    // Carregar os vídeos
                    $('.video-noticia').each(function(){
                        var src = $(this).data('src');
                        if(src){
                            $(this).find('source[type="video/mp4"]').attr('src', src);
                            this.load();
                        }
                    });
                } else {
                    // Ocultar vídeos
                    $('.video-container').hide();
                    $('.conteudo-col').removeClass('col-lg-8').addClass('col-lg-12');
                    
                    // Pausar e limpar vídeos para economizar recursos
                    $('.video-noticia').each(function(){
                        this.pause();
                        this.currentTime = 0;
                    });
                }
            });

            var demo2 = $('.demo1').bootstrapDualListbox({
                nonSelectedListLabel: 'Disponíveis',
                selectedListLabel: 'Selecionadas',
               
            });

            // Carregar programas de TV independente da emissora
            carregarProgramasTv();

            $('.ver-mais').click(function(){
                var id = $(this).data('id');
                $('#transcricao-limitada-' + id).addClass('d-none');
                $('#transcricao-completa-' + id).removeClass('d-none');
            });

            $('.ver-menos').click(function(){
                var id = $(this).data('id');
                $('#transcricao-completa-' + id).addClass('d-none');
                $('#transcricao-limitada-' + id).removeClass('d-none');
                // Rolagem suave para o início do texto limitado
                $('html, body').animate({
                    scrollTop: $('#transcricao-limitada-' + id).offset().top - 450
                }, 400);
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

        function carregarProgramasTv(){

            var programa_selecionado = {{ $programa_selecionado ?? 0 }};

            $.ajax({
                url: host+'/api/programa-tv/buscar',
                type: 'GET',
                beforeSend: function() {
                    $('#programa_id').append('<option value="">Carregando...</option>').val('');
                },
                success: function(data) {

                    $('#programa_id').find('option').remove();

                    if(data.length == 0) {                            
                        $('#programa_id').append('<option value="">Nenhum programa cadastrado</option>').val('');
                        return;
                    }

                    $('#programa_id').append('<option value="">Selecione um programa</option>').val('');

                    data.forEach(element => {
                        let option = new Option(element.text, element.id);
                        $('#programa_id').append(option);
                    });

                    if(programa_selecionado > 0) {
                        $('#programa_id').val(programa_selecionado);
                    }
                    
                },
                error: function(){
                    $('#programa_id').find('option').remove();
                    $('#programa_id').append('<option value="">Erro ao carregar programas</option>').val('');
                }
            });

        };

        $(document).ready(function(){
            $('#cd_cliente').trigger('change');
        });
    </script>
@endsection