@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row ml-1">
                <div class="col-md-9">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Web
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Prints
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Problemas
                    </h4>
                </div>
                <div class="col-md-3">
                    <a href="{{ url('noticia/web') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Notícias</a>
                </div>
            </div>
        </div>
        <div class="card-body ml-3 mr-3">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col col-lg-12 col-sm-12">                        
                    
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