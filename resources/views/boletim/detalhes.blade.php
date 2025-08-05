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
                    <a href="{{ url('boletim/'.$boletim->id.'/enviar') }}" class="btn btn-success pull-right"><i class="fa fa-send"></i> Verificar e Enviar</a>
                    <a href="{{ url('boletim/'.$boletim->id.'/visualizar') }}" class="btn btn-warning pull-right"><i class="fa fa-eye"></i> Visualizar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                <span class="pull-right mr-3">Total de notícias: {{ count($noticias_impresso) + count($noticias_web) + count($noticias_radio) + count($noticias_tv) }}</span>
                @if(count($noticias_impresso) > 0)
                    <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-newspaper-o"></i> Clipagens de Jornal</p>
                @endif
                @foreach($noticias_impresso as $key => $noticia)
                    <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                        <p style="margin-bottom: 0px;">
                            <strong>Id:</strong> {!! $noticia['id'] !!}
                            <a title="Editar" href="{{ url('noticia-impressa/'.$noticia['id'].'/editar') }}" class="text-info" target="_blank"><i class="fa fa-edit"></i>Editar</a>
                        </p>
                        <p style="margin-bottom: 0px;"><strong>Título:</strong> {!! ($noticia['titulo']) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
                        <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
                        <p style="margin-bottom: 0px;"><strong>Veículo:</strong> {{ $noticia['fonte'] }}</p>
                        @if($noticia['secao'])
                            <p style="margin-bottom: 0px;"><strong>Seção:</strong> {{ ($noticia['secao']) ? $noticia['secao'] : 'Não informado' }}</p>
                        @endif
                        <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
                        <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset($noticia['path_midia']) }}" target="_blank">Veja</a></p>
                    </div>
                @endforeach

                @if(count($noticias_web) > 0)
                    <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-globe"></i> Clipagens de Web</p>
                @endif
                @foreach($noticias_web as $key => $noticia)
                    <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                        <p style="margin-bottom: 0px;">
                            <strong>Id:</strong> {!! $noticia['id'] !!}
                            <a title="Editar" href="{{ url('noticia/web/'.$noticia['id'].'/editar') }}" class="text-info" target="_blank"><i class="fa fa-edit"></i>Editar</a>
                        </p>
                        <p style="margin-bottom: 0px;"><strong>Título:</strong> {!! ($noticia['titulo']) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
                        <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
                        <p style="margin-bottom: 0px;"><strong>Veículo:</strong> {{ $noticia['fonte'] }}</p>
                        @if($noticia['secao'])
                            <p style="margin-bottom: 0px;"><strong>Seção:</strong> {{ ($noticia['secao']) ? $noticia['secao'] : 'Não informado' }}</p>
                        @endif
                        <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
                        <p style="margin-bottom: 0px;"><strong>Link:</strong><a href="{{ $noticia['url_noticia'] }}" target="_blank"> Acesse</a></p>
                        @if($dados['fl_print'])
                            <p style="margin-bottom: 10px;">
                                <strong>Print:</strong>
                                @if($noticia['erro'])
                                    <span style="color: red;">Imagem com erro</span>
                                @else
                                    <a href="{{ asset($noticia['path_midia']) }}" target="_blank"> Veja</a>
                                @endif
                            </p>
                        @endif
                    </div>
                @endforeach

                @if(count($noticias_tv) > 0)
                    <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-tv"></i> Clipagens de TV</p>
                @endif
                @foreach($noticias_tv as $key => $noticia)
                    <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                        <p style="margin-bottom: 0px;">
                            <strong>Id:</strong> {!! $noticia['id'] !!}
                            <a title="Editar" href="{{ url('noticia/tv/'.$noticia['id'].'/editar') }}" class="text-info" target="_blank"><i class="fa fa-edit"></i>Editar</a>
                        </p>
                        <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
                        <p style="margin-bottom: 0px;"><strong>Emissora:</strong> {{ $noticia['fonte'] }}</p>
                        <p style="margin-bottom: 0px;"><strong>Programa:</strong> {{ $noticia['programa'] }}</p>
                        <p style="margin-bottom: 0px;"><strong>Duração:</strong> {{ $noticia['duracao'] }}</p>
                        <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
                        <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset($noticia['path_midia']) }}" target="_blank">Assista</a></p>
                    </div>
                @endforeach

                @if(count($noticias_radio) > 0)
                    <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-volume-up"></i> Clipagens de Rádio</p>
                @endif
                @foreach($noticias_radio as $key => $noticia)
                    <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                        <p style="margin-bottom: 0px;">
                            <strong>Id:</strong> {!! $noticia['id'] !!}
                            <a title="Editar" href="{{ url('noticia-radio/'.$noticia['id'].'/editar') }}" class="text-info" target="_blank"><i class="fa fa-edit"></i>Editar</a>
                        </p>
                        <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
                        <p style="margin-bottom: 0px;"><strong>Emissora:</strong> {{ $noticia['fonte'] }}</p>
                        <p style="margin-bottom: 0px;"><strong>Programa:</strong> {{ $noticia['programa'] }}</p>
                        <p style="margin-bottom: 0px;"><strong>Duração:</strong> {{ $noticia['duracao'] }}</p>
                        <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
                        <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset($noticia['path_midia']) }}" target="_blank">Ouça</a></p>
                    </div>
                @endforeach
            </div>  
        </div>
    </div>
</div> 
@endsection