@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="nc-icon nc-chart-pie-36"></i> Dashboard 
                    </h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">

                <div class="col-lg-2 col-md-2 col-sm-12">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card card-stats">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-4 col-md-4">
                                        <div class="icon-big text-center icon-warning">
                                            <i class="fa fa-instagram text-pink"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-md-8 mt-3">
                                        <div class="numbers">
                                            <p class="card-title"><a href="{{ url('monitoramento/media/instagram') }}">{{ $totais['total_insta'] }}</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>   
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card card-stats">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4">
                                        <div class="icon-big text-center icon-warning">
                                            <i class="fa fa-facebook text-facebook"></i>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-8 mt-3">
                                        <div class="numbers">
                                            <p class="card-title"><a href="{{ url('monitoramento/media/facebook') }}">{{ $totais['total_face'] }}</a></p>
                                        </div>
                                    </div>
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
                                            <i class="fa fa-twitter text-info"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8 mt-3">
                                        <div class="numbers">
                                            <p class="card-title"><a href="{{ url('monitoramento/media/twitter') }}">{{ $totais['total_twitter'] }}</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>

                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="custom-cloud" id='cloud'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="card car-chart">
                        <div class="card-header">
                          <h5 class="card-title">Monitoramento de Redes Sociais</h5>
                          <p class="">Total de coletas diárias por rede social no período de {{ $periodo_relatorio['data_inicial'] }} à {{ $periodo_relatorio['data_final'] }}</p>
                        </div>
                        <div class="card-body">
                          <canvas id="chartActivity"></canvas>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="card card-stats">
                        <div class="card-body ">
                            <h6 class="text-center">Termos Ativos</h6>
                            <table class="table">
                                <thead class="">
                                    <tr>
                                        <th>Mídia Social</th>
                                        <th>Termo</th>
                                        <th class="text-center">Menções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($terms as $term)
                                        <tr>
                                            <td>{{ $term->socialMedia->name }}</td>
                                            <td>{{ $term->term }}</td>
                                            <td class="text-center">
                                                @switch($term->social_media_id)
                                                    @case(App\Enums\SocialMedia::INSTAGRAM)
                                                        {{ $term->medias->count() }}
                                                        @break
                                                    @case(App\Enums\SocialMedia::TWITTER)
                                                        {{ $term->mediasTwitter->count() }}
                                                        @break
                                                    @case(App\Enums\SocialMedia::FACEBOOK)
                                                        {{ $term->pagePosts->count() + $term->pagePostsComments->count() }}
                                                        @break
                                                    @default                        
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>   
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="card card-stats">
                        <div class="card-body ">
                            <h6 class="text-center">Hashtags Ativas</h6>
                            <table class="table">
                                <thead class="">
                                    <tr>
                                        <th>Mídia Social</th>
                                        <th>Hashtag</th>
                                        <th class="text-center">Menções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hashtags as $hashtag)
                                        <tr>
                                            <td>{{ $hashtag->socialMedia->name }}</td>
                                            <td>#{{ $hashtag->hashtag }}</td>
                                            <td class="text-center">
                                                @switch($hashtag->social_media_id)
                                                    @case(App\Enums\SocialMedia::INSTAGRAM)
                                                        {{ $hashtag->medias->count() }}
                                                        @break
                                                    @case(App\Enums\SocialMedia::TWITTER)
                                                        {{ $hashtag->mediasTwitter->count() }}
                                                        @break 
                                                    @case(App\Enums\SocialMedia::FACEBOOK)
                                                        {{ $hashtag->pagePosts->count() + $hashtag->pagePostsComments->count() }}
                                                        @break
                                                    @default                         
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>   
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

        var dados = null;
        var host =  $('meta[name="base-url"]').attr('content');

        var APP_URL = {!! json_encode(url('/')) !!};
        var tamanho = 0.02;

        fetch(APP_URL+'/nuvem-palavras/words', {
            method: 'GET', 
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        }).then(function(response) {
            return response.json();
        }).then(function(response){
            
            let words = [];

            $('body').loader('hide');
            const _token = $('meta[name="csrf-token"]').attr('content');

            Object.entries(response).forEach(element => {
                words.push(
                    {
                        text: element[0], 
                        weight: element[1],
                        html: {
                            class: 'cloud-word'
                        },                        
                    }
                );
            });

            let cloud = $('#cloud').jQCloud(words, {
                autoResize: true,
                classPattern: null,
                colors: ["#66C2A5", "#FC8D62", "#800026", "#E78AC3", "#A6D854", "#FFD92F", "#E5C494", "#B3B3B3"],
                fontSize: function (width, height, step) {
                    if (step < 5)
                        tamanho = tamanho - 0.001;
                    return width * tamanho * step + 'px';
                }
            });            
        });

        $.ajax({
            url: host+'/monitoramento/medias/historico/{{ $periodo_padrao }}',
            type: 'GET',
            success: function(response) {
                dados = response;
                initDashboardPageCharts();
            }
        }); 

        function initDashboardPageCharts() {

        chartColor = "#FFFFFF";

        var cardStatsMiniLineColor = "#fff",
        cardStatsMiniDotColor = "#fff";

        ctx = document.getElementById('chartActivity').getContext("2d");

        gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
        gradientStroke.addColorStop(0, '#80b6f4');
        gradientStroke.addColorStop(1, chartColor);

        gradientFill = ctx.createLinearGradient(0, 170, 0, 50);
        gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
        gradientFill.addColorStop(1, "rgba(249, 99, 59, 0.40)");

        myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dados.data_formatada,
            datasets: [
            {
                label: "Instagram",
                    borderColor: '#e91ea1',
                    fill: true,
                    backgroundColor: '#e91ea1',
                    hoverBorderColor: '#fcc468',
                    borderWidth: 8,
                    data: dados.dados_instagram,
                },
                {
                    label: "Facebook",
                    borderColor: '#3f51b5',
                    fill: true,
                    backgroundColor: '#3f51b5',
                    hoverBorderColor: '#3f51b5',
                    borderWidth: 8,
                    data: dados.dados_facebook,
                },
                {
                    label: "Twitter",
                    borderColor: '#51bcda',
                    fill: true,
                    backgroundColor: '#51bcda',
                    hoverBorderColor: '#51bcda',
                    borderWidth: 8,
                    data: dados.dados_twitter,
                }
            ]
        },
        options: {

            tooltips: {
            tooltipFillColor: "rgba(0,0,0,0.5)",
            tooltipFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            tooltipFontSize: 14,
            tooltipFontStyle: "normal",
            tooltipFontColor: "#fff",
            tooltipTitleFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            tooltipTitleFontSize: 14,
            tooltipTitleFontStyle: "bold",
            tooltipTitleFontColor: "#fff",
            tooltipYPadding: 6,
            tooltipXPadding: 6,
            tooltipCaretSize: 8,
            tooltipCornerRadius: 6,
            tooltipXOffset: 10,
            },


            legend: {

            display: false
            },
            scales: {

            yAxes: [{
                ticks: {
                fontColor: "#9f9f9f",
                fontStyle: "bold",
                beginAtZero: true,
                maxTicksLimit: 5,
                padding: 20
                },
                gridLines: {
                zeroLineColor: "transparent",
                display: true,
                drawBorder: false,
                color: '#9f9f9f',
                }

            }],
            xAxes: [{
                barPercentage: 0.4,
                gridLines: {
                zeroLineColor: "white",
                display: false,

                drawBorder: false,
                color: 'transparent',
                },
                ticks: {
                padding: 20,
                fontColor: "#9f9f9f",
                fontStyle: "bold"
                }
            }]
            }
        }
        });
        }
    });
</script>
@endsection