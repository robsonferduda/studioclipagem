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
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['jornal-impresso/web']]) !!}
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

                    @if($dados->count())
                        <h6 class="px-3">Mostrando {{ $dados->count() }} de {{ $dados->total() }} Arquivos Coletados</h6>
                    @endif

                    {{ $dados->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final])->links('vendor.pagination.bootstrap-4') }}    

                    @foreach ($dados as $key => $noticia)
                        <div class="card">
                            <div class="card-body">                           
                                <div class="row">
                                    <div class="col-lg-2 col-sm-12 mb-1">
                                        @if($noticia->primeiraPagina)
                                            <img src="{{ Storage::disk('s3')->temporaryUrl($noticia->primeiraPagina->path_pagina_s3, '+2 minutes') }}" alt="Página ">
                                        @endif
                                    </div>
                                    <div class="col-lg-10 col-sm-10 mb-1">
                                        <p><strong>{{ ($noticia->fonte) ? $noticia->fonte->nome : 'Não Identificado' }}</strong></p>
                                        <p>{{ ($noticia->titulo) ? $noticia->titulo : '' }}</p>
                                        <p><a href="{{ url('jornal-impresso/edicao/'.$noticia->id.'/paginas') }}">{{ $noticia->paginas->count() }} Páginas</a></p>
                                        
                                        <p>Publicado em: {{ ($noticia->dt_pub) ? \Carbon\Carbon::parse($noticia->dt_pub)->format('d/m/Y H:i:s') : 'Não informado' }}</p>
                                        <p>Coletado em {{ \Carbon\Carbon::parse($noticia->dt_coleta)->format('d/m/Y H:i:s')  }}</p>                        
                                    </div>
                                </div>                               
                            </div>                            
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    $( document ).ready(function() {

       

    });
</script>
@endsection