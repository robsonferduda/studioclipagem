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
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ $fonte->nome }} <a href="{{$fonte->url }}" target="BLANK"><i class="fa fa-external-link text-info"></i></a>
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('buscar-web') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Notícias Web</a>
                    <a href="{{ url('fonte-web/listar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Fontes Web</a>
                    <a href="{{ url('fonte-web/listar') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="nc-icon nc-minimal-left"></i> Voltar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-8 col-sm-12">
                    <input type="hidden" id="id_fonte" value="{{ $fonte->id }}"/>
                    <canvas id="speedChart"></canvas>
                    <p class="text-center mt-2">Coletas dos últimos 7 dias</p>
                </div>
                <div class="col-lg-4 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body ">
                           <div class="row">
                              <div class="col-5 col-md-4">
                                 <div class="icon-big text-center icon-warning">
                                    <i class="fa fa-globe text-info"></i>
                                 </div>
                              </div>
                              <div class="col-7 col-md-8">
                                 <div class="numbers">
                                    <p class="card-category">Coletas de Hoje</p>
                                    <p class="card-title">{{ $total_dia }}</p>
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
                            <h6 class="card-title">Clientes Citados Hoje</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled team-members">
                                @forelse ($clientes as $cliente)
                                    <li>
                                        <div class="row">
                                            <div class="col-md-3 col-3">
                                                <img class="rounded-circle" src="{{ asset('img/clientes/logo/'.$cliente->logo) }}" alt="Circle Image" class="">
                                            </div>
                                            <div class="col-md-9 col-9">
                                                <h5 class="mt-1 mb-0">{{ $cliente->nome }}</h5>
                                                <p class="mt-0 mb-0"><a href="{{ url("monitoramento/cliente/".$cliente->id) }}">Ver Monitoramento</a></p>
                                                <p class="mt-0 mb-0">
                                                    @if($cliente->total > 1)
                                                        <span class="text-muted"><small>{{ $cliente->total }} notícias</small></span>
                                                    @else
                                                        <span class="text-muted"><small>{{ $cliente->total }} notícia</small></span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                @empty 
                                    <li>
                                        Nenhum cliente vinculado às notícias de hoje
                                    </li>
                                @endforelse
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

        var id_fonte = $("#id_fonte").val();

        $.ajax({
            url: '../../fonte-web/totais/semana/'+id_fonte,
            type: 'GET',
            success: function(result) {

                var speedCanvas = document.getElementById("speedChart");
                var dataFirst = {
                    data: result.total,
                    fill: false,
                    borderColor: '#fbc658',
                    backgroundColor: 'transparent',
                    pointBorderColor: '#fbc658',
                    pointRadius: 4,
                    pointHoverRadius: 4,
                    pointBorderWidth: 8,
                };
               
            
                var speedData={labels: result.data,datasets:[dataFirst]};
                
                var chartOptions={legend:{display:false,position:'top'},   scales: {
                        yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            min: 0
                        }    
                        }]
                    }};

                var lineChart=new Chart(speedCanvas,{type:'line',hover:false,data:speedData,options:chartOptions});

                       
            },
            error: function(response){

            },
            complete: function(response) {
                     
            }
        }); 
      
    });
</script>
@endsection