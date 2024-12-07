@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-volume-up ml-3"></i> Rádio 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Dashboard 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('radio/noticias') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-table"></i> Notícias</a>
                    <a href="{{ url('radio/noticias/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar Notícia</a>                    
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card card-stats">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-success">
                                        <i class="fa fa-volume-up text-success"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                        <p class="card-category">Emissoras Cadastradas</p>
                                        <p class="card-title">{{ $total_emissora_tv }}</p>
                                        <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-calendar"></i>
                                    Última Atualização em {{ \Carbon\Carbon::parse($ultima_atualizacao_tv)->format('d/m/Y H:i:s') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card card-stats">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-volume-up text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                        <p class="card-category">Emissoras Gravando</p>
                                        <p class="card-title">{{ $total_emissora_tv }}</p>
                                        <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-calendar"></i>
                                    Última Atualização em {{ \Carbon\Carbon::parse($ultima_atualizacao_tv)->format('d/m/Y H:i:s') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card card-stats">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-warning">
                                        <i class="nc-icon nc-chart-bar-32 text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                        <p class="card-category">Notícias de Hoje</p>
                                        <p class="card-title">{{ $total_noticia_tv }}</p>
                                        <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-calendar"></i>
                                    Última Atualização em {{ \Carbon\Carbon::parse($ultima_atualizacao)->format('d/m/Y H:i:s') }}
                                </div>
                            </div>
                        </div>
                    </div>     
                    
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card card-stats">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-danger">
                                        <i class="nc-icon nc-briefcase-24 text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                        <p class="card-category">Clientes de Rádio</p>
                                        <p class="card-title">{{ $total_noticia_tv }}</p>
                                        <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-calendar"></i>
                                    Última Atualização em {{ \Carbon\Carbon::parse($ultima_atualizacao)->format('d/m/Y H:i:s') }}
                                </div>
                            </div>
                        </div>
                    </div>            
                </div> 
            </div>
            <div class="col-md-9">

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