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
                <div style="clear: both;">
                    <span style="font-size: 8px;">SINOPSE</span>   
                    <p style="font-size: 13px; margin:0px; padding: 0px;">{!! $noticia->sinopse !!}</p> 
                </div>
            </div>          
        @endforeach
@endsection