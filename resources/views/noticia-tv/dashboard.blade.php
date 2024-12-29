@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-tv ml-3"></i> TV 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Dashboard 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('tv/noticias') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-tv"></i> Notícias</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-9 col-md-6 col-sm-6">
                    <div class="card car-chart">
                        <div class="card-header">
                          <p class="">Total de vídeos coletados no período de {{ \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dt_final)->format('d/m/Y') }}</p>
                        </div>
                        <div class="card-body">
                            <div class="erro-grafico"></div>
                            <canvas id="chartjs-0" class="chartjs">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card card-stats">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-tv text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                        <p class="card-category">Emissoras</p>
                                        <p class="card-title"></p>
                                        <p><a href="{{ url('tv/emissoras') }}">{{ $total_emissoras }}</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-calendar"></i>
                                    Última Atualização em 
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
                                        <i class="fa fa-tags text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                        <p class="card-category">Programas</p>
                                        <p class="card-title"></p>
                                        <p><a href="{{ url('tv/emissoras/programas') }}">{{ $total_programas }}</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-calendar"></i>
                                    Última Atualização em 
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
                                        <p class="card-category">Vídeos de Hoje</p>
                                        <p class="card-title"></p>
                                        <p><a href="{{ url('tv/videos') }}">{{ $total_videos_tv }}</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-calendar"></i>
                                    Última Atualização em
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card card-stats">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-success">
                                        <i class="nc-icon nc-chart-bar-32 text-success"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                        <p class="card-category">Notícias de Hoje</p>
                                        <p class="card-title"></p>
                                        <p><a href="{{ url('tv/noticias') }}">{{ $total_noticias_tv }}</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-calendar"></i>
                                    Última Atualização em
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
@section('script')
    <script>
        $(document).ready(function() { 

            var host =  $('meta[name="base-url"]').attr('content');

            var dados = null;

            $.ajax({
                url: host+'/tv/videos/estatisticas',
                type: 'GET',
                success: function(response) {
                    dados = response;
                    initDashboardPageCharts();
                },
                error: function(){
                    $(".erro-grafico").html('<span class="text-danger">Erro ao carregar estatísticas</span>');
                }
            }); 

            function initDashboardPageCharts() {
        
                new Chart(document.getElementById("chartjs-0"), {
                    "type": "line",
                    "data": {
                        "labels": dados.label,
                        "datasets": [{
                            "label": "Vídeos coletados por dia",
                            "data": dados.totais,
                            "fill": true,
                            "borderColor": "rgb(75, 192, 192)",
                            "lineTension": 0.1
                        }]
                    },
                    "options": {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                });
            }
            
        });
    </script>
@endsection