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
                                        <label>Fonte</label>
                                        <select class="form-control select2" name="id_fonte" id="id_fonte">
                                            <option value="">Selecione uma fonte</option>
                                            @foreach ($fontes as $fonte)
                                                <option value="{{ $fonte->id }}" {{ (old("id_fonte") or $fonte->id == $fonte_selecionada)  ? "selected" : "" }}>{{ $fonte->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control cliente" name="cliente" id="cd_cliente">
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
                                <div class="col-md-6 col-sm-6">
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
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <a href="{{ url('noticias/impresso/limpar') }}" class="btn btn-warning btn-limpar mb-3"><i class="fa fa-refresh"></i> Limpar</a>
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
                                <div class="row conteudo-total-{{ $noticia->id }}" data-id="{{ $noticia->id }}">
                                    <div class="col-lg-2 col-md-2 col-sm-12 mb-1 box-imagem-{{ $noticia->id }}" style="min-height: 200px;">
                                        <a href="{{ url('noticia-impressa/imagem/download/'.$noticia->id) }}" target="_BLANK">
                                            <img class="load-imagem" data-id="{{ $noticia->id }}" src="">
                                        </a>
                                    </div>
                                    <div class="col-lg-10 col-sm-10 mb-1"> 
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 mb-1"> 
                                                <div class="conteudo-{{ $noticia->id }}">
                                                    <p class="font-weight-bold mb-1">{{ $noticia->titulo }}</p>
                                                    <h6><a href="{{ url('fonte-impresso/'.$noticia->id_fonte.'/editar') }}" target="_BLANK">{{ ($noticia->fonte) ? $noticia->fonte->nome : '' }}</a></h6>  
                                                    <h6 style="color: #FF5722;">{{ ($noticia->cd_estado) ? $noticia->estado->nm_estado : '' }}{{ ($noticia->cd_cidade) ? "/".$noticia->cidade->nm_cidade : '' }}</h6>  
                                                    <h6 class="text-muted mb-1">{{ \Carbon\Carbon::parse($noticia->dt_clipagem)->format('d/m/Y') }} - {{ ($noticia->fonte) ? $noticia->fonte->nome : '' }}  {{ ($noticia->id_sessao_impresso) ? "- ".$noticia->secao->ds_sessao : '' }}</h6> 
                                                    <p class="mb-1">
                                                        @if($noticia->nu_pagina_atual)
                                                            Página <strong>{{ $noticia->nu_pagina_atual }}</strong></strong>
                                                        @else
                                                            <span class="text-danger">Página não informada</span>
                                                        @endif
                                                    </p>  
                                                    <p class="mb-1">
                                                        <strong>Retorno de Mídia: </strong>
                                                        @if(is_numeric($noticia->valor))
                                                            {{ number_format((float) $noticia->valor, 2, ',', '.') }}
                                                        @else
                                                            Não informado
                                                        @endif
                                                    </p> 
                                                    <div class="clientes-noticia clientes-noticia-{{ $noticia->id }}" data-id="{{ $noticia->id }}" data-tipo="1">
                                                        
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
                <div class="col-md-12 modal-imagem"></div>
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
    <script>
        $(document).ready(function(){

            var host =  $('meta[name="base-url"]').attr('content');

            $('.load-imagem').each(function() {

                const imgElement = $(this);
                const noticiaId = imgElement.data('id');

                $.ajax({
                    url: host+'/noticia/impressa/imagem-path/' + noticiaId, // ajuste se o endpoint for diferente
                    type: 'GET',
                    beforeSend: function(){
                        $(".box-imagem-"+noticiaId).loader('show');
                    },
                    success: function(response) {
                        if (response.path) {
                            imgElement.attr('src', response.path);
                        }
                    },
                    error: function() {
                        console.error('Erro ao carregar imagem da notícia ID ' + noticiaId);
                    },
                    complete: function() {
                        $(".box-imagem-"+noticiaId).loader('hide');
                    }
                });
            });

            $(".btn-visualizar-noticia").click(function(){

                var id = $(this).data("id");
                var chave = ".conteudo-"+id;
                var sinopse = ".sinopse-"+id;
                var imagem = ".box-imagem-"+id;

                $(".modal-conteudo").html($(chave).html());
                $(".modal-sinopse").html($(sinopse).text().replace(/\n/g, "<br />"));
                $(".modal-imagem").html($(imagem).html());

                $("#showNoticia").modal("show");

            });
        });

        $(document).ready(function(){
            $('.cliente').trigger('change');
        });
    </script>
@endsection