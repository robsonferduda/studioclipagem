@extends('layouts.app')
@section('content')
@permission('dashboard-impresso')
<div class="row">   
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card card-stats">
                    <div class="card-body ">
                        <div class="row mb-2">
                            <div class="col-12 col-md-12" style="min-height: 100px">
                                @forelse(Auth::user()->roles as $role)
                                    <span class="badge pull-right" style="background: {{ $role->display_color }}; border-color: {{ $role->display_color }};">{{ $role->display_name }}</span>
                                @empty
                                    <span class="text-danger">Nenhum perfil associado</span>
                                @endforelse
                                Olá, <strong>{{ Auth::user()->name }}</strong>! <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</div>
@endpermission
@permission('dashboard-tv')
<div class="row">   
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card card-stats">
                    <div class="card-body ">
                        <div class="row mb-2">
                            <div class="col-12 col-md-12" style="min-height: 100px">
                                @forelse(Auth::user()->roles as $role)
                                    <span class="badge pull-right" style="background: {{ $role->display_color }}; border-color: {{ $role->display_color }};">{{ $role->display_name }}</span>
                                @empty
                                    <span class="text-danger">Nenhum perfil associado</span>
                                @endforelse
                                Olá, <strong>{{ Auth::user()->name }}</strong>! <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</div>
