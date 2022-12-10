@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o"></i> Jornal Impresso 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Dashboard 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('jornal-impresso/upload') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-upload"></i> Upload</a>
                    <a href="{{ url('jornal-impresso/processamento') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-cogs"></i> Processamento</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-6 col-sm-6">
                    @foreach ($dados as $key => $noticia)
                        <div class="card">
                            <div class="card-body">                           
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <img src="{{ asset("jornal-impresso/".$noticia->fonte->id_knewin."/20221116/img/pagina_".$key + 1.".png") }}" alt="..." class="img-thumbnail">
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <h6>{{ $noticia->titulo }}</h6>
                                        <p>{{ $noticia->fonte->ds_fonte }} - {{ \Carbon\Carbon::parse($noticia->dt_clipagem)->format('d/m/Y') }}</p>
                                        <p>
                                            {{ Str::limit($noticia->texto, 450, " ...") }}
                                        </p>
                                        @if($noticia->nu_pagina_atual == 1)
                                            <p>Primeira Página</p>
                                        @else
                                            <p>Página <strong>{{ $noticia->nu_pagina_atual }}</strong> de <strong>{{ $noticia->nu_paginas_total }}</strong></p>
                                        @endif
                                    </div>
                                </div>                               
                            </div>
                            <div class="card-footer">
                            <hr class="p-0">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="pull-right">
                                            <button class="btn btn-success btn-round btn-icon btn-sm">
                                            <i class="nc-icon nc-simple-add"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection