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
                    <div class="d-flex justify-content-end align-items-center" style="margin-right: 12px; gap: 10px;">
                        <!-- Botão de exportação completa -->
                        <div class="btn-group" role="group" aria-label="Exportar dashboard completo">
                            <button type="button" class="btn btn-sm btn-success" onclick="exportarDashboardCompleto('png')" title="Exportar dashboard completo como PNG">
                                <i class="fa fa-download mr-1"></i>PNG
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="exportarDashboardCompleto('pdf')" title="Exportar dashboard completo como PDF">
                                <i class="fa fa-file-pdf-o mr-1"></i>PDF
                            </button>
                        </div>
                        
                        <!-- Filtros de período -->
                        <div class="btn-group" role="group">
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-line-chart text-primary"></i> Evolução das Notícias
                            </h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Exportar gráfico">
                                <button type="button" class="btn btn-outline-primary btn-export" 
                                        onclick="exportarGrafico('evolucao', 'png')" title="Exportar como imagem PNG">
                                    <i class="fa fa-image mr-1"></i>PNG
                                </button>
                                <button type="button" class="btn btn-outline-success btn-export" 
                                        onclick="exportarGrafico('evolucao', 'csv')" title="Exportar dados como CSV">
                                    <i class="fa fa-table mr-1"></i>CSV
                                </button>
                                <button type="button" class="btn btn-outline-info btn-export" 
                                        onclick="exportarGrafico('evolucao', 'svg')" title="Exportar como SVG vetorial">
                                    <i class="fa fa-code mr-1"></i>SVG
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-evolucao" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de distribuição por mídia -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-pie-chart text-success"></i> Distribuição por Mídia
                            </h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Exportar gráfico">
                                <button type="button" class="btn btn-outline-primary btn-export" 
                                        onclick="exportarGrafico('midia', 'png')" title="Exportar como imagem PNG">
                                    <i class="fa fa-image mr-1"></i>PNG
                                </button>
                                <button type="button" class="btn btn-outline-success btn-export" 
                                        onclick="exportarGrafico('midia', 'csv')" title="Exportar dados como CSV">
                                    <i class="fa fa-table mr-1"></i>CSV
                                </button>
                                <button type="button" class="btn btn-outline-info btn-export" 
                                        onclick="exportarGrafico('midia', 'svg')" title="Exportar como SVG vetorial">
                                    <i class="fa fa-code mr-1"></i>SVG
                                </button>
                            </div>
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-smile-o text-warning"></i> Sentimento Geral das Notícias
                            </h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Exportar gráfico">
                                <button type="button" class="btn btn-outline-primary btn-export" 
                                        onclick="exportarGrafico('sentimento-geral', 'png')" title="Exportar como imagem PNG">
                                    <i class="fa fa-image mr-1"></i>PNG
                                </button>
                                <button type="button" class="btn btn-outline-success btn-export" 
                                        onclick="exportarGrafico('sentimento-geral', 'csv')" title="Exportar dados como CSV">
                                    <i class="fa fa-table mr-1"></i>CSV
                                </button>
                                <button type="button" class="btn btn-outline-info btn-export" 
                                        onclick="exportarGrafico('sentimento-geral', 'svg')" title="Exportar como SVG vetorial">
                                    <i class="fa fa-code mr-1"></i>SVG
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-sentimento-geral" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-pie-chart text-info"></i> Sentimento por Tipo de Mídia
                            </h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Exportar elemento">
                                <button type="button" class="btn btn-outline-primary btn-export" 
                                        onclick="exportarElemento('graficos-sentimento-midia', 'sentimento-midia', 'png')" title="Exportar como imagem PNG">
                                    <i class="fa fa-image mr-1"></i>PNG
                                </button>
                                <button type="button" class="btn btn-outline-success btn-export" 
                                        onclick="exportarElemento('graficos-sentimento-midia', 'sentimento-midia', 'csv')" title="Exportar dados como CSV">
                                    <i class="fa fa-table mr-1"></i>CSV
                                </button>
                            </div>
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-tags text-info"></i> Tags Mais Utilizadas
                            </h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Exportar elemento">
                                <button type="button" class="btn btn-outline-primary btn-export" 
                                        onclick="exportarElemento('tags-container', 'tags', 'png')" title="Exportar como imagem PNG">
                                    <i class="fa fa-image mr-1"></i>PNG
                                </button>
                                <button type="button" class="btn btn-outline-success btn-export" 
                                        onclick="exportarElemento('tags-container', 'tags', 'csv')" title="Exportar dados como CSV">
                                    <i class="fa fa-table mr-1"></i>CSV
                                </button>
                            </div>
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-tags text-info"></i> Tags Mais Utilizadas
                            </h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Exportar elemento">
                                <button type="button" class="btn btn-outline-primary btn-export" 
                                        onclick="exportarElemento('tags-container', 'tags', 'png')" title="Exportar como imagem PNG">
                                    <i class="fa fa-image mr-1"></i>PNG
                                </button>
                                <button type="button" class="btn btn-outline-success btn-export" 
                                        onclick="exportarElemento('tags-container', 'tags', 'csv')" title="Exportar dados como CSV">
                                    <i class="fa fa-table mr-1"></i>CSV
                                </button>
                            </div>
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

            <!-- Seção condicional para Nuvem de Palavras-Chave -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-cloud text-info"></i> Nuvem de Palavras Mais Frequentes
                            </h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Exportar elemento">
                                <button type="button" class="btn btn-outline-primary btn-export" 
                                        onclick="exportarElemento('wordcloud-container', 'palavras-chave', 'png')" title="Exportar como imagem PNG">
                                    <i class="fa fa-image mr-1"></i>PNG
                                </button>
                                <button type="button" class="btn btn-outline-success btn-export" 
                                        onclick="exportarElemento('wordcloud-container', 'palavras-chave', 'csv')" title="Exportar dados como CSV">
                                    <i class="fa fa-table mr-1"></i>CSV
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="wordcloud-container" style="height: 400px; width: 100%;">
                                <!-- Nuvem de palavras será inserida via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção condicional para Retorno de Mídia -->
            @if($fl_retorno_midia)
            <div class="row mb-4">
                <!-- Card de Resumo de Retorno -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-money text-success"></i> Resumo Retorno de Mídia
                            </h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Exportar elemento">
                                <button type="button" class="btn btn-outline-primary btn-export" 
                                        onclick="exportarElemento('resumo-retorno-container', 'resumo-retorno', 'png')" title="Exportar como imagem PNG">
                                    <i class="fa fa-image mr-1"></i>PNG
                                </button>
                                <button type="button" class="btn btn-outline-success btn-export" 
                                        onclick="exportarElemento('resumo-retorno-container', 'resumo-retorno', 'csv')" title="Exportar dados como CSV">
                                    <i class="fa fa-table mr-1"></i>CSV
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="resumo-retorno-container">
                                <!-- Resumo será inserido via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gráfico de Retorno por Tipo -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-bar-chart text-success"></i> Retorno de Mídia por Tipo
                            </h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Exportar gráfico">
                                <button type="button" class="btn btn-outline-primary btn-export" 
                                        onclick="exportarGrafico('retorno', 'png')" title="Exportar como imagem PNG">
                                    <i class="fa fa-image mr-1"></i>PNG
                                </button>
                                <button type="button" class="btn btn-outline-success btn-export" 
                                        onclick="exportarGrafico('retorno', 'csv')" title="Exportar dados como CSV">
                                    <i class="fa fa-table mr-1"></i>CSV
                                </button>
                                <button type="button" class="btn btn-outline-info btn-export" 
                                        onclick="exportarGrafico('retorno', 'svg')" title="Exportar como SVG vetorial">
                                    <i class="fa fa-code mr-1"></i>SVG
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-retorno" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ranking de Veículos por Retorno -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-trophy text-warning"></i> Top 10 Veículos por Retorno de Mídia
                            </h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Exportar elemento">
                                <button type="button" class="btn btn-outline-primary btn-export" 
                                        onclick="exportarElemento('ranking-retorno-tab-content', 'ranking-veiculos-retorno', 'png')" title="Exportar como imagem PNG">
                                    <i class="fa fa-image mr-1"></i>PNG
                                </button>
                                <button type="button" class="btn btn-outline-success btn-export" 
                                        onclick="exportarElemento('ranking-retorno-tab-content', 'ranking-veiculos-retorno', 'csv')" title="Exportar dados como CSV">
                                    <i class="fa fa-table mr-1"></i>CSV
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Abas para tipos de mídia -->
                            <ul class="nav nav-tabs nav-tabs-sm mb-3" id="ranking-retorno-tabs" role="tablist">
                                @if($cliente->fl_web)
                                <li class="nav-item">
                                    <a class="nav-link active" id="ranking-web-tab" data-toggle="tab" href="#ranking-web-retorno" role="tab">Web</a>
                                </li>
                                @endif
                                @if($cliente->fl_impresso)
                                <li class="nav-item">
                                    <a class="nav-link @if(!$cliente->fl_web) active @endif" id="ranking-impresso-tab" data-toggle="tab" href="#ranking-impresso-retorno" role="tab">Impresso</a>
                                </li>
                                @endif
                                @if($cliente->fl_radio)
                                <li class="nav-item">
                                    <a class="nav-link @if(!$cliente->fl_web && !$cliente->fl_impresso) active @endif" id="ranking-radio-tab" data-toggle="tab" href="#ranking-radio-retorno" role="tab">Rádio</a>
                                </li>
                                @endif
                                @if($cliente->fl_tv)
                                <li class="nav-item">
                                    <a class="nav-link @if(!$cliente->fl_web && !$cliente->fl_impresso && !$cliente->fl_radio) active @endif" id="ranking-tv-tab" data-toggle="tab" href="#ranking-tv-retorno" role="tab">TV</a>
                                </li>
                                @endif
                            </ul>
                            
                            <!-- Conteúdo das abas -->
                            <div class="tab-content" id="ranking-retorno-tab-content">
                                @if($cliente->fl_web)
                                <div class="tab-pane fade show active" id="ranking-web-retorno" role="tabpanel">
                                    <div id="ranking-web-retorno-container">
                                        <!-- Lista de ranking web será inserida via JavaScript -->
                                    </div>
                                </div>
                                @endif
                                @if($cliente->fl_impresso)
                                <div class="tab-pane fade @if(!$cliente->fl_web) show active @endif" id="ranking-impresso-retorno" role="tabpanel">
                                    <div id="ranking-impresso-retorno-container">
                                        <!-- Lista de ranking impresso será inserida via JavaScript -->
                                    </div>
                                </div>
                                @endif
                                @if($cliente->fl_radio)
                                <div class="tab-pane fade @if(!$cliente->fl_web && !$cliente->fl_impresso) show active @endif" id="ranking-radio-retorno" role="tabpanel">
                                    <div id="ranking-radio-retorno-container">
                                        <!-- Lista de ranking rádio será inserida via JavaScript -->
                                    </div>
                                </div>
                                @endif
                                @if($cliente->fl_tv)
                                <div class="tab-pane fade @if(!$cliente->fl_web && !$cliente->fl_impresso && !$cliente->fl_radio) show active @endif" id="ranking-tv-retorno" role="tabpanel">
                                    <div id="ranking-tv-retorno-container">
                                        <!-- Lista de ranking TV será inserida via JavaScript -->
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Top Fontes e Áreas -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-newspaper-o text-primary"></i> Top 10 Fontes por Mídia
                            </h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Exportar elemento">
                                <button type="button" class="btn btn-outline-primary btn-export" 
                                        onclick="exportarElemento('fontes-tab-content', 'top-fontes', 'png')" title="Exportar como imagem PNG">
                                    <i class="fa fa-image mr-1"></i>PNG
                                </button>
                                <button type="button" class="btn btn-outline-success btn-export" 
                                        onclick="exportarElemento('fontes-tab-content', 'top-fontes', 'csv')" title="Exportar dados como CSV">
                                    <i class="fa fa-table mr-1"></i>CSV
                                </button>
                            </div>
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-sitemap text-warning"></i> Top 10 Áreas
                            </h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Exportar elemento">
                                <button type="button" class="btn btn-outline-primary btn-export" 
                                        onclick="exportarElemento('areas-container', 'top-areas', 'png')" title="Exportar como imagem PNG">
                                    <i class="fa fa-image mr-1"></i>PNG
                                </button>
                                <button type="button" class="btn btn-outline-success btn-export" 
                                        onclick="exportarElemento('areas-container', 'top-areas', 'csv')" title="Exportar dados como CSV">
                                    <i class="fa fa-table mr-1"></i>CSV
                                </button>
                            </div>
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
<!-- Biblioteca para captura de tela de elementos HTML -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<!-- Biblioteca para manipulação de SVG -->
<script src="https://cdn.jsdelivr.net/npm/canvg@4.0.1/dist/umd.js"></script>
<!-- Biblioteca para geração de PDF -->
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<!-- Biblioteca para nuvem de palavras - versão mais estável -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.1.0/wordcloud2.min.js"></script>

