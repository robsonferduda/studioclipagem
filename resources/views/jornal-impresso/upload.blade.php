@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Impressos 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Arquivos
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Upload 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('impresso') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-newspaper-o"></i> Dashboard</a>
                    <a href="{{ url('jornal-impresso/buscar') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-search"></i> Buscar Impressos</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-lg-12 col-md-3 mb-12">
                <div class="form-group" style="">
                    <div class='content' style="padding-bottom: 0px !important;">
                        <span>Clique para buscar ou arraste os arquivos</span>
                        {{ Form::open(array('url' => 'jornal-impresso/upload', 'method' => 'POST', 'name'=>'product_images', 'id'=>'dropzone', 'class'=>'dropzone', 'files' => true)) }}

                        {{ Form::close() }}
                    </div>
                </div>
            </div>   
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['jornal-impresso/uploads']]) !!}
                        <div class="form-group m-3">
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
                                <div class="col-md-2">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3" style="margin-top: 25px;"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="col-lg-12 col-md-3 mb-12">
                <h6>Arquivos Enviados</h6>
                @forelse($jornais_pendentes as $key => $jornal)
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="float-left">
                                        <i class="fa fa-file-pdf-o text-danger mr-3 mt-4" aria-hidden="true" style="font-size: 35px;"></i>
                                    </div>
                                    <div class="float-left">
                                        <p class="mb-1"><strong>{{ $jornal->titulo }}</strong></p>
                                        <p class="mb-1 text-muted">{{ substr($jornal->path_s3, strrpos($jornal->path_s3, '/') + 1) }}</p>
                                        <p>Enviado em  {{ \Carbon\Carbon::parse($jornal->created_at)->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                    @if(count($jornal->paginas))
                                        <div class="pull-right">
                                            <span class="badge badge-pill badge-success">Processado</span>
                                            {{ count($jornal->paginas) }} páginas
                                        </div>
                                    @else
                                        <div class="pull-right">
                                            <span class="badge badge-pill badge-danger">Pendente</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="row">
                        <div class="col-lg-12">
                            <p class="text-danger">Não existem arquivos enviados nas datas especificadas</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    Dropzone.autoDiscover = false;

    $( document ).ready(function() {

        var host = $('meta[name="base-url"]').attr('content');
       
        $(".dropzone").dropzone({ 
            acceptedFiles: ".jpeg,.jpg,.png,.pdf",
            init: function() { 
                myDropzone = this;

                this.on('success', function (file, json) {
                    
                });
            }
        });
    });
</script>
@endsection