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
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['jornal-impresso/noticias']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" required="true" value="{{ date('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ date('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-8 col-sm-12">
                                    <div class="form-group">
                                        <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca</span></label>
                                        <input type="text" class="form-control" name="termo" id="termo" minlength="3" placeholder="Termo" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Fonte</label>
                                        <select class="form-control select2" name="regra" id="regra">
                                            <option value="">Selecione uma fonte</option>
                                            @foreach ($emissoras as $emissora)
                                                <option value="{{ $emissora->id }}">{{ $emissora }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}

                    @if(count($dados) > 0)
                        <h6 class="px-3">Mostrando {{ $dados->count() }} de {{ $dados->total() }} Páginas</h6>

                        {{ $dados->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final])->links('vendor.pagination.bootstrap-4') }}
                    @endif

                    @foreach ($dados as $key => $noticia)
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2 col-sm-12">
                                        @if($noticia->fonte)
                                            <img src="{{ asset('img/noticia-impressa/'.$noticia->ds_caminho_img) }}" alt="..." class="img-thumbnail">
                                        @else

                                        @endif
                                    </div>
                                    <div class="col-lg-10 col-sm-12">
                                        <h6>{{ $noticia->titulo }}</h6>
                                        <p>{{ ($noticia->fonte) ? $noticia->fonte->nome : 'Não identificada' }} - {{ \Carbon\Carbon::parse($noticia->dt_clipagem)->format('d/m/Y') }}</p>
                                        <p>
                                            {{ Str::limit($noticia->texto, 800, " ...") }}
                                        </p>
                                        @if($noticia->nu_pagina_atual == 1)
                                            <p>Primeira Página</p>
                                        @else
                                            <p>Página <strong>{{ $noticia->nu_pagina_atual }}</strong> de <strong>{{ $noticia->nu_paginas_total }}</strong></p>
                                        @endif
                                        <p><strong>Retorno de Mídia</strong>: {!! ($noticia->valor_retorno) ? "R$ ".$noticia->valor_retorno : '<span class="text-danger">Não calculado</span>' !!}</p>
                                        <div>
                                            <a class="btn btn-success btn-sm" href="{{ asset('jornal-impresso/noticia/editar/'.$noticia->id) }}"><i class="fa fa-edit"> </i> Editar Notícia</a>
                                            <a class="btn btn-danger btn-sm" download target="_blank" href="{{ asset('jornal-impresso/processados/'.($noticia->fila) ? $noticia->fila : '') }}" role="button"><i class="fa fa-file-pdf-o"> </i> Documento Original</a>
                                            <a class="btn btn-primary btn-sm" download target="_blank" href="{{ asset('jornal-impresso/'.$noticia->fonte->codigo.'/'.\Carbon\Carbon::parse($noticia->dt_clipagem)->format('Ymd').'/img/pagina_'.$noticia->nu_pagina_atual.'.png') }}" role="button"><i class="fa fa-file-image-o"> </i> Página Atual</a>
                                            <a class="btn btn-success btn-sm" href="{{ asset('jornal-impresso/noticia/'.$noticia->id) }}" role="button"><i class="fa fa-eye"> </i> Detalhes</a>
                                        </div>
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