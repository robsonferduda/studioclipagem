@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-volume-up"></i> Rádio 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Dashboard 
                    </h4>
                </div>
                <div class="col-md-4">
                    
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
                                    <div class="col-lg-9 col-sm-12">
                                        <h6>{{ $noticia->titulo }}</h6>
                                        <p>{{ ($noticia->fonte) ? $noticia->fonte->ds_fonte : 'Fonte desconhecida' }} - {{ \Carbon\Carbon::parse($noticia->dt_clipagem)->format('d/m/Y') }}</p>
                                        <p>
                                            {{ Str::limit($noticia->texto, 450, " ...") }}
                                        </p>
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