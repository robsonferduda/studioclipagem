@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row ml-1">
                <div class="col-md-9">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Web
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Prints
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Problemas
                    </h4>
                </div>
                <div class="col-md-3">
                    <a href="{{ url('noticia/web') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Notícias</a>
                </div>
            </div>
        </div>
        <div class="card-body ml-3 mr-3">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['noticia/web/prints']]) !!}
                        <div class="form-group">
                            <div class="row">
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
                                <div class="col-md-2">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3" style="margin-top: 25px;"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
                <div class="col-lg-12 col-md-3 mb-12">
                    <h6>NOTÍCIAS COM ERRO DE PRINT</h6>
                    @forelse($erros as $key => $noticia)
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="float-left">
                                            <i class="fa fa-file-image-o text-danger mr-3 mt-4" aria-hidden="true" style="font-size: 35px;"></i>
                                        </div>
                                        <div class="float-left">
                                            <p class="mb-1"><strong>{{ $noticia->titulo_noticia }}</strong></p>
                                            <p class="mb-1 text-muted"><a href="{{ url('jornal-impresso/web/download/'.$noticia->id) }}">Ver notícia</a></p>
                                            <p>Notícia cadastrada em {{ \Carbon\Carbon::parse($noticia->created_at)->format('d/m/Y H:i:s') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="row">
                            <div class="col-lg-12">
                                <p class="text-danger">Não foram encontrados prints com problemas</p>
                            </div>
                        </div>
                    @endforelse
            </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');

    });
</script>
@endsection