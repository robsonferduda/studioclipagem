@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o"></i> Jornal Impresso 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Listar 
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('impresso') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-newspaper-o"></i> Dashboard</a>
                    <a href="{{ url('jornal-impresso/upload') }}" class="btn btn-info pull-right mr-1"><i class="fa fa-upload"></i> Upload</a>
                    <a href="{{ url('jornal-impresso/processamento') }}" class="btn btn-warning pull-right mr-1"><i class="fa fa-cogs"></i> Processamento</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                     
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection