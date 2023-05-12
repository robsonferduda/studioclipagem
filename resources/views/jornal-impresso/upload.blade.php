@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Jornal Impresso 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Arquivos
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Upload 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('impresso') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-newspaper-o"></i> Dashboard</a>
                    <a href="{{ url('jornal-impresso/processamento') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-list"></i> Fila</a>
                    <a href="{{ url('jornal-impresso/processar') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="fa fa-cogs"></i> Processar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-lg-12 col-md-3 mb-12">
                <h6>Os arquivos permanecerão aqui até serem processados pelo sistema</h6>
                <div class="form-group" style="">
                    <div class='content'>
                        <span>Clique para buscar ou arraste os arquivos</span>
                        {{ Form::open(array('url' => 'jornal-impresso/upload', 'method' => 'POST', 'name'=>'product_images', 'id'=>'dropzone', 'class'=>'dropzone', 'files' => true)) }}

                        {{ Form::close() }}
                    </div>
                </div>
            </div>   
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    Dropzone.autoDiscover = false;

    $( document ).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');
       
        $(".dropzone").dropzone({ 
            acceptedFiles: ".jpeg,.jpg,.png,.pdf",
            init: function() { 
                myDropzone = this;

                $.ajax({
                    url: host+'/jornal-impresso/pendentes/listar',
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