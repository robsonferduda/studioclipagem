@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Impressos
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Dashboard
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('fonte-impresso/listar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-newspaper-o"></i> Fontes de Impresso</a>
                    <a href="{{ url('impresso/noticias') }}" class="btn btn-info pull-right mr-1"><i class="fa fa-newspaper-o"></i> Notícias</a>
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
                          <p class="">Total de coletas diárias cadastradas no período de {{ \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dt_final)->format('d/m/Y') }}</p>
                        </div>
                        <div class="card-body">
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
                                        <i class="fa fa-newspaper-o text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                        <p class="card-category">Fontes Disponíveis</p>
                                        <p class="card-title"></p>
                                        <p>{{ $total_fonte_impressos }}</p>
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
                                        <p class="card-category">Coletas de Hoje</p>
                                        <p class="card-title"></p>
                                        <p>{{ $total_noticias_impressas }}</p>
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
                url: host+'/impresso/coleta/estatisticas',
                type: 'GET',
                success: function(response) {
                    dados = response;
                    initDashboardPageCharts();
                },
                error: function(){
                    alert("Erro");
                }
            }); 

            function initDashboardPageCharts() {
        
        new Chart(document.getElementById("chartjs-0"), {
            "type": "line",
            "data": {
                "labels": dados.label,
                "datasets": [{
                    "label": "Notícias por dia",
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
