@extends('relatorio.pdf.padrao_individual')
@section('content')
    @include("relatorio/pdf/cabecalho_individual")
    <p style="font-weight: bold; margin-top: 0px; margin-bottom: 5px;">{{ $noticia->titulo }}</p>
    <div class="image-container" style="background: white;">
        <img style="width: 98%; margin-top: 10px;" src="{{ asset('img/noticia-impressa/'.$noticia->ds_caminho_img) }}"/>
        <div class="hidden-text">
            {{ $noticia->sinopse }}
        </div>
    </div>
@endsection