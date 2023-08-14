@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-tv ml-3"></i> TV 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Decupagem 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('tv/noticias/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                    <a href="{{ url('tv/noticias') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-table"></i> Notícias</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-lg-12 col-md-3 mb-12">
                <h6>Arraste ou solte os arquivos para realizar o processamento</h6>
                <div class="form-group" style="">
                    <div class='content'>
                        <span>Clique para buscar ou arraste os arquivos</span>
                        {{ Form::open(array('url' => 'noticia_tv/upload', 'method' => 'POST', 'name'=>'product_images', 'id'=>'dropzone', 'class'=>'dropzone', 'files' => true)) }}

                        {{ Form::close() }}
                    </div>
                    {{ Form::open(array('url' => 'noticia_tv/decupagem/processar', 'method' => 'POST', 'name'=>'product_images')) }}
                        <button type="submit" class="btn btn-primary"><i class="fa fa-cogs"></i> Processar</button>
                    {{ Form::close() }}
                </div>
            </div>   
        </div>
    </div>
</div> 
@endsection
@section('script')
    <script>

        Dropzone.autoDiscover = false;

        $(document).ready(function() { 

            var host =  $('meta[name="base-url"]').attr('content');

            $(".dropzone").dropzone({ 
                acceptedFiles: ".doc, .docx",
                init: function() { 
                    myDropzone = this;

                    $.ajax({
                        url: host+'/noticia-tv/decupagem/listar',
                        type: 'get',
                        dataType: 'json',
                        success: function(response){

                        $.each(response, function(key,value) {
                            var mockFile = { name: value.name, size: value.size };

                            myDropzone.emit("addedfile", mockFile);
                            myDropzone.emit("complete", mockFile);

                        });

                        }
                    });
                }
            });

        });
    </script>
@endsection