@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fa fa-desktop"></i> Monitoramento do Sistema
                </h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
        <!-- Card Rádio -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Monitoramento de Programas - Rádio</h4>
                    <button id="btn-atualizar-radio" class="btn btn-sm btn-primary">
                        <i class="fa fa-refresh"></i> Atualizar
                    </button>
                </div>
                <div class="card-body">
                    <div id="loading-radio" class="text-center" style="display: none;">
                        <i class="fa fa-spinner fa-spin"></i> Carregando...
                    </div>
                    <div id="erro-radio" class="alert alert-danger" style="display: none;"></div>
                    <div class="table-responsive">
                        <table id="tabela-radio" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Emissora</th>
                                    <th>Horário</th>
                                    <th>Status</th>
                                    <th>Esperadas</th>
                                    <th>Encontradas</th>
                                    <th>Faltantes</th>
                                    <th>%</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dados carregados via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card TV -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Monitoramento de Programas - TV</h4>
                    <button id="btn-atualizar-tv" class="btn btn-sm btn-primary">
                        <i class="fa fa-refresh"></i> Atualizar
                    </button>
                </div>
                <div class="card-body">
                    <div id="loading-tv" class="text-center" style="display: none;">
                        <i class="fa fa-spinner fa-spin"></i> Carregando...
                    </div>
                    <div id="erro-tv" class="alert alert-danger" style="display: none;"></div>
                    <div class="table-responsive">
                        <table id="tabela-tv" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Programa</th>
                                    <th>Horário</th>
                                    <th>Status</th>
                                    <th>Min. Decorridos</th>
                                    <th>Duração Vídeo</th>
                                    <th>Esperados</th>
                                    <th>Encontrados</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dados carregados via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<style>
.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
}

.status-ok {
    background-color: #28a745;
    color: white;
}

.status-problema {
    background-color: #ffc107;
    color: black;
}

.status-critico {
    background-color: #dc3545;
    color: white;
}

.status-em-andamento {
    background-color: #17a2b8;
    color: white;
}

.status-finalizado {
    background-color: #6c757d;
    color: white;
}

.status-nao-iniciado {
    background-color: #6c757d;
    color: white;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.card-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.table th {
    font-size: 12px;
    font-weight: 600;
    color: #666;
    border-top: none;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    font-size: 13px;
    vertical-align: middle;
    padding: 8px;
}

.btn-sm {
    padding: 4px 12px;
    font-size: 12px;
}
</style>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Auto-refresh a cada 30 segundos
    setInterval(function() {
        carregarDadosRadio();
        carregarDadosTv();
    }, 30000);

    // Carregar dados iniciais
    carregarDadosRadio();
    carregarDadosTv();

    // Event listeners para botões de atualizar
    $('#btn-atualizar-radio').click(function() {
        carregarDadosRadio();
    });

    $('#btn-atualizar-tv').click(function() {
        carregarDadosTv();
    });
});

function carregarDadosRadio() {
    $('#loading-radio').show();
    $('#erro-radio').hide();
    $('#tabela-radio tbody').empty();

    $.ajax({
        url: '{{ route("monitoramento.sistema.radio") }}',
        method: 'GET',
        success: function(response) {
            $('#loading-radio').hide();
            
            if (response.success) {
                preencherTabelaRadio(response.data);
            } else {
                $('#erro-radio').text(response.message || 'Erro ao carregar dados').show();
            }
        },
        error: function(xhr, status, error) {
            $('#loading-radio').hide();
            $('#erro-radio').text('Erro na comunicação com o servidor: ' + error).show();
        }
    });
}

function carregarDadosTv() {
    $('#loading-tv').show();
    $('#erro-tv').hide();
    $('#tabela-tv tbody').empty();

    $.ajax({
        url: '{{ route("monitoramento.sistema.tv") }}',
        method: 'GET',
        success: function(response) {
            $('#loading-tv').hide();
            
            if (response.success) {
                preencherTabelaTv(response.data);
            } else {
                $('#erro-tv').text(response.message || 'Erro ao carregar dados').show();
            }
        },
        error: function(xhr, status, error) {
            $('#loading-tv').hide();
            $('#erro-tv').text('Erro na comunicação com o servidor: ' + error).show();
        }
    });
}

function preencherTabelaRadio(dados) {
    var tbody = $('#tabela-radio tbody');
    tbody.empty();

    if (dados.length === 0) {
        tbody.append('<tr><td colspan="8" class="text-center">Nenhum problema detectado no momento</td></tr>');
        return;
    }

    dados.forEach(function(item) {
        var statusClass = getStatusClass(item.Status);
        var statusProgramacaoClass = getStatusProgramacaoClass(item['Status da Programação']);
        
        var row = '<tr>' +
            '<td>' + item.Emissora + '</td>' +
            '<td>' + item['Horário Início'] + ' - ' + item['Horário Fim'] + '</td>' +
            '<td><span class="status-badge ' + statusProgramacaoClass + '">' + item['Status da Programação'] + '</span></td>' +
            '<td>' + item['Gravações Esperadas (15min)'] + '</td>' +
            '<td>' + item['Gravações Encontradas'] + '</td>' +
            '<td>' + item['Gravações Faltantes'] + '</td>' +
            '<td>' + (item['Porcentagem Completa'] || 0) + '%</td>' +
            '<td><span class="status-badge ' + statusClass + '">' + item.Status + '</span></td>' +
            '</tr>';
        tbody.append(row);
    });
}

function preencherTabelaTv(dados) {
    var tbody = $('#tabela-tv tbody');
    tbody.empty();

    if (dados.length === 0) {
        tbody.append('<tr><td colspan="8" class="text-center">Nenhum problema detectado no momento</td></tr>');
        return;
    }

    dados.forEach(function(item) {
        var statusClass = getStatusClass(item.Status);
        var statusProgramaClass = getStatusProgramacaoClass(item['Status do Programa']);
        
        var row = '<tr>' +
            '<td>' + item.Programa + '</td>' +
            '<td>' + item['Horário Início'] + ' - ' + item['Horário Fim'] + '</td>' +
            '<td><span class="status-badge ' + statusProgramaClass + '">' + item['Status do Programa'] + '</span></td>' +
            '<td>' + item['Minutos Decorridos'] + '</td>' +
            '<td>' + item['Duração de Cada Vídeo (min)'] + ' min</td>' +
            '<td>' + item['Vídeos Esperados Até Agora'] + '</td>' +
            '<td>' + item['Vídeos Encontrados'] + '</td>' +
            '<td><span class="status-badge ' + statusClass + '">' + item.Status + '</span></td>' +
            '</tr>';
        tbody.append(row);
    });
}

function getStatusClass(status) {
    switch(status) {
        case 'OK':
            return 'status-ok';
        case 'PROBLEMA DETECTADO':
            return 'status-problema';
        case 'PROBLEMA CRÍTICO':
            return 'status-critico';
        default:
            return 'status-ok';
    }
}

function getStatusProgramacaoClass(status) {
    switch(status) {
        case 'Em andamento':
            return 'status-em-andamento';
        case 'Finalizado':
            return 'status-finalizado';
        case 'Não iniciado':
            return 'status-nao-iniciado';
        default:
            return 'status-nao-iniciado';
    }
}
</script>
@endsection
