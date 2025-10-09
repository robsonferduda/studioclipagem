@extends('layouts.app')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-dashboard"></i> Dashboard
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ $cliente->nome }}
                    </h4>
                </div>
                <div class="col-md-4">
                    <div class="btn-group pull-right" style="margin-right: 12px;">
                        <button type="button" class="btn btn-sm btn-primary active" id="periodo-7" data-periodo="7">
                            7 dias
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="periodo-14" data-periodo="14">
                            14 dias
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="periodo-30" data-periodo="30">
                            30 dias
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="periodo-mes" data-periodo="mes_anterior">
                            Mês anterior
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            
            <!-- Loading -->
            <div id="loading-dashboard" class="text-center py-4" style="display: none;">
                <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2">Carregando dados do dashboard...</p>
            </div>

            <!-- Cards com totais -->
            <div id="cards-totais" class="row mb-4">
                <!-- Cards serão inseridos via JavaScript -->
            </div>

            <!-- Gráficos principais -->
            <div class="row mb-4">
                <!-- Gráfico de evolução temporal -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-line-chart text-primary"></i> Evolução das Notícias
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-evolucao" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de distribuição por mídia -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-pie-chart text-success"></i> Distribuição por Mídia
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-midia" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção condicional para Sentimento -->
            @if($fl_sentimento)
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-smile-o text-warning"></i> Sentimento Geral das Notícias
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-sentimento-geral" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-pie-chart text-info"></i> Sentimento por Tipo de Mídia
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="graficos-sentimento-midia">
                                <!-- Gráficos por mídia serão inseridos via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tags mais utilizadas -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-tags text-info"></i> Tags Mais Utilizadas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="tags-container">
                                <!-- Tags serão inseridas via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-tags text-info"></i> Tags Mais Utilizadas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="tags-container">
                                <!-- Tags serão inseridas via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Seção condicional para Retorno de Mídia -->
            @if($fl_retorno_midia)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-money text-success"></i> Retorno de Mídia por Tipo
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-retorno" width="400" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Top Fontes e Áreas -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-newspaper-o text-primary"></i> Top 10 Fontes por Mídia
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Abas para tipos de mídia -->
                            <ul class="nav nav-tabs nav-tabs-sm mb-3" id="fontes-tabs" role="tablist">
                                @if($cliente->fl_web)
                                <li class="nav-item">
                                    <a class="nav-link active" id="web-tab" data-toggle="tab" href="#web-fontes" role="tab">Web</a>
                                </li>
                                @endif
                                @if($cliente->fl_impresso)
                                <li class="nav-item">
                                    <a class="nav-link @if(!$cliente->fl_web) active @endif" id="impresso-tab" data-toggle="tab" href="#impresso-fontes" role="tab">Impresso</a>
                                </li>
                                @endif
                                @if($cliente->fl_radio)
                                <li class="nav-item">
                                    <a class="nav-link @if(!$cliente->fl_web && !$cliente->fl_impresso) active @endif" id="radio-tab" data-toggle="tab" href="#radio-fontes" role="tab">Rádio</a>
                                </li>
                                @endif
                                @if($cliente->fl_tv)
                                <li class="nav-item">
                                    <a class="nav-link @if(!$cliente->fl_web && !$cliente->fl_impresso && !$cliente->fl_radio) active @endif" id="tv-tab" data-toggle="tab" href="#tv-fontes" role="tab">TV</a>
                                </li>
                                @endif
                            </ul>
                            
                            <!-- Conteúdo das abas -->
                            <div class="tab-content" id="fontes-tab-content">
                                @if($cliente->fl_web)
                                <div class="tab-pane fade show active" id="web-fontes" role="tabpanel">
                                    <div id="web-fontes-container">
                                        <!-- Lista de fontes web será inserida via JavaScript -->
                                    </div>
                                </div>
                                @endif
                                @if($cliente->fl_impresso)
                                <div class="tab-pane fade @if(!$cliente->fl_web) show active @endif" id="impresso-fontes" role="tabpanel">
                                    <div id="impresso-fontes-container">
                                        <!-- Lista de fontes impressas será inserida via JavaScript -->
                                    </div>
                                </div>
                                @endif
                                @if($cliente->fl_radio)
                                <div class="tab-pane fade @if(!$cliente->fl_web && !$cliente->fl_impresso) show active @endif" id="radio-fontes" role="tabpanel">
                                    <div id="radio-fontes-container">
                                        <!-- Lista de emissoras de rádio será inserida via JavaScript -->
                                    </div>
                                </div>
                                @endif
                                @if($cliente->fl_tv)
                                <div class="tab-pane fade @if(!$cliente->fl_web && !$cliente->fl_impresso && !$cliente->fl_radio) show active @endif" id="tv-fontes" role="tabpanel">
                                    <div id="tv-fontes-container">
                                        <!-- Lista de emissoras de TV será inserida via JavaScript -->
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($fl_areas)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-sitemap text-warning"></i> Top 10 Áreas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="areas-container">
                                <!-- Lista de áreas será inserida via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Links úteis -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-link text-info"></i> Ações Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="btn-group" role="group">
                                <a href="{{ url('cliente/noticias') }}" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Pesquisar Notícias
                                </a>
                                <a href="{{ url('cliente/relatorios') }}" class="btn btn-success">
                                    <i class="fa fa-file-pdf-o"></i> Meus Relatórios
                                </a>
                                @if($cliente->fl_area_restrita)
                                <a href="{{ url('configuracoes') }}" class="btn btn-warning">
                                    <i class="fa fa-cogs"></i> Configurações
                                </a>
                                @endif
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
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