@endpermission
@permission('dashboard')
    <div class="row">   
        <div class="col-md-4">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-3 col-md-3">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-newspaper-o text-info"></i>
                                    </div>
                                </div>
                                <div class="col-9 col-md-9">
                                    <div class="numbers">
                                        <p class="card-category">JORNAL</p>
                                        <p class="card-title total_jornal">
                                            <i class="fa fa-circle-o-notch fa-spin fa-fw text-gray"></i>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-3 col-md-3">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-globe text-danger"></i>
                                    </div>
                                </div>
                                <div class="col-9 col-md-9">
                                    <div class="numbers">
                                        <p class="card-category">WEB</p>
                                        <p class="card-title total_web">
                                            <i class="fa fa-circle-o-notch fa-spin fa-fw text-gray"></i>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-3 col-md-3">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-volume-up text-success"></i>
                                    </div>
                                </div>
                                <div class="col-9 col-md-9">
                                    <div class="numbers">
                                        <p class="card-category">RÁDIO</p>
                                        <p class="card-title total_radio">
                                            <i class="fa fa-circle-o-notch fa-spin fa-fw text-gray"></i>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-3 col-md-3">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-tv text-warning"></i>
                                    </div>
                                </div>
                                <div class="col-9 col-md-9">
                                    <div class="numbers">
                                        <p class="card-category">TELEVISÃO</p>
                                        <p class="card-title total_tv">
                                            <i class="fa fa-circle-o-notch fa-spin fa-fw text-gray"></i>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
            <div class="row">

                @if($programas_erros)
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="alert alert-danger alert-with-icon" data-notify="container">
                            <button type="button" aria-hidden="true" class="close">×</button>
                            <span data-notify="icon" class="ti-bell" style="top: 40% !important;"><i class="fa fa-tv"></i></span>
                            <span data-notify="message">Existem <strong>{{ $programas_erros }}</strong> emissoras de TV com erro de gravação! <a href="{{ url("tv/emissoras/programas") }}" style="color: white; font-weight: bold;">Clique aqui</a> para verificar e atualizar</span>
                        </div>
                    </div>
                @endif

                @if($programas_radio_erros)
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="alert alert-danger alert-with-icon" data-notify="container">
                            <button type="button" aria-hidden="true" class="close">×</button>
                            <span data-notify="icon" class="ti-bell" style="top: 40% !important;"><i class="fa fa-volume-up"></i></span>
                            <span data-notify="message">Existem <strong>{{ $programas_radio_erros }}</strong> emissoras de Rádio com erro de gravação! <a href="{{ url("emissoras/radio") }}" style="color: white; font-weight: bold;">Clique aqui</a> para verificar e atualizar</span>
                        </div>
                    </div>
                @endif

                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card card-maiores-coletas">
                        <div class="card-header">
                            <h6 class="card-title"><i class="fa fa-line-chart" aria-hidden="true"></i> Maiores Coletas</h6>
                        </div>
                        <div class="card-content">
                            <ul class="list-unstyled team-members ml-3 mr-3 maiores-coletas"></ul>
                            <p class="mr-2 ml-3 text-danger text-maiores-coletas d-none"><i class="fa fa-hourglass-start mr-1"></i>Nenhuma coleta realizada no dia de hoje</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card card-sem-coleta">
                        <div class="card-header">
                            <h6 class="card-title"><i class="fa fa-ban" aria-hidden="true"></i> Sem Coleta</h6>
                        </div>
                        <div class="card-content">
                            <ul class="list-unstyled team-members ml-3 mr-3 sem-coleta"></ul>
                            <p class="mr-2 ml-3 text-danger text-sem-coleta d-none"><i class="fa fa-hourglass-start mr-1"></i>Nenhuma coleta realizada no dia de hoje</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div class="col-md-8">
            <div class="card card-timeline card-plain">
                <h6>{{ \Carbon\Carbon::parse(Session::get('data_atual'))->format('d/m/Y') }}</h6>
                <div class="card-content">
                    <ul class="timeline timeline-simple">
                    <li class="timeline-inverted">
                        <div class="timeline-badge warning">
                           <i class="fa fa-globe fa-2x mt-0"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6 class="text-dark"><i class="fa fa-globe fa-1x mt-0"></i> COLETAS WEB <span class="badge badge-pill badge-warning pull-right">{{ $total_coletas }} coletas</span></h6>
                            </div>
                            <div class="timeline-body">
                                @if(count($coletas))
                                    <table id="bootstrap-table" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Início</th>
                                                <th>Fonte</th>
                                                <th class="center">Total Coletado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($coletas as $fonte)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($fonte->crawlead_at)->format('d/m/Y H:i:s') }}</td>
                                                    <td>
                                                        {{ $fonte->nome }} <br/>
                                                        <a href="{{ $fonte->url }}"  target="_blank">{{ $fonte->url }}</a>
                                                    </td>
                                                    <td class="center">
                                                        <a href="{{ url("fonte-web/estatisticas/".$fonte->id) }}">
                                                            <span class="total-coletas" id="total_coletas_{{ $fonte->id }}" data-id="{{ $fonte->id }}">
                                                                <i class="fa fa-circle-o-notch fa-spin fa-fw text-gray"></i>
                                                            </span>
                                                        </a>
                                                    </td>
                                                </tr>   
                                            @endforeach                                   
                                        </tbody>
                                    </table>
                                @else
                                    <p><i class="fa fa-hourglass-start mr-1"></i>Nenhuma coleta realizada no dia de hoje</p>
                                @endif
                            </div>
                        </div>
                    </li>
                    <li class="timeline-inverted">
                        <div class="timeline-badge info">
                           <i class="nc-icon nc-sound-wave font-20"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6 class="text-dark"><i class="nc-icon nc-sound-wave font-20"></i> REGISTRO DE MONITORAMENTO DIÁRIO<span class="badge badge-pill badge-primary pull-right">{{ $total_monitoramentos }} EXECUÇÕES</span></h6>
                            </div>
                            <div class="timeline-body box-execucao">
                                @forelse($execucoes as $execucao)
                                 
                                                
                                                    <div class="row mb-0 linha-execucao">
                                                        <div class="col-12 col-md-12">                                           
                                                            
                                                                <h6 class="mb-0 font-weight-bold text-danger">
                                                                    {{ ($execucao->monitoramento and $execucao->monitoramento->cliente) ? $execucao->monitoramento->cliente->nome : 'Cliente não informado' }} - 
                                                                    <span class="">{{ ($execucao->monitoramento->nome) ? $execucao->monitoramento->nome : 'Nome não informado' }} </span>
                                                                    <span class="pull-right text-info">
                                                                       <a href="{{ url('monitoramento/'.$execucao->id.'/noticias') }}">{{ $execucao->total_vinculado }} Notícias</a>
                                                                    </span>
                                                                </h6>
                                                            
                                                            <p class="text-muted mb-1 mt-0">{{ $execucao->monitoramento->expressao }}</p>   
                                                            <p class="mb-2 text-tempo">
                                                                Execução iniciada em <strong>{{ \Carbon\Carbon::parse($execucao->created_at)->format('d/m/Y H:i:s') }}</strong> com duração de <strong>
                                                                 @if(\Carbon\Carbon::create($execucao->updated_at)->diffInMinutes(\Carbon\Carbon::create($execucao->created_at)))
                                                                    {{ \Carbon\Carbon::create($execucao->updated_at)->diffInMinutes(\Carbon\Carbon::create($execucao->created_at)) }} </strong> minutos
                                                                @else
                                                                    {{ \Carbon\Carbon::create($execucao->updated_at)->diffInSeconds(\Carbon\Carbon::create($execucao->created_at)) }} </strong> segundos
                                                                @endif
                                                            
                                                            </p>                                         
                                                        </div>
                                                    </div>
                                @empty
                                    <p class="text-danger"><i class="fa fa-hourglass-start mr-1"></i>Nenhum monitoramento executado no dia de hoje</p>
                                @endforelse 
                            </div>
                        </div>
                    </li>
                    <li class="timeline-inverted">
                        <div class="timeline-badge success">
                           <i class="fa fa-tags fa-2x mt-1"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6 class="text-dark"><i class="fa fa-tags fa-1x mt-1"></i> ÁREAS <span class="badge badge-pill badge-success pull-right"> NOTÍCIAS</span></h6>
                            </div>
                            <div class="timeline-body estatisticas-areas">
                               
                            </div>
                        </div>
                     </li>
                  </ul>
               </div>
            </div>
        </div>
    </div>