<script>
// ==================== VARIÁVEIS GLOBAIS ====================
let dadosGlobais = {};
let graficos = {};

// ==================== FUNÇÕES DE EXPORTAÇÃO (ESCOPO GLOBAL) ====================

/**
 * Função principal para exportar gráficos Chart.js
 */
function exportarGrafico(nomeGrafico, formato) {
    console.log('Tentando exportar gráfico:', nomeGrafico, 'formato:', formato);
    
    const grafico = graficos[nomeGrafico];
    if (!grafico) {
        console.error('Gráfico não encontrado:', nomeGrafico, 'Gráficos disponíveis:', Object.keys(graficos));
        alert('Gráfico não encontrado ou ainda não foi carregado: ' + nomeGrafico);
        return;
    }
    
    console.log('Gráfico encontrado:', grafico);
    const nomeArquivo = `grafico-${nomeGrafico}-${new Date().toISOString().split('T')[0]}`;
    
    // Mostrar feedback visual
    mostrarFeedbackExportacao(true);
    
    try {
        switch(formato) {
            case 'png':
                exportarGraficoPNG(grafico, nomeArquivo);
                break;
            case 'csv':
                exportarGraficoCSV(grafico, nomeGrafico, nomeArquivo);
                break;
            case 'svg':
                exportarGraficoSVG(grafico, nomeArquivo);
                break;
            default:
                throw new Error('Formato não suportado: ' + formato);
        }
        
        // Feedback de sucesso
        setTimeout(() => {
            mostrarFeedbackExportacao(false);
            mostrarMensagemSucesso(`${formato.toUpperCase()} exportado com sucesso!`);
        }, 500);
        
    } catch (error) {
        console.error('Erro durante exportação:', error);
        mostrarFeedbackExportacao(false);
        alert('Erro ao exportar: ' + error.message);
    }
}

/**
 * Função para exportar elementos HTML (não Chart.js)
 */
