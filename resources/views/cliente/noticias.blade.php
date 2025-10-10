@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o ml-3"></i> Notícias
                    </h4>
                </div>
                <div class="col-md-4">
                    
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                {!! Form::open(['id' => 'frm_user_create', 'url' => ['relatorios']]) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="btn-group" role="group" id="presetsData">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="hoje">Hoje</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="ontem">Ontem</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="7dias">Últimos 7 dias</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="30dias">Últimos 30 dias</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="mes">Este mês</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="mesanterior">Mês anterior</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo de Filtro de Data</label>
                                <select class="form-control" name="tipo_filtro_data" id="tipo_filtro_data">
                                    <option value="coleta" selected>🗄️ Data de Coleta (quando foi coletada pelo sistema)</option>
                                    <option value="clipagem">📅 Data de Clipagem (quando foi publicada/veiculada)</option>
                                </select>
                                <small class="form-text text-muted">
                                    Escolha se deseja filtrar pela data em que a notícia foi coletada pelo sistema ou pela data de publicação original.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Data Inicial</label>
                                <input type="text" class="form-control datepicker" name="dt_inicial" id="dt_inicial" placeholder="__/__/____" value="{{ ($dt_inicial) ? \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Data Final</label>
                                <input type="text" class="form-control datepicker" name="dt_final" id="dt_final" placeholder="__/__/____" value="{{ ($dt_final) ? \Carbon\Carbon::parse($dt_final)->format('d/m/Y') : '' }}">
                            </div>
                        </div>                        
                    
                        @role('administradores')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <select class="form-control cliente" name="id_cliente" id="id_cliente">
                                        <option value="">Selecione um cliente</option>
                                        @foreach($clientes as $cli)
                                            <option value="{{ $cli->id }}">{{ $cli->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endrole

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Termo de busca</label>
                                <input type="text" class="form-control" name="termo" id="termo" placeholder="Termo" value="{{ old('termo') }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Filtrar por Tags 
                                    <button type="button" class="btn btn-sm btn-outline-secondary ml-2" id="btnRecarregarTags" title="Recarregar lista de tags">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning ml-1" id="btnLimparTags" title="Limpar tags selecionadas">
                                        <i class="fa fa-eraser"></i>
                                    </button>
                                </label>
                                <div id="tags-filtro-container" class="border rounded p-3" style="min-height: 60px; background-color: #f8f9fa;">
                                    <div class="text-muted text-center">
                                        <i class="fa fa-spinner fa-spin"></i> Carregando tags...
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    Clique nas tags para filtrar as notícias. Tags selecionadas ficam em azul.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Filtrar por Fonte/Emissora/Programa 
                                    <button type="button" class="btn btn-sm btn-outline-secondary ml-2" id="btnRecarregarFontes" title="Recarregar lista de fontes">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning ml-1" id="btnLimparFontes" title="Limpar fontes selecionadas">
                                        <i class="fa fa-eraser"></i>
                                    </button>
                                </label>
                                
                                <!-- Abas para diferentes tipos de mídia -->
                                <div class="card">
                                    <div class="card-header p-0">
                                        <ul class="nav nav-tabs nav-fill fontes-tabs" id="fontesTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="web-fontes-tab" data-toggle="tab" href="#web-fontes" role="tab" aria-controls="web-fontes" aria-selected="true">
                                                    <i class="fa fa-globe text-primary"></i> Fontes Web
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="impresso-fontes-tab" data-toggle="tab" href="#impresso-fontes" role="tab" aria-controls="impresso-fontes" aria-selected="false">
                                                    <i class="fa fa-newspaper-o text-warning"></i> Jornais
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="tv-fontes-tab" data-toggle="tab" href="#tv-fontes" role="tab" aria-controls="tv-fontes" aria-selected="false">
                                                    <i class="fa fa-television text-danger"></i> TV (Emissoras/Programas)
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="radio-fontes-tab" data-toggle="tab" href="#radio-fontes" role="tab" aria-controls="radio-fontes" aria-selected="false">
                                                    <i class="fa fa-volume-up text-success"></i> Rádio (Emissoras/Programas)
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="fontesTabContent">
                                            <div class="tab-pane fade show active" id="web-fontes" role="tabpanel" aria-labelledby="web-fontes-tab">
                                                <div class="mb-2">
                                                    <input type="text" class="form-control form-control-sm fonte-search" 
                                                           placeholder="🔍 Buscar fonte web..." 
                                                           data-target="fontes-web-container"
                                                           onkeyup="filtrarFontes(this)">
                                                </div>
                                                <div id="fontes-web-container" class="border rounded p-3" style="min-height: 120px; max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                                                    <div class="text-muted text-center">
                                                        <i class="fa fa-spinner fa-spin"></i> Carregando fontes web...
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="impresso-fontes" role="tabpanel" aria-labelledby="impresso-fontes-tab">
                                                <div class="mb-2">
                                                    <input type="text" class="form-control form-control-sm fonte-search" 
                                                           placeholder="🔍 Buscar jornal..." 
                                                           data-target="fontes-impresso-container"
                                                           onkeyup="filtrarFontes(this)">
                                                </div>
                                                <div id="fontes-impresso-container" class="border rounded p-3" style="min-height: 120px; max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                                                    <div class="text-muted text-center">
                                                        <i class="fa fa-spinner fa-spin"></i> Carregando jornais...
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tv-fontes" role="tabpanel" aria-labelledby="tv-fontes-tab">
                                                <div class="mb-2">
                                                    <input type="text" class="form-control form-control-sm fonte-search" 
                                                           placeholder="🔍 Buscar emissora ou programa de TV..." 
                                                           data-target="fontes-tv-container"
                                                           onkeyup="filtrarFontes(this)">
                                                </div>
                                                <div id="fontes-tv-container" class="border rounded p-3" style="min-height: 120px; max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                                                    <div class="text-muted text-center">
                                                        <i class="fa fa-spinner fa-spin"></i> Carregando emissoras e programas de TV...
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="radio-fontes" role="tabpanel" aria-labelledby="radio-fontes-tab">
                                                <div class="mb-2">
                                                    <input type="text" class="form-control form-control-sm fonte-search" 
                                                           placeholder="🔍 Buscar emissora ou programa de rádio..." 
                                                           data-target="fontes-radio-container"
                                                           onkeyup="filtrarFontes(this)">
                                                </div>
                                                <div id="fontes-radio-container" class="border rounded p-3" style="min-height: 120px; max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                                                    <div class="text-muted text-center">
                                                        <i class="fa fa-spinner fa-spin"></i> Carregando emissoras e programas de rádio...
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <small class="form-text text-muted">
                                    Use o campo de busca 🔍 para encontrar fontes específicas. Clique nas fontes/emissoras/programas para filtrar as notícias. Itens selecionados ficam em azul. O número entre parênteses indica quantas notícias existem da fonte.
                                </small>
                            </div>
                        </div>

                        @if($fl_areas)
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Áreas do Cliente</label>
                                <div id="areas-checkbox-group" class="d-flex flex-wrap" style="gap: 15px;">
            
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($fl_sentimento)
                        <div class="col-md-12">
                            <label class="form-label fw-semibold mb-2">
                                Sentimento
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <label class="form-check-label check-midia">
                                        <input class="form-check-input" type="checkbox" name="sentimento[]" value="1" id="sentimento_positivo" checked>
                                        <span class="form-check-sign"></span>
                                        <span class="text-success"><i class="fa fa-smile-o text-success"></i> Positivo</span>
                                    </label>
                                </div>
                                <div class="form-check ml-3">
                                    <label class="form-check-label check-midia">
                                        <input class="form-check-input" type="checkbox" name="sentimento[]" value="-1" id="sentimento_negativo" checked>
                                        <span class="form-check-sign"></span>
                                        <span class="text-danger"><i class="fa fa-frown-o text-danger"></i> Negativo</span>
                                    </label>
                                </div>
                                <div class="form-check ml-3">
                                    <label class="form-check-label check-midia">
                                        <input class="form-check-input" type="checkbox" name="sentimento[]" value="0" id="sentimento_neutro" checked>
                                        <span class="form-check-sign"></span>
                                        <span class="text-warning"><i class="fa fa-ban text-warning"></i> Neutro</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($fl_sentimento)
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-semibold mb-2">
                                    Incluir sentimento no relatório
                                </label>
                                <div class="form-check">
                                    <label class="form-check-label check-midia">
                                        <input class="form-check-input" type="checkbox" name="mostrar_sentimento_relatorio" id="mostrar_sentimento_relatorio" checked value="true">
                                        <span class="form-check-sign"></span>
                                        <span class="text-info"><i class="fa fa-smile-o text-info"></i> Incluir análise de sentimento</span>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Desmarque esta opção se não quiser incluir a análise de sentimento no relatório PDF.
                                </small>
                            </div>
                        </div>
                        @endif
                        @if($fl_retorno_midia)
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-semibold mb-2">
                                    Incluir retorno de mídia no relatório
                                </label>
                                <div class="form-check">
                                    <label class="form-check-label check-midia">
                                        <input class="form-check-input" type="checkbox" name="mostrar_retorno_relatorio" id="mostrar_retorno_relatorio" checked value="true">
                                        <span class="form-check-sign"></span>
                                        <span class="text-info"><i class="fa fa-money text-info"></i> Incluir valores de retorno de mídia</span>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Desmarque esta opção se não quiser incluir os valores de retorno de mídia no relatório PDF.
                                </small>
                            </div>
                        </div>
                        @endif
                    </div>  
                    
                    @if($cliente)
                        <div class="row">
                            <div class="col-md-12 mt-2">
                                <label class="form-label fw-semibold mb-2">
                                    Clipagem por tipo de mídia
                                </label>                            
                                @if($fl_impresso || $fl_web || $fl_radio || $fl_tv)
                                    <div class="d-flex flex-wrap gap-2">
                                        @if($fl_impresso)
                                            <div class="form-check">
                                                <div class="form-check">
                                                    <label class="form-check-label check-midia">
                                                        <input class="form-check-input" type="checkbox" name="fl_impresso" checked value="true">
                                                        <span class="form-check-sign"></span>
                                                        <span class="text-secondary"><i class="fa fa-newspaper-o"></i> Impressos</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                
                                        @if($fl_web)
                                            <div class="form-check ml-3">
                                                <div class="form-check">
                                                    <label class="form-check-label check-midia">
                                                        <input class="form-check-input" type="checkbox" name="fl_web" checked value="true">
                                                        <span class="form-check-sign"></span>
                                                        <span class="text-secondary"><i class="fa fa-globe"></i> Web</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                
                                        @if($fl_radio)
                                            <div class="form-check ml-3">
                                                <div class="form-check">
                                                    <label class="form-check-label check-midia">
                                                        <input class="form-check-input" type="checkbox" name="fl_radio" checked value="true">
                                                        <span class="form-check-sign"></span>
                                                        <span class="text-secondary"><i class="fa fa-volume-up"></i> Rádio</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                
                                        @if($fl_tv)
                                            <div class="form-check ml-3">
                                                <div class="form-check">
                                                    <label class="form-check-label check-midia">
                                                        <input class="form-check-input" type="checkbox" name="fl_tv" checked value="true">
                                                        <span class="form-check-sign"></span>
                                                        <span class="text-secondary"><i class="fa fa-television"></i> TV</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>Atenção:</strong> Nenhum tipo de mídia está habilitado para seu cliente. Entre em contato com o administrador para configurar os tipos de mídia disponíveis.
                                    </div>
                                @endif
                            </div>        
                        </div>       
                    @else
                        <div class="col-md-12 mt-2" id="tipos-midia-container">
                            <!-- Os checkboxes de mídia serão inseridos aqui via JS -->
                        </div>
                    @endif
                    
                    <div class="card-footer text-center mb-3">
                        <button type="button" class="btn btn-info" id="btn-pesquisar" name="acao" value="pesquisar"><i class="fa fa-search"></i> Pesquisar</button>
                    </div>
                {!! Form::close() !!} 
            </div>
            <div class="border-top p-3 bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex gap-4 flex-wrap align-items-center">
                        <div>
                            <strong>Total encontradas:</strong>
                            <span id="totalNoticias" class="badge bg-secondary">0</span>
                        </div>
                        <div class="ml-2">
                            <strong>Selecionadas para relatório:</strong>
                            <span id="totalSelecionadas" class="badge bg-info">0</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center mt-2 mt-md-0">
                        <button type="button" class="btn btn-warning" id="btnGerenciarTags" title="Adicionar tags às notícias selecionadas">
                            <i class="fa fa-tags"></i> Gerenciar Tags
                        </button>
                        <button type="button" class="btn btn-danger" id="btnGerarRelatorio">
                            <i class="fa fa-file-pdf-o"></i>
                            Gerar Relatório PDF (<span id="qtdSelecionadasBtn">0</span>)
                        </button>
                    </div>
                </div>
            </div>
            <div id="resultado-relatorio">
                {{-- Os resultados serão inseridos aqui via AJAX --}}
            </div>
        </div>
    </div>
</div> 





<!-- Modal para Gerenciar Tags -->
<div class="modal fade" id="modalGerenciarTags" tabindex="-1" role="dialog" aria-labelledby="modalGerenciarTagsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGerenciarTagsLabel">
                    <i class="fa fa-tags"></i> Gerenciar Tags das Notícias
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Notícias selecionadas:</strong> <span id="qtdNoticiasParaTags">0</span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="novaTag">
                                <i class="fa fa-plus"></i> Adicionar Nova Tag
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="novaTag" placeholder="Digite o nome da nova tag...">
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="button" id="btnAdicionarTag">
                                        <i class="fa fa-plus"></i> Adicionar
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Digite o nome da tag e clique em "Adicionar" para aplicar às notícias selecionadas.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>
                                <i class="fa fa-list"></i> Tags Existentes das Notícias Selecionadas
                            </label>
                            <div id="tagsExistentes" class="border rounded p-3" style="min-height: 100px; background-color: #f8f9fa;">
                                <div class="text-muted text-center">
                                    <i class="fa fa-spinner fa-spin"></i> Carregando tags...
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Clique no "X" ao lado de uma tag para removê-la das notícias selecionadas.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<!-- Arquivo de debug (opcional) -->
<script src="{{ asset('js/debug-relatorio.js') }}"></script>

<style>
.checkbox-table {
    width: 18px !important;
    height: 18px !important;
    margin: 0 !important;
    appearance: checkbox !important;
    -webkit-appearance: checkbox !important;
    -moz-appearance: checkbox !important;
    position: relative !important;
}

.checkbox-table::after,
.checkbox-table::before {
    display: none !important;
    content: none !important;
}

.checkbox-table:checked::after,
.checkbox-table:checked::before {
    display: none !important;
    content: none !important;
}

/* Estilos para as tabs */
.tabs-container {
    margin-top: 1rem;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    padding: 0.75rem 1rem;
    font-weight: 500;
    color: #495057;
    text-decoration: none;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    color: #495057;
    text-decoration: none;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link.active .fa {
    color: inherit;
}

.nav-tabs .nav-link .fa {
    margin-right: 0.5rem;
}

.tab-content {
    border: 1px solid #dee2e6;
    border-top: 0;
    border-bottom-left-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
    padding: 1.5rem;
    background-color: #fff;
}

.nav-tabs {
    border-bottom: 1px solid #dee2e6;
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}

/* Estilos para expandir notícias */
.noticia-row {
    cursor: pointer;
    transition: background-color 0.2s;
}

.noticia-row:hover {
    background-color: #f8f9fa;
}

.noticia-row:hover .expand-icon {
    color: #007bff;
}

.noticia-detalhes {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.expand-icon {
    transition: transform 0.2s, color 0.2s;
    color: #6c757d;
}

.expand-icon.rotated {
    transform: rotate(180deg);
    color: #007bff;
}

.detalhes-container {
    padding: 20px;
    margin: 10px 0;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    background-color: #fff;
}

.detalhes-container h6 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 5px;
    margin-top: 15px;
}

.detalhes-container h6:first-child {
    margin-top: 0;
}

.detalhes-container p {
    margin-bottom: 10px;
    color: #6c757d;
}

.detalhes-texto {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    padding: 10px;
    border-radius: 5px;
    background-color: #f9f9f9;
    font-size: 14px;
    line-height: 1.5;
}

/* Estilos para visualização de mídia */
.midia-container {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    background-color: #f8f9fa;
}

.midia-container img {
    transition: transform 0.2s ease;
    cursor: pointer;
}

.midia-container img:hover {
    transform: scale(1.02);
}

.midia-container video,
.midia-container audio {
    border-radius: 5px;
}

.midia-placeholder {
    padding: 20px;
    text-align: center;
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    color: #6c757d;
}

.midia-placeholder i {
    font-size: 2rem;
    margin-bottom: 10px;
    display: block;
}

/* Estilos para tags */
.tag-badge {
    display: inline-block;
    padding: 3px 8px;
    margin: 2px;
    background-color: #007bff;
    color: white;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.tag-badge-removivel {
    display: inline-block;
    padding: 5px 10px;
    margin: 3px;
    background-color: #17a2b8;
    color: white;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.tag-badge-removivel:hover {
    background-color: #dc3545;
}

.tag-badge-removivel .remove-tag {
    margin-left: 5px;
    font-weight: bold;
    cursor: pointer;
}

.tags-container {
    max-height: 150px;
    overflow-y: auto;
}

.tag-filter-item {
    display: inline-block;
    margin: 3px;
    padding: 5px 12px;
    background-color: #e9ecef;
    border-radius: 15px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
    user-select: none;
}

.tag-filter-item:hover {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
}

.tag-filter-item.selected {
    background-color: #007bff;
    color: white;
    border-color: #0056b3;
    font-weight: 500;
}

.tag-filter-item.selected:hover {
    background-color: #0056b3;
    border-color: #004085;
}

/* Estilos para filtros de fonte/emissora/programa - removidos (duplicados) */

.fonte-filter-item.emissora {
    background-color: #28a745;
    color: white;
    border-color: #1e7e34;
}

.fonte-filter-item.emissora:hover {
    background-color: #1e7e34;
    border-color: #155724;
}

.fonte-filter-item.programa {
    background-color: #17a2b8;
    color: white;
    border-color: #117a8b;
}

.fonte-filter-item.programa:hover {
    background-color: #117a8b;
    border-color: #0c5460;
}

/* Estilos para as tabs de fontes */
.fontes-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    padding: 0.5rem 0.75rem;
    font-weight: 500;
    color: #495057;
    text-decoration: none;
    font-size: 13px;
}

.fontes-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    color: #495057;
    text-decoration: none;
}

.fontes-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.fontes-tabs .nav-link.active .fa {
    color: inherit;
}

.fontes-tabs .nav-link .fa {
    margin-right: 0.5rem;
}

/* Estilos para campos de busca de fontes */
.fonte-search {
    border: 1px solid #dee2e6;
    border-radius: 20px;
    padding: 8px 15px;
    font-size: 13px;
    transition: all 0.3s ease;
}

.fonte-search:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    outline: 0;
}

/* Estilos para badges de contagem de notícias */
.badge-light {
    background-color: #6c757d !important;
    color: white !important;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 10px;
}

/* Melhoria na exibição das fontes */
.fonte-filter-item {
    display: inline-block;
    margin: 3px;
    padding: 8px 12px;
    background-color: #e9ecef;
    border-radius: 18px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
    user-select: none;
    max-width: 100%;
    word-wrap: break-word;
}

.fonte-filter-item:hover {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
}

.fonte-filter-item.selected {
    background-color: #007bff;
    color: white;
    border-color: #0056b3;
    font-weight: 500;
}

.fonte-filter-item.selected:hover {
    background-color: #0056b3;
    border-color: #004085;
}

/* Estilos para badges das tags na tabela */
.badge-tag {
    display: inline-block;
    padding: 4px 10px;
    margin: 1px 3px 1px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    font-size: 10px;
    font-weight: 500;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    transition: transform 0.2s ease;
}

.badge-tag:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

/* Estilos para badges de palavras-chave */
.badge-keyword {
    display: inline-block;
    padding: 4px 8px;
    margin: 1px 2px 1px 0;
    background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%);
    color: white;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    box-shadow: 0 1px 3px rgba(0,0,0,0.25);
    transition: all 0.2s ease;
    text-transform: lowercase;
}

.badge-keyword:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.4);
    background: linear-gradient(135deg, #ff5252 0%, #ff8f00 100%);
}

/* Destaque de palavras-chave no texto */
.keyword-highlight {
    background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%);
    padding: 2px 6px;
    border-radius: 6px;
    font-weight: 600;
    color: white;
    border: none;
    box-shadow: 0 1px 3px rgba(255, 107, 107, 0.4);
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    transition: all 0.2s ease;
    display: inline-block;
    margin: 0 1px;
}