@endpermission
@role('cliente')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="card-title mb-0"><i class="fa fa-bar-chart"></i> Resumo de Notícias por Mídia</h6>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-3 pull-right">
                                <div class="form-group">
                                    <input type="text" class="form-control datepicker" readonly name="dt_inicio" id="dt_inicio">
                                </div>
                            </div>
                            <div class="col-md-3 pull-right">
                                <div class="form-group">
                                    <input type="text" class="form-control datepicker" readonly name="dt_final" id="dt_final">
                                </div>
                            </div>
                            <div class="col-md-6 pull-right">
                                <div class="form-group">
                                    <select id="filtro-periodo" class="form-control w-100">
                                        <option value="7">Últimos 7 dias</option>
                                        <option value="14">Últimos 14 dias</option>
                                        <option value="30">Últimos 30 dias</option>
                                        <option value="mes_anterior">Mês anterior</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-9 col-md-6 col-sm-6">
                        <div class="grafico-midias">
                            <canvas id="graficoMidias"></canvas>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card card-stats">
                                    <div class="card-body ">
                                        <div class="row totais">
                                            <div class="col-5 col-md-4">
                                                <div class="icon-big text-center icon-success"><i class="fa fa-globe text-success"></i></div>
                                            </div>
                                            <div class="col-7 col-md-8">
                                                <div class="numbers">
                                                    <p class="card-category">WEB</p>
                                                    <p class="card-title"></p>
                                                    <p class="total-web">0</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card card-stats">
                                    <div class="card-body ">
                                        <div class="row totais">
                                            <div class="col-5 col-md-4">
                                                <div class="icon-big text-center icon-danger"><i class="fa fa-newspaper-o text-danger"></i></div>
                                            </div>
                                            <div class="col-7 col-md-8">
                                                <div class="numbers">
                                                    <p class="card-category">IMPRESSO</p>
                                                    <p class="card-title"></p>
                                                    <p class="total-impresso">0</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                       
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card card-stats">
                                    <div class="card-body ">
                                        <div class="row totais">
                                            <div class="col-5 col-md-4">
                                                <div class="icon-big text-center icon-danger"><i class="fa fa-volume-up text-info"></i></div>
                                            </div>
                                            <div class="col-7 col-md-8">
                                                <div class="numbers">
                                                    <p class="card-category">RÁDIO</p>
                                                    <p class="card-title"></p>
                                                    <p class="total-radio">0</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card card-stats">
                                    <div class="card-body ">
                                        <div class="row totais">
                                            <div class="col-5 col-md-4">
                                                <div class="icon-big text-center icon-danger"><i class="fa fa-tv text-warning"></i></div>
                                            </div>
                                            <div class="col-7 col-md-8">
                                                <div class="numbers">
                                                    <p class="card-category">TV</p>
                                                    <p class="card-title"></p>
                                                    <p class="total-tv">0</p>
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
        </div>
    </div>
    
</div>
@endrole
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
    $(document).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');

        // Função auxiliar para formatar data como YYYY-MM-DD
        function formatDate(date) {
            let ano = date.getFullYear();
            let mes = String(date.getMonth() + 1).padStart(2, '0'); // Mês começa em 0
            let dia = String(date.getDate()).padStart(2, '0');
            return dia+"/"+mes+"/"+ano;
        }

        @if(Auth::user()->hasRole('cliente'))

            function carregarGraficoMidias(periodo = '7') {

                // Data atual
                let hoje = new Date();

                // Data início: hoje - periodo dias
                let dt_inicio = new Date();
                dt_inicio.setDate(hoje.getDate() - (periodo -1));

                $("#dt_inicio").val(formatDate(dt_inicio));
                $("#dt_final").val(formatDate(hoje));

                $.ajax({
                    url: '{{ url("dashboard/grafico-midias") }}',
                    type: 'GET',
                    data: { periodo: periodo, tipo: 'linha' },
                    beforeSend: function() {
                        $('.grafico-midias').loader('show');
                        $('.totais').loader('show');
                    },
                    success: function(res) {

                        if(window.graficoMidias instanceof Chart) {
                            window.graficoMidias.destroy();
                        }

                        $(".total-tv").html(res.total.tv);      
                        $(".total-radio").html(res.total.radio);
                        $(".total-impresso").html(res.total.jornal);
                        $(".total-web").html(res.total.web);                  

                        var ctx = document.getElementById('graficoMidias').getContext('2d');
                        window.graficoMidias = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: res.labels, // dias do período
                                datasets: [
                                    {
                                        label: 'Web',
                                        data: res.data.web,
                                        borderColor: '#6bd098',
                                        backgroundColor: '#6bd098',
                                        fill: false,
                                        tension: 0.2
                                    },
                                    {
                                        label: 'Jornal',
                                        data: res.data.jornal,
                                        borderColor: '#ef8157',
                                        backgroundColor: '#ef8157',
                                        fill: false,
                                        tension: 0.2
                                    },
                                    {
                                        label: 'Rádio',
                                        data: res.data.radio,
                                        borderColor: '#51bcda',
                                        backgroundColor: '#51bcda',
                                        fill: false,
                                        tension: 0.2
                                    },
                                    {
                                        label: 'TV',
                                        data: res.data.tv,
                                        borderColor: '#fbc658',
                                        backgroundColor: '#fbc658',
                                        fill: false,
                                        tension: 0.2
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: true }
                                },
                                scales: {
                                    y: { beginAtZero: true },
                                    x: { title: { display: true, text: 'Dia' } }
                                }
                            }
                        });
                    },
                    complete: function(){
                        $('.grafico-midias').loader('hide');
                        $('.totais').loader('hide');
                    }
                });
            }

            $('#filtro-periodo').on('change', function() {
                carregarGraficoMidias($(this).val());
            });

            carregarGraficoMidias();

        @endif

        $.ajax({
            url: host+'/noticias/estatisticas/areas',
            type: 'GET',
            success: function(data) {
                if(data.length){
                    
                    data.forEach(element => {                    
                        $(".estatisticas-areas").append('<span class="badge badge-pill badge-default ml-2">'+element.descricao+' ('+element.total+')</span>');
                    });

                }else{
                    $(".estatisticas-areas").html('<p class="text-danger"><i class="fa fa-exclamation-circle mr-1"></i>Identificação de áreas não realizada</p>');
                }
            },
            error: function(response){
                
            },
            complete: function(){
                    
            }
        });

        $('.total-coletas').each(function(i, obj) {
            
            var id_fonte = $(this).data("id");
            
            $.ajax({
                url: host+'/fonte-web/estatisticas/coletas/'+id_fonte,
                type: 'GET',
                success: function(data) {
                            
                },
                error: function(response){
                
                },
                complete: function(){
                    
                }
            }).done(function (data) {
                var chave = "#total_coletas_"+id_fonte;
                $(chave).text(data[0].total);
            });
            
        });

        $.ajax({
            url: host+'/fonte-web/estatisticas/top/10',
            type: 'GET',
            beforeSend: function() {               
                $('.card-maiores-coletas').loader('show');
            },
            success: function(data) {

                if(data.length){
                    $(".text-maiores-coletas").addClass("d-none");
                }else{
                    $(".text-maiores-coletas").removeClass("d-none");
                }

                data.forEach(element => {                    
                    $(".maiores-coletas").append('<li style="border-bottom: 1px solid #ebebeb; margin-bottom: 3px;"><div class="row"><div class="col-md-9">'+element.nome+'<br><span class="text-muted"><small>'+element.url+'</small></span></div> <div class="col-md-2 text-right"><p class="mt-2">'+element.total+'</p></div></div></li>');
                });

            },
            error: function(){

            },
            complete: function(){
                $('.card-maiores-coletas').loader('hide');
            }
        });

        $.ajax({
            url: host+'/fonte-web/estatisticas/sem/10',
            type: 'GET',
            beforeSend: function() {               
                $('.card-sem-coleta').loader('show');
            },
            success: function(data) {

                if(data.length){
                    $(".text-sem-coleta").addClass("d-none");
                }else{
                    $(".text-sem-coleta").removeClass("d-none");
                }
                
                data.forEach(element => {
                    
                    $(".sem-coleta").append('<li style="border-bottom: 1px solid #ebebeb; margin-bottom: 3px;"><div class="row"><div class="col-md-9">'+element.nome+'<br><span class="text-muted"><small>'+element.url+'</small></span></div> <div class="col-md-2 text-right"><p class="mt-2">'+element.total+'</p></div></div></li>');

                });

            },
            error: function(){

            },
            complete: function(){
                $('.card-sem-coleta').loader('hide');
            }
        });

        $.ajax({
            url: host+'/inicio/estatisticas',
            type: 'GET',
            beforeSend: function() {
                
            },
            success: function(data) {
                $(".total_radio > .fa-spin").remove();
                $(".total_tv > .fa-spin").remove();
                $(".total_jornal > .fa-spin").remove();
                $(".total_web > .fa-spin").remove();

                $(".total_jornal").append('<a href="'+host+'/impresso">'+data.impresso+'</a>');
                $(".total_tv").append('<a href="'+host+'/tv/dashboard">'+data.tv+'</a>');
                $(".total_radio").append('<a href="'+host+'/radio/dashboard">'+data.radio+'</a>');
                $(".total_web").append('<a href="'+host+'/noticia/web/dashboard">'+data.web+'</a>');
            },
            error: function(){
                $(".total_radio").text('Erro ao carregar').css('color','#dc3545').css('font-size', '18px');
                $(".total_tv").text('Erro ao carregar').css('color','#dc3545').css('font-size', '18px');
                $(".total_jornal").text('Erro ao carregar').css('color','#dc3545').css('font-size', '18px');
                $(".total_web").text('Erro ao carregar').css('color','#dc3545').css('font-size', '18px');
            },
            complete: function(){
                
            }
        });

        $(".btn-refresh").click(function(){
            
            Swal.fire({
                input: 'text',
                title: "Alterar Data",
                text: "Informe a data que deseja visualizar",              
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                confirmButtonText: '<i class="fa fa-refresh"></i> Atualizar Data',
                cancelButtonText: '<i class="fa fa-times"></i> Cancelar',
                preConfirm: () => {
                    if ($(".swal2-input").val()) {
                        return true;
                    } else {
                        Swal.showValidationMessage('Campo obrigatório')   
                    }
                },
                didOpen: () => {
                    $('.swal2-input').mask('00/00/0000',{ "placeholder": "dd/mm/YYYY" });
                }
            }).then(function(result) {
                if (result.isConfirmed) {

                    var data = $(".swal2-input").val();

                    if(data){

                        $.ajax({
                            url: host+'/alterar-data',
                            type: 'POST',
                            data: {
                                    "_token": $('meta[name="csrf-token"]').attr('content'),
                                    "data": data
                            },
                            success: function(response) {
                                window.location.reload();                                
                            },
                            error: function(response){
                                console.log(response);
                            }
                        });
                    }else{
                        return false;
                    }
                }
            });

        });
    });
</script>
@endsection