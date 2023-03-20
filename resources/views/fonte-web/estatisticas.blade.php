@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Jornal Web
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Fontes
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Estatísticas
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('buscar-web') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Notícias Web</a>
                    <a href="{{ url('fonte-web/listar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Fontes Web</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-8 col-sm-12">
                    <canvas id="speedChart"></canvas>
                </div>
                <div class="col-lg-4 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body ">
                           <div class="row">
                              <div class="col-5 col-md-4">
                                 <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-favourite-28 text-danger"></i>
                                 </div>
                              </div>
                              <div class="col-7 col-md-8">
                                 <div class="numbers">
                                    <p class="card-category">Notícias Coletadas</p>
                                    <p class="card-title">235</p>
                                    <p></p>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="card-footer ">
                           <hr>
                           <div class="stats">
                                <i class="fa fa-refresh"></i>
                                Última atualização em {{ date("d/m/Y H:i:s") }}
                           </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Clientes Citados</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled team-members">
                                <li>
                                    <div class="row">
                                        <div class="col-md-4 col-4">
                                            <img src="{{ asset('img/logo_zurich.png') }}" alt="Circle Image" class="">
                                        </div>
                                        <div class="col-md-8 col-8">
                                            <a href="{{ url("/") }}">Zurich</a>
                                        <br>
                                        <span class="text-muted"><small>5 Citações</small></span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
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

        var speedCanvas = document.getElementById("speedChart");
        var dataFirst = {
            data: [0, 13, 3, 20, 30, 11, 1],
            fill: false,
            borderColor: '#fbc658',
            backgroundColor: 'transparent',
            pointBorderColor: '#fbc658',
            pointRadius: 4,
            pointHoverRadius: 4,
            pointBorderWidth: 8,
        };
      
        var speedData={labels:["23/02","23/02","23/02","23/02","23/02","23/02","23/02"],datasets:[dataFirst]};var chartOptions={legend:{display:false,position:'top'}};

        var lineChart=new Chart(speedCanvas,{type:'line',hover:false,data:speedData,options:chartOptions});
      
    });
</script>
@endsection
