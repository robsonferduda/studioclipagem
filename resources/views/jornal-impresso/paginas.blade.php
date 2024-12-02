@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Jornal Web 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias 
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('fonte-web/listar') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-database"></i> Fontes Web</a>
                    <a href="{{ url('noticia/web/cadastrar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                @foreach ($paginas as $key => $pagina)
                    <div class="card">
                        <div class="card-body">                           
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-12 mb-1">
                                    <img src="{{ Storage::disk('s3')->temporaryUrl($pagina->path_pagina_s3, '+2 minutes') }}" alt="Girl in a jacket">
                                </div>
                                <div class="col-lg-10 col-sm-10 mb-1"> 
                                    <h6>{{ $pagina->edicao->fonte->nome }}</h6>  
                                    <p>Página <strong>{{ $pagina->n_pagina }}</strong>/<strong>{{ count($pagina->edicao->paginas) }}</strong></p>  
                                    <div class="panel panel-success">
                                        <div class="conteudo-noticia mb-1">
                                            {!! ($pagina->texto_extraido) ?  Str::limit($pagina->texto_extraido, 1000, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                        </div>
                                        <div class="panel-body">
                                            {!! ($pagina->texto_extraido) ?  $pagina->texto_extraido  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                        </div>
                                        <div class="panel-heading">
                                            <h3 class="panel-title"><span class="btn-show">Mostrar Mais</span></h3>
                                        </div>
                                    </div> 
                                    <a href="{{ url('jornal-impresso/noticia/extrair/web',$pagina->id) }}" class="btn btn-success btn-extrair-noticia"><i class="fa fa-database"></i> Extrair Notícia</a>                
                                </div>
                            </div>                               
                        </div>                            
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    $( document ).ready(function() {

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

    });
</script>
@endsection