function exportarElemento(elementId, nomeElemento, formato) {
    console.log('Tentando exportar elemento:', elementId, 'nome:', nomeElemento, 'formato:', formato);
    
    const elemento = document.getElementById(elementId);
    if (!elemento) {
        console.error('Elemento não encontrado:', elementId);
        alert('Elemento não encontrado: ' + elementId);
        return;
    }
    
    console.log('Elemento encontrado:', elemento);
    const nomeArquivo = `${nomeElemento}-${new Date().toISOString().split('T')[0]}`;
    
    // Mostrar feedback visual
    mostrarFeedbackExportacao(true);
    
    try {
        switch(formato) {
            case 'png':
                exportarElementoPNG(elemento, nomeArquivo);
                break;
            case 'csv':
                exportarElementoCSV(elementId, nomeElemento, nomeArquivo);
                break;
            default:
                throw new Error('Formato não suportado para este elemento: ' + formato);
        }
        
        // Feedback de sucesso
        setTimeout(() => {
            mostrarFeedbackExportacao(false);
            mostrarMensagemSucesso(`${formato.toUpperCase()} exportado com sucesso!`);
        }, 500);
        
    } catch (error) {
        console.error('Erro durante exportação:', error);
        mostrarFeedbackExportacao(false);
        alert('Erro ao exportar: ' + error.message);
    }
}

/**
 * Função para exportar o dashboard completo
 */
function exportarDashboardCompleto(formato) {
    console.log('Iniciando exportação do dashboard completo em formato:', formato);
    
    // Verificar se os dados estão carregados
    if (!dadosGlobais || Object.keys(dadosGlobais).length === 0) {
        alert('⚠️ Aguarde o carregamento completo do dashboard antes de exportar.');
        return;
    }
    
    // Elementos do dashboard para capturar
    const dashboardContainer = document.querySelector('.card-body');
    if (!dashboardContainer) {
        alert('Erro: Container do dashboard não encontrado.');
        return;
    }
    
    const nomeArquivo = `dashboard-completo-${new Date().toISOString().split('T')[0]}`;
    
    // Mostrar feedback visual
    mostrarFeedbackExportacao(true);
    
    try {
        switch(formato) {
            case 'png':
                exportarDashboardPNG(dashboardContainer, nomeArquivo);
                break;
            case 'pdf':
                exportarDashboardPDF(dashboardContainer, nomeArquivo);
                break;
            default:
                throw new Error('Formato não suportado: ' + formato);
        }
    } catch (error) {
        console.error('Erro durante exportação do dashboard:', error);
        mostrarFeedbackExportacao(false);
        alert('Erro ao exportar dashboard: ' + error.message);
    }
}

/**
 * Função para testar se as exportações estão funcionando
 */
function testarExportacao() {
    console.log('=== INICIANDO TESTE DE EXPORTAÇÃO ===');
    console.log('Dados globais disponíveis:', dadosGlobais);
    console.log('Gráficos disponíveis:', Object.keys(graficos));
    
    // Testar se dadosGlobais está carregado
    if (!dadosGlobais || Object.keys(dadosGlobais).length === 0) {
        alert('⚠️ Dados não carregados ainda. Aguarde o carregamento da dashboard.');
        return;
    }
    
    // Testar se os gráficos estão disponíveis
    if (!graficos || Object.keys(graficos).length === 0) {
        alert('⚠️ Gráficos não carregados ainda. Aguarde o carregamento da dashboard.');
        return;
    }
    
    // Testar exportação CSV simples
    try {
        const tags = dadosGlobais.top_tags || [];
        if (tags.length > 0) {
            let csvContent = 'Tag,Quantidade\n';
            tags.slice(0, 3).forEach(tag => {
                csvContent += `"${tag.tag}",${tag.total}\n`;
            });
            
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            downloadFile(url, 'teste-tags.csv');
            URL.revokeObjectURL(url);
            
            mostrarMensagemSucesso('✅ Teste de CSV bem-sucedido!');
            console.log('✅ Teste CSV: OK');
        }
    } catch (error) {
        console.error('❌ Erro no teste CSV:', error);
        alert('❌ Erro no teste CSV: ' + error.message);
    }
    
    // Testar exportação PNG de gráfico
    try {
        const primeiroGrafico = Object.keys(graficos)[0];
        if (primeiroGrafico && graficos[primeiroGrafico]) {
            const canvas = graficos[primeiroGrafico].canvas;
            const url = canvas.toDataURL('image/png');
            downloadFile(url, 'teste-grafico.png');
            
            mostrarMensagemSucesso('✅ Teste de PNG bem-sucedido!');
            console.log('✅ Teste PNG: OK');
        }
    } catch (error) {
        console.error('❌ Erro no teste PNG:', error);
        alert('❌ Erro no teste PNG: ' + error.message);
    }
    
    console.log('=== TESTE DE EXPORTAÇÃO CONCLUÍDO ===');
}

// ==================== FUNÇÕES AUXILIARES ====================

/**
 * Exporta gráfico Chart.js como PNG
 */
function exportarGraficoPNG(grafico, nomeArquivo) {
    try {
        const canvas = grafico.canvas;
        const url = canvas.toDataURL('image/png');
        downloadFile(url, `${nomeArquivo}.png`);
    } catch (error) {
        console.error('Erro ao exportar PNG:', error);
        throw new Error('Erro ao exportar gráfico como PNG');
    }
}

/**
 * Exporta elemento HTML como PNG usando html2canvas
 */
function exportarElementoPNG(elemento, nomeArquivo) {
    html2canvas(elemento, {
        backgroundColor: '#ffffff',
        scale: 2,
        logging: false,
        allowTaint: true,
        useCORS: true
    }).then(canvas => {
        const url = canvas.toDataURL('image/png');
        downloadFile(url, `${nomeArquivo}.png`);
    }).catch(error => {
        console.error('Erro ao exportar elemento como PNG:', error);
        throw new Error('Erro ao exportar elemento como PNG');
    });
}

/**
 * Exporta dashboard completo como PNG
 */
function exportarDashboardPNG(elemento, nomeArquivo) {
    // Esconder temporariamente o loading se estiver visível
    const loadingElement = document.getElementById('loading-dashboard');
    const wasLoadingVisible = loadingElement && loadingElement.style.display !== 'none';
    if (wasLoadingVisible) {
        loadingElement.style.display = 'none';
    }
    
    html2canvas(elemento, {
        backgroundColor: '#ffffff',
        scale: 1.5, // Qualidade alta mas não tanto para não travar
        logging: false,
        allowTaint: true,
        useCORS: true,
        width: elemento.scrollWidth,
        height: elemento.scrollHeight,
        scrollX: 0,
        scrollY: 0
    }).then(canvas => {
        // Restaurar loading se estava visível
        if (wasLoadingVisible && loadingElement) {
            loadingElement.style.display = 'block';
        }
        
        const url = canvas.toDataURL('image/png');
        downloadFile(url, `${nomeArquivo}.png`);
        
        // Feedback de sucesso
        setTimeout(() => {
            mostrarFeedbackExportacao(false);
            mostrarMensagemSucesso('PNG do dashboard exportado com sucesso!');
        }, 500);
    }).catch(error => {
        // Restaurar loading se estava visível
        if (wasLoadingVisible && loadingElement) {
            loadingElement.style.display = 'block';
        }
        
        console.error('Erro ao exportar dashboard como PNG:', error);
        mostrarFeedbackExportacao(false);
        throw new Error('Erro ao exportar dashboard como PNG');
    });
}

/**
 * Exporta dashboard completo como PDF (usando mesma captura do PNG)
 */
