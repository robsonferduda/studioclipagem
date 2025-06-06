@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o ml-3"></i> Notícias 
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
            <div class="col-md-12">
                {!! Form::open(['id' => 'frm_user_create', 'url' => ['noticias']]) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Termo de busca</label>
                                <input type="text" class="form-control" name="termo" id="termo" placeholder="Termo" value="{{ old('nome') }}">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Data Inicial</label>
                                <input type="text" class="form-control datepicker" name="dt_inicial" id="dt_inicial" placeholder="__/__/____" value="{{ ($dt_inicial) ? \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Data Final</label>
                                <input type="text" class="form-control datepicker" name="dt_final" id="dt_final" placeholder="__/__/____" value="{{ ($dt_final) ? \Carbon\Carbon::parse($dt_final)->format('d/m/Y') : '' }}">
                            </div>
                        </div>
                    </div>           
                    <div class="card-footer text-center mb-3">
                        <button type="submit" class="btn btn-info" name="acao" value="pesquisar"><i class="fa fa-search"></i> Pesquisar</button>
                    </div>
                {!! Form::close() !!} 
            </div>
            <div class="col-md-12">
                @forelse ($dados as $noticia)
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    @switch($noticia->tipo)
                                        @case('web')
                                            @php
                                                $tipo_formatado = '<i class="fa fa-globe"></i> Web';
                                            @endphp
                                        @break
                                        @case('tv')
                                            @php
                                                $tipo_formatado = '<i class="fa fa-television"></i> TV';
                                            @endphp
                                        @break
                                        @case('radio')
                                            @php
                                                $tipo_formatado = '<i class="fa fa-volume-up"></i> Rádio';
                                            @endphp
                                        @break
                                        @case('impresso')
                                            @php
                                                $tipo_formatado = '<i class="fa fa-newspaper-o"></i> Impressos';
                                            @endphp
                                        @break
                                        @default
                                            @php
                                                $tipo_formatado = 'Clipagens';
                                            @endphp
                                        @break                                    
                                    @endswitch
                                    <p style="text-transform: uppercase; font-weight: 600;">{!! $tipo_formatado !!}</p>                            
                                    <h6 style="font-weight: 600;">{{ $noticia->titulo }}</h6>
                                    <h6 style="font-weight: 600;" class="text-muted">{{ $noticia->data_formatada }} - {{ $noticia->fonte }}</h6>
                                    <p class="mb-2">
                                        <span>{{ $noticia->cliente }}</span>
                                        @switch($noticia->sentimento)
                                            @case(-1)
                                                <i class="fa fa-frown-o text-danger"></i>
                                                <a href="{{ url('noticia/'.$noticia->id.'/tipo/'.$noticia->tipo.'/cliente/'.$noticia->tipo.'/sentimento/0/atualizar') }}"><i class="fa fa-ban op-2"></i></a>
                                                <a href="{{ url('noticia/'.$noticia->id.'/tipo/'.$noticia->tipo.'/cliente/'.$noticia->tipo.'/sentimento/1/atualizar') }}"><i class="fa fa-smile-o op-2"></i></a>
                                            @break
                                            @case(0)
                                                <a href="{{ url('noticia/'.$noticia->id.'/tipo/'.$noticia->tipo.'/cliente/'.$noticia->tipo.'/sentimento/-1/atualizar') }}"><i class="fa fa-frown-o op-2"></i></a> 
                                                <i class="fa fa-ban text-primary"></i>
                                                <a href="{{ url('noticia/'.$noticia->id.'/tipo/'.$noticia->tipo.'/cliente/'.$noticia->tipo.'/sentimento/1/atualizar') }}"><i class="fa fa-smile-o op-2"></i></a>                                            
                                            @break
                                            @case(1)
                                                <a href="{{ url('noticia/'.$noticia->id.'/tipo/'.$noticia->tipo.'/cliente/'.$noticia->tipo.'/sentimento/-1/atualizar') }}"><i class="fa fa-frown-o op-2"></i></a>
                                                <a href="{{ url('noticia/'.$noticia->id.'/tipo/'.$noticia->tipo.'/cliente/'.$noticia->tipo.'/sentimento/0/atualizar') }}"><i class="fa fa-ban op-2"></i></a>
                                                <i class="fa fa-smile-o text-success"></i>
                                            @break                                            
                                        @endswitch
                                    </p>
                                    <p>
                                        {!! $noticia->sinopse !!}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div style="text-align: right">
                                        @if($noticia->tipo == 'web')
                                            <a title="Extrair Imagem" class="btn btn-warning btn-sm" href="{{ url('noticia/web/importar-imagem',$noticia->id) }}" role="button"><i class="fa fa-picture-o"> </i></a>
                                            <a title="Editar" class="btn btn-info btn-sm" href="{{ url('noticia/web/'.$noticia->id.'/editar') }}" target="_BLANK" role="button"><i class="fa fa-edit"> </i></a>
                                        @endif
                                        @if($noticia->tipo == 'impresso')
                                            <a title="Editar" class="btn btn-info btn-sm" href="{{ url('noticia-impressa/'.$noticia->id.'/editar') }}" target="_BLANK" role="button"><i class="fa fa-edit"> </i></a>
                                        @endif
                                        <a title="Gerar PDF" class="btn btn-danger btn-sm" href="{{ url("relatorios/".$noticia->tipo."/pdf/".$noticia->id) }}" role="button"><i class="fa fa-file-pdf-o"> </i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    
                @endforelse
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    $( document ).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');

    });
</script>
@endsection