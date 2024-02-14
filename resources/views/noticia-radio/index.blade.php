@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-volume-up ml-3"></i> Rádio
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('radio/noticias/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                    <a href="{{ url('radio/estatisticas') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Estatísticas</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['radios']]) !!}
                    <div class="form-group m-3 w-70">
                        <div class="row mb-0">
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Data Inicial</label>
                                    <input type="text" class="form-control datepicker dt-search" name="dt_inicial" id="dt_inicial" required="true" value="{{ ($dt_inicial) ? date('d/m/Y', strtotime($dt_inicial)) : date('d/m/Y') }}" placeholder="__/__/____">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Data Final</label>
                                    <input type="text" class="form-control datepicker dt-search" name="dt_final" required="true" value="{{ ($dt_final) ? date('d/m/Y', strtotime($dt_final)) : date('d/m/Y') }}" placeholder="__/__/____">
                                </div>
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <div class="form-group">
                                    <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca na sinopse</span></label>
                                    <input type="text" class="form-control" name="termo" id="termo" minlength="3" placeholder="Termo" value="{{ $termo }}">
                                </div>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-md-12 checkbox-radios mb-0">
                                <button type="submit" id="btn-find" class="btn btn-primary mt-4 btn-search"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}

                    @if($noticias->count())
                        <h6 class="px-3">Mostrando {{ $noticias->count() }} de {{ $noticias->total() }} Notícias</h6>
                    @endif

                    {{ $noticias->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}  
            </div>
            <div class="col-md-12">
                @foreach($noticias as $noticia)
                    <div class="card ml-2 mr-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <span class="dt_noticia_box">{!! !empty($noticia->dt_noticia) ? date('d/m/Y', strtotime($noticia->dt_noticia)) : '' !!}</span>
                                    <h6>{!! ($noticia->cliente and $noticia->cliente->pessoa) ? $noticia->cliente->pessoa->nome : 'Nenhum cliente vinculado' !!}</h6>
                                    <p>{!! $noticia->emissora->ds_emissora ?? '' !!} - {!! $noticia->programa->nome ?? 'Nenhum Programa Vinculado' !!} {{ ($noticia->horario) ? ' - '.$noticia->horario : "" }}</p>
                                    <p>{!! $noticia->sinopse !!}</p>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">                                    
                                    <audio controls>
                                        <source src="{{ asset('noticias-radio/'. substr($noticia->arquivo, 0, 10).'/'.$noticia->arquivo) }}" type="audio/ogg">
                                        <source src="{{ asset('noticias-radio/'. substr($noticia->arquivo, 0, 10).'/'.$noticia->arquivo) }}" type="audio/mpeg">
                                      Your browser does not support the audio element.
                                      </audio>
                                    <div style="position: absolute; bottom: 5px; right: 5px;">
                                        <a title="Editar" href="{{ url('radio/noticias/'.$noticia->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                        <a title="Excluir" href="{{ url('radio/noticias/'.$noticia->id.'/remover') }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-trash fa-2x"></i></a>
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
@endsection