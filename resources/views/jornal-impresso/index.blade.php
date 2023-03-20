@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o"></i> Jornal Impresso
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Dashboard
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('jornal-impresso/listar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-newspaper-o"></i> Jornais</a>
                    <a href="{{ url('jornal-impresso/upload') }}" class="btn btn-info pull-right mr-1"><i class="fa fa-upload"></i> Upload</a>
                    <a href="{{ url('jornal-impresso/processamento') }}" class="btn btn-warning pull-right mr-1"><i class="fa fa-cogs"></i> Processamento</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-9 col-sm-9">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['buscar-impresso']]) !!}
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
                                            @foreach ($fontes as $fonte)
                                                <option value="{{ $fonte->id }}">{{ $fonte->ds_fonte }}</option>
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

                    @if($dados->count())
                        <h6 class="px-3">Mostrando {{ $dados->count() }} de {{ $dados->total() }} Páginas</h6>
                    @endif

                    {{ $dados->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final])->links('vendor.pagination.bootstrap-4') }}

                    @foreach ($dados as $key => $noticia)
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2 col-sm-12">
                                        @if($noticia->fonte)
                                            <img src="{{ asset('jornal-impresso/'.$noticia->fonte->id_knewin.'/'.\Carbon\Carbon::parse($noticia->dt_clipagem)->format('Ymd').'/img/pagina_'.$noticia->nu_pagina_atual.'.png') }}" alt="..." class="img-thumbnail">
                                        @else

                                        @endif
                                    </div>
                                    <div class="col-lg-10 col-sm-12">
                                        <h6>{{ $noticia->titulo }}</h6>
                                        <p>{{ ($noticia->fonte) ? $noticia->fonte->ds_fonte : 'Não identificada' }} - {{ \Carbon\Carbon::parse($noticia->dt_clipagem)->format('d/m/Y') }}</p>
                                        <p>
                                            {{ Str::limit($noticia->texto, 800, " ...") }}
                                        </p>
                                        @if($noticia->nu_pagina_atual == 1)
                                            <p>Primeira Página</p>
                                        @else
                                            <p>Página <strong>{{ $noticia->nu_pagina_atual }}</strong> de <strong>{{ $noticia->nu_paginas_total }}</strong></p>
                                        @endif
                                        <div>
                                            <a class="btn btn-danger btn-sm" download target="_blank" href="{{ asset('jornal-impresso/processados/'.$noticia->fila->ds_arquivo) }}" role="button"><i class="fa fa-file-pdf-o"> </i> Documento Original</a>
                                            <a class="btn btn-primary btn-sm" download target="_blank" href="{{ asset('jornal-impresso/'.$noticia->fonte->id_knewin.'/'.\Carbon\Carbon::parse($noticia->dt_clipagem)->format('Ymd').'/img/pagina_'.$noticia->nu_pagina_atual.'.png') }}" role="button"><i class="fa fa-file-image-o"> </i> Página Atual</a>
                                            <a class="btn btn-success btn-sm" href="{{ asset('jornal-impresso/noticia/'.$noticia->id) }}" role="button"><i class="fa fa-eye"> </i> Detalhes</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card card-stats">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-newspaper-o text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                        <p class="card-category">Jornal Impresso</p>
                                        <p class="card-title">{{ $total_impresso }}</p>
                                        <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-calendar"></i>
                                    Última Atualização em {{ \Carbon\Carbon::parse($ultima_atualizacao_impresso)->format('d/m/Y H:i:s') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card card-stats">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-warning">
                                        <i class="nc-icon nc-chart-bar-32 text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                        <p class="card-category">Páginas Coletadas</p>
                                        <p class="card-title"></p>
                                        <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-calendar"></i>
                                    Última Atualização em 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
