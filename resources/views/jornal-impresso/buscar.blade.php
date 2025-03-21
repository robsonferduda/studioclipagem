@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Impressos
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Buscar
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('fonte-impresso/listar') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-database"></i> Fontes Impressos</a>
                    <a href="{{ url('jornal-impresso/upload') }}" class="btn btn-warning pull-right mr-3"><i class="fa fa-upload"></i> Upload</a>
                    <a href="{{ url('noticia/impresso/cadastrar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['jornal-impresso/buscar']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Tipo de Data</label>
                                        <select class="form-control select2" name="regra" id="regra">
                                            <option value="dt_noticia">Data de Cadastro</option>
                                            <option value="dt_clipagem">Data do Clipping</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" required="true" value="{{ \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y H:i:s') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ \Carbon\Carbon::parse($dt_final)->format('d/m/Y H:i:s') }}" placeholder="__/__/____">
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
                                        <label for="expressao" class="form-label">Expressão de Busca <span class="text-primary">Digite o termo ou expressão de busca</span></label>
                                        <textarea class="form-control" name="expressao" id="expressao" rows="3">{{ $expressao }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!} 

                    @if(count($impressos))
                        <h6 class="px-3">Mostrando {{ $impressos->count() }} de {{ $impressos->total() }} arquivos coletados</h6>
                        {{ $impressos->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final])->links('vendor.pagination.bootstrap-4') }} 
                    @endif  

                    @foreach ($impressos as $key => $pagina)
                        <div class="card">
                            <div class="card-body">                           
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-sm-12 mb-1">
                                        <a href="{{ url('jornal-impresso/web/pagina/download/'.$pagina->id) }}">
                                            <img class="lazy-load" data-src="{{ Storage::disk('s3')->temporaryUrl($pagina->path_pagina_s3, '+2 minutes') }}" alt="Página {{ $pagina->n_pagina }}">
                                        </a>
                                    </div>
                                    <div class="col-lg-10 col-sm-10 mb-1"> 
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 mb-1"> 
                                                <h6 class="conteudo-fonte-{{ $pagina->id }}">{{ ($pagina->edicao->fonte) ? $pagina->edicao->fonte->nome : 'Não identificada' }} - {{ \Carbon\Carbon::parse($pagina->dt_clipagem)->format('d/m/Y') }}</h6>
                                                <h6 class="text-muted conteudo-estado-{{ $pagina->id }}">
                                                    {{ ($pagina->edicao->fonte and $pagina->edicao->fonte->estado) ? $pagina->edicao->fonte->estado->nm_estado : '' }}
                                                    {{ ($pagina->edicao->fonte and $pagina->edicao->fonte->cidade) ? '/ '.$pagina->edicao->fonte->cidade->nm_cidade : '' }}
                                                </h6>
                                                <p class="paginas-{{ $pagina->id }}">Página <strong>{{ $pagina->n_pagina }}</strong>/<strong>{{ count($pagina->edicao->paginas) }}</strong></p>  
                                                <div class="panel panel-success">
                                                    <div class="conteudo-noticia mb-1">
                                                        {!! ($pagina->texto_extraido) ?  Str::limit($pagina->texto_extraido, 800, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                                    </div>
                                                    <div class="panel-body conteudo-{{ $pagina->id }}">
                                                        {!! ($pagina->texto_extraido) ?  $pagina->texto_extraido  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                                    </div>
                                                </div>        
                                            </div>  
                                            <div class="col-lg-12 col-md-12 col-sm-12 mb-1 rodape-noticia"> 
                                                <button class="btn btn-primary btn-visualizar-noticia" data-id="{{ $pagina->id }}"><i class="fa fa-eye"></i> Visualizar</button> 
                                                <a href="{{ url('jornal-impresso/noticia/extrair/web',$pagina->id) }}" class="btn btn-success btn-extrair-noticia"><i class="fa fa-database"></i> Extrair Notícia</a>  
                                            </div>
                                        </div>    
                                    </div>
                                </div>                               
                            </div>                            
                        </div>
                    @endforeach
                    @if(count($impressos))
                        {{ $impressos->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final])->links('vendor.pagination.bootstrap-4') }} 
                    @endif
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
                    <h6 class="text-muted modal-estado mt-0 mb-1"></h6>
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

        document.addEventListener("DOMContentLoaded", function() {
            var lazyImages = [].slice.call(document.querySelectorAll("img.lazy-load"));

            if ("IntersectionObserver" in window) {
                let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            let lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.classList.remove("lazy-load");
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                });

                lazyImages.forEach(function(lazyImage) {
                    lazyImageObserver.observe(lazyImage);
                });
            } else {
                // Fallback for browsers that do not support IntersectionObserver
                lazyImages.forEach(function(lazyImage) {
                    lazyImage.src = lazyImage.dataset.src;
                });
            }
        });

        $(document).ready(function(){

            var host =  $('meta[name="base-url"]').attr('content');

            var demo2 = $('.demo1').bootstrapDualListbox({
                nonSelectedListLabel: 'Disponíveis',
                selectedListLabel: 'Selecionadas',
            
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
        
        });
    </script>
@endsection