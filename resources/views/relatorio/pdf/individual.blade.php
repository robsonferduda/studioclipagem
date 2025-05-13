@extends('relatorio.pdf.padrao_individual')
@section('content')
    @include("relatorio/pdf/cabecalho_individual")
    <p style="font-weight: bold; margin-top: 0px; margin-bottom: 5px;">{{ $noticia->titulo }}</p>
    <div>
        {{ $noticia->sinopse }}
    </div>
    <img style="width: 98%; margin-top: 10px;" src="{{ asset('img/noticia-impressa/'.$noticia->ds_caminho_img) }}"/>
@endsection