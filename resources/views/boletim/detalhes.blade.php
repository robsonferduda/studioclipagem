@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="card-title ml-2">
                        <i class="fa fa-file-o"></i> Boletim 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ $boletim->titulo }}
                    </h5>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('boletins') }}" class="btn btn-primary pull-right"><i class="fa fa-file-o"></i> Boletins</a>
                    <a href="{{ url('boletim/'.$boletim->id.'/enviar') }}" class="btn btn-success pull-right"><i class="fa fa-send"></i> Verificar e Enviar</a>
                    <a href="{{ url('boletim/'.$boletim->id.'/visualizar') }}" class="btn btn-warning pull-right"><i class="fa fa-eye"></i> Visualizar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @include("boletim/corpo_boletim")  
        </div>
    </div>
</div> 
@endsection