function exportarDashboardPDF(elemento, nomeArquivo) {
    // Esconder temporariamente o loading se estiver visível
    const loadingElement = document.getElementById('loading-dashboard');
    const wasLoadingVisible = loadingElement && loadingElement.style.display !== 'none';
    if (wasLoadingVisible) {
        loadingElement.style.display = 'none';
    }
    
    // Usar EXATAMENTE as mesmas configurações do PNG para garantir resultado idêntico
    html2canvas(elemento, {
        backgroundColor: '#ffffff',
        scale: 1.5, // Mesma escala do PNG para qualidade idêntica
        logging: false,
        allowTaint: true,
        useCORS: true,
        width: elemento.scrollWidth,
        height: elemento.scrollHeight,
        scrollX: 0,
        scrollY: 0
    }).then(canvas => {
        // Restaurar loading se estava visível
        if (wasLoadingVisible && loadingElement) {
            loadingElement.style.display = 'block';
        }
        
        // Usar mesma qualidade de imagem do PNG
        const imgData = canvas.toDataURL('image/png');
        
        // Criar PDF
        const { jsPDF } = window.jspdf;
        
        // Determinar orientação baseada no aspect ratio
        const canvasAspectRatio = canvas.height / canvas.width;
        const isLandscape = canvasAspectRatio < 0.75; // Se muito mais largo que alto, usar paisagem
        
        const pdf = new jsPDF(isLandscape ? 'l' : 'p', 'mm', 'a4');
        
        // Dimensões da página
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();
        
        // Calcular dimensões para preencher melhor a página mantendo proporção
        let imgWidth, imgHeight;
        
        if (canvasAspectRatio > (pdfHeight - 30) / (pdfWidth - 20)) {
            // Imagem muito alta - limitar pela altura
            imgHeight = pdfHeight - 30; // margem de 15mm em cima e embaixo
            imgWidth = imgHeight / canvasAspectRatio;
        } else {
            // Imagem cabe na altura - limitar pela largura
            imgWidth = pdfWidth - 20; // margem de 10mm de cada lado
            imgHeight = imgWidth * canvasAspectRatio;
        }
        
        // Centralizar na página
        const x = (pdfWidth - imgWidth) / 2;
        const y = (pdfHeight - imgHeight) / 2;
        
        // Adicionar a imagem (sem título para deixar mais limpo como o PNG)
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);
        
        // Adicionar apenas a data no canto discretamente
        pdf.setFontSize(7);
        pdf.setFont(undefined, 'normal');
        pdf.setTextColor(150, 150, 150);
        const dataAtual = new Date().toLocaleDateString('pt-BR');
        pdf.text(`Gerado em: ${dataAtual}`, 10, pdfHeight - 3);
        
        // Download do PDF
        pdf.save(`${nomeArquivo}.pdf`);
        
        // Feedback de sucesso
        setTimeout(() => {
            mostrarFeedbackExportacao(false);
            mostrarMensagemSucesso('PDF do dashboard exportado com sucesso!');
        }, 500);
        
    }).catch(error => {
        // Restaurar loading se estava visível
        if (wasLoadingVisible && loadingElement) {
            loadingElement.style.display = 'block';
        }
        
        console.error('Erro ao exportar dashboard como PDF:', error);
        mostrarFeedbackExportacao(false);
        throw new Error('Erro ao exportar dashboard como PDF');
    });
}

/**
 * Exporta dados do gráfico Chart.js como CSV
 */
function exportarGraficoCSV(grafico, nomeGrafico, nomeArquivo) {
    try {
        let csvContent = '';
        const data = grafico.data;
        
        // Headers específicos por tipo de gráfico
        switch(nomeGrafico) {
            case 'evolucao':
                csvContent = 'Data,Total de Notícias\n';
                data.labels.forEach((label, index) => {
                    csvContent += `${label},${data.datasets[0].data[index]}\n`;
                });
                break;
                
            case 'midia':
            case 'sentimento-geral':
                csvContent = 'Categoria,Valor\n';
                data.labels.forEach((label, index) => {
                    csvContent += `${label},${data.datasets[0].data[index]}\n`;
                });
                break;
                
            case 'retorno':
                csvContent = 'Tipo de Mídia,Valor (R$)\n';
                data.labels.forEach((label, index) => {
                    csvContent += `${label},"R$ ${parseFloat(data.datasets[0].data[index]).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}"\n`;
                });
                break;
                
            default:
                csvContent = 'Label,Valor\n';
                data.labels.forEach((label, index) => {
                    csvContent += `${label},${data.datasets[0].data[index]}\n`;
                });
        }
        
        downloadCSV(csvContent, `${nomeArquivo}.csv`);
    } catch (error) {
        console.error('Erro ao exportar CSV:', error);
        throw new Error('Erro ao exportar dados como CSV');
    }
}

/**
 * Exporta dados de elementos HTML como CSV
 */
function exportarElementoCSV(elementId, nomeElemento, nomeArquivo) {
    let csvContent = '';
    
    try {
        switch(nomeElemento) {
            case 'tags':
                csvContent = exportarTagsCSV();
                break;
            case 'sentimento-midia':
                csvContent = exportarSentimentoMidiaCSV();
                break;
            case 'resumo-retorno':
                csvContent = exportarResumoRetornoCSV();
                break;
            case 'ranking-veiculos-retorno':
                csvContent = exportarRankingVeiculosRetornoCSV();
                break;
            case 'top-fontes':
                csvContent = exportarTopFontesCSV();
                break;
            case 'top-areas':
                csvContent = exportarTopAreasCSV();
                break;
            default:
                throw new Error('Exportação CSV não implementada para este elemento');
        }
        
        if (csvContent) {
            downloadCSV(csvContent, `${nomeArquivo}.csv`);
        }
    } catch (error) {
        console.error('Erro ao exportar elemento como CSV:', error);
        throw error;
    }
}

/**
 * Exporta gráfico Chart.js como SVG
 */
