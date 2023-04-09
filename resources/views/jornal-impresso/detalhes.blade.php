@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o"></i> Jornal Impresso 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Detalhes 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('impresso') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-newspaper-o"></i> Dashboard</a>
                    <a href="{{ url('jornal-impresso/processamento') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="fa fa-cogs"></i> Processamento</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row"> 
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>

                <div class="col-lg-3 col-md-3">
                    @if($noticia->fonte)
                        <img src="{{ asset('jornal-impresso/'.$noticia->fonte->id_knewin.'/'.\Carbon\Carbon::parse($noticia->dt_clipagem)->format('Ymd').'/img/pagina_'.$noticia->nu_pagina_atual.'.png') }}" alt="..." class="img-thumbnail">
                    @else

                    @endif
                </div>

                <div class="col-lg-9 col-md-9">
                    <h6>{{ $noticia->titulo }}</h6>
                    <p>{{ ($noticia->fonte) ? $noticia->fonte->ds_fonte : 'Não identificada' }} - {{ \Carbon\Carbon::parse($noticia->dt_clipagem)->format('d/m/Y') }}</p>
                    @if($noticia->nu_pagina_atual == 1)
                        <p>Primeira Página</p>
                    @else
                        <p>Página <strong>{{ $noticia->nu_pagina_atual }}</strong> de <strong>{{ $noticia->nu_paginas_total }}</strong></p>
                    @endif
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