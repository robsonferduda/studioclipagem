@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Jornal Web 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ ($noticia->fonte) ? $noticia->fonte->nome : 'Não identificada' }} 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Detalhes 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url()->previous() }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="nc-icon nc-minimal-left"></i> Voltar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row"> 
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>
                <div class="col-lg-12 col-md-12">
                    <p><strong>{{ $noticia->titulo_noticia }}</strong></p>
                    <p>{{ ($noticia->fonte) ? $noticia->fonte->nome : 'Não identificada' }} - {{ \Carbon\Carbon::parse($noticia->data_noticia)->format('d/m/Y') }} - Coletada em {{ \Carbon\Carbon::parse($noticia->data_insert)->format('d/m/Y H:i:s') }}</p>
                    <p>
                        {!! $noticia->conteudo->conteudo !!}
                    </p>
                    <p><a href="{{ $noticia->url_noticia }}" target="_BLANK">Ver Original</a></p>
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