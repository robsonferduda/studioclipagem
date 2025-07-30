@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-file-pdf-o ml-3"></i> Relatórios 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Gerar
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
                timeout: 10000,
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

        // Pesquisar notícias
        function pesquisarNoticias() {
            var formData = {
                data_inicio: converterDataParaISO($('#dt_inicial').val()),
                data_fim: converterDataParaISO($('#dt_final').val()),
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

            // Adicionar termo de busca
            formData.termo = $('#termo').val().trim();

            console.log('Enviando dados para pesquisa:', formData);

            $.ajax({
                url: window.host + '/cliente/relatorios/listar-noticias',
                type: 'POST',
                data: formData,
                dataType: 'json',
                timeout: 30000, // 30 segundos de timeout
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Resposta recebida:', response);
                    
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
                html += '<th width="30" class="text-center"><i class="fa fa-expand-alt" title="Clique na linha para expandir/recolher"></i></th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                // Notícias
                noticiasArray.forEach(function(noticia) {
                    if (noticia && noticia.id) {
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
                            html += '<td>' + obterSentimentoHtml(noticia.sentimento) + '</td>';
                        }
                        if (window.mostrarRetornoMidia) {
                            html += '<td>' + (noticia.valor > 0 ? 'R$ ' + Number(noticia.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : 'N/A') + '</td>';
                        }
                        html += '<td class="text-center"><i class="fa fa-chevron-down expand-icon" data-noticia-id="' + noticia.id + '"></i></td>';
                        html += '</tr>';
                    }
                });

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

            // Mostrar loading
            $('#btnGerarRelatorio').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Gerando...');

            $.ajax({
                url: window.host + '/cliente/relatorios/gerar-pdf',
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

    });

    // Funções globais
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

    function atualizarContadores() {
        var totalSelecionadas = $('.selecionar-noticia:checked').length;
        $('#totalSelecionadas').text(totalSelecionadas);
        $('#qtdSelecionadasBtn').text(totalSelecionadas);
    }

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
            timeout: 15000, // 15 segundos de timeout
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
                    detalhesHtml += '<p>' + (noticia.titulo || 'Sem título') + '</p>';
                    
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
                    }
                    
                    if (window.mostrarRetornoMidia) {
                        detalhesHtml += '<h6>Valor:</h6>';
                        detalhesHtml += '<p>' + (noticia.valor > 0 ? 'R$ ' + Number(noticia.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : 'N/A') + '</p>';
                    }
                    
                    if (noticia.tags) {
                        detalhesHtml += '<h6>Tags:</h6>';
                        detalhesHtml += '<p>' + noticia.tags + '</p>';
                    }
                    
                    detalhesHtml += '<h6>Conteúdo:</h6>';
                    detalhesHtml += '<div class="detalhes-texto">';
                    detalhesHtml += (noticia.texto || 'Sem conteúdo').replace(/\n/g, '<br>');
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

        // Mostrar loading no botão da aba
        $('#btnGerarRelatorioImpressoAba').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Gerando...');

        $.ajax({
            url: window.host + '/cliente/relatorios/gerar-pdf-impresso',
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


</script>
@endsection