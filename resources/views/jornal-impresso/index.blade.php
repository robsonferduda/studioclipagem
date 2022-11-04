@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o"></i> Jornal Impresso 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Dashboard 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('jornal-impresso/upload') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-upload"></i> Upload</a>
                    <a href="{{ url('jornal-impresso/processamento') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-cogs"></i> Processamento</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            
        </div>
    </div>
</div> 
@endsection