<script>
$(document).ready(function() {
    const host = $('meta[name="base-url"]').attr('content');
    let dadosGlobais = {};
    let graficos = {};
    
    // Flags de permissões
    const flSentimento = {{ $fl_sentimento ? 'true' : 'false' }};
    const flRetornoMidia = {{ $fl_retorno_midia ? 'true' : 'false' }};
    const flAreas = {{ $fl_areas ? 'true' : 'false' }};
    
    // Carrega dados iniciais (7 dias)
    carregarDadosDashboard('7');
    
    // Event listeners para botões de período
    $('.btn-group button[data-periodo]').click(function() {
        const periodo = $(this).data('periodo');
        
        // Atualiza visual dos botões
        $('.btn-group button[data-periodo]').removeClass('btn-primary active').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
        
        // Carrega dados
        carregarDadosDashboard(periodo);
    });
    
    function carregarDadosDashboard(periodo) {
        $('#loading-dashboard').show();
        $('#cards-totais, .row').not('#loading-dashboard').hide();
        
        $.ajax({
            url: host + '/cliente/dashboard/dados',
            type: 'GET',
            data: { periodo: periodo },
            dataType: 'json',
            timeout: 300000, // 5 minutos para ambiente de desenvolvimento
            success: function(response) {
                if (response.success) {
                    dadosGlobais = response.dados;
                    console.log('Dados carregados:', dadosGlobais);
                    
                    // Renderiza todos os componentes
                    renderizarCardsTotais();
                    renderizarGraficoEvolucao();
                    renderizarGraficoMidia();
                    renderizarTopFontes();
                    renderizarTopTags();
                    
                    if (flSentimento) {
                        renderizarGraficosEntimento();
                    }
                    
                    if (flRetornoMidia) {
                        renderizarGraficoRetorno();
                    }
                    
                    if (flAreas) {
                        renderizarTopAreas();
                    }
                    
                    $('#loading-dashboard').hide();
                    $('#cards-totais, .row').not('#loading-dashboard').show();
                } else {
                    console.error('Erro na resposta:', response.message);
                    mostrarErro(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro na requisição:', error);
                if (status === 'timeout') {
                    mostrarErro('Timeout na requisição - o servidor está demorando para responder. Tente novamente.');
                } else {
                    mostrarErro('Erro ao carregar dados do dashboard: ' + error);
                }
            }
        });
    }
    
    function renderizarCardsTotais() {
        const totais = dadosGlobais.totais_midia || {};
        let html = '';
        
        // Card Total Geral
        html += `
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats dashboard-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5 col-md-4">
                                <div class="icon-big text-center icon-warning">
                                    <i class="fa fa-newspaper-o text-primary"></i>
                                </div>
                            </div>
                            <div class="col-7 col-md-8">
                                <div class="numbers">
                                    <p class="card-category">Total de Notícias</p>
                                    <p class="card-title">${totais.total || 0}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Card Web (se habilitado)
        if (totais.web !== undefined) {
            html += `
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-stats dashboard-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-globe text-info"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Notícias Web</p>
                                        <p class="card-title">${totais.web || 0}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Card TV (se habilitado)
        if (totais.tv !== undefined) {
            html += `
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-stats dashboard-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-television text-danger"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Notícias TV</p>
                                        <p class="card-title">${totais.tv || 0}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Card Rádio (se habilitado)
        if (totais.radio !== undefined) {
            html += `
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-stats dashboard-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-volume-up text-success"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Notícias Rádio</p>
                                        <p class="card-title">${totais.radio || 0}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Card Impresso (se habilitado)
        if (totais.impresso !== undefined) {
            html += `
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-stats dashboard-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-newspaper-o text-warning"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Notícias Impressas</p>
                                        <p class="card-title">${totais.impresso || 0}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        $('#cards-totais').html(html);
    }
    
    function renderizarGraficoEvolucao() {
        const evolucao = dadosGlobais.evolucao_temporal || [];
        
        if (evolucao.length === 0) {
            $('#grafico-evolucao').parent().html('<p class="text-center text-muted">Nenhum dado disponível</p>');
            return;
        }
        
        const ctx = document.getElementById('grafico-evolucao').getContext('2d');
        
        // Destroi gráfico anterior se existir
        if (graficos.evolucao) {
            graficos.evolucao.destroy();
        }
        
        graficos.evolucao = new Chart(ctx, {
            type: 'line',
            data: {
                labels: evolucao.map(item => moment(item.data).format('DD/MM')),
                datasets: [{
                    label: 'Notícias por Dia',
                    data: evolucao.map(item => item.total),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    
    function renderizarGraficoMidia() {
        const totais = dadosGlobais.totais_midia || {};
        
        // Filtra apenas tipos com valores > 0
        const dados = [];
        const labels = [];
        const cores = [];
        
        if (totais.web > 0) {
            dados.push(totais.web);
            labels.push('Web');
            cores.push('#17a2b8');
        }
        if (totais.tv > 0) {
            dados.push(totais.tv);
            labels.push('TV');
            cores.push('#dc3545');
        }
        if (totais.radio > 0) {
            dados.push(totais.radio);
            labels.push('Rádio');
            cores.push('#28a745');
        }
        if (totais.impresso > 0) {
            dados.push(totais.impresso);
            labels.push('Impresso');
            cores.push('#ffc107');
        }
        
        if (dados.length === 0) {
            $('#grafico-midia').parent().html('<p class="text-center text-muted">Nenhum dado disponível</p>');
            return;
        }
        
        const ctx = document.getElementById('grafico-midia').getContext('2d');
        
        // Destroi gráfico anterior se existir
        if (graficos.midia) {
            graficos.midia.destroy();
        }
        
        graficos.midia = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dados,
                    backgroundColor: cores,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    function renderizarGraficosEntimento() {
        // Gráfico de sentimento geral
        renderizarGraficoSentimentoGeral();
        
        // Gráficos de sentimento por mídia
        renderizarGraficosSentimentoPorMidia();
    }
    
    function renderizarGraficoSentimentoGeral() {
        const sentimentos = dadosGlobais.sentimentos || {};
        
        const ctx = document.getElementById('grafico-sentimento-geral').getContext('2d');
        
        // Destroi gráfico anterior se existir
        if (graficos.sentimentoGeral) {
            graficos.sentimentoGeral.destroy();
        }
        
        const total = (sentimentos.positivo || 0) + (sentimentos.neutro || 0) + (sentimentos.negativo || 0);
        
        if (total === 0) {
            $('#grafico-sentimento-geral').parent().html('<p class="text-center text-muted">Nenhum dado disponível</p>');
            return;
        }
        
        // Calcular porcentagens
        const percPositivo = ((sentimentos.positivo || 0) / total * 100).toFixed(1);
        const percNeutro = ((sentimentos.neutro || 0) / total * 100).toFixed(1);
        const percNegativo = ((sentimentos.negativo || 0) / total * 100).toFixed(1);
        
        graficos.sentimentoGeral = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [
                    `Positivo (${percPositivo}%)`, 
                    `Neutro (${percNeutro}%)`, 
                    `Negativo (${percNegativo}%)`
                ],
                datasets: [{
                    data: [sentimentos.positivo || 0, sentimentos.neutro || 0, sentimentos.negativo || 0],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    function renderizarGraficosSentimentoPorMidia() {
        const sentimentosPorMidia = dadosGlobais.sentimentos_por_midia || {};
        let html = '';
        
        // Tipos de mídia habilitados
        const midias = [];
        if (sentimentosPorMidia.web && (sentimentosPorMidia.web.positivo + sentimentosPorMidia.web.neutro + sentimentosPorMidia.web.negativo) > 0) {
            midias.push({tipo: 'web', nome: 'Web', cor: '#17a2b8'});
        }
        if (sentimentosPorMidia.tv && (sentimentosPorMidia.tv.positivo + sentimentosPorMidia.tv.neutro + sentimentosPorMidia.tv.negativo) > 0) {
            midias.push({tipo: 'tv', nome: 'TV', cor: '#dc3545'});
        }
        if (sentimentosPorMidia.radio && (sentimentosPorMidia.radio.positivo + sentimentosPorMidia.radio.neutro + sentimentosPorMidia.radio.negativo) > 0) {
            midias.push({tipo: 'radio', nome: 'Rádio', cor: '#28a745'});
        }
        if (sentimentosPorMidia.impresso && (sentimentosPorMidia.impresso.positivo + sentimentosPorMidia.impresso.neutro + sentimentosPorMidia.impresso.negativo) > 0) {
            midias.push({tipo: 'impresso', nome: 'Impresso', cor: '#ffc107'});
        }
        
        if (midias.length === 0) {
            html = '<p class="text-center text-muted">Nenhum dado disponível</p>';
        } else {
            // Criar pequenos gráficos para cada mídia
            midias.forEach(function(midia, index) {
                const dados = sentimentosPorMidia[midia.tipo];
                const total = dados.positivo + dados.neutro + dados.negativo;
                
                if (total > 0) {
                    const percPositivo = ((dados.positivo / total) * 100).toFixed(1);
                    const percNeutro = ((dados.neutro / total) * 100).toFixed(1);
                    const percNegativo = ((dados.negativo / total) * 100).toFixed(1);
                    
                    html += `
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <h6 class="mb-2">
                                    <span class="badge" style="background-color: ${midia.cor}">${midia.nome}</span>
                                    <small class="text-muted">(${total} notícias)</small>
                                </h6>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" style="width: ${percPositivo}%" title="Positivo: ${dados.positivo} (${percPositivo}%)">
                                        ${percPositivo}%
                                    </div>
                                    <div class="progress-bar bg-warning" style="width: ${percNeutro}%" title="Neutro: ${dados.neutro} (${percNeutro}%)">
                                        ${percNeutro}%
                                    </div>
                                    <div class="progress-bar bg-danger" style="width: ${percNegativo}%" title="Negativo: ${dados.negativo} (${percNegativo}%)">
                                        ${percNegativo}%
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <small class="d-block"><i class="fa fa-circle text-success"></i> Positivo: ${dados.positivo}</small>
                                <small class="d-block"><i class="fa fa-circle text-warning"></i> Neutro: ${dados.neutro}</small>
                                <small class="d-block"><i class="fa fa-circle text-danger"></i> Negativo: ${dados.negativo}</small>
                            </div>
                        </div>
                    `;
                }
            });
        }
        
        $('#graficos-sentimento-midia').html(html);
    }
    
    function renderizarGraficoRetorno() {
        const retorno = dadosGlobais.retorno_midia || {};
        
        const ctx = document.getElementById('grafico-retorno').getContext('2d');
        
        // Destroi gráfico anterior se existir
        if (graficos.retorno) {
            graficos.retorno.destroy();
        }
        
        graficos.retorno = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Web', 'TV', 'Rádio', 'Impresso'],
                datasets: [{
                    label: 'Valor (R$)',
                    data: [retorno.web || 0, retorno.tv || 0, retorno.radio || 0, retorno.impresso || 0],
                    backgroundColor: ['#17a2b8', '#dc3545', '#28a745', '#ffc107'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    
    function renderizarTopFontes() {
        const fontes = dadosGlobais.top_fontes || {};
        
        // Renderiza fontes para cada tipo de mídia
        ['web', 'impresso', 'radio', 'tv'].forEach(function(tipo) {
            const container = `#${tipo}-fontes-container`;
            const fontesDoTipo = fontes[tipo] || [];
            let html = '';
            
            if (fontesDoTipo.length === 0) {
                html = '<p class="text-center text-muted">Nenhum dado disponível</p>';
            } else {
                html = '<div class="list-group list-group-flush">';
                fontesDoTipo.forEach(function(fonte, index) {
                    const posicao = index + 1;
                    const badgeClass = 'badge-success';
                    
                    html += `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge ${badgeClass} mr-2">${posicao}º</span>
                                <strong>${fonte.fonte}</strong>
                                <small class="text-muted ml-2">(${fonte.total} registros)</small>
                            </div>
                            <span class="badge badge-light">${fonte.total} notícias</span>
                        </div>
                    `;
                });
                html += '</div>';
            }
            
            $(container).html(html);
        });
    }
    
    function renderizarTopTags() {
        const tags = dadosGlobais.top_tags || [];
        let html = '';
        
        if (tags.length === 0) {
            html = '<p class="text-center text-muted">Nenhuma tag encontrada</p>';
        } else {
            html = '<div class="tag-cloud">';
            tags.forEach(function(tag, index) {
                const fontSize = Math.max(12, 20 - (index * 1));
                html += `
                    <span class="tag-item" style="font-size: ${fontSize}px;">
                        ${tag.tag} (${tag.total})
                    </span>
                `;
            });
            html += '</div>';
        }
        
        $('#tags-container').html(html);
    }
    
    function renderizarTopAreas() {
        const areas = dadosGlobais.top_areas || [];
        let html = '';
        
        if (areas.length === 0) {
            html = '<p class="text-center text-muted">Nenhum dado disponível</p>';
        } else {
            html = '<div class="list-group list-group-flush">';
            areas.forEach(function(area, index) {
                const posicao = index + 1;
                const badgeClass = 'badge-success';
                
                html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge ${badgeClass} mr-2">${posicao}º</span>
                            <strong>${area.area}</strong>
                        </div>
                        <span class="badge badge-light">${area.total} notícias</span>
                    </div>
                `;
            });
            html += '</div>';
        }
        
        $('#areas-container').html(html);
    }
    
    function mostrarErro(mensagem) {
        $('#loading-dashboard').hide();
        $('#cards-totais').html(`
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> ${mensagem}
                </div>
            </div>
        `).show();
    }
});
</script>

<style>
.dashboard-card {
    transition: transform 0.2s ease;
    border: 1px solid #dee2e6;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card-stats .card-body {
    padding: 15px;
}

.card-stats .numbers p {
    margin: 0;
}

.card-stats .card-category {
    font-size: 12px;
    color: #9A9A9A;
    margin-bottom: 5px;
}

.card-stats .card-title {
    font-size: 28px;
    font-weight: bold;
    color: #3C4858;
}

.icon-big {
    font-size: 3em;
    min-height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.tag-cloud {
    text-align: center;
    line-height: 2.5;
}

.tag-item {
    display: inline-block;
    margin: 5px;
    padding: 8px 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    font-weight: 500;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.tag-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.list-group-item {
    border-left: none;
    border-right: none;
    padding: 12px 0;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

#loading-dashboard {
    min-height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.btn-group .btn.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

/* Responsividade */
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-wrap: wrap;
    }
    
    .btn-group .btn {
        flex: 1;
        margin-bottom: 5px;
    }
    
    .card-stats .icon-big {
        font-size: 2em;
        min-height: 48px;
    }
    
    .card-stats .card-title {
        font-size: 24px;
    }
}

/* Novos estilos para sentimento */
.progress {
    overflow: visible !important;
}

.progress-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.9);
    font-weight: bold;
    font-size: 11px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    min-width: 20px;
}

.list-group-item {
    transition: background-color 0.2s ease;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

/* Melhorias para gráficos de sentimento por mídia */
#graficos-sentimento-midia .progress {
    border-radius: 10px;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
}

#graficos-sentimento-midia .badge {
    color: white;
    font-weight: 500;
}

/* Tooltips para barras de progresso */
.progress[title]:hover {
    cursor: help;
}
</style>
@endsection
