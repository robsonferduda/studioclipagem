@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-7">
                    <h4 class="card-title ml-2">
                        <i class="fa fa-file-o"></i> Boletim 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ $boletim->titulo }}
                    </h4>
                </div>
                <div class="col-md-5">
                    <a href="{{ url('boletins') }}" class="btn btn-primary pull-right"><i class="fa fa-file-o"></i> Boletins</a>
                    <a href="{{ url('boletim/'.$boletim->id.'/visualizar') }}" class="btn btn-warning pull-right"><i class="fa fa-eye"></i> Visualizar</a>
                    <a href="{{ url('boletim/'.$boletim->id.'/enviar') }}" class="btn btn-success pull-right"><i class="fa fa-send"></i> Verificar Envio</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                <span>Foram encontradas {{ count($dados) }} notícias</span><hr/>
                @php
                    $area = "";
                    $tipo = "";
                    $tipo_formatado = "";
                    $flag = false;
                @endphp
                @foreach($dados as $key => $noticia)
                    @if($noticia->area != null)

                        @if($noticia->area != $area and $flag)
                            </div>
                        @endif

                        @if($noticia->area != $area)
                            <div class="px-3 py-2 mb-3" style="">
                                <p style="color: #2196f3; font-size: 20px !important; text-transform: uppercase; border-bottom: 1px solid #2196f3;"><i class="fa fa-newspaper-o" aria-hidden="true"></i> {{ $noticia->area }}</p>
                            @php
                                $flag = true;
                            @endphp
                        @endif

                        @if($noticia->clipagem != $tipo or($noticia->clipagem == $tipo and $noticia->area != $area))
                            @switch($noticia->clipagem)
                                @case('web')
                                    @php
                                        $tipo_formatado = '<i class="fa fa-globe"></i> Clipagens de Web';
                                    @endphp
                                @break
                                @case('tv')
                                    @php
                                        $tipo_formatado = '<i class="fa fa-television"></i> Clipagens de TV';
                                    @endphp
                                @break
                                @case('radio')
                                    @php
                                        $tipo_formatado = '<i class="fa fa-volume-up"></i> Clipagens de Rádio';
                                    @endphp
                                @break
                                @case('jornal')
                                    @php
                                        $tipo_formatado = '<i class="fa fa-newspaper-o"></i> Clipagens de Jornal';
                                    @endphp
                                @break
                                @default
                                    @php
                                        $tipo_formatado = 'Clipagens';
                                    @endphp
                                @break                                    
                            @endswitch
                            <p style="text-transform: uppercase; font-weight: 600;">{!! $tipo_formatado !!}</p>
                        @endif

                        @if($noticia->clipagem == 'tv')
                            
                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                                <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->data)) }}</p>
                                <p style="margin-bottom: 0px;"><strong>Emissora:</strong> {{ $noticia->INFO1 }}</p>
                                @if($noticia->INFO2)
                                    <p style="margin-bottom: 0px;"><strong>Programa:</strong> {{ $noticia->INFO2 }}</p>
                                @endif
                                <p style="margin-bottom: 0px;"><strong>Duração:</strong> {{ gmdate("H:i:s", $noticia->segundos)}}</p>
                                <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>

                                @php 
                                    $file_headers = @get_headers(env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.mp4');
                                @endphp
                                @if(!(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found'))
                                    <p style="margin-bottom: 0px;"><strong>Link:</strong> <a href="{{ env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.mp4' }}" download>Download</a></p>
                                @endif

                                <video width="320" height="240" controls>
                                    <source src="{{ env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.mp4' }}" type="video/mp4">
                                    Seu navegador não suporta a execução de vídeos, faça o download para poder assitir.
                                </video>
                            </div>

                        @elseif($noticia->clipagem == 'radio')

                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                                <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->data)) }}</p>
                                <p style="margin-bottom: 0px;"><strong>Emissora:</strong> {{ $noticia->INFO1 }}</p>
                                @if($noticia->INFO2)
                                    <p style="margin-bottom: 0px;"><strong>Programa:</strong> {{ $noticia->INFO2 }}</p>
                                @endif
                                <p style="margin-bottom: 0px;"><strong>Duração:</strong> {{ gmdate("H:i:s", $noticia->segundos)}}</p>
                                <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>

                                @php 
                                    $file_headers = @get_headers(env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.mp3');
                                @endphp
                                @if(!(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found'))
                                    <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.mp3' }}" download>Download</a></p>
                                @endif

                                <audio width="320" height="240" controls>
                                    <source src="{{ env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.mp3' }}" type="audio/mpeg">
                                    Seu navegador não suporta a execução de áudios, faça o download para poder ouvir.
                                </audio>
                            </div>
                        
                        @elseif($noticia->clipagem == 'web')

                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                                <p style="margin-bottom: 0px;"><strong>Título:</strong> {{ $noticia->titulo }}</p>
                                <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->data)) }}</p>
                                <p style="margin-bottom: 0px;"><strong>Veículo:</strong> {{ $noticia->INFO1 }}</p>
                                @if($noticia->INFO2)
                                    <p style="margin-bottom: 0px;"><strong>Seção:</strong> {{ $noticia->INFO2 }}</p>
                                @endif
                                <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>
                                <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ $noticia->link }}" target="BLANK" download>Acesse</a></p>

                                @php 
                                    $file_headers = @get_headers($noticia->url);
                                @endphp
                                @if(!(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found'))
                                    <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Print:</strong> <a href="{{ $noticia->url }}" target="BLANK" download>Veja</a></p>
                                @endif

                            </div>                            

                        @else

                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                                <p style="margin-bottom: 0px;"><strong>Título:</strong> {{ $noticia->titulo }}</p>
                                <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->data)) }}</p>
                                <p style="margin-bottom: 0px;"><strong>Veículo:</strong> {{ $noticia->INFO1 }}</p>
                                @if($noticia->INFO2)
                                    <p style="margin-bottom: 0px;"><strong>Seção:</strong> {{ $noticia->INFO2 }}</p>
                                @endif
                                <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>

                                @php 
                                    $file_headers = @get_headers($noticia->url);
                                @endphp
                                @if(!(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found'))
                                    <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ $noticia->url }}" download>Download</a></p>
                                @endif
                            </div>

                        @endif

                        @php
                            $area = $noticia->area;
                            $tipo = $noticia->clipagem;
                        @endphp

                    @endif
                 @endforeach
            </div>        
        </div>
    </div>
</div> 
@endsection