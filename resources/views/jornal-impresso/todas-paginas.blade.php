@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Impressos
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Arquivos Web 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Páginas
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('fonte-impresso/listar') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-database"></i> Fontes Impressos</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['jornal-impresso/paginas']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
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
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Fonte <span class="text-primary">Somente fontes do tipo <strong>Coleta Web</strong></span></label>
                                        <select class="form-control select2" name="fonte" id="fonte">
                                            <option value="">Selecione uma fonte</option>
                                            @foreach ($fontes as $fonte)
                                                <option value="{{ $fonte->id }}" {{ ($busca_fonte == $fonte->id ) ? 'selected' : '' }}>{{ $fonte->nome }}</option>
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
                </div>
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
                                    <h6>{{ ($pagina->edicao->fonte) ? $pagina->edicao->fonte->nome : '' }}</h6>  
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