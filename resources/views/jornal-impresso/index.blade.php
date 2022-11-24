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
            <div class="col-md-12">
                <div class="col-lg-12 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <div class="col-lg-3 col-sm-12">
                                    <img src="{{ asset('jornal-impresso/1514/20221116/img/pagina_0.png') }}" alt="..." class="img-thumbnail">
                                </div>
                                <div class="col-lg-9 col-sm-12">
                                    <h6>Tìtulo</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                        <hr>
                        <div class="row">
                        <div class="col-sm-7">
                        <div class="footer-title">Financial Statistics</div>
                        </div>
                        <div class="col-sm-5">
                        <div class="pull-right">
                        <button class="btn btn-success btn-round btn-icon btn-sm">
                        <i class="nc-icon nc-simple-add"></i>
                        </button>
                        </div>
                        </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection