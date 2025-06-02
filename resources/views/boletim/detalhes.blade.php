@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="card-title ml-2">
                        <i class="fa fa-file-o"></i> Boletim 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ $boletim->titulo }}
                    </h5>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('boletins') }}" class="btn btn-primary pull-right"><i class="fa fa-file-o"></i> Boletins</a>
                    <a href="{{ url('boletim/'.$boletim->id.'/enviar') }}" class="btn btn-success pull-right"><i class="fa fa-send"></i> Verificar Envio</a>
                    <a href="{{ url('boletim/'.$boletim->id.'/visualizar') }}" class="btn btn-warning pull-right"><i class="fa fa-eye"></i> Visualizar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12">
                        <p class="mb-0"><strong>Cliente:</strong> {{ $boletim->cliente->nome }}</p>
                        <p class="mb-0"><strong>Título:</strong> {{ $boletim->titulo }}</p>
                        <p class="mb-0"><strong>Data de Criação:</strong> {{ date('d/m/Y H:i', strtotime($boletim->created_at)) }}</p>
                        <p class="mb-0"><strong>Data de Envio:</strong> {{ ($boletim->dt_envio) ? date('d/m/Y H:i', strtotime($boletim->dt_envio)) : 'Não enviado' }}</p>
                        <p class="mb-0"><strong>Responsável:</strong> {!! ($boletim->usuario) ? $boletim->usuario->name : '<span>Não informado</span>'  !!}</p>
                        <p class="mb-0"><strong>Total de visualizações:</strong> {{ ( $boletim->total_views) ? $boletim->total_views : 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <span class="pull-right mr-3">Total de notícias: {{ count($noticias_impresso) + count($noticias_web) + count($noticias_radio) + count($noticias_tv) }}</span>
            </div>
            <div class="col-md-12">
                <hr/>
            </div>
            <div class="col-md-12">
                        <span class="pull-right mr-3">Total de notícias: {{ count($noticias_impresso) }}</span>
                        @if(count($noticias_impresso) > 0)
                            <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-newspaper-o"></i> Clipagens de Jornal</p>
                        @endif
                        @foreach($noticias_impresso as $key => $noticia)
                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                                <p style="margin-bottom: 0px;"><strong>Título:</strong> {!! ($noticia->titulo) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
                                <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->dt_clipagem)) }}</p>
                                <p style="margin-bottom: 0px;"><strong>Veículo:</strong> {{ $noticia->fonte->nome }}</p>
                                <p style="margin-bottom: 0px;"><strong>Seção:</strong> {{ $noticia->INFO2 }}</p>
                                <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>
                                <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset('img/noticia-impressa/'.$noticia->ds_caminho_img) }}" download>Download</a></p>
                            </div>
                        @endforeach
            </div>                        
        </div>
    </div>
</div> 
@endsection