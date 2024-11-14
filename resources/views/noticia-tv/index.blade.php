@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-tv ml-3"></i> Televisão
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('tv/noticias/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                    <a href="{{ url('tv/estatisticas') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Estatísticas</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['tv/noticias']]) !!}
                    <div class="form-group m-3 w-70">
                        <div class="row mb-0">
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Data Inicial</label>
                                    <input type="text" class="form-control datepicker dt-search" name="dt_inicial" id="dt_inicial" required="true" value="{{ ($dt_inicial) ? date('d/m/Y', strtotime($dt_inicial)) : date('d/m/Y') }}" placeholder="__/__/____">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Data Final</label>
                                    <input type="text" class="form-control datepicker dt-search" name="dt_final" required="true" value="{{ ($dt_final) ? date('d/m/Y', strtotime($dt_final)) : date('d/m/Y') }}" placeholder="__/__/____">
                                </div>
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <div class="form-group">
                                    <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca na sinopse</span></label>
                                    <input type="text" class="form-control" name="termo" id="termo" minlength="3" placeholder="Termo" value="{{ $termo }}">
                                </div>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-md-12 checkbox-radios mb-0">
                                <button type="submit" id="btn-find" class="btn btn-primary mt-4 btn-search"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}

                    @if($noticias->count())
                        <h6 class="px-3">Mostrando {{ $noticias->count() }} de {{ $noticias->total() }} Notícias</h6>
                    @endif

                    {{ $noticias->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}  
            </div>
            <div class="col-md-12">
                @foreach($noticias as $noticia)
                    <div class="card ml-2 mr-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <span class="dt_noticia_box">{!! !empty($noticia->dt_noticia) ? date('d/m/Y', strtotime($noticia->dt_noticia)) : '' !!}</span>
                                    <h6>{!! ($noticia->cliente and $noticia->cliente->pessoa) ? $noticia->cliente->pessoa->nome : 'Nenhum cliente vinculado' !!}</h6>
                                    <p>{!! $noticia->emissora->nome_emissora ?? '' !!} {{ ($noticia->horario) ? ' - '.$noticia->horario : "" }}</p>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">  
                                    <div class="row">  
                                        <div class="col-lg-3 col-md-3 col-sm-12">                               
                                            <video width="100%" height="auto" controls>
                                                <source src="https://docmidia-files.s3.us-east-1.amazonaws.com/app/files/streams/479516717_20241108_163711.mp4" type="video/mp4">
                                                <source src="https://docmidia-files.s3.us-east-1.amazonaws.com/app/files/streams/479516717_20241108_163711.mp4" type="video/ogg">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                        <div class="col-lg-9 col-md-9 col-sm-12">
                                            <div style="margin-bottom: 15px;">
                                                {!! ($noticia->transcricao) ?  Str::limit($noticia->transcricao, 700, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                            </div>
                                            <div style="position: absolute; bottom: 15px; left: 0px;">
                                                @switch($noticia->sentimento)
                                                    @case(0)
                                                        <span><i class="fa fa-smile-o fa-2x op-2"></i></span>                                                
                                                        <span><i class="fa fa-frown-o fa-2x op-2"></i></span>
                                                        <span><i class="fa fa-ban fa-2x text-info"></i></span>
                                                    @break
                                                    @case(-1)
                                                        <span><i class="fa fa-smile-o fa-2x op-2"></i></span>                                                
                                                        <span><i class="fa fa-frown-o fa-2x text-danger"></i></span>
                                                        <span><i class="fa fa-ban fa-2x op-2"></i></span>
                                                    @break
                                                    @case(1)
                                                        <span><i class="fa fa-smile-o fa-2x text-success"></i></span>                                                
                                                        <span><i class="fa fa-frown-o fa-2x op-2"></i></span>
                                                        <span><i class="fa fa-ban fa-2x op-2"></i></span>
                                                    @break  
                                                    @default
                                                        <span><i class="fa fa-smile-o fa-2x op-2"></i></span>                                                
                                                        <span><i class="fa fa-frown-o fa-2x op-2"></i></span>
                                                        <span><i class="fa fa-ban fa-2x op-2"></i></span>
                                                    @break                                           
                                                @endswitch
                                            </div>
                                        </div>     
                                    </div>                               
                                    <div style="position: absolute; bottom: 0px; right: 5px;">
                                        @if($noticia->cliente)
                                            <a title="Editar" href="{{ url('tv/noticias/'.$noticia->id.'/cliente/'.$noticia->cliente->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                            <a title="Excluir" href="{{ url('tv/noticias/'.$noticia->id.'/cliente/'.$noticia->cliente->id.'/remover') }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-trash fa-2x"></i></a>
                                        @else 
                                            <a title="Editar" href="{{ url('tv/noticias/'.$noticia->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                            <a title="Excluir" href="{{ url('tv/noticias/'.$noticia->id.'/remover') }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-trash fa-2x"></i></a>
                                        @endif
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
@endsection