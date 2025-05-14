@extends('layouts.relatorios')
@section('content')
    @include("relatorio/pdf/cabecalho")
        @foreach($dados as $key => $noticia)
            <div class="info borda" style="margin-bottom: 0px; clear: both;">
                <div>
                    <div style="float:left; margin-right: 10px;">
                        <span style="font-size: 8px;  margin:0px; padding: 0px;">EMISSORA</span>  
                        <h1 style="font-size: 16px; margin:0px; padding: 0px;">{{ $noticia->fonte }}</h1>  
                    </div>  
                    
                    <div style="float:right; text-align: right;">
                        {{ $noticia->data_formatada }}
                    </div>
                </div>
                <div class="image-container" style="background: white;">
                    <img style="width: 98%; margin-top: 10px;" src="{{ asset('img/noticia-impressa/'.$noticia->ds_caminho_img) }}"/>
                    <div class="hidden-text">
                        {{ $noticia->sinopse }}
                    </div>
                </div>
            </div>          
        @endforeach
@endsection