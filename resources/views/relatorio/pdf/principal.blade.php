@extends('layouts.relatorios')
@section('content')
    @include("relatorio/pdf/cabecalho")
        @foreach($dados as $key => $noticia)
            <div class="image-container" style="background: white;">
                <h1 style="font-size: 16px; margin:0px; padding: 0px;">{{ $noticia->fonte }}</h1>
                <p style="font-weight: bold; margin-top: 0px; margin-bottom: 5px;">{{ $noticia->titulo }}</p>
                @if($noticia->tipo_midia == 'imagem')
                    <img style="width: 98%; margin-top: 10px;" src="{{ asset('img/noticia-impressa/'.$noticia->midia) }}"/>
                @endif
                <div class="hidden-text">
                    {{ $noticia->sinopse }}
                </div>
            </div>          
        @endforeach
@endsection