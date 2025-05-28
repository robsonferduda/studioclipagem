@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row ml-1">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Web 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Detalhes 
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('noticia/web/dashboard') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('noticia/web') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-newspaper-o"></i> Listar Notícias</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row"> 
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>
                <div class="col-lg-12 col-md-12 m-3">
                    <p><strong>{{ $noticia->titulo_noticia }}</strong></p>
                    <p>{{ ($noticia->fonte) ? $noticia->fonte->nome : 'Não identificada' }} - {{ \Carbon\Carbon::parse($noticia->data_noticia)->format('d/m/Y') }} - Coletada em {{ \Carbon\Carbon::parse($noticia->data_insert)->format('d/m/Y H:i:s') }}</p>
                    <p><a class="text-info" href="{{ $noticia->url_noticia }}" target="_BLANK"><span><i class="fa fa-globe"></i> Ver Original</span></a></p>
                    <p>
                        {!! $noticia->conteudo->conteudo !!}
                    </p>
                    
                    @if($noticia->ds_caminho_img)
                        <img src="{{ asset('img/noticia-web/'.$noticia->ds_caminho_img) }}" alt="Página {{ $noticia->ds_caminho_img }}">
                    @elseif($noticia->path_screenshot)                                         
                        <img src="{{ Storage::disk('s3')->temporaryUrl($noticia->path_screenshot, '+30 minutes') }}" 
                                                alt="Print notícia {{ $noticia->noticia_id }}" 
                                                class="img-fluid img-thumbnail" 
                                                style="width: 100%; height: auto; border: none;">
                    @else
                        <img src="{{ asset('img/no-print.png') }}" 
                                                alt="Sem Print" 
                                                class="img-fluid img-thumbnail" 
                                                style="width: 100%; height: auto; border: none;">
                    @endif
                </div>   
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    $( document ).ready(function() {

    });
</script>
@endsection