.keyword-highlight:hover {
    background: linear-gradient(135deg, #ff5252 0%, #ff8f00 100%);
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(255, 107, 107, 0.5);
}

/* Variações de cores para as tags */
.badge-tag:nth-child(even) {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.badge-tag:nth-child(3n) {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    color: #333;
    text-shadow: none;
}

.badge-tag:nth-child(4n) {
    background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    color: #333;
    text-shadow: none;
}

.badge-tag:nth-child(5n) {
    background: linear-gradient(135deg, #96fbc4 0%, #f9f586 100%);
    color: #333;
    text-shadow: none;
}

/* Estilos para dropdown de sentimento */
.sentimento-select {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 2px 5px;
    transition: all 0.3s ease;
}

.sentimento-select:hover {
    border-color: #007bff;
}

.sentimento-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    outline: 0;
}

.sentimento-select.border-success {
    border-color: #28a745 !important;
    box-shadow: 0 0 0 0.2rem rgba(40,167,69,.25) !important;
}

.sentimento-select option {
    padding: 5px;
}

/* Container para dropdown de sentimento com ícone de loading */
.sentimento-container {
    display: inline-flex !important;
    align-items: center !important;
    white-space: nowrap !important;
    min-height: 32px;
}

/* Ícone de loading ao lado do dropdown */
.sentimento-loading {
    margin-left: 8px !important;
    color: #007bff !important;
    font-size: 14px !important;
}
</style>
<script>
    // Definir variáveis globais no escopo window
    window.host = $('meta[name="base-url"]').attr('content');
    window.noticiasCarregadas = {};
    window.noticiasCarregadasCount = 0;

    $('#id_cliente').on('change', function() {

        var clienteId = $(this).val();
        // Limpa o container
        $('#tipos-midia-container').html('<div class="text-muted">Carregando tipos de mídia...</div>');
        if (!clienteId) {
            $('#tipos-midia-container').html('');
            return;
        }
        $.ajax({
            url: window.host + '/cliente/flags-midia/' + clienteId,
            type: 'GET',
            dataType: 'json',
            success: function(flags) {
                var html = '<label class="form-label fw-semibold mb-2">Clipagem por tipo de mídia</label>';
                html += '<div class="d-flex flex-wrap gap-2">';
                if (flags.fl_impresso) {
                    html += `<div class="form-check">
                        <label class="form-check-label check-midia">
                            <input class="form-check-input" type="checkbox" name="fl_impresso" checked value="true">
                            <span class="form-check-sign"></span>
                            <span class="text-secondary"><i class="fa fa-newspaper-o"></i> Impressos</span>
                        </label>
                    </div>`;
                }
                if (flags.fl_web) {
                    html += `<div class="form-check ml-3">
                        <label class="form-check-label check-midia">
                            <input class="form-check-input" type="checkbox" name="fl_web" checked value="true">
                            <span class="form-check-sign"></span>
                            <span class="text-secondary"><i class="fa fa-globe"></i> Web</span>
                        </label>
                    </div>`;
                }
                if (flags.fl_radio) {
                    html += `<div class="form-check ml-3">
                        <label class="form-check-label check-midia">
                            <input class="form-check-input" type="checkbox" name="fl_radio" checked value="true">
                            <span class="form-check-sign"></span>
                            <span class="text-secondary"><i class="fa fa-volume-up"></i> Rádio</span>
                        </label>
                    </div>`;
                }
                if (flags.fl_tv) {
                    html += `<div class="form-check ml-3">
                        <label class="form-check-label check-midia">
                            <input class="form-check-input" type="checkbox" name="fl_tv" checked value="true">
                            <span class="form-check-sign"></span>
                            <span class="text-secondary"><i class="fa fa-television"></i> TV</span>
                        </label>
                    </div>`;
                }
                if (!flags.fl_impresso && !flags.fl_web && !flags.fl_radio && !flags.fl_tv) {
                    html += `<div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Atenção:</strong> Nenhum tipo de mídia está habilitado para este cliente.
                    </div>`;
                }
                html += '</div>';
                $('#tipos-midia-container').html(html);
            },
            error: function() {
                $('#tipos-midia-container').html('<div class="alert alert-danger">Erro ao carregar tipos de mídia do cliente.</div>');
            }
        });
    });
    
    // Flag para controlar visibilidade das áreas
    window.mostrarAreas = {{ $fl_areas ? 'true' : 'false' }};
    
    // Flag para controlar visibilidade do sentimento
    window.mostrarSentimento = {{ $fl_sentimento ? 'true' : 'false' }};
    
    // Flag para controlar visibilidade do retorno de mídia
    window.mostrarRetornoMidia = {{ $fl_retorno_midia ? 'true' : 'false' }};
    
    // Flag para controlar visibilidade dos botões de relatório com imagens
    @if(isset($fl_print))
        window.mostrarBotoesImagem = {{ $fl_print ? 'true' : 'false' }};
        console.log('🔍 DEBUG fl_print definido:', '{{ $fl_print ? "true" : "false" }}');
        console.log('🔍 DEBUG fl_print valor bruto:', {{ isset($fl_print) ? ($fl_print ? 1 : 0) : 'null' }});
    @else
        window.mostrarBotoesImagem = false;
        console.log('❌ DEBUG fl_print NÃO DEFINIDO - usando false por padrão');
    @endif
    
    // Debug: verificar valores finais
    console.log('🔍 DEBUG mostrarBotoesImagem final:', window.mostrarBotoesImagem);
    console.log('🔍 DEBUG tipo da variável:', typeof window.mostrarBotoesImagem);
    
    // Debug: informações do cliente
    @if(isset($cliente))
        console.log('🔍 DEBUG Cliente ID:', {{ $cliente->id ?? 'null' }});
        console.log('🔍 DEBUG Cliente Nome:', '{{ $cliente->nome ?? "sem nome" }}');
    @else
        console.log('❌ DEBUG Cliente não definido');
    @endif

    $( document ).ready(function() {



        // Carregar áreas do cliente logado ao inicializar (apenas se a seção existir)
        if ($('#areas-checkbox-group').length > 0) {
            carregarAreasCliente();
        }

        // Carregar tags disponíveis
        carregarTagsDisponiveis();

        // Carregar fontes disponíveis
        carregarFontesDisponiveis();

        // Verificar se Font Awesome está carregado
        if (!$('.fa').length && !$('link[href*="font-awesome"]').length) {
            console.warn('Font Awesome não está carregado corretamente. Alguns ícones podem não ser exibidos.');
        }

        // Preset de datas
        $('#presetsData button').on('click', function() {
            let preset = $(this).data('preset');
            let hoje = moment();
            let dt_inicial = '';
            let dt_final = '';

            switch(preset) {
                case 'hoje':
                    dt_inicial = hoje.format('DD/MM/YYYY');
                    dt_final = hoje.format('DD/MM/YYYY');
                    break;
                case 'ontem':
                    dt_inicial = hoje.clone().subtract(1, 'days').format('DD/MM/YYYY');
                    dt_final = hoje.clone().subtract(1, 'days').format('DD/MM/YYYY');
                    break;
                case '7dias':
                    dt_inicial = hoje.clone().subtract(6, 'days').format('DD/MM/YYYY');
                    dt_final = hoje.format('DD/MM/YYYY');
                    break;
                case '30dias':
                    dt_inicial = hoje.clone().subtract(29, 'days').format('DD/MM/YYYY');
                    dt_final = hoje.format('DD/MM/YYYY');
                    break;
                case 'mes':
                    dt_inicial = hoje.clone().startOf('month').format('DD/MM/YYYY');
                    dt_final = hoje.format('DD/MM/YYYY');
                    break;
                case 'mesanterior':
                    dt_inicial = hoje.clone().subtract(1, 'months').startOf('month').format('DD/MM/YYYY');
                    dt_final = hoje.clone().subtract(1, 'months').endOf('month').format('DD/MM/YYYY');
                    break;
            }

            $('#dt_inicial').val(dt_inicial);
            $('#dt_final').val(dt_final);
        });



        // Carregar áreas do cliente logado
        function carregarAreasCliente() {
            // Verificar se o elemento existe antes de fazer a requisição
            if ($('#areas-checkbox-group').length === 0) {
                console.log('Elemento #areas-checkbox-group não encontrado');
                return;
            }
            
            console.log('Carregando áreas do cliente...');
            
            $.ajax({
                url: window.host + '/api/cliente/areas',
                type: 'GET',
                dataType: 'json',
                timeout: 3600000, // 1 hora
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Áreas carregadas:', response);
                    
                    var areasHtml = '';
                    
                    if (response && Array.isArray(response)) {
                        response.forEach(function(area) {
                            areasHtml += '<div class="form-check" style="margin-right: 10px; margin-bottom: 8px;">';
                            areasHtml += '<label class="form-check-label">';
                            areasHtml += '<input class="form-check-input" type="checkbox" name="areas[]" value="' + area.id + '">';
                            areasHtml += '<span class="form-check-sign"></span>';
                            areasHtml += '<span>' + area.nome + '</span>';
                            areasHtml += '</label>';
                            areasHtml += '</div>';
                        });
                    } else {
                        areasHtml = '<p class="text-muted">Nenhuma área encontrada</p>';
                    }
                    
                    $('#areas-checkbox-group').html(areasHtml);
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao carregar áreas:', {
                        status: status,
                        error: error,
                        xhr: xhr.responseText
                    });
                    
                    if (xhr.status === 404) {
                        $('#areas-checkbox-group').html('<p class="text-warning">Rota não encontrada. Verifique se o sistema está configurado corretamente.</p>');
                    } else if (xhr.status === 401) {
                        $('#areas-checkbox-group').html('<p class="text-danger">Acesso negado. Faça login novamente.</p>');
                    } else {
                        $('#areas-checkbox-group').html('<p class="text-muted">Erro ao carregar áreas. Tente novamente mais tarde.</p>');
                    }
                }
            });
        }

        // Limpar áreas
        function limparAreas() {
            $('#areas-checkbox-group').empty();
        }

        // Botão pesquisar
        $('#btn-pesquisar').on('click', function() {
            pesquisarNoticias();
        });

        // Botão gerenciar tags
        $('#btnGerenciarTags').on('click', function() {
            abrirModalGerenciarTags();
        });

        // Botão recarregar tags
        $('#btnRecarregarTags').on('click', function() {
            carregarTagsDisponiveis();
        });

        // Botão limpar tags selecionadas
        $('#btnLimparTags').on('click', function() {
            $('.tag-filter-item.selected').removeClass('selected');
            console.log('Tags filtro limpas');
        });

        // Botão recarregar fontes
        $('#btnRecarregarFontes').on('click', function() {
            carregarFontesDisponiveis();
        });

        // Botão limpar fontes selecionadas
        $('#btnLimparFontes').on('click', function() {
            $('.fonte-filter-item.selected').removeClass('selected');
            limparBuscasFontes();
            console.log('Fontes filtro limpas');
        });

        // Botão adicionar tag no modal
        $('#btnAdicionarTag').on('click', function() {
            adicionarTagNoticiaSelecionadas();
        });

        // Enter na caixa de nova tag
        $('#novaTag').on('keypress', function(e) {
            if (e.which === 13) {
                adicionarTagNoticiaSelecionadas();
            }
        });

        // Pesquisar notícias
        function pesquisarNoticias() {

            var formData = {
                data_inicio: converterDataParaISO($('#dt_inicial').val()),
                data_fim: converterDataParaISO($('#dt_final').val()),
                tipo_filtro_data: $('#tipo_filtro_data').val() || 'coleta',
                tipos_midia: [],
                status: [],
                retorno: $('input[name="retorno"]:checked').val() || 'com_retorno',
                valor: ['com_valor', 'sem_valor'], // Incluir ambos por padrão
                areas: []
            };

            // Tipos de mídia
            if ($('input[name="fl_web"]').length && $('input[name="fl_web"]').is(':checked')) formData.tipos_midia.push('web');
            if ($('input[name="fl_tv"]').length && $('input[name="fl_tv"]').is(':checked')) formData.tipos_midia.push('tv');
            if ($('input[name="fl_radio"]').length && $('input[name="fl_radio"]').is(':checked')) formData.tipos_midia.push('radio');
            if ($('input[name="fl_impresso"]').length && $('input[name="fl_impresso"]').is(':checked')) formData.tipos_midia.push('impresso');

            // Status/Sentimento (apenas se a seção existir)
            if (window.mostrarSentimento) {
                $('input[name="sentimento[]"]:checked').each(function() {
                    var valor = $(this).val();
                    if (valor == '1') formData.status.push('positivo');
                    else if (valor == '-1') formData.status.push('negativo');
                    else if (valor == '0') formData.status.push('neutro');
                });
                
                // Se nenhum sentimento foi selecionado, incluir todos
                if (formData.status.length === 0) {
                    formData.status = ['positivo', 'negativo', 'neutro'];
                }
            } else {
                // Se não mostrar sentimento, incluir todos os status por padrão
                formData.status = ['positivo', 'negativo', 'neutro'];
            }

            // Áreas (apenas se a seção existir)
            if ($('#areas-checkbox-group').length > 0) {
                $('input[name="areas[]"]:checked').each(function() {
                    formData.areas.push(parseInt($(this).val()));
                });
            }

            // Validações
            if (!formData.data_inicio || !formData.data_fim) {
                alert('Por favor, preencha as datas inicial e final.');
                return;
            }

            if (formData.tipos_midia.length === 0) {
                // Verificar se há pelo menos uma opção de mídia disponível
                var opcoesMidiaDisponiveis = $('input[name="fl_web"], input[name="fl_tv"], input[name="fl_radio"], input[name="fl_impresso"]').length;
                if (opcoesMidiaDisponiveis === 0) {
                    alert('Nenhum tipo de mídia está habilitado para seu cliente. Entre em contato com o administrador.');
                } else {
                    alert('Por favor, selecione ao menos um tipo de mídia.');
                }
                return;
            }

            // Mostrar loading
            $('#resultado-relatorio').html('<div class="text-center my-4"><i class="fa fa-spinner fa-spin fa-2x"></i> Carregando notícias...</div>');

            // Adicionar token CSRF
            formData._token = $('meta[name="csrf-token"]').attr('content');

            var cliente = $("#id_cliente").val();
            if(cliente){
                formData.cliente = cliente;
            }
            
            // Adicionar termo de busca
            formData.termo = $('#termo').val().trim();

            // Tags filtro
            formData.tags_filtro = [];
            $('.tag-filter-item.selected').each(function() {
                formData.tags_filtro.push($(this).data('tag'));
            });

            // Fontes filtro
            formData.fontes_filtro = getSelectedFontes();

            console.log('Tags selecionadas para filtro:', formData.tags_filtro);
            console.log('Enviando dados para pesquisa:', formData);

            // DEBUG: Verificar dados antes de enviar
            console.log('=== DEBUG AJAX REQUEST ===');
            console.log('URL:', window.host + '/cliente/relatorios/listar-noticias');
            console.log('FormData completo:', JSON.stringify(formData, null, 2));
            console.log('Tipo de filtro de data:', formData.tipo_filtro_data);
            console.log('Tags selecionadas específicamente:', formData.tags_filtro);
            console.log('==========================');

            $.ajax({
                url: window.host + '/cliente/relatorios/listar-noticias',
                type: 'POST',
                data: formData,
                dataType: 'json',
                timeout: 3600000, // 1 hora de timeout
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                },
                success: function(response) {
                    console.log('=== DEBUG AJAX RESPONSE ===');
                    console.log('Response success:', response.success);
                    console.log('Total Web:', response.noticias?.web?.length || 0);
                    console.log('Total TV:', response.noticias?.tv?.length || 0);
                    console.log('Total Radio:', response.noticias?.radio?.length || 0);
                    console.log('Total Impresso:', response.noticias?.impresso?.length || 0);
                    console.log('Filtros aplicados no backend:', response.filtros_aplicados);
                    
                    // Verificar se pelo menos uma notícia web tem tags para debug
                    if (response.noticias?.web?.length > 0) {
                        console.log('Exemplo notícia web:', response.noticias.web[0]);
                        console.log('Tags da primeira notícia web:', response.noticias.web[0].tags);
                    }
                    console.log('=============================');
                    
                    if (response.success && response.noticias) {
                        window.noticiasCarregadas = response.noticias;
                        exibirNoticias(response.noticias);
                        atualizarContadores();
                    } else {
                        $('#resultado-relatorio').html('<div class="alert alert-danger">' + (response.message || 'Erro desconhecido') + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', {
                        status: status,
                        error: error,
                        xhr: xhr.responseText
                    });
                    
                    var errorMessage = '';
                    if (xhr.status === 404) {
                        errorMessage = 'Rota não encontrada. Verifique se o sistema está configurado corretamente.';
                    } else if (xhr.status === 401) {
                        errorMessage = 'Acesso negado. Faça login novamente.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Erro interno do servidor. Tente novamente mais tarde.';
                    } else if (status === 'timeout') {
                        errorMessage = 'Tempo limite da requisição excedido. Tente novamente.';
                    } else {
                        errorMessage = 'Erro ao buscar notícias. Tente novamente.';
                    }
                    
                    $('#resultado-relatorio').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                }
            });
        }

        // Exibir notícias
        function exibirNoticias(noticias) {
            try {
                var html = '';
                var totalNoticias = 0;

                // Contar total de notícias
                if (noticias && typeof noticias === 'object') {
                    Object.keys(noticias).forEach(function(tipo) {
                        if (noticias[tipo] && Array.isArray(noticias[tipo])) {
                            totalNoticias += noticias[tipo].length;
                        }
                    });
                }

                if (totalNoticias === 0) {
                    html = '<div class="alert alert-info">Nenhuma notícia encontrada para os critérios informados.</div>';
                } else {
                    // Cabeçalho com controles
                    html += '<div class="d-flex justify-content-between align-items-center mb-3">';
                    html += '<h5>Notícias Encontradas (' + totalNoticias + ')</h5>';
                    html += '<div>';
                    html += '<button type="button" class="btn btn-sm btn-outline-primary" onclick="selecionarTodas()">Selecionar Todas</button>';
                    html += '<button type="button" class="btn btn-sm btn-outline-secondary ml-2" onclick="deselecionarTodas()">Desmarcar Todas</button>';
                    html += '<button type="button" class="btn btn-sm btn-outline-warning ml-2" onclick="fecharTodasExpandidas()">Fechar Todas Expandidas</button>';
                    html += '</div>';
                    html += '</div>';

                    // Criar estrutura de abas
                    html += '<div class="tabs-container">';
                    html += '<ul class="nav nav-tabs nav-fill" id="noticiasTab" role="tablist">';
                    
                    var primeiraAba = true;
                    var tiposComNoticias = [];
                    
                    // Criar abas apenas para tipos com notícias
                    Object.keys(noticias).forEach(function(tipo) {
                        if (noticias[tipo] && Array.isArray(noticias[tipo]) && noticias[tipo].length > 0) {
                            tiposComNoticias.push(tipo);
                            var icone = obterIconeTipo(tipo);
                            var titulo = obterTituloTipo(tipo);
                            var cor = obterCorTipo(tipo);
                            
                            html += '<li class="nav-item" role="presentation">';
                            html += '<a class="nav-link ' + (primeiraAba ? 'active' : '') + '" id="' + tipo + '-tab" data-toggle="tab" href="#' + tipo + '-content" role="tab" aria-controls="' + tipo + '-content" aria-selected="' + (primeiraAba ? 'true' : 'false') + '">';
                            html += '<i class="fa ' + icone + ' ' + cor + '"></i> ';
                            html += titulo + ' (' + noticias[tipo].length + ')';
                            html += '</a>';
                            html += '</li>';
                            
                            primeiraAba = false;
                        }
                    });
                    
                    html += '</ul>';
                    html += '<div class="tab-content" id="noticiasTabContent">';
                    
                    // Criar conteúdo das abas
                    primeiraAba = true;
                    tiposComNoticias.forEach(function(tipo) {
                        html += '<div class="tab-pane fade ' + (primeiraAba ? 'show active' : '') + '" id="' + tipo + '-content" role="tabpanel" aria-labelledby="' + tipo + '-tab">';
                        html += gerarTabelaTipoMidia(tipo, noticias[tipo]);
                        html += '</div>';
                        primeiraAba = false;
                    });
                    
                    html += '</div>';
                    html += '</div>';
                }

                $('#resultado-relatorio').html(html);
                
                // Inicializar as tabs do Bootstrap
                setTimeout(function() {
                    try {
                        if (typeof $.fn.tab !== 'undefined') {
                            $('#noticiasTab a').on('click', function(e) {
                                e.preventDefault();
                                $(this).tab('show');
                            });
                            
                            // Mostrar a primeira aba por padrão
                            $('#noticiasTab a:first').tab('show');
                        } else {
                            // Fallback manual para as tabs se o Bootstrap não estiver disponível
                            $('#noticiasTab a').on('click', function(e) {
                                e.preventDefault();
                                var target = $(this).attr('href');
                                
                                // Remover active de todas as tabs
                                $('#noticiasTab a').removeClass('active');
                                $('.tab-pane').removeClass('active show');
                                
                                // Adicionar active na tab clicada
                                $(this).addClass('active');
                                $(target).addClass('active show');
                            });
                            
                            // Mostrar a primeira aba por padrão
                            $('#noticiasTab a:first').addClass('active');
                            $('.tab-pane:first').addClass('active show');
                        }
                    } catch (e) {
                        console.error('Erro ao inicializar tabs:', e);
                    }
                }, 100); // Pequeno delay para garantir que o DOM foi renderizado
                
                // Atualizar contador de notícias totais
                $('#totalNoticias').text(totalNoticias);
                
            } catch (e) {
                console.error('Erro ao exibir notícias:', e);
                $('#resultado-relatorio').html('<div class="alert alert-danger">Erro ao exibir notícias. Tente novamente.</div>');
            }
        }

        // Funções auxiliares para obter propriedades dos tipos de mídia
        function obterIconeTipo(tipo) {
            switch(tipo) {
                case 'web': return 'fa-globe';
                case 'tv': return 'fa-television';
                case 'radio': return 'fa-volume-up';
                case 'impresso': return 'fa-newspaper-o';
                default: return 'fa-file';
            }
        }

        function obterTituloTipo(tipo) {
            switch(tipo) {
                case 'web': return 'Notícias Web';
                case 'tv': return 'Notícias TV';
                case 'radio': return 'Notícias Rádio';
                case 'impresso': return 'Notícias Impressas';
                default: return 'Notícias';
            }
        }

        function obterCorTipo(tipo) {
            switch(tipo) {
                case 'web': return 'text-primary';
                case 'tv': return 'text-danger';
                case 'radio': return 'text-success';
                case 'impresso': return 'text-warning';
                default: return 'text-secondary';
            }
        }

        // Gerar tabela para um tipo de mídia (sem card wrapper)
        function gerarTabelaTipoMidia(tipo, noticiasArray) {
            try {
                var html = '';

                // Verificar se noticiasArray é válido
                if (!noticiasArray || !Array.isArray(noticiasArray)) {
                    return '<div class="alert alert-warning">Nenhuma notícia encontrada para este tipo de mídia.</div>';
                }

                // Cabeçalho com controles específicos do tipo
                html += '<div class="d-flex justify-content-between align-items-center mb-3">';
                html += '<div>';
                html += '<button type="button" class="btn btn-sm btn-outline-primary" onclick="selecionarTodasTipoBtn(\'' + tipo + '\', true)">Selecionar Todas</button>';
                html += '<button type="button" class="btn btn-sm btn-outline-secondary ml-2" onclick="selecionarTodasTipoBtn(\'' + tipo + '\', false)">Desmarcar Todas</button>';
                
                // Botões específicos com imagens (apenas se cliente tem permissão fl_print)
                console.log('🔍 DEBUG gerarTabelaTipoMidia - tipo:', tipo, 'mostrarBotoesImagem:', window.mostrarBotoesImagem);
                if (window.mostrarBotoesImagem === true) {
                    console.log('✅ Adicionando botões de imagem para tipo:', tipo);
                    if (tipo === 'web') {
                        html += '<button type="button" class="btn btn-sm btn-success ml-3" id="btnGerarRelatorioWebAba" onclick="gerarRelatorioWebAba()"><i class="fa fa-globe"></i> Gerar Relatório Web com Imagens</button>';
                    }
                    
                    if (tipo === 'impresso') {
                        html += '<button type="button" class="btn btn-sm btn-warning ml-3" id="btnGerarRelatorioImpressoAba" onclick="gerarRelatorioImpressoAba()"><i class="fa fa-newspaper-o"></i> Gerar Relatório Impresso com Imagens</button>';
                    }
                } else {
                    console.log('❌ Botões de imagem bloqueados - fl_print = false');
                }
                
                html += '</div>';
                html += '<div class="text-muted">';
                html += '<i class="fa fa-info-circle"></i> ' + noticiasArray.length + ' notícias encontradas';
                html += '</div>';
                html += '</div>';

                // Cabeçalho da tabela
                html += '<div class="table-responsive">';
                html += '<table class="table table-sm table-hover">';
                html += '<thead>';
                html += '<tr>';
                html += '<th width="50"><input type="checkbox" class="selecionar-todas-' + tipo + ' checkbox-table" onchange="selecionarTodasTipo(\'' + tipo + '\', this)"></th>';
                
                // Colunas diferentes para TV e Rádio
                if (tipo === 'tv' || tipo === 'radio') {
                    html += '<th>Programa</th>';
                    html += '<th>Veículo</th>';
                    html += '<th>Horário</th>';
                    html += '<th>Duração</th>';
                } else {
                    html += '<th>Título</th>';
                    html += '<th>Veículo</th>';
                }
                
                            html += '<th>Data</th>';
            if (window.mostrarAreas) {
                html += '<th>Área</th>';
            }
            if (window.mostrarSentimento) {
                html += '<th>Sentimento</th>';
            }
            if (window.mostrarRetornoMidia) {
                html += '<th>Valor</th>';
            }
                html += '<th>Tags</th>';
                html += '<th>Palavras-Chave</th>';
                html += '<th width="30" class="text-center"><i class="fa fa-expand-alt" title="Clique na linha para expandir/recolher"></i></th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                // Notícias
                var noticiasProcessadas = 0;
                var noticiasRenderizadas = 0;
                
                console.log('🔍 DEBUG gerarTabelaTipoMidia - tipo:', tipo, 'total notícias recebidas:', noticiasArray.length);
                
                noticiasArray.forEach(function(noticia) {
                    noticiasProcessadas++;
                    if (noticia && (noticia.id !== undefined && noticia.id !== null)) {
                        noticiasRenderizadas++;
                        html += '<tr class="noticia-row" data-noticia-id="' + noticia.id + '" data-tipo="' + tipo + '" style="cursor: pointer;" onclick="toggleNoticiaDetalhes(' + noticia.id + ', \'' + tipo + '\', this)">';
                        html += '<td onclick="event.stopPropagation()"><input type="checkbox" class="selecionar-noticia checkbox-table" data-tipo="' + tipo + '" data-id="' + noticia.id + '" onchange="atualizarContadores()"></td>';
                        
                        // Dados diferentes para TV e Rádio
                        if (tipo === 'tv' || tipo === 'radio') {
                            html += '<td>' + (noticia.programa || 'N/A') + '</td>';
                            html += '<td>' + (noticia.veiculo || 'N/A') + '</td>';
                            html += '<td>' + (noticia.horario || 'N/A') + '</td>';
                            html += '<td>' + (noticia.duracao || 'N/A') + '</td>';
                        } else {
                            html += '<td><strong>' + (noticia.titulo || 'Sem título') + '</strong></td>';
                            html += '<td>' + (noticia.veiculo || 'N/A') + '</td>';
                        }
                        
                        html += '<td>' + (noticia.data_formatada || 'N/A') + '</td>';
                        if (window.mostrarAreas) {
                            html += '<td>' + (noticia.area || 'N/A') + '</td>';
                        }
                        if (window.mostrarSentimento) {
                            html += '<td>' + obterSentimentoDropdown(noticia.sentimento, noticia.id, tipo) + '</td>';
                        }
                        if (window.mostrarRetornoMidia) {
                            html += '<td>' + (noticia.valor > 0 ? 'R$ ' + Number(noticia.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : 'N/A') + '</td>';
                        }
                        
                        // Coluna de tags
                        html += '<td>';
                        if (noticia.tags && Array.isArray(noticia.tags) && noticia.tags.length > 0) {
                            noticia.tags.forEach(function(tag) {
                                html += '<span class="badge-tag">' + tag + '</span>';
                            });
                        } else {
                            html += '<span class="text-muted" style="font-size: 11px;">sem tags</span>';
                        }
                        html += '</td>';
                        
                        // Coluna de palavras-chave encontradas
                        html += '<td>';
                        if (noticia.palavras_chave_encontradas && Array.isArray(noticia.palavras_chave_encontradas) && noticia.palavras_chave_encontradas.length > 0) {
                            noticia.palavras_chave_encontradas.forEach(function(palavra) {
                                html += '<span class="badge-keyword">' + palavra + '</span>';
                            });
                        } else {
                            html += '<span class="text-muted" style="font-size: 11px;">nenhuma</span>';
                        }
                        html += '</td>';
                        
                        html += '<td class="text-center"><i class="fa fa-chevron-down expand-icon" data-noticia-id="' + noticia.id + '"></i></td>';
                        html += '</tr>';
                    } else {
                        // Log notícias que estão sendo filtradas
                        console.log('⚠️ DEBUG Notícia filtrada - tipo:', tipo, 'noticia:', noticia);
                    }
                });
                
                console.log('📊 DEBUG Estatísticas - tipo:', tipo, 'processadas:', noticiasProcessadas, 'renderizadas:', noticiasRenderizadas, 'filtradas:', (noticiasProcessadas - noticiasRenderizadas));

                html += '</tbody>';
                html += '</table>';
                html += '</div>';

                return html;
                
            } catch (e) {
                console.error('Erro ao gerar tabela para tipo ' + tipo + ':', e);
                return '<div class="alert alert-danger">Erro ao gerar tabela para ' + tipo + '. Tente novamente.</div>';
            }
        }

        // Botão gerar relatório
        $('#btnGerarRelatorio').on('click', function() {
            gerarRelatorio();
        });

        // A função gerarRelatorioWebAba() será chamada diretamente pelo onclick do botão na aba

        // Gerar relatório
        function gerarRelatorio() {
            var noticiasSelecionadas = obterNoticiasSelecionadas();
            
            if (Object.keys(noticiasSelecionadas).length === 0 || 
                (noticiasSelecionadas.web.length === 0 && noticiasSelecionadas.tv.length === 0 && 
                 noticiasSelecionadas.radio.length === 0 && noticiasSelecionadas.impresso.length === 0)) {
                alert('Por favor, selecione ao menos uma notícia para gerar o relatório.');
                return;
            }

            var formData = {
                data_inicio: converterDataParaISO($('#dt_inicial').val()),
                data_fim: converterDataParaISO($('#dt_final').val()),
                ids_web: noticiasSelecionadas.web,
                ids_tv: noticiasSelecionadas.tv,
                ids_radio: noticiasSelecionadas.radio,
                ids_impresso: noticiasSelecionadas.impresso
            };
            
            // Adiciona flag de mostrar retorno de mídia se o usuário tem permissão
            if (window.mostrarRetornoMidia) {
                formData.mostrar_retorno_relatorio = $('#mostrar_retorno_relatorio').is(':checked') ? 'true' : 'false';
            } else {
                formData.mostrar_retorno_relatorio = 'false';
            }
            
            // Adiciona flag de mostrar sentimento se o usuário tem permissão
            if (window.mostrarSentimento) {
                formData.mostrar_sentimento_relatorio = $('#mostrar_sentimento_relatorio').is(':checked') ? 'true' : 'false';
            } else {
                formData.mostrar_sentimento_relatorio = 'false';
            }

            // Adicionar token CSRF
            formData._token = $('meta[name="csrf-token"]').attr('content');

            var cliente = $("#id_cliente").val();
            if(cliente){
                formData.cliente = cliente;
            }

            // Mostrar loading
            $('#btnGerarRelatorio').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Gerando...');

            var host =  $('meta[name="base-url"]').attr('content');

            $.ajax({
                url: host + '/cliente/relatorios/gerar-pdf',
                type: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Response:', response);
                    if (response.success) {
                        console.log('Download URL:', response.download_url);
                        
                        // Download direto usando a nova rota que força download
                        var downloadUrl = response.download_url || (host + '/cliente/'+response.cliente+'/relatorios/download/' + response.arquivo);
                        var fileName = response.arquivo || 'relatorio.pdf';
                        console.log('Iniciando download de:', downloadUrl);
                        console.log('Nome do arquivo:', fileName);
                        
                        // Método simples e eficaz: redirecionamento da janela
                        window.location.href = downloadUrl;
                    } else {
                        alert('Erro ao gerar relatório: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao gerar relatório:', error);
                    
                    var errorMessage = '';
                    if (xhr.status === 404) {
                        errorMessage = 'Rota não encontrada. Verifique se o sistema está configurado corretamente.';
                    } else if (xhr.status === 401) {
                        errorMessage = 'Acesso negado. Faça login novamente.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Erro interno do servidor. Tente novamente mais tarde.';
                    } else {
                        errorMessage = 'Erro ao gerar relatório. Tente novamente.';
                    }
                    
                    alert(errorMessage);
                },
                complete: function() {
                    $('#btnGerarRelatorio').prop('disabled', false).html('<i class="fa fa-file-pdf-o"></i> Gerar Relatório PDF (<span id="qtdSelecionadasBtn">0</span>)');
                }
            });
        }

        

        // As funções obterNoticiasSelecionadas e atualizarContadores foram movidas para o escopo global

        // Inicializar contadores
        $('#totalNoticias').text(0);

        // Função para debug
        function debugInfo() {
            console.log('=== DEBUG INFO ===');
            console.log('Host:', window.host);
            console.log('Mostrar áreas:', window.mostrarAreas);
            console.log('Mostrar sentimento:', window.mostrarSentimento);
            console.log('Mostrar retorno de mídia:', window.mostrarRetornoMidia);
            console.log('Notícias carregadas:', window.noticiasCarregadas);
            console.log('==================');
        }
        
        // Adicionar função de debug ao escopo global para facilitar o debug
        window.debugRelatorioDados = debugInfo;

        // ===== FUNÇÕES DE TAGS =====

        // Carregar tags disponíveis para filtro
        function carregarTagsDisponiveis() {
            console.log('Carregando tags disponíveis...');
            
            $.ajax({
                url: window.host + '/cliente/tags/disponiveis',
                type: 'GET',
                dataType: 'json',
                timeout: 3600000, // 1 hora
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Tags disponíveis carregadas:', response);
                    
                    var tagsHtml = '';
                    
                    if (response && Array.isArray(response) && response.length > 0) {
                        response.forEach(function(tag) {
                            tagsHtml += '<span class="tag-filter-item" data-tag="' + tag + '" onclick="toggleTagFilter(\'' + tag + '\')">';
                            tagsHtml += '<i class="fa fa-tag mr-1"></i>' + tag;
                            tagsHtml += '</span>';
                        });
                    } else {
                        tagsHtml = '<div class="text-muted text-center">Nenhuma tag encontrada</div>';
                    }
                    
                    $('#tags-filtro-container').html(tagsHtml);
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao carregar tags:', {
                        status: status,
                        error: error,
                        xhr: xhr.responseText
                    });
                    
                    $('#tags-filtro-container').html('<div class="text-danger text-center">Erro ao carregar tags</div>');
                }
            });
        }

        // Abrir modal de gerenciar tags
        function abrirModalGerenciarTags() {
            var noticiasSelecionadas = obterNoticiasSelecionadas();
            var totalSelecionadas = noticiasSelecionadas.web.length + noticiasSelecionadas.tv.length + 
                                   noticiasSelecionadas.radio.length + noticiasSelecionadas.impresso.length;
            
            if (totalSelecionadas === 0) {
                alert('Por favor, selecione ao menos uma notícia para gerenciar tags.');
                return;
            }

            $('#qtdNoticiasParaTags').text(totalSelecionadas);
            $('#novaTag').val('');
            carregarTagsNoticiaSelecionadas();
            $('#modalGerenciarTags').modal('show');
        }

        // A função carregarTagsNoticiaSelecionadas foi movida para o escopo global

        // Adicionar tag às notícias selecionadas
        function adicionarTagNoticiaSelecionadas() {
            var novaTag = $('#novaTag').val().trim();
            
            if (!novaTag) {
                alert('Por favor, digite o nome da tag.');
                return;
            }

            var noticiasSelecionadas = obterNoticiasSelecionadas();
            
            $('#btnAdicionarTag').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adicionando...');
            
            $.ajax({
                url: window.host + '/cliente/tags/adicionar',
                type: 'POST',
                data: {
                    tag: novaTag,
                    ids_web: noticiasSelecionadas.web,
                    ids_tv: noticiasSelecionadas.tv,
                    ids_radio: noticiasSelecionadas.radio,
                    ids_impresso: noticiasSelecionadas.impresso,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                timeout: 3600000, // 1 hora
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Resposta ao adicionar tag:', response);
                    
                    if (response.success) {
                        $('#novaTag').val('');
                        carregarTagsNoticiaSelecionadas();
                        carregarTagsDisponiveis(); // Atualizar lista de filtros
                        
                        // Mostrar notificação de sucesso
                        console.log('Tag "' + novaTag + '" adicionada com sucesso a ' + response.noticias_afetadas + ' notícias.');
                    } else {
                        alert('Erro ao adicionar tag: ' + (response.message || 'Erro desconhecido'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao adicionar tag:', {
                        status: status,
                        error: error,
                        xhr: xhr.responseText
                    });
                    
                    alert('Erro ao adicionar tag. Tente novamente.');
                },
                complete: function() {
                    $('#btnAdicionarTag').prop('disabled', false).html('<i class="fa fa-plus"></i> Adicionar');
                }
            });
        }



    });

    // ===== FUNÇÕES GLOBAIS =====

    // Obter notícias selecionadas
    function obterNoticiasSelecionadas() {
        var selecionadas = {
            web: [],
            tv: [],
            radio: [],
            impresso: []
        };

        $('.selecionar-noticia:checked').each(function() {
            var tipo = $(this).data('tipo');
            var id = $(this).data('id');
            selecionadas[tipo].push(id);
        });

        return selecionadas;
    }

    // Atualizar contadores
    function atualizarContadores() {
        try {
            var totalSelecionadas = $('.selecionar-noticia:checked').length;
            $('#totalSelecionadas').text(totalSelecionadas);
            $('#qtdSelecionadasBtn').text(totalSelecionadas);
        } catch (e) {
            console.error('Erro ao atualizar contadores:', e);
        }
    }

    // Carregar tags das notícias selecionadas
    function carregarTagsNoticiaSelecionadas() {
        var noticiasSelecionadas = obterNoticiasSelecionadas();
        
        $('#tagsExistentes').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Carregando tags...</div>');
        
        $.ajax({
            url: window.host + '/cliente/tags/noticias',
            type: 'POST',
            data: {
                ids_web: noticiasSelecionadas.web,
                ids_tv: noticiasSelecionadas.tv,
                ids_radio: noticiasSelecionadas.radio,
                ids_impresso: noticiasSelecionadas.impresso,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            timeout: 3600000, // 1 hora
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Tags das notícias selecionadas:', response);
                
                var tagsHtml = '';
                
                if (response && Array.isArray(response) && response.length > 0) {
                    response.forEach(function(tag) {
                        tagsHtml += '<span class="tag-badge-removivel" data-tag="' + tag + '">';
                        tagsHtml += '<i class="fa fa-tag mr-1"></i>' + tag + ' <span class="remove-tag" onclick="removerTagNoticiaSelecionadas(\'' + tag + '\')">×</span>';
                        tagsHtml += '</span>';
                    });
                } else {
                    tagsHtml = '<div class="text-muted text-center"><i class="fa fa-info-circle mr-1"></i>Nenhuma tag encontrada nas notícias selecionadas</div>';
                }
                
                $('#tagsExistentes').html(tagsHtml);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar tags das notícias:', {
                    status: status,
                    error: error,
                    xhr: xhr.responseText
                });
                
                $('#tagsExistentes').html('<div class="alert alert-danger">Erro ao carregar tags. Tente novamente.</div>');
            }
        });
    }

    function selecionarTodas() {
        $('.selecionar-noticia').prop('checked', true);
        $('.selecionar-todas-web, .selecionar-todas-tv, .selecionar-todas-radio, .selecionar-todas-impresso').prop('checked', true);
        atualizarContadores();
    }

    function deselecionarTodas() {
        $('.selecionar-noticia').prop('checked', false);
        $('.selecionar-todas-web, .selecionar-todas-tv, .selecionar-todas-radio, .selecionar-todas-impresso').prop('checked', false);
        atualizarContadores();
    }

    function selecionarTodasTipo(tipo, checkbox) {
        $('.selecionar-noticia[data-tipo="' + tipo + '"]').prop('checked', checkbox.checked);
        atualizarContadores();
    }

    function selecionarTodasTipoBtn(tipo, selecionar) {
        $('.selecionar-noticia[data-tipo="' + tipo + '"]').prop('checked', selecionar);
        $('.selecionar-todas-' + tipo).prop('checked', selecionar);
        atualizarContadores();
    }

    // Função atualizarContadores já existe no escopo global acima

    function fecharTodasExpandidas() {
        // Contar quantas notícias estão expandidas
        var expandidas = $('.noticia-detalhes:visible').length;
        
        if (expandidas === 0) {
            alert('Nenhuma notícia está expandida no momento.');
            return;
        }
        
        // Fechar todas as linhas de detalhes visíveis
        $('.noticia-detalhes').hide();
        
        // Resetar todos os ícones de expansão
        $('.expand-icon').removeClass('rotated');
        
        // Mostrar mensagem de confirmação
        var mensagem = expandidas === 1 ? '1 notícia expandida foi fechada.' : expandidas + ' notícias expandidas foram fechadas.';
        console.log(mensagem);
    }

    function toggleNoticiaDetalhes(id, tipo, elemento) {
        var $row = $(elemento);
        var $icon = $row.find('.expand-icon');
        var detalhesId = 'detalhes-' + tipo + '-' + id;
        var $detalhesRow = $('#' + detalhesId);
        
        // Se já existe a linha de detalhes, apenas mostrar/esconder
        if ($detalhesRow.length > 0) {
            if ($detalhesRow.is(':visible')) {
                // Esconder detalhes
                $detalhesRow.hide();
                $icon.removeClass('rotated');
            } else {
                // Mostrar detalhes
                $detalhesRow.show();
                $icon.addClass('rotated');
            }
            return;
        }
        
        // Criar nova linha de detalhes
        var colunas = $row.find('td').length;
        var loadingRow = '<tr id="' + detalhesId + '" class="noticia-detalhes">';
        loadingRow += '<td colspan="' + colunas + '">';
        loadingRow += '<div class="detalhes-container">';
        loadingRow += '<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Carregando detalhes...</div>';
        loadingRow += '</div>';
        loadingRow += '</td>';
        loadingRow += '</tr>';
        
        $row.after(loadingRow);
        $icon.addClass('rotated');
        
        console.log('Carregando detalhes para:', {id: id, tipo: tipo, url: window.host + '/cliente/relatorios/noticia/' + id + '/' + tipo});
        
        // Carregar detalhes via AJAX
        $.ajax({
            url: window.host + '/cliente/relatorios/noticia/' + id + '/' + tipo,
            type: 'GET',
            dataType: 'json',
            timeout: 3600000, // 1 hora de timeout
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Resposta recebida para notícia:', response);
                
                if (response.success && response.noticia) {
                    var noticia = response.noticia;
                    var detalhesHtml = '';
                    
                    detalhesHtml += '<div class="detalhes-container">';
                    detalhesHtml += '<div class="row">';
                    detalhesHtml += '<div class="col-md-12">';
                    
                    detalhesHtml += '<h6>Título:</h6>';
                    var tituloExibir = noticia.titulo_destacado || noticia.titulo || 'Sem título';
                    detalhesHtml += '<p>' + tituloExibir + '</p>';
                    
                    detalhesHtml += '<h6>Veículo:</h6>';
                    detalhesHtml += '<p>' + (noticia.veiculo || 'Sem veículo') + '</p>';
                    
                    detalhesHtml += '<h6>Data:</h6>';
                    detalhesHtml += '<p>' + (noticia.data_formatada || 'Sem data') + '</p>';
                    
                    if (window.mostrarAreas) {
                        detalhesHtml += '<h6>Área:</h6>';
                        detalhesHtml += '<p>' + (noticia.area || 'Sem área') + '</p>';
                    }
                    
                    if (window.mostrarSentimento) {
                        detalhesHtml += '<h6>Sentimento:</h6>';
                        detalhesHtml += '<p>' + obterSentimentoHtml(noticia.sentimento) + '</p>';
                    }
                    
                    // Palavras-chave encontradas
                    if (noticia.palavras_chave_encontradas && noticia.palavras_chave_encontradas.length > 0) {
                        detalhesHtml += '<h6>Palavras-chave encontradas:</h6>';
                        detalhesHtml += '<p>';
                        noticia.palavras_chave_encontradas.forEach(function(palavra) {
                            detalhesHtml += '<span class="badge-keyword mr-1">' + palavra + '</span>';
                        });
                        detalhesHtml += '</p>';
                    }
                    
                    // Campos específicos por tipo
                    if (tipo === 'web' && noticia.link) {
                        detalhesHtml += '<h6>Link:</h6>';
                        detalhesHtml += '<p><a href="' + noticia.link + '" target="_blank">Acessar notícia</a></p>';
                    }
                    
                    if ((tipo === 'tv' || tipo === 'radio') && (noticia.programa || noticia.horario)) {
                        detalhesHtml += '<h6>Programa:</h6>';
                        detalhesHtml += '<p>' + (noticia.programa || 'N/A') + '</p>';
                        detalhesHtml += '<h6>Horário:</h6>';
                        detalhesHtml += '<p>' + (noticia.horario || 'N/A') + '</p>';
                        detalhesHtml += '<h6>Duração:</h6>';
                        detalhesHtml += '<p>' + (noticia.duracao || 'N/A') + '</p>';
                    }
                    
                    if (window.mostrarRetornoMidia) {
                        detalhesHtml += '<h6>Valor:</h6>';
                        detalhesHtml += '<p>' + (noticia.valor > 0 ? 'R$ ' + Number(noticia.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : 'N/A') + '</p>';
                    }
                    
                    if (noticia.tags) {
                        detalhesHtml += '<h6>Tags:</h6>';
                        detalhesHtml += '<p>' + noticia.tags + '</p>';
                    }
                    
                    // Visualização de mídia
                    detalhesHtml += '<div class="row mt-3">';
                    detalhesHtml += '<div class="col-md-6">';
                    
                    switch(tipo) {
                        case 'radio':
                            detalhesHtml += '<h6><i class="fa fa-volume-up text-primary"></i> Áudio:</h6>';
                            if (noticia.midia) {
                                detalhesHtml += '<div class="midia-container p-2">';
                                detalhesHtml += '<audio width="100%" controls style="width: 100%;">';
                                detalhesHtml += '<source src="' + window.host + '/audio/noticia-radio/' + noticia.midia + '" type="audio/mpeg">';
                                detalhesHtml += 'Seu navegador não suporta a execução de áudios, faça o download para poder ouvir.';
                                detalhesHtml += '</audio>';
                                detalhesHtml += '</div>';
                            } else {
                                detalhesHtml += '<div class="midia-placeholder">';
                                detalhesHtml += '<i class="fa fa-volume-off"></i>';
                                detalhesHtml += '<span>Notícia sem áudio vinculado</span>';
                                detalhesHtml += '</div>';
                            }
                            break;
                            
                        case 'tv':
                            detalhesHtml += '<h6><i class="fa fa-tv text-primary"></i> Vídeo:</h6>';
                            if (noticia.midia) {
                                detalhesHtml += '<div class="midia-container p-2">';
                                detalhesHtml += '<video width="100%" height="240" controls>';
                                detalhesHtml += '<source src="' + window.host + '/video/noticia-tv/' + noticia.midia + '" type="video/mp4">';
                                detalhesHtml += '<source src="' + window.host + '/video/noticia-tv/' + noticia.midia.replace('.mp4', '.ogg') + '" type="video/ogg">';
                                detalhesHtml += 'Seu navegador não suporta a exibição de vídeos.';
                                detalhesHtml += '</video>';
                                detalhesHtml += '</div>';
                            } else {
                                detalhesHtml += '<div class="midia-placeholder">';
                                detalhesHtml += '<i class="fa fa-video-camera"></i>';
                                detalhesHtml += '<span>Notícia sem vídeo vinculado</span>';
                                detalhesHtml += '</div>';
                            }
                            break;
                            
                        case 'impresso':
                            detalhesHtml += '<h6><i class="fa fa-newspaper-o text-primary"></i> Imagem:</h6>';
                            if (noticia.midia) {
                                detalhesHtml += '<div class="midia-container">';
                                detalhesHtml += '<a href="' + window.host + '/noticia-impressa/imagem/download/' + noticia.id + '" target="_blank">';
                                detalhesHtml += '<img src="' + window.host + '/img/noticia-impressa/' + noticia.midia + '" alt="Imagem da notícia" class="img-fluid" style="width: 100%; height: auto; max-height: 300px; object-fit: contain;">';
                                detalhesHtml += '</a>';
                                detalhesHtml += '</div>';
                                detalhesHtml += '<small class="text-muted d-block mt-2"><i class="fa fa-info-circle"></i> Clique na imagem para visualizar em tamanho completo</small>';
                            } else {
                                detalhesHtml += '<div class="midia-placeholder">';
                                detalhesHtml += '<i class="fa fa-image"></i>';
                                detalhesHtml += '<span>Notícia sem print vinculado</span>';
                                detalhesHtml += '</div>';
                            }
                            break;
                            
                        case 'web':
                            detalhesHtml += '<h6><i class="fa fa-globe text-primary"></i> Imagem:</h6>';
                            // URL da imagem no S3 usando o padrão screenshot_noticia_{id}.jpg
                            var s3ImageUrl = 'https://docmidia-files.s3.amazonaws.com/screenshot/screenshot_noticia_' + noticia.id + '.jpg';
                            detalhesHtml += '<div class="midia-container">';
                            detalhesHtml += '<a href="' + s3ImageUrl + '" target="_blank">';
                            detalhesHtml += '<img src="' + s3ImageUrl + '" alt="Screenshot da notícia" class="img-fluid" style="width: 100%; height: auto; max-height: 300px; object-fit: contain;" onerror="this.parentElement.parentElement.innerHTML=\'<div class=&quot;midia-placeholder&quot;><i class=&quot;fa fa-image&quot;></i><span>Screenshot não disponível</span></div>\'">';
                            detalhesHtml += '</a>';
                            detalhesHtml += '</div>';
                            detalhesHtml += '<small class="text-muted d-block mt-2"><i class="fa fa-info-circle"></i> Clique na imagem para visualizar em tamanho completo (S3)</small>';
                            break;
                    }
                    
                    detalhesHtml += '</div>';
                    detalhesHtml += '<div class="col-md-6">';
                    detalhesHtml += '<h6>Conteúdo:</h6>';
                    detalhesHtml += '<div class="detalhes-texto">';
                    var textoExibir = noticia.texto_destacado || noticia.sinopse_destacado || noticia.texto || noticia.sinopse || 'Sem conteúdo';
                    detalhesHtml += textoExibir.replace(/\n/g, '<br>');
                    detalhesHtml += '</div>';
                    detalhesHtml += '</div>';
                    detalhesHtml += '</div>';
                    
                    detalhesHtml += '</div>';
                    detalhesHtml += '</div>';
                    detalhesHtml += '</div>';
                    
                    $('#' + detalhesId + ' td').html(detalhesHtml);
                    
                } else {
                    $('#' + detalhesId + ' td').html('<div class="detalhes-container"><div class="alert alert-danger">' + (response.message || 'Erro ao carregar detalhes') + '</div></div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar notícia:', {
                    status: status,
                    error: error,
                    xhr: xhr.responseText
                });
                
                var errorMessage = '';
                if (xhr.status === 404) {
                    errorMessage = 'Notícia não encontrada.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Erro interno do servidor.';
                } else if (status === 'timeout') {
                    errorMessage = 'Tempo limite excedido.';
                } else {
                    errorMessage = 'Erro ao carregar detalhes. Tente novamente.';
                }
                
                $('#' + detalhesId + ' td').html('<div class="detalhes-container"><div class="alert alert-danger">' + errorMessage + '</div></div>');
            }
        });
    }



    function obterSentimentoHtml(sentimento) {
        try {
            var sentimentoInt = parseInt(sentimento);
            
            if (sentimentoInt === 1) {
                return '<span class="text-success"><i class="fa fa-smile-o text-success"></i> Positivo</span>';
            } else if (sentimentoInt === -1) {
                return '<span class="text-danger"><i class="fa fa-frown-o text-danger"></i> Negativo</span>';
            } else if (sentimentoInt === 0) {
                return '<span class="text-warning"><i class="fa fa-ban text-warning"></i> Neutro</span>';
            } else {
                return '<span class="text-secondary"><i class="fa fa-question text-secondary"></i> Não definido</span>';
            }
        } catch (e) {
            console.error('Erro ao processar sentimento:', e);
            return '<span class="text-secondary"><i class="fa fa-question text-secondary"></i> Não definido</span>';
        }
    }

    function obterSentimentoDropdown(sentimento, noticiaId, tipo) {
        try {
            var sentimentoInt = parseInt(sentimento) || 0;
            var selectId = 'sentimento_' + tipo + '_' + noticiaId;
            var containerId = 'container_' + selectId;
            
            // Container inline para o select e o ícone de loading
            var html = '<div id="' + containerId + '" class="sentimento-container">';
            
            html += '<select class="form-control form-control-sm sentimento-select" ';
            html += 'id="' + selectId + '" ';
            html += 'data-noticia-id="' + noticiaId + '" ';
            html += 'data-tipo="' + tipo + '" ';
            html += 'onchange="alterarSentimentoNoticia(this)" ';
            html += 'style="width: 130px; font-size: 12px;">';
            
            // Opção Positivo
            html += '<option value="1"' + (sentimentoInt === 1 ? ' selected' : '') + '>';
            html += '😊 Positivo</option>';
            
            // Opção Neutro
            html += '<option value="0"' + (sentimentoInt === 0 ? ' selected' : '') + '>';
            html += '😐 Neutro</option>';
            
            // Opção Negativo
            html += '<option value="-1"' + (sentimentoInt === -1 ? ' selected' : '') + '>';
            html += '😞 Negativo</option>';
            
            html += '</select>';
            html += '</div>'; // Fechar container
            
            return html;
        } catch (e) {
            console.error('Erro ao gerar dropdown de sentimento:', e);
            return obterSentimentoHtml(sentimento);
        }
    }

    // Gerar relatório web específico com imagens (função global para onclick)
    // NOTA: Esta função só é chamada se o cliente tiver fl_print = true
    function gerarRelatorioWebAba() {
        // Pega apenas as notícias web selecionadas
        var noticiasWebSelecionadas = [];
        $('.selecionar-noticia[data-tipo="web"]:checked').each(function() {
            noticiasWebSelecionadas.push($(this).data('id'));
        });
        
        if (noticiasWebSelecionadas.length === 0) {
            alert('Por favor, selecione ao menos uma notícia Web para gerar o relatório.');
            return;
        }

        var formData = {
            data_inicio: converterDataParaISO($('#dt_inicial').val()),
            data_fim: converterDataParaISO($('#dt_final').val()),
            ids_web: noticiasWebSelecionadas
        };
        
        // Adicionar token CSRF
        formData._token = $('meta[name="csrf-token"]').attr('content');

        var cliente = $("#id_cliente").val();
        if(cliente){
            formData.cliente = cliente;
        }

        // Mostrar loading no botão da aba
        $('#btnGerarRelatorioWebAba').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Gerando...');

        $.ajax({
            url: window.host + '/cliente/relatorios/gerar-pdf-web',
            type: 'POST',
            data: formData,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Response:', response);
                if (response.success) {
                    console.log('Download URL:', response.download_url);
                    
                    // Download direto usando a nova rota que força download
                    var downloadUrl = response.download_url || (window.host + '/cliente/relatorios/download/' + response.arquivo);
                    var fileName = response.arquivo || 'relatorio-web.pdf';
                    console.log('Iniciando download de:', downloadUrl);
                    console.log('Nome do arquivo:', fileName);
                    
                    // Método simples e eficaz: redirecionamento da janela
                    window.location.href = downloadUrl;
                } else {
                    alert('Erro ao gerar relatório Web: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao gerar relatório Web:', error);
                
                var errorMessage = '';
                if (xhr.status === 404) {
                    errorMessage = 'Rota não encontrada. Verifique se o sistema está configurado corretamente.';
                } else if (xhr.status === 401) {
                    errorMessage = 'Acesso negado. Faça login novamente.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Erro interno do servidor. Tente novamente mais tarde.';
                } else {
                    errorMessage = 'Erro ao gerar relatório Web. Tente novamente.';
                }
                
                alert(errorMessage);
            },
            complete: function() {
                $('#btnGerarRelatorioWebAba').prop('disabled', false).html('<i class="fa fa-globe"></i> Gerar Relatório Web com Imagens');
            }
        });
    }

    // Gerar relatório impresso específico com imagens (função global para onclick)
    // NOTA: Esta função só é chamada se o cliente tiver fl_print = true
    function gerarRelatorioImpressoAba() {
        // Pega apenas as notícias impressas selecionadas
        var noticiasImpressoSelecionadas = [];
        $('.selecionar-noticia[data-tipo="impresso"]:checked').each(function() {
            noticiasImpressoSelecionadas.push($(this).data('id'));
        });
        
        if (noticiasImpressoSelecionadas.length === 0) {
            alert('Por favor, selecione ao menos uma notícia Impressa para gerar o relatório.');
            return;
        }

        var formData = {
            data_inicio: converterDataParaISO($('#dt_inicial').val()),
            data_fim: converterDataParaISO($('#dt_final').val()),
            ids_impresso: noticiasImpressoSelecionadas
        };
        
        // Adicionar token CSRF
        formData._token = $('meta[name="csrf-token"]').attr('content');

        var cliente = $("#id_cliente").val();
        if(cliente){
            formData.cliente = cliente;
        }

        // Mostrar loading no botão da aba
        $('#btnGerarRelatorioImpressoAba').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Gerando...');

        var host =  $('meta[name="base-url"]').attr('content');

        $.ajax({
            url: host + '/cliente/relatorios/gerar-pdf-impresso',
            type: 'POST',
            data: formData,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Response:', response);
                if (response.success) {
                    console.log('Download URL:', response.download_url);
                    
                    // Download direto usando a nova rota que força download
                    var downloadUrl = response.download_url || (host + '/cliente/relatorios/download/' + response.arquivo);
                    var fileName = response.arquivo || 'relatorio-impresso.pdf';
                    console.log('Iniciando download de:', downloadUrl);
                    console.log('Nome do arquivo:', fileName);
                    
                    // Método simples e eficaz: redirecionamento da janela
                    window.location.href = downloadUrl;
                } else {
                    alert('Erro ao gerar relatório Impresso: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao gerar relatório Impresso:', error);
                
                var errorMessage = '';
                if (xhr.status === 404) {
                    errorMessage = 'Rota não encontrada. Verifique se o sistema está configurado corretamente.';
                } else if (xhr.status === 401) {
                    errorMessage = 'Acesso negado. Faça login novamente.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Erro interno do servidor. Tente novamente mais tarde.';
                } else {
                    errorMessage = 'Erro ao gerar relatório Impresso. Tente novamente.';
                }
                
                alert(errorMessage);
            },
            complete: function() {
                $('#btnGerarRelatorioImpressoAba').prop('disabled', false).html('<i class="fa fa-newspaper-o"></i> Gerar Relatório Impresso com Imagens');
            }
        });
    }

    // Função auxiliar para converter data (também precisa estar no escopo global)
    function converterDataParaISO(data) {
        if (!data) return '';
        var partes = data.split('/');
        if (partes.length === 3) {
            return partes[2] + '-' + partes[1] + '-' + partes[0];
        }
        return data;
    }

    // Função global para toggle de tag no filtro
    function toggleTagFilter(tag) {
        var tagElement = $('.tag-filter-item[data-tag="' + tag + '"]');
        
        if (tagElement.hasClass('selected')) {
            tagElement.removeClass('selected');
        } else {
            tagElement.addClass('selected');
        }
        
        console.log('Tag "' + tag + '" toggled. Tags selecionadas:', getSelectedTags());
    }

    // Função para obter tags selecionadas
    function getSelectedTags() {
        var selectedTags = [];
        $('.tag-filter-item.selected').each(function() {
            selectedTags.push($(this).data('tag'));
        });
        return selectedTags;
    }

    // Função global para remover tag (chamada pelos onclick dos elementos HTML)
    function removerTagNoticiaSelecionadas(tag) {
        if (!confirm('Deseja remover a tag "' + tag + '" das notícias selecionadas?')) {
            return;
        }

        var noticiasSelecionadas = obterNoticiasSelecionadas();
        
        $.ajax({
            url: window.host + '/cliente/tags/remover',
            type: 'POST',
            data: {
                tag: tag,
                ids_web: noticiasSelecionadas.web,
                ids_tv: noticiasSelecionadas.tv,
                ids_radio: noticiasSelecionadas.radio,
                ids_impresso: noticiasSelecionadas.impresso,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            timeout: 3600000, // 1 hora
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Resposta ao remover tag:', response);
                
                if (response.success) {
                    // Recarregar as tags no modal
                    if ($('#modalGerenciarTags').hasClass('show')) {
                        carregarTagsNoticiaSelecionadas();
                    }
                    carregarTagsDisponiveis(); // Atualizar lista de filtros
                    
                    // Mostrar notificação de sucesso
                    console.log('Tag "' + tag + '" removida com sucesso de ' + response.noticias_afetadas + ' notícias.');
                } else {
                    alert('Erro ao remover tag: ' + (response.message || 'Erro desconhecido'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao remover tag:', {
                    status: status,
                    error: error,
                    xhr: xhr.responseText
                });
                
                alert('Erro ao remover tag. Tente novamente.');
            }
        });
    }

    // Função global para alterar sentimento de uma notícia
    function alterarSentimentoNoticia(selectElement) {
        var $select = $(selectElement);
        var noticiaId = $select.data('noticia-id');
        var tipo = $select.data('tipo');
        var novoSentimento = $select.val();
        var valorOriginal = $select.data('valor-original') || $select.val();
        
        // Armazenar valor original na primeira alteração
        if (!$select.data('valor-original')) {
            $select.data('valor-original', valorOriginal);
        }
        
        // Desabilitar o select durante a requisição
        $select.prop('disabled', true);
        
        // Adicionar indicador visual de carregamento ao lado direito do dropdown
        var $container = $select.closest('.sentimento-container');
        var $loadingIcon = $('<i class="fa fa-spinner fa-spin sentimento-loading"></i>');
        $container.append($loadingIcon);
        
        $.ajax({
            url: window.host + '/cliente/relatorios/alterar-sentimento',
            type: 'POST',
            data: {
                noticia_id: noticiaId,
                tipo: tipo,
                sentimento: novoSentimento,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            timeout: 3600000, // 1 hora
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Resposta ao alterar sentimento:', response);
                
                if (response.success) {
                    // Mostrar feedback visual de sucesso
                    $select.addClass('border-success');
                    setTimeout(function() {
                        $select.removeClass('border-success');
                    }, 2000);
                    
                    // Atualizar valor original
                    $select.data('valor-original', novoSentimento);
                    
                    // Log de sucesso
                    var sentimentoTexto = getSentimentoTexto(novoSentimento);
                    console.log('Sentimento da notícia ' + tipo + ' #' + noticiaId + ' alterado para: ' + sentimentoTexto);
                    
                } else {
                    // Reverter para valor original em caso de erro
                    $select.val(valorOriginal);
                    alert('Erro ao alterar sentimento: ' + (response.message || 'Erro desconhecido'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao alterar sentimento:', {
                    status: status,
                    error: error,
                    xhr: xhr.responseText
                });
                
                // Reverter para valor original em caso de erro
                $select.val(valorOriginal);
                
                var errorMessage = '';
                if (xhr.status === 404) {
                    errorMessage = 'Rota não encontrada. Verifique se o sistema está configurado corretamente.';
                } else if (xhr.status === 401) {
                    errorMessage = 'Acesso negado. Faça login novamente.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Erro interno do servidor. Tente novamente mais tarde.';
                } else if (status === 'timeout') {
                    errorMessage = 'Tempo limite excedido. Tente novamente.';
                } else {
                    errorMessage = 'Erro ao alterar sentimento. Tente novamente.';
                }
                
                alert(errorMessage);
            },
            complete: function() {
                // Reabilitar o select e remover indicador de carregamento
                $select.prop('disabled', false);
                $select.closest('.sentimento-container').find('.sentimento-loading').remove();
            }
        });
    }

    // Função auxiliar para obter texto do sentimento
    function getSentimentoTexto(valor) {
        switch(parseInt(valor)) {
            case 1: return 'Positivo';
            case 0: return 'Neutro';
            case -1: return 'Negativo';
            default: return 'Não definido';
        }
    }

    // ===== FUNÇÕES DE FONTES/EMISSORAS/PROGRAMAS =====

    // Carregar todas as fontes disponíveis
    function carregarFontesDisponiveis() {
        console.log('Carregando fontes disponíveis...');
        
        // Carregar fontes web
        carregarFontesWeb();
        
        // Carregar fontes impresso
        carregarFontesImpresso();
        
        // Carregar fontes TV
        carregarFontesTv();
        
        // Carregar fontes Rádio
        carregarFontesRadio();
    }

    // Carregar fontes Web
    function carregarFontesWeb() {
        $.ajax({
            url: window.host + '/cliente/fontes/web',
            type: 'GET',
            dataType: 'json',
            timeout: 3600000, // 1 hora
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Fontes web carregadas:', response);
                
                try {
                    var fontesHtml = '';
                    
                    // Verificar se a resposta tem estrutura de erro
                    if (response && response.success === false) {
                        console.error('Erro do servidor:', response.message);
                        fontesHtml = '<div class="text-danger text-center"><i class="fa fa-exclamation-triangle"></i> ' + response.message + '</div>';
                    }
                    // Verificar se é array de fontes
                    else if (response && Array.isArray(response) && response.length > 0) {
                        response.forEach(function(fonte, index) {
                            try {
                                // Validar dados da fonte com valores padrão
                                var fonteId = fonte.id || 0;
                                var fonteNome = fonte.nome || 'Fonte sem nome';
                                var fonteTotalNoticias = fonte.total_noticias || 0;
                                
                                // Escapar aspas simples no nome para o onclick
                                var nomeEscapado = fonteNome.replace(/'/g, "\\'");
                                
                                fontesHtml += '<span class="fonte-filter-item" data-tipo="web" data-fonte-id="' + fonteId + '" data-nome="' + fonteNome.toLowerCase() + '" onclick="toggleFonteFilter(\'web\', ' + fonteId + ', \'' + nomeEscapado + '\')">';
                                fontesHtml += '<i class="fa fa-globe mr-1"></i>' + fonteNome;
                                if (fonteTotalNoticias > 0) {
                                    fontesHtml += ' <small class="badge badge-light ml-1">(' + fonteTotalNoticias + ')</small>';
                                }
                                fontesHtml += '</span>';
                            } catch (itemError) {
                                console.error('Erro ao processar fonte no índice ' + index + ':', itemError, fonte);
                            }
                        });
                    }
                    // Resposta vazia ou não é array
                    else {
                        console.warn('Resposta inesperada para fontes web:', response);
                        fontesHtml = '<div class="text-muted text-center">Nenhuma fonte web com notícias encontrada para este cliente</div>';
                    }
                    
                    $('#fontes-web-container').html(fontesHtml);
                    
                } catch (processingError) {
                    console.error('Erro no processamento da resposta de fontes web:', processingError);
                    $('#fontes-web-container').html('<div class="text-danger text-center"><i class="fa fa-exclamation-triangle"></i> Erro no processamento dos dados</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar fontes web:', {
                    status: status,
                    error: error,
                    xhr: xhr.responseText
                });
                
                var errorMsg = 'Erro ao carregar fontes web';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                $('#fontes-web-container').html(
                    '<div class="text-danger text-center">' + 
                    '<i class="fa fa-exclamation-triangle"></i> ' + errorMsg + 
                    '<br><small>Verifique o console do navegador para mais detalhes</small>' +
                    '</div>'
                );
            }
        });
    }

    // Carregar fontes Impresso
    function carregarFontesImpresso() {
        $.ajax({
            url: window.host + '/cliente/fontes/impresso',
            type: 'GET',
            dataType: 'json',
            timeout: 3600000, // 1 hora
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Fontes impresso carregadas:', response);
                
                var fontesHtml = '';
                
                if (response && Array.isArray(response) && response.length > 0) {
                    response.forEach(function(fonte) {
                        fontesHtml += '<span class="fonte-filter-item" data-tipo="impresso" data-fonte-id="' + fonte.id + '" data-nome="' + fonte.nome.toLowerCase() + '" onclick="toggleFonteFilter(\'impresso\', ' + fonte.id + ', \'' + fonte.nome + '\')">';
                        fontesHtml += '<i class="fa fa-newspaper-o mr-1"></i>' + fonte.nome;
                        if (fonte.total_noticias) {
                            fontesHtml += ' <small class="badge badge-light ml-1">(' + fonte.total_noticias + ')</small>';
                        }
                        fontesHtml += '</span>';
                    });
                } else {
                    fontesHtml = '<div class="text-muted text-center">Nenhum jornal com notícias encontrado para este cliente</div>';
                }
                
                $('#fontes-impresso-container').html(fontesHtml);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar fontes impresso:', {
                    status: status,
                    error: error,
                    xhr: xhr.responseText
                });
                
                $('#fontes-impresso-container').html('<div class="text-danger text-center">Erro ao carregar jornais</div>');
            }
        });
    }

    // Carregar fontes TV
    function carregarFontesTv() {
        $.ajax({
            url: window.host + '/cliente/fontes/tv',
            type: 'GET',
            dataType: 'json',
            timeout: 3600000, // 1 hora
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Fontes TV carregadas:', response);
                
                var fontesHtml = '';
                
                if (response && Array.isArray(response) && response.length > 0) {
                    response.forEach(function(fonte) {
                        var tipoClass = fonte.tipo === 'emissora' ? 'emissora' : 'programa';
                        var icone = fonte.tipo === 'emissora' ? 'fa-television' : 'fa-play';
                        
                        fontesHtml += '<span class="fonte-filter-item ' + tipoClass + '" data-tipo="tv" data-subtipo="' + fonte.tipo + '" data-fonte-id="' + fonte.id + '" data-nome="' + fonte.nome.toLowerCase() + '" onclick="toggleFonteFilter(\'tv\', ' + fonte.id + ', \'' + fonte.nome + '\', \'' + fonte.tipo + '\')">';
                        fontesHtml += '<i class="fa ' + icone + ' mr-1"></i>' + fonte.nome;
                        if (fonte.total_noticias) {
                            fontesHtml += ' <small class="badge badge-light ml-1">(' + fonte.total_noticias + ')</small>';
                        }
                        fontesHtml += '</span>';
                    });
                } else {
                    fontesHtml = '<div class="text-muted text-center">Nenhuma emissora/programa de TV com notícias encontrado para este cliente</div>';
                }
                
                $('#fontes-tv-container').html(fontesHtml);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar fontes TV:', {
                    status: status,
                    error: error,
                    xhr: xhr.responseText
                });
                
                $('#fontes-tv-container').html('<div class="text-danger text-center">Erro ao carregar fontes TV</div>');
            }
        });
    }

    // Carregar fontes Rádio
    function carregarFontesRadio() {
        $.ajax({
            url: window.host + '/cliente/fontes/radio',
            type: 'GET',
            dataType: 'json',
            timeout: 3600000, // 1 hora
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Fontes rádio carregadas:', response);
                
                var fontesHtml = '';
                
                if (response && Array.isArray(response) && response.length > 0) {
                    response.forEach(function(fonte) {
                        var tipoClass = fonte.tipo === 'emissora' ? 'emissora' : 'programa';
                        var icone = fonte.tipo === 'emissora' ? 'fa-volume-up' : 'fa-play';
                        
                        fontesHtml += '<span class="fonte-filter-item ' + tipoClass + '" data-tipo="radio" data-subtipo="' + fonte.tipo + '" data-fonte-id="' + fonte.id + '" data-nome="' + fonte.nome.toLowerCase() + '" onclick="toggleFonteFilter(\'radio\', ' + fonte.id + ', \'' + fonte.nome + '\', \'' + fonte.tipo + '\')">';
                        fontesHtml += '<i class="fa ' + icone + ' mr-1"></i>' + fonte.nome;
                        if (fonte.total_noticias) {
                            fontesHtml += ' <small class="badge badge-light ml-1">(' + fonte.total_noticias + ')</small>';
                        }
                        fontesHtml += '</span>';
                    });
                } else {
                    fontesHtml = '<div class="text-muted text-center">Nenhuma emissora/programa de rádio com notícias encontrado para este cliente</div>';
                }
                
                $('#fontes-radio-container').html(fontesHtml);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar fontes rádio:', {
                    status: status,
                    error: error,
                    xhr: xhr.responseText
                });
                
                $('#fontes-radio-container').html('<div class="text-danger text-center">Erro ao carregar fontes rádio</div>');
            }
        });
    }

    // Função global para toggle de fonte no filtro
    function toggleFonteFilter(tipo, fonteId, fonteNome, subtipo) {
        var seletor = '.fonte-filter-item[data-tipo="' + tipo + '"][data-fonte-id="' + fonteId + '"]';
        var fonteElement = $(seletor);
        
        if (fonteElement.hasClass('selected')) {
            fonteElement.removeClass('selected');
        } else {
            fonteElement.addClass('selected');
        }
        
        console.log('Fonte "' + fonteNome + '" (' + tipo + (subtipo ? '/' + subtipo : '') + ') toggled. Fontes selecionadas:', getSelectedFontes());
    }

    // Função para obter fontes selecionadas
    function getSelectedFontes() {
        var selectedFontes = {
            web: [],
            impresso: [],
            tv: {
                emissoras: [],
                programas: []
            },
            radio: {
                emissoras: [],
                programas: []
            }
        };
        
        $('.fonte-filter-item.selected').each(function() {
            var $elemento = $(this);
            var tipo = $elemento.data('tipo');
            var fonteId = $elemento.data('fonte-id');
            var subtipo = $elemento.data('subtipo');
            
            if (tipo === 'web') {
                selectedFontes.web.push(fonteId);
            } else if (tipo === 'impresso') {
                selectedFontes.impresso.push(fonteId);
            } else if (tipo === 'tv') {
                if (subtipo === 'emissora') {
                    selectedFontes.tv.emissoras.push(fonteId);
                } else if (subtipo === 'programa') {
                    selectedFontes.tv.programas.push(fonteId);
                }
            } else if (tipo === 'radio') {
                if (subtipo === 'emissora') {
                    selectedFontes.radio.emissoras.push(fonteId);
                } else if (subtipo === 'programa') {
                    selectedFontes.radio.programas.push(fonteId);
                }
            }
        });
        
        return selectedFontes;
    }

    // Função para filtrar fontes em tempo real
    function filtrarFontes(input) {
        var termo = input.value.toLowerCase().trim();
        var targetContainer = input.dataset.target;
        var $container = $('#' + targetContainer);
        
        if (!termo) {
            // Se não há termo de busca, mostrar todas as fontes
            $container.find('.fonte-filter-item').show();
            return;
        }
        
        $container.find('.fonte-filter-item').each(function() {
            var $fonte = $(this);
            var nome = $fonte.data('nome') || '';
            
            if (nome.includes(termo)) {
                $fonte.show();
            } else {
                $fonte.hide();
            }
        });
    }

    // Função para limpar todas as buscas de fontes
    function limparBuscasFontes() {
        $('.fonte-search').val('');
        $('.fonte-filter-item').show();
    }




</script>
@endsection