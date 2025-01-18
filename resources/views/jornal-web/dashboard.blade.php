@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Web 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Estatísticas 
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('fonte-web/listar') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-database"></i> Fontes Web</a>
                    <a href="{{ url('noticia/web/cadastrar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-9 col-sm-9">
                    <div class="card car-chart">
                        <div class="card-header">
                        <p class="">Total de notícias diárias cadastradas no período de {{ \Carbon\Carbon::parse($data_inicial)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($data_final)->format('d/m/Y') }}</p>
                        </div>
                        <div class="card-body grafico">
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
                                        <i class="nc-icon nc-globe text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                        <p class="card-category">Sites</p>
                                        <p class="card-title">{{ $total_sites }}</p>
                                        <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-calendar"></i>
                                    Última Atualização em {{ \Carbon\Carbon::parse($ultima_atualizacao_web)->format('d/m/Y H:i:s') }} 
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
                                        <p class="card-category">Notícias Coletadas</p>
                                        <p class="card-title">{{ $total_noticias }}</p>
                                        <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-calendar"></i>
                                    Última Atualização em {{ \Carbon\Carbon::parse($ultima_atualizacao_noticia)->format('d/m/Y H:i:s') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">   
                <div class="col-md-12">  
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title"><i class="fa fa-line-chart" aria-hidden="true"></i> Maiores Coletas</h6>
                                </div>
                                <div class="card-content">
                                    @if($top_sites)
                                        <ul class="list-unstyled team-members ml-3 mr-3">
                                            @foreach ($top_sites as $key => $site)
                                                @if($site->total == 0) @php break 1; @endphp @endif
                                                <li style="border-bottom: 1px solid #ebebeb; margin-bottom: 3px;">
                                                    <div class="row">                                            
                                                        <div class="col-md-9">
                                                            {{ $site->nome }}
                                                            <br>
                                                            <span class="text-muted"><small>{{ $site->url }}</small></span>
                                                        </div>   
                                                        <div class="col-md-2 text-right">
                                                            <p class="mt-2">{{ $site->total }}</p>
                                                        </div>             
                                                    </div>
                                                </li>                                        
                                            @endforeach                                
                                        </ul>
                                    @else
                                        <p class="mr-2 ml-3"><i class="fa fa-hourglass-start mr-1"></i>Nenhuma coleta realizada no dia de hoje</p>
                                    @endif
                                </div>
                            </div>
                        </div>
        
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title"><i class="fa fa-ban" aria-hidden="true"></i> Sem Coleta - <strong class="text-danger">{{ count($sem_coleta) }} Sites</strong></h6>
                                </div>
                                <div class="card-content">
                                    @if($sem_coleta)
                                        <ul class="list-unstyled team-members ml-3 mr-3">
                                            @foreach ($sem_coleta as $key => $site)
                                               
                                                    <li style="border-bottom: 1px solid #ebebeb; margin-bottom: 3px;">
                                                        <div class="row">                                            
                                                            <div class="col-md-9">
                                                                {{ $site->nome }}
                                                                <br>
                                                                <span class="text-muted"><small>{{ $site->url }}</small></span>
                                                            </div>   
                                                            <div class="col-md-2 text-right">
                                                                <p class="mt-2">{{ $site->total }}</p>
                                                            </div>             
                                                        </div>
                                                    </li>
                                                
                                            @endforeach                                
                                        </ul>
                                    @else
                                        <p class="mr-2 ml-3"><i class="fa fa-hourglass-start mr-1"></i>Nenhuma coleta realizada no dia de hoje</p>
                                    @endif
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
                url: host+'/jornal-web/estatisticas',
                type: 'GET',
                beforeSend: function() {
                    $('.grafico').loader('show');
                },
                success: function(response) {
                    dados = response;
                    initDashboardPageCharts();
                },
                error: function(){
                    $(".erro-grafico").html('<span class="text-danger">Erro ao carregar estatísticas</span>');
                },
                complete: function(){
                    $('.grafico').loader('show');
                }
            }); 

            function initDashboardPageCharts() {

                new Chart(document.getElementById("chartjs-0"), {
                    "type": "line",
                    "data": {
                        "labels": dados.label,
                        "datasets": [{
                            "label": "Notícias coletadas por dia",
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