@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o"></i> Jornal Impresso 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Upload 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('jornal-impresso') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-newspaper-o"></i> Dashboard</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-lg-12 col-md-3 mb-12">
                <div class="form-group" style="position: relative;">
                    <div class='content'>
                        <span>Busque ou arraste os arquivos</span>
                        {{ Form::open(array('url' => 'imageUpload', 'method' => 'PUT', 'name'=>'product_images', 'id'=>'myImageDropzone', 'class'=>'dropzone', 'files' => true)) }}

                        {{ Form::close() }}
                    </div>
                </div>
            </div>   
        </div>
    </div>
</div> 
@endsection