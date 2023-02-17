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
                    <a href="{{ url('jornal-web') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="nc-icon nc-minimal-left"></i> Voltar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row"> 
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>
                <div class="col-lg-12 col-md-12">
                    <h6>{{ $noticia->titulo }}</h6>
                    <p>{{ ($noticia->fonte) ? $noticia->fonte->nome : 'Não identificada' }} - {{ \Carbon\Carbon::parse($noticia->dt_clipagem)->format('d/m/Y') }}</p>
                    <p>
                        {{ $noticia->texto }}
                    </p>
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