function exportarGraficoSVG(grafico, nomeArquivo) {
    try {
        // Para SVG, vamos usar uma abordagem simplificada convertendo o canvas para SVG
        const canvas = grafico.canvas;
        
        // Criar SVG básico com a imagem (sem declaração XML para evitar problemas de parsing)
        const svgContent = '<svg width="' + canvas.width + '" height="' + canvas.height + '" xmlns="http://www.w3.org/2000/svg">' +
            '<image href="' + canvas.toDataURL() + '" width="' + canvas.width + '" height="' + canvas.height + '"/>' +
            '</svg>';
        
        downloadFile('data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgContent), nomeArquivo + '.svg');
    } catch (error) {
        console.error('Erro ao exportar SVG:', error);
        throw new Error('Erro ao exportar gráfico como SVG');
    }
}

// ==================== FUNÇÕES AUXILIARES DE CSV ====================

function exportarTagsCSV() {
    const tags = dadosGlobais.top_tags || [];
    let csv = 'Tag,Quantidade\n';
    tags.forEach(tag => {
        csv += `"${tag.tag}",${tag.total}\n`;
    });
    return csv;
}

function exportarSentimentoMidiaCSV() {
    const sentimentos = dadosGlobais.sentimentos_por_midia || {};
    let csv = 'Tipo de Mídia,Positivo,Neutro,Negativo,Total\n';
    
    Object.keys(sentimentos).forEach(tipo => {
        const dados = sentimentos[tipo];
        const total = dados.positivo + dados.neutro + dados.negativo;
        csv += `${tipo.charAt(0).toUpperCase() + tipo.slice(1)},${dados.positivo},${dados.neutro},${dados.negativo},${total}\n`;
    });
    
    return csv;
}

function exportarResumoRetornoCSV() {
    const retorno = dadosGlobais.retorno_midia || {};
    let csv = 'Tipo de Mídia,Valor (R$)\n';
    
    ['web', 'tv', 'radio', 'impresso'].forEach(tipo => {
        if (retorno[tipo] > 0) {
            csv += `${tipo.charAt(0).toUpperCase() + tipo.slice(1)},"R$ ${parseFloat(retorno[tipo]).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}"\n`;
        }
    });
    
    csv += `\nTotal,"R$ ${parseFloat(retorno.total || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}"\n`;
    
    return csv;
}

function exportarRankingVeiculosRetornoCSV() {
    const ranking = dadosGlobais.ranking_veiculos_retorno || {};
    let csv = 'Posição,Veículo,Tipo de Mídia,Valor (R$),Total de Notícias\n';
    
    ['web', 'tv', 'radio', 'impresso'].forEach(tipo => {
        const veiculos = ranking[tipo] || [];
        veiculos.forEach((veiculo, index) => {
            csv += `${index + 1},"${veiculo.veiculo}",${tipo.charAt(0).toUpperCase() + tipo.slice(1)},"R$ ${parseFloat(veiculo.valor_total).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}",${veiculo.total_noticias}\n`;
        });
    });
    
    return csv;
}

function exportarTopFontesCSV() {
    const fontes = dadosGlobais.top_fontes || {};
    let csv = 'Posição,Fonte,Tipo de Mídia,Total de Notícias\n';
    
    ['web', 'tv', 'radio', 'impresso'].forEach(tipo => {
        const fontesDoTipo = fontes[tipo] || [];
        fontesDoTipo.forEach((fonte, index) => {
            csv += `${index + 1},"${fonte.fonte}",${tipo.charAt(0).toUpperCase() + tipo.slice(1)},${fonte.total}\n`;
        });
    });
    
    return csv;
}

function exportarTopAreasCSV() {
    const areas = dadosGlobais.top_areas || [];
    let csv = 'Posição,Área,Total de Notícias\n';
    areas.forEach((area, index) => {
        csv += `${index + 1},"${area.area}",${area.total}\n`;
    });
    return csv;
}

function exportarPalavrasChaveCSV() {
    const palavrasChave = dadosGlobais.palavras_chave || [];
    let csv = 'Posição,Palavra-Chave,Frequência\n';
    palavrasChave.forEach((palavra, index) => {
        csv += `${index + 1},"${palavra.text}",${palavra.size}\n`;
    });
    return csv;
}

// ==================== FUNÇÕES UTILITÁRIAS ====================

/**
 * Faz download de um arquivo
 */
function downloadFile(url, filename) {
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Faz download de arquivo CSV
 */
function downloadCSV(csvContent, filename) {
    const BOM = '\uFEFF'; // BOM para UTF-8
    const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    downloadFile(url, filename);
    URL.revokeObjectURL(url);
}

// ==================== FUNÇÕES DE FEEDBACK ====================

/**
 * Mostra/esconde feedback visual durante exportação
 */
function mostrarFeedbackExportacao(mostrar) {
    if (mostrar) {
        // Criar elemento de loading se não existir
        let loadingElement = document.getElementById('export-loading-overlay');
        if (!loadingElement) {
            loadingElement = document.createElement('div');
            loadingElement.id = 'export-loading-overlay';
            loadingElement.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                            background: rgba(0,0,0,0.5); z-index: 9999; display: flex; 
                            align-items: center; justify-content: center;">
                    <div style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
                        <i class="fa fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                        <p class="mb-0">Preparando exportação...</p>
                    </div>
                </div>
            `;
            document.body.appendChild(loadingElement);
        }
        loadingElement.style.display = 'block';
    } else {
        const loadingElement = document.getElementById('export-loading-overlay');
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
    }
}

/**
 * Mostra mensagem de sucesso
 */
function mostrarMensagemSucesso(mensagem) {
    // Criar toast de sucesso
    const toast = document.createElement('div');
    toast.className = 'alert alert-success';
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        border: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    toast.innerHTML = `
        <i class="fa fa-check-circle mr-2"></i>
        ${mensagem}
        <button type="button" class="close" onclick="this.parentElement.remove()">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    // Animar entrada
    setTimeout(() => {
        toast.style.opacity = '1';
    }, 100);
    
    // Remover automaticamente após 3 segundos
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            if (toast.parentElement) {
                toast.parentElement.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// ==================== INICIALIZAÇÃO DA DASHBOARD ====================

$(document).ready(function() {
    const host = $('meta[name="base-url"]').attr('content');
    
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
                    renderizarNuvemPalavrasChave();
                    
                    if (flSentimento) {
                        renderizarGraficosEntimento();
                    }
                    
                    if (flRetornoMidia) {
                        renderizarGraficoRetorno();
                        renderizarResumoRetorno();
                        renderizarRankingVeiculosRetorno();
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
        
        // Filtra apenas tipos com valores > 0
        const dados = [];
        const labels = [];
        const cores = [];
        
        if (retorno.web > 0) {
            dados.push(retorno.web);
            labels.push('Web');
            cores.push('#17a2b8');
        }
        if (retorno.tv > 0) {
            dados.push(retorno.tv);
            labels.push('TV');
            cores.push('#dc3545');
        }
        if (retorno.radio > 0) {
            dados.push(retorno.radio);
            labels.push('Rádio');
            cores.push('#28a745');
        }
        if (retorno.impresso > 0) {
            dados.push(retorno.impresso);
            labels.push('Impresso');
            cores.push('#ffc107');
        }
        
        if (dados.length === 0) {
            $('#grafico-retorno').parent().html('<p class="text-center text-muted">Nenhum dado disponível</p>');
            return;
        }
        
        graficos.retorno = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Valor (R$)',
                    data: dados,
                    backgroundColor: cores,
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
                                return 'R$ ' + value.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                }
            }
        });
    }
    
    function renderizarResumoRetorno() {
        const retorno = dadosGlobais.retorno_midia || {};
        const total = retorno.total || 0;
        
        let html = '';
        
        if (total === 0) {
            html = '<p class="text-center text-muted">Nenhum valor de retorno disponível</p>';
        } else {
            html = `
                <div class="text-center mb-3">
                    <h4 class="text-success font-weight-bold mb-1">
                        R$ ${parseFloat(total).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}
                    </h4>
                    <small class="text-muted">Total Geral</small>
                </div>
                <hr class="my-3">
            `;
            
            // Lista detalhada por tipo
            if (retorno.web > 0) {
                html += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="fa fa-globe text-info mr-2"></i>Web</span>
                        <strong class="text-success">R$ ${parseFloat(retorno.web).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}</strong>
                    </div>
                `;
            }
            
            if (retorno.tv > 0) {
                html += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="fa fa-television text-danger mr-2"></i>TV</span>
                        <strong class="text-success">R$ ${parseFloat(retorno.tv).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}</strong>
                    </div>
                `;
            }
            
            if (retorno.radio > 0) {
                html += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="fa fa-volume-up text-success mr-2"></i>Rádio</span>
                        <strong class="text-success">R$ ${parseFloat(retorno.radio).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}</strong>
                    </div>
                `;
            }
            
            if (retorno.impresso > 0) {
                html += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="fa fa-newspaper-o text-warning mr-2"></i>Impresso</span>
                        <strong class="text-success">R$ ${parseFloat(retorno.impresso).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}</strong>
                    </div>
                `;
            }
        }
        
        $('#resumo-retorno-container').html(html);
    }
    
    function renderizarRankingVeiculosRetorno() {
        const rankingRetorno = dadosGlobais.ranking_veiculos_retorno || {};
        
        // Renderiza ranking para cada tipo de mídia
        ['web', 'impresso', 'radio', 'tv'].forEach(function(tipo) {
            const container = `#ranking-${tipo}-retorno-container`;
            const veiculosDoTipo = rankingRetorno[tipo] || [];
            let html = '';
            
            if (veiculosDoTipo.length === 0) {
                html = '<p class="text-center text-muted">Nenhum dado disponível</p>';
            } else {
                html = '<div class="list-group list-group-flush">';
                veiculosDoTipo.forEach(function(veiculo, index) {
                    const posicao = index + 1;
                    const badgeClass = 'badge-success'; // Todas as badges verdes
                    
                    const valorFormatado = parseFloat(veiculo.valor_total).toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    html += `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge ${badgeClass} mr-2">${posicao}º</span>
                                <strong>${veiculo.veiculo}</strong>
                                <small class="text-muted ml-2">(${veiculo.total_noticias} notícias)</small>
                            </div>
                            <span class="badge badge-success font-weight-bold">R$ ${valorFormatado}</span>
                        </div>
                    `;
                });
                html += '</div>';
            }
            
            $(container).html(html);
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
    
    function renderizarNuvemPalavrasChave() {
        const palavrasChave = dadosGlobais.palavras_chave || [];
        const container = document.getElementById('wordcloud-container');
        
        console.log('Palavras-chave recebidas:', palavrasChave);
        
        if (palavrasChave.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-5"><i class="fa fa-info-circle fa-2x mb-3"></i><br>Nenhuma palavra frequente encontrada para o período selecionado.<br><small>Tente um período maior ou verifique se há notícias no período.</small></div>';
            return;
        }
        
        // Limpar container
        container.innerHTML = '';
        
        // Verificar se WordCloud está disponível
        if (typeof WordCloud === 'undefined') {
            console.warn('WordCloud2.js não carregado, usando fallback HTML');
            renderizarNuvemSimples(palavrasChave, container);
            return;
        }
        
        // Criar canvas para a nuvem de palavras
        const canvas = document.createElement('canvas');
        canvas.width = container.offsetWidth || 800;
        canvas.height = 400;
        canvas.style.width = '100%';
        canvas.style.height = '400px';
        container.appendChild(canvas);
        
        // Preparar dados para WordCloud2 no formato correto: [[text, weight], ...]
        const wordList = palavrasChave.map(item => [item.text, parseInt(item.size)]);
        
        console.log('Lista de palavras formatada:', wordList);
        
        // Aguardar um momento para o canvas ser renderizado
        setTimeout(() => {
            try {
                WordCloud(canvas, {
                    list: wordList,
                    gridSize: 8,
                    weightFactor: function (size) {
                        // Normaliza o tamanho das palavras de forma mais segura
                        const maxSize = Math.max(...palavrasChave.map(p => parseInt(p.size)));
                        const minSize = Math.min(...palavrasChave.map(p => parseInt(p.size)));
                        
                        if (maxSize === minSize || maxSize === 0) {
                            return 20; // Tamanho padrão se todos têm o mesmo valor
                        }
                        
                        const normalizedSize = (size - minSize) / (maxSize - minSize);
                        return Math.max(12, normalizedSize * 48 + 12); // Entre 12 e 60px
                    },
                    fontFamily: 'Arial, sans-serif',
                    color: function (word, weight, fontSize, distance, theta) {
                        // Cores fixas baseadas no índice da palavra
                        const colors = [
                            '#007bff', '#28a745', '#dc3545', '#ffc107', 
                            '#17a2b8', '#6f42c1', '#fd7e14', '#20c997',
                            '#e83e8c', '#6c757d'
                        ];
                        const index = wordList.findIndex(item => item[0] === word);
                        return colors[index % colors.length];
                    },
                    rotateRatio: 0.1, // Menos rotação para melhor legibilidade
                    backgroundColor: 'transparent',
                    minFontSize: 12,
                    drawOutOfBound: false,
                    shrinkToFit: true,
                    hover: function(item, dimension, event) {
                        if (item) {
                            canvas.style.cursor = 'pointer';
                            canvas.title = `"${item[0]}" aparece ${item[1]} vez(es)`;
                        } else {
                            canvas.style.cursor = 'default';
                            canvas.title = '';
                        }
                    },
                    click: function(item) {
                        if (item) {
                            console.log('Palavra clicada:', item[0], 'Frequência:', item[1]);
                        }
                    }
                });
                
                console.log(`✅ Nuvem de palavras renderizada com ${palavrasChave.length} palavras`);
                
            } catch (error) {
                console.error('Erro ao renderizar nuvem de palavras:', error);
                console.warn('Usando fallback HTML devido ao erro');
                container.innerHTML = '';
                renderizarNuvemSimples(palavrasChave, container);
            }
        }, 100);
    }
    
    /**
     * Renderiza nuvem de palavras simples usando HTML/CSS como fallback
     */
    function renderizarNuvemSimples(palavrasChave, container) {
        const maxSize = Math.max(...palavrasChave.map(p => parseInt(p.size)));
        const minSize = Math.min(...palavrasChave.map(p => parseInt(p.size)));
        
        let html = '<div class="wordcloud-simple" style="text-align: center; line-height: 2.5; padding: 20px;">';
        
        const colors = [
            '#007bff', '#28a745', '#dc3545', '#ffc107', 
            '#17a2b8', '#6f42c1', '#fd7e14', '#20c997',
            '#e83e8c', '#6c757d'
        ];
        
        palavrasChave.forEach((palavra, index) => {
            const size = parseInt(palavra.size);
            let fontSize = 16;
            
            if (maxSize !== minSize) {
                const normalizedSize = (size - minSize) / (maxSize - minSize);
                fontSize = Math.max(12, normalizedSize * 32 + 12); // Entre 12 e 44px
            }
            
            const color = colors[index % colors.length];
            
            html += `
                <span class="wordcloud-word" 
                      style="display: inline-block; margin: 8px; padding: 6px 12px; 
                             font-size: ${fontSize}px; font-weight: bold; color: ${color};
                             background: ${color}15; border-radius: 20px; cursor: pointer;
                             transition: transform 0.2s ease, box-shadow 0.2s ease;"
                      title="${palavra.text}: ${size} ocorrências"
                      onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)';"
                      onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    ${palavra.text}
                    <small style="opacity: 0.7; font-size: 0.8em;"> (${size})</small>
                </span>
            `;
        });
        
        html += '</div>';
        html += '<p class="text-center text-muted mt-3"><small><i class="fa fa-info-circle"></i> Nuvem de palavras em modo compatibilidade</small></p>';
        
        container.innerHTML = html;
        console.log(`✅ Nuvem de palavras simples renderizada com ${palavrasChave.length} palavras`);
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
        console.log('Tentando exportar gráfico:', nomeGrafico, 'formato:', formato);
        
        const grafico = graficos[nomeGrafico];
        if (!grafico) {
            console.error('Gráfico não encontrado:', nomeGrafico, 'Gráficos disponíveis:', Object.keys(graficos));
            alert('Gráfico não encontrado ou ainda não foi carregado: ' + nomeGrafico);
            return;
        }
        
        console.log('Gráfico encontrado:', grafico);
        const nomeArquivo = `grafico-${nomeGrafico}-${new Date().toISOString().split('T')[0]}`;
        
        // Mostrar feedback visual
        mostrarFeedbackExportacao(true);
        
        try {
            switch(formato) {
                case 'png':
                    exportarGraficoPNG(grafico, nomeArquivo);
                    break;
                case 'csv':
                    exportarGraficoCSV(grafico, nomeGrafico, nomeArquivo);
                    break;
                case 'svg':
                    exportarGraficoSVG(grafico, nomeArquivo);
                    break;
                default:
                    throw new Error('Formato não suportado: ' + formato);
            }
            
            // Feedback de sucesso
            setTimeout(() => {
                mostrarFeedbackExportacao(false);
                mostrarMensagemSucesso(`${formato.toUpperCase()} exportado com sucesso!`);
            }, 500);
            
        } catch (error) {
            console.error('Erro durante exportação:', error);
            mostrarFeedbackExportacao(false);
            alert('Erro ao exportar: ' + error.message);
        }
    }
    
    /**
     * Função para exportar elementos HTML (não Chart.js)
     */
    function exportarElemento(elementId, nomeElemento, formato) {
        console.log('Tentando exportar elemento:', elementId, 'nome:', nomeElemento, 'formato:', formato);
        
        const elemento = document.getElementById(elementId);
        if (!elemento) {
            console.error('Elemento não encontrado:', elementId);
            alert('Elemento não encontrado: ' + elementId);
            return;
        }
        
        console.log('Elemento encontrado:', elemento);
        const nomeArquivo = `${nomeElemento}-${new Date().toISOString().split('T')[0]}`;
        
        // Mostrar feedback visual
        mostrarFeedbackExportacao(true);
        
        try {
            switch(formato) {
                case 'png':
                    exportarElementoPNG(elemento, nomeArquivo);
                    break;
                case 'csv':
                    exportarElementoCSV(elementId, nomeElemento, nomeArquivo);
                    break;
                default:
                    throw new Error('Formato não suportado para este elemento: ' + formato);
            }
            
            // Feedback de sucesso
            setTimeout(() => {
                mostrarFeedbackExportacao(false);
                mostrarMensagemSucesso(`${formato.toUpperCase()} exportado com sucesso!`);
            }, 500);
            
        } catch (error) {
            console.error('Erro durante exportação:', error);
            mostrarFeedbackExportacao(false);
            alert('Erro ao exportar: ' + error.message);
        }
    }
    
    /**
     * Exporta gráfico Chart.js como PNG
     */
    function exportarGraficoPNG(grafico, nomeArquivo) {
        try {
            const canvas = grafico.canvas;
            const url = canvas.toDataURL('image/png');
            downloadFile(url, `${nomeArquivo}.png`);
        } catch (error) {
            console.error('Erro ao exportar PNG:', error);
            alert('Erro ao exportar gráfico como PNG.');
        }
    }
    
    /**
     * Exporta elemento HTML como PNG usando html2canvas
     */
    function exportarElementoPNG(elemento, nomeArquivo) {
        // Mostra loading
        const loadingHtml = '<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Preparando exportação...</div>';
        const originalHtml = elemento.innerHTML;
        
        html2canvas(elemento, {
            backgroundColor: '#ffffff',
            scale: 2,
            logging: false,
            allowTaint: true,
            useCORS: true
        }).then(canvas => {
            const url = canvas.toDataURL('image/png');
            downloadFile(url, `${nomeArquivo}.png`);
        }).catch(error => {
            console.error('Erro ao exportar elemento como PNG:', error);
            alert('Erro ao exportar elemento como PNG.');
        });
    }
    
    /**
     * Exporta dados do gráfico Chart.js como CSV
     */
    function exportarGraficoCSV(grafico, nomeGrafico, nomeArquivo) {
        try {
            let csvContent = '';
            const data = grafico.data;
            
            // Headers específicos por tipo de gráfico
            switch(nomeGrafico) {
                case 'evolucao':
                    csvContent = 'Data,Total de Notícias\n';
                    data.labels.forEach((label, index) => {
                        csvContent += `${label},${data.datasets[0].data[index]}\n`;
                    });
                    break;
                    
                case 'midia':
                case 'sentimento-geral':
                    csvContent = 'Categoria,Valor\n';
                    data.labels.forEach((label, index) => {
                        csvContent += `${label},${data.datasets[0].data[index]}\n`;
                    });
                    break;
                    
                case 'retorno':
                    csvContent = 'Tipo de Mídia,Valor (R$)\n';
                    data.labels.forEach((label, index) => {
                        csvContent += `${label},"R$ ${parseFloat(data.datasets[0].data[index]).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}"\n`;
                    });
                    break;
                    
                default:
                    csvContent = 'Label,Valor\n';
                    data.labels.forEach((label, index) => {
                        csvContent += `${label},${data.datasets[0].data[index]}\n`;
                    });
            }
            
            downloadCSV(csvContent, `${nomeArquivo}.csv`);
        } catch (error) {
            console.error('Erro ao exportar CSV:', error);
            alert('Erro ao exportar dados como CSV.');
        }
    }
    
    /**
     * Exporta dados de elementos HTML como CSV
     */
    function exportarElementoCSV(elementId, nomeElemento, nomeArquivo) {
        let csvContent = '';
        
        try {
            switch(nomeElemento) {
                case 'tags':
                    csvContent = exportarTagsCSV();
                    break;
                case 'sentimento-midia':
                    csvContent = exportarSentimentoMidiaCSV();
                    break;
                case 'resumo-retorno':
                    csvContent = exportarResumoRetornoCSV();
                    break;
                case 'ranking-veiculos-retorno':
                    csvContent = exportarRankingVeiculosRetornoCSV();
                    break;
                case 'top-fontes':
                    csvContent = exportarTopFontesCSV();
                    break;
                case 'top-areas':
                    csvContent = exportarTopAreasCSV();
                    break;
                case 'palavras-chave':
                    csvContent = exportarPalavrasChaveCSV();
                    break;
                default:
                    alert('Exportação CSV não implementada para este elemento.');
                    return;
            }
            
            if (csvContent) {
                downloadCSV(csvContent, `${nomeArquivo}.csv`);
            }
        } catch (error) {
            console.error('Erro ao exportar elemento como CSV:', error);
            alert('Erro ao exportar dados como CSV.');
        }
    }
    
    /**
     * Exporta gráfico Chart.js como SVG
     */
    function exportarGraficoSVG(grafico, nomeArquivo) {
        try {
            // Para SVG, vamos usar uma abordagem simplificada convertendo o canvas para SVG
            const canvas = grafico.canvas;
            
            // Criar SVG básico com a imagem (sem declaração XML para evitar problemas de parsing)
            const svgContent = '<svg width="' + canvas.width + '" height="' + canvas.height + '" xmlns="http://www.w3.org/2000/svg">' +
                '<image href="' + canvas.toDataURL() + '" width="' + canvas.width + '" height="' + canvas.height + '"/>' +
                '</svg>';
            
            downloadFile('data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgContent), nomeArquivo + '.svg');
        } catch (error) {
            console.error('Erro ao exportar SVG:', error);
            alert('Erro ao exportar gráfico como SVG.');
        }
    }
    
    // ==================== FUNÇÕES AUXILIARES DE CSV ====================
    
    function exportarTagsCSV() {
        const tags = dadosGlobais.top_tags || [];
        let csv = 'Tag,Quantidade\n';
        tags.forEach(tag => {
            csv += `"${tag.tag}",${tag.total}\n`;
        });
        return csv;
    }
    
    function exportarSentimentoMidiaCSV() {
        const sentimentos = dadosGlobais.sentimentos_por_midia || {};
        let csv = 'Tipo de Mídia,Positivo,Neutro,Negativo,Total\n';
        
        Object.keys(sentimentos).forEach(tipo => {
            const dados = sentimentos[tipo];
            const total = dados.positivo + dados.neutro + dados.negativo;
            csv += `${tipo.charAt(0).toUpperCase() + tipo.slice(1)},${dados.positivo},${dados.neutro},${dados.negativo},${total}\n`;
        });
        
        return csv;
    }
    
    function exportarResumoRetornoCSV() {
        const retorno = dadosGlobais.retorno_midia || {};
        let csv = 'Tipo de Mídia,Valor (R$)\n';
        
        ['web', 'tv', 'radio', 'impresso'].forEach(tipo => {
            if (retorno[tipo] > 0) {
                csv += `${tipo.charAt(0).toUpperCase() + tipo.slice(1)},"R$ ${parseFloat(retorno[tipo]).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}"\n`;
            }
        });
        
        csv += `\nTotal,"R$ ${parseFloat(retorno.total || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}"\n`;
        
        return csv;
    }
    
    function exportarRankingVeiculosRetornoCSV() {
        const ranking = dadosGlobais.ranking_veiculos_retorno || {};
        let csv = 'Posição,Veículo,Tipo de Mídia,Valor (R$),Total de Notícias\n';
        
        ['web', 'tv', 'radio', 'impresso'].forEach(tipo => {
            const veiculos = ranking[tipo] || [];
            veiculos.forEach((veiculo, index) => {
                csv += `${index + 1},"${veiculo.veiculo}",${tipo.charAt(0).toUpperCase() + tipo.slice(1)},"R$ ${parseFloat(veiculo.valor_total).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}",${veiculo.total_noticias}\n`;
            });
        });
        
        return csv;
    }
    
    function exportarTopFontesCSV() {
        const fontes = dadosGlobais.top_fontes || {};
        let csv = 'Posição,Fonte,Tipo de Mídia,Total de Notícias\n';
        
        ['web', 'tv', 'radio', 'impresso'].forEach(tipo => {
            const fontesDoTipo = fontes[tipo] || [];
            fontesDoTipo.forEach((fonte, index) => {
                csv += `${index + 1},"${fonte.fonte}",${tipo.charAt(0).toUpperCase() + tipo.slice(1)},${fonte.total}\n`;
            });
        });
        
        return csv;
    }
    
    function exportarTopAreasCSV() {
        const areas = dadosGlobais.top_areas || [];
        let csv = 'Posição,Área,Total de Notícias\n';
        areas.forEach((area, index) => {
            csv += `${index + 1},"${area.area}",${area.total}\n`;
        });
        return csv;
    }
    
    // ==================== FUNÇÕES UTILITÁRIAS ====================
    
    /**
     * Faz download de um arquivo
     */
    function downloadFile(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    /**
     * Faz download de arquivo CSV
     */
    function downloadCSV(csvContent, filename) {
        const BOM = '\uFEFF'; // BOM para UTF-8
        const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        downloadFile(url, filename);
        URL.revokeObjectURL(url);
    }
    
    // ==================== FUNÇÕES DE FEEDBACK ====================
    
    /**
     * Mostra/esconde feedback visual durante exportação
     */
    function mostrarFeedbackExportacao(mostrar) {
        if (mostrar) {
            // Criar elemento de loading se não existir
            let loadingElement = document.getElementById('export-loading-overlay');
            if (!loadingElement) {
                loadingElement = document.createElement('div');
                loadingElement.id = 'export-loading-overlay';
                loadingElement.innerHTML = `
                    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                                background: rgba(0,0,0,0.5); z-index: 9999; display: flex; 
                                align-items: center; justify-content: center;">
                        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
                            <i class="fa fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                            <p class="mb-0">Preparando exportação...</p>
                        </div>
                    </div>
                `;
                document.body.appendChild(loadingElement);
            }
            loadingElement.style.display = 'block';
        } else {
            const loadingElement = document.getElementById('export-loading-overlay');
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
        }
    }
    
    /**
     * Mostra mensagem de sucesso
     */
    function mostrarMensagemSucesso(mensagem) {
        // Criar toast de sucesso
        const toast = document.createElement('div');
        toast.className = 'alert alert-success';
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            min-width: 300px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        toast.innerHTML = `
            <i class="fa fa-check-circle mr-2"></i>
            ${mensagem}
            <button type="button" class="close" onclick="this.parentElement.remove()">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        
        document.body.appendChild(toast);
        
        // Animar entrada
        setTimeout(() => {
            toast.style.opacity = '1';
        }, 100);
        
        // Remover automaticamente após 3 segundos
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.parentElement.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
    
    // ==================== FUNÇÃO DE TESTE ====================
    
    /**
     * Função para testar se as exportações estão funcionando
     */
    function testarExportacao() {
        console.log('=== INICIANDO TESTE DE EXPORTAÇÃO ===');
        console.log('Dados globais disponíveis:', dadosGlobais);
        console.log('Gráficos disponíveis:', Object.keys(graficos));
        
        // Testar se dadosGlobais está carregado
        if (!dadosGlobais || Object.keys(dadosGlobais).length === 0) {
            alert('⚠️ Dados não carregados ainda. Aguarde o carregamento da dashboard.');
            return;
        }
        
        // Testar se os gráficos estão disponíveis
        if (!graficos || Object.keys(graficos).length === 0) {
            alert('⚠️ Gráficos não carregados ainda. Aguarde o carregamento da dashboard.');
            return;
        }
        
        // Testar exportação CSV simples
        try {
            const tags = dadosGlobais.top_tags || [];
            if (tags.length > 0) {
                let csvContent = 'Tag,Quantidade\n';
                tags.slice(0, 3).forEach(tag => {
                    csvContent += `"${tag.tag}",${tag.total}\n`;
                });
                
                const blob = new Blob([csvContent], { type: 'text/csv' });
                const url = URL.createObjectURL(blob);
                downloadFile(url, 'teste-tags.csv');
                URL.revokeObjectURL(url);
                
                mostrarMensagemSucesso('✅ Teste de CSV bem-sucedido!');
                console.log('✅ Teste CSV: OK');
            }
        } catch (error) {
            console.error('❌ Erro no teste CSV:', error);
            alert('❌ Erro no teste CSV: ' + error.message);
        }
        
        // Testar exportação PNG de gráfico
        try {
            const primeiroGrafico = Object.keys(graficos)[0];
            if (primeiroGrafico && graficos[primeiroGrafico]) {
                const canvas = graficos[primeiroGrafico].canvas;
                const url = canvas.toDataURL('image/png');
                downloadFile(url, 'teste-grafico.png');
                
                mostrarMensagemSucesso('✅ Teste de PNG bem-sucedido!');
                console.log('✅ Teste PNG: OK');
            }
        } catch (error) {
            console.error('❌ Erro no teste PNG:', error);
            alert('❌ Erro no teste PNG: ' + error.message);
        }
        
        console.log('=== TESTE DE EXPORTAÇÃO CONCLUÍDO ===');
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

/* Estilos para botões de exportação */
.btn-export {
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    min-width: 50px;
    border-radius: 4px;
    opacity: 0.8;
    transition: all 0.2s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-export:hover {
    opacity: 1;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-export i {
    font-size: 10px;
}

.btn-export.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.btn-export.btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

.btn-export.btn-outline-info:hover {
    background-color: #17a2b8;
    border-color: #17a2b8;
    color: white;
}

/* Responsividade para botões de exportação */
@media (max-width: 768px) {
    .btn-export {
        padding: 3px 8px;
        font-size: 10px;
        min-width: 45px;
        margin-bottom: 2px;
    }
    
    .btn-export i {
        font-size: 9px;
    }
    
    .card-header .btn-group {
        flex-wrap: wrap;
        gap: 3px;
    }
    
    .card-header.d-flex {
        flex-direction: column;
        align-items: stretch;
    }
    
    .card-header .btn-group {
        margin-top: 8px;
        justify-content: center;
    }
    
    /* Responsividade para header do dashboard */
    .d-flex.justify-content-end {
        flex-direction: column;
        gap: 10px !important;
        align-items: stretch !important;
    }
    
    .btn-group .btn {
        font-size: 11px;
        padding: 5px 8px;
    }
}

/* Estilos específicos para os botões de exportação completa */
.btn-success.btn-sm {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.btn-success.btn-sm:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(40, 167, 69, 0.3);
}

.btn-outline-success.btn-sm {
    border-color: #28a745;
    color: #28a745;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.btn-outline-success.btn-sm:hover {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(40, 167, 69, 0.3);
}

/* Separação visual entre os grupos de botões */
.d-flex.justify-content-end > .btn-group:first-child {
    border-right: 2px solid #dee2e6;
    padding-right: 10px;
    margin-right: 10px;
}

/* Animação de loading para exportação */
.export-loading {
    position: relative;
}

.export-loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.export-loading::before {
    content: '\f110';
    font-family: FontAwesome;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 24px;
    color: #007bff;
    animation: fa-spin 1s infinite linear;
    z-index: 1001;
}
</style>
@endsection
