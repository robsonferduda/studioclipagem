@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-file-pdf-o ml-3"></i> Relatórios
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('cliente/noticias') }}" class="btn btn-primary pull-right" style="margin-right: 12px;">
                        <i class="fa fa-plus"></i> Gerar Novo Relatório
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            
            <!-- Filtros -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Data Inicial</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Data Final</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label><br>
                        <button type="button" class="btn btn-primary" id="btnFiltrar">
                            <i class="fa fa-search"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-warning" id="btnLimpar">
                            <i class="fa fa-refresh"></i> Limpar
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Itens por página</label>
                        <select class="form-control" id="per_page">
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Loading -->
            <div id="loading" class="text-center" style="display: none;">
                <i class="fa fa-spinner fa-spin fa-3x"></i>
                <p class="mt-2">Carregando relatórios...</p>
            </div>

            <!-- Lista de Relatórios -->
            <div id="relatorios-container">
                <div id="lista-relatorios">
                    <!-- Conteúdo será carregado via JavaScript -->
                </div>

                <!-- Paginação -->
                <div id="pagination-container" class="text-center mt-3">
                    <!-- Paginação será carregada via JavaScript -->
                </div>
            </div>

            <!-- Mensagem quando não há relatórios -->
            <div id="no-relatorios" class="text-center py-5" style="display: none;">
                <i class="fa fa-file-pdf-o fa-5x text-muted mb-3"></i>
                <h4 class="text-muted">Nenhum relatório encontrado</h4>
                <p class="text-muted">
                    Não há relatórios salvos para os filtros selecionados.<br>
                    <a href="{{ url('cliente/noticias') }}" class="btn btn-primary mt-2">
                        <i class="fa fa-plus"></i> Gerar seu primeiro relatório
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Preview do Relatório -->
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-labelledby="modalPreviewLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalPreviewLabel">Detalhes do Relatório</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="preview-content">
                    <!-- Conteúdo será carregado via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <a href="#" class="btn btn-primary" id="btnDownloadModal" target="_blank">
                    <i class="fa fa-download"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let currentPage = 1;
    let totalPages = 1;

    // Carrega relatórios na inicialização
    carregarRelatorios();

    // Event listeners
    $('#btnFiltrar').click(function() {
        currentPage = 1;
        carregarRelatorios();
    });

    $('#btnLimpar').click(function() {
        $('#data_inicio').val('');
        $('#data_fim').val('');
        $('#per_page').val('15');
        currentPage = 1;
        carregarRelatorios();
    });

    $('#per_page').change(function() {
        currentPage = 1;
        carregarRelatorios();
    });

    // Função para carregar relatórios
    function carregarRelatorios() {
        $('#loading').show();
        $('#relatorios-container').hide();
        $('#no-relatorios').hide();

        const params = {
            page: currentPage,
            per_page: $('#per_page').val(),
            data_inicio: $('#data_inicio').val(),
            data_fim: $('#data_fim').val()
        };

        $.ajax({
            url: '{{ url("cliente/relatorios") }}',
            method: 'GET',
            data: params,
            success: function(response) {
                $('#loading').hide();
                
                if (response.success && response.data.length > 0) {
                    renderizarRelatorios(response.data);
                    renderizarPaginacao(response.pagination);
                    $('#relatorios-container').show();
                } else {
                    $('#no-relatorios').show();
                }
            },
            error: function(xhr) {
                $('#loading').hide();
                console.error('Erro ao carregar relatórios:', xhr);
                
                let message = 'Erro ao carregar relatórios.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                alert(message);
            }
        });
    }

    // Função para renderizar lista de relatórios
    function renderizarRelatorios(relatorios) {
        let html = '<div class="row">';
        
        relatorios.forEach(function(relatorio) {
            const tiposMidia = Object.entries(relatorio.tipos_midia || {})
                .filter(([tipo, ativo]) => ativo)
                .map(([tipo]) => {
                    const icons = {
                        'web': '<i class="fa fa-globe text-primary"></i>',
                        'impresso': '<i class="fa fa-newspaper-o text-warning"></i>',
                        'tv': '<i class="fa fa-tv text-danger"></i>',
                        'radio': '<i class="fa fa-volume-up text-success"></i>'
                    };
                    return icons[tipo] || '';
                }).join(' ');

            const valorTotal = relatorio.valor_total || 'N/A';
            const tamanhoArquivo = relatorio.tamanho_arquivo ? 
                formatarTamanhoArquivo(relatorio.tamanho_arquivo) : 'N/A';

            html += `
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 relatorio-card" data-id="${relatorio.id}">
                        <div class="card-header pb-2">
                            <h6 class="card-title mb-1">
                                <i class="fa fa-file-pdf-o text-danger"></i>
                                ${relatorio.titulo}
                            </h6>
                            <small class="text-muted">
                                <i class="fa fa-calendar"></i> ${relatorio.data_criacao}
                            </small>
                        </div>
                        <div class="card-body pb-2">
                            <p class="card-text small text-muted mb-2">
                                ${relatorio.descricao}
                            </p>
                            
                            <div class="row text-center mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Período</small><br>
                                    <strong>${formatarPeriodo(relatorio.data_inicio, relatorio.data_fim)}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Notícias</small><br>
                                    <strong>${relatorio.total_noticias}</strong>
                                </div>
                            </div>

                            <div class="text-center mb-2">
                                <small class="text-muted">Tipos de Mídia</small><br>
                                ${tiposMidia || '<span class="text-muted">N/A</span>'}
                            </div>

                            ${relatorio.valor_total ? `
                                <div class="text-center mb-2">
                                    <small class="text-muted">Valor Total</small><br>
                                    <strong class="text-success">${valorTotal}</strong>
                                </div>
                            ` : ''}

                            <div class="row text-center">
                                <div class="col-12">
                                    <small class="text-muted">
                                        <i class="fa fa-hdd-o"></i> ${tamanhoArquivo}
                                        ${relatorio.filtros_aplicados.termo_busca ? 
                                            `<br><i class="fa fa-search"></i> "${relatorio.filtros_aplicados.termo_busca}"` : ''}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group btn-group-sm w-100" role="group">
                                <button type="button" class="btn btn-outline-info btn-preview" data-relatorio='${JSON.stringify(relatorio)}'>
                                    <i class="fa fa-eye"></i> Detalhes
                                </button>
                                <a href="${relatorio.url_s3}" target="_blank" class="btn btn-outline-success">
                                    <i class="fa fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        $('#lista-relatorios').html(html);

        // Event listener para preview
        $('.btn-preview').click(function() {
            const relatorio = JSON.parse($(this).attr('data-relatorio'));
            mostrarPreview(relatorio);
        });

        // Hover effect nos cards
        $('.relatorio-card').hover(
            function() { $(this).addClass('shadow'); },
            function() { $(this).removeClass('shadow'); }
        );
    }

    // Função para renderizar paginação
    function renderizarPaginacao(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            $('#pagination-container').html('');
            return;
        }

        currentPage = pagination.current_page;
        totalPages = pagination.last_page;

        let html = '<nav aria-label="Paginação de relatórios"><ul class="pagination justify-content-center">';
        
        // Botão anterior
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">
                <i class="fa fa-angle-left"></i>
            </a>
        </li>`;

        // Páginas
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
        }

        // Botão próximo
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">
                <i class="fa fa-angle-right"></i>
            </a>
        </li>`;

        html += '</ul></nav>';

        // Info da paginação
        html += `<p class="text-muted text-center mt-2">
            Mostrando ${pagination.from} a ${pagination.to} de ${pagination.total} relatórios
        </p>`;

        $('#pagination-container').html(html);

        // Event listener para paginação
        $('.page-link').click(function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            if (page && page !== currentPage && page >= 1 && page <= totalPages) {
                currentPage = page;
                carregarRelatorios();
            }
        });
    }

    // Função para mostrar preview do relatório
    function mostrarPreview(relatorio) {
        const filtros = relatorio.filtros_aplicados;
        
        let html = `
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fa fa-info-circle text-info"></i> Informações Gerais</h5>
                    <table class="table table-sm">
                        <tr><td><strong>Título:</strong></td><td>${relatorio.titulo}</td></tr>
                        <tr><td><strong>Criado em:</strong></td><td>${relatorio.data_criacao}</td></tr>
                        <tr><td><strong>Período:</strong></td><td>${formatarPeriodo(relatorio.data_inicio, relatorio.data_fim)}</td></tr>
                        <tr><td><strong>Total de Notícias:</strong></td><td>${relatorio.total_noticias}</td></tr>
                        <tr><td><strong>Tamanho:</strong></td><td>${formatarTamanhoArquivo(relatorio.tamanho_arquivo)}</td></tr>
                        ${relatorio.valor_total ? `<tr><td><strong>Valor Total:</strong></td><td class="text-success">${relatorio.valor_total}</td></tr>` : ''}
                    </table>
                </div>
                <div class="col-md-6">
                    <h5><i class="fa fa-filter text-warning"></i> Filtros Aplicados</h5>
                    <table class="table table-sm">
                        <tr><td><strong>Tipo de Data:</strong></td><td>${filtros.tipo_filtro_data || 'N/A'}</td></tr>
                        <tr><td><strong>Termo de Busca:</strong></td><td>${filtros.termo_busca || 'Nenhum'}</td></tr>
                        <tr><td><strong>Tags:</strong></td><td>${filtros.tags_count > 0 ? filtros.tags_count + ' selecionadas' : 'Nenhuma'}</td></tr>
                        <tr><td><strong>Fontes:</strong></td><td>${filtros.fontes_count > 0 ? filtros.fontes_count + ' selecionadas' : 'Nenhuma'}</td></tr>
                    </table>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5><i class="fa fa-list text-primary"></i> Tipos de Mídia Incluídos</h5>
                    <div class="text-center">
        `;

        Object.entries(relatorio.tipos_midia || {}).forEach(([tipo, ativo]) => {
            if (ativo) {
                const labels = {
                    'web': '<span class="badge badge-primary"><i class="fa fa-globe"></i> Web</span>',
                    'impresso': '<span class="badge badge-warning"><i class="fa fa-newspaper-o"></i> Impressos</span>',
                    'tv': '<span class="badge badge-danger"><i class="fa fa-tv"></i> TV</span>',
                    'radio': '<span class="badge badge-success"><i class="fa fa-volume-up"></i> Rádio</span>'
                };
                html += labels[tipo] + ' ';
            }
        });

        html += `
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5><i class="fa fa-link text-info"></i> Links</h5>
                    <div class="btn-group">
                        <a href="${relatorio.url_s3}" target="_blank" class="btn btn-success">
                            <i class="fa fa-cloud-download"></i> Download direto (S3)
                        </a>
                        <a href="${relatorio.nome_arquivo}" target="_blank" class="btn btn-primary">
                            <i class="fa fa-download"></i> Download local
                        </a>
                    </div>
                </div>
            </div>
        `;

        $('#preview-content').html(html);
        $('#btnDownloadModal').attr('href', relatorio.url_s3);
        $('#modalPreview').modal('show');
    }

    // Funções auxiliares
    function formatarPeriodo(inicio, fim) {
        if (!inicio || !fim) return 'N/A';
        if (inicio === fim) return inicio;
        return `${inicio} a ${fim}`;
    }

    function formatarTamanhoArquivo(bytes) {
        if (!bytes) return 'N/A';
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
    }
});
</script>

<style>
.relatorio-card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.relatorio-card:hover {
    transform: translateY(-2px);
    border-color: #007bff;
}

.relatorio-card .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.relatorio-card .card-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.badge {
    margin-right: 5px;
}

#loading {
    padding: 50px 0;
}

.shadow {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
