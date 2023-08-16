@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-file-text-o ml-3"></i> Pautas 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Vincular Notícia
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('pautas') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa  fa-table"></i> Pautas</a>
                    <a href="{{ url('pauta/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar Pauta</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-lg-12 col-sm-12">
                <p class="mb-1"><strong>Cliente</strong>: {{ $pauta->descricao }}</p>
                <p class="mb-1"><strong>Pauta</strong>: {{ $pauta->descricao }}</p>
                <p><strong>Notícias do Cliente</strong></p>
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm-pautas', 'class' => 'form-horizontal', 'url' => ['pautas']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="form-check mt-3">
                                @foreach($noticias as $noticia)
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="true">
                                            {{ $noticia->sinopse }}
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>            
        </div>
    </div>
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() { 

            var host =  $('meta[name="base-url"]').attr('content');

        });
    </script>
@endsection