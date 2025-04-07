@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Impressos
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Arquivos Web 
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('fonte-impresso/listar') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-database"></i> Fontes Impressos</a>
                    <a href="{{ url('noticia/impresso/cadastrar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['jornal-impresso/edicoes']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" required="true" value="{{ \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ \Carbon\Carbon::parse($dt_final)->format('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label>Fonte</label>
                                        <select class="form-control select2" name="fonte" id="fonte">
                                            <option value="">Selecione uma fonte</option>
                                            @foreach ($fontes as $f)
                                                <option value="{{ $f->id }}" {{ ($fonte == $f->id ) ? 'selected' : '' }}>{{ $f->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-primary w-100" style="margin-top: 24px;"><i class="fa fa-search"></i> </button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!} 

                    @if($edicoes->count())
                        <h6 class="px-3">Mostrando {{ $edicoes->count() }} de {{ $edicoes->total() }} edições coletadas</h6>
                    @endif

                    {{ $edicoes->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'), 'fonte' => $fonte])->links('vendor.pagination.bootstrap-4') }}    
                        @foreach ($edicoes as $key => $edicao)
                            <div class="card">
                                <div class="card-body">                           
                                    <div class="row">
                                        <!--
                                        <div class="col-lg-2 col-sm-12 mb-1">
                                            @if($edicao->primeiraPagina)
                                                <a href="{{ url('jornal-impresso/web/download/'.$edicao->id) }}" target="_BLANK"><img src="{{ Storage::disk('s3')->temporaryUrl($edicao->primeiraPagina->path_pagina_s3, '+2 minutes') }}" alt="Página "></a>
                                            @endif
                                        </div>
                                        -->
                                        <div class="col-lg-12 col-sm-12 mb-1">
                                            <span class="pull-right">{{ $edicao->paginas->count() }} Páginas</span>
                                            <h6>
                                                <a href="{{ url('fonte-impresso/'.$edicao->fonte->id.'/editar') }}" target="_BLANK">{{ ($edicao->fonte) ? $edicao->fonte->nome : '' }}</a>
                                            </h6>  
                                            <h6 style="color: #FF5722;">
                                                {{ ($edicao->fonte and $edicao->fonte->estado) ? $edicao->fonte->estado->nm_estado : '' }}
                                                {{ ($edicao->fonte and $edicao->fonte->cidade) ? '/ '.$edicao->fonte->cidade->nm_cidade : '' }}
                                            </h6>  
                                            <h6 class="text-muted mb-1">{{ \Carbon\Carbon::parse($edicao->dt_pub)->format('d/m/Y') }} - {{ ($edicao->fonte) ? $edicao->fonte->nome : '' }}</h6> 
                                            <p class="mb-0"><strong>{{ ($edicao->titulo) ? $edicao->titulo : '' }}</strong></p>
                                            <p class="mb-0"><a href="{{ url('jornal-impresso/edicao/'.$edicao->id.'/paginas') }}"> Clique para ver os arquivos </a></p>
                                            
                                            <p>Arquivo publicado em {{ ($edicao->dt_pub) ? \Carbon\Carbon::parse($edicao->dt_pub)->format('d/m/Y H:i:s') : 'Não informado' }}
                                                e coletado em {{ \Carbon\Carbon::parse($edicao->created_at)->format('d/m/Y H:i:s')  }}
                                            </p>                        
                                        </div>
                                    </div>                               
                                </div>                            
                            </div>
                        @endforeach
                    {{ $edicoes->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'), 'fonte' => $fonte])->links('vendor.pagination.bootstrap-4') }} 
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