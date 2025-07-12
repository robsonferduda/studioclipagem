// Debug para o sistema de relatórios
(function() {
    'use strict';
    
    // Função para testar se as requisições estão funcionando
    window.testRelatorioDados = function() {
        console.log('=== TESTE DE RELATÓRIOS ===');
        
        const host = window.host || $('meta[name="base-url"]').attr('content');
        console.log('Host:', host);
        
        // Testar carregamento de áreas
        console.log('Testando carregamento de áreas...');
        $.ajax({
            url: host + '/api/cliente/areas',
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('✓ Áreas carregadas com sucesso:', response);
            },
            error: function(xhr, status, error) {
                console.error('✗ Erro ao carregar áreas:', {
                    status: status,
                    error: error,
                    xhr: xhr.responseText
                });
            }
        });
        
        // Testar listagem de notícias com dados básicos
        console.log('Testando listagem de notícias...');
        const testData = {
            data_inicio: '2024-01-01',
            data_fim: '2024-12-31',
            tipos_midia: ['web', 'tv', 'radio', 'impresso'],
            status: ['positivo', 'negativo', 'neutro'],
            retorno: ['com_retorno'],
            valor: ['com_valor', 'sem_valor'],
            areas: [],
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: host + '/cliente/relatorios/listar-noticias',
            type: 'POST',
            data: testData,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('✓ Notícias carregadas com sucesso:', response);
            },
            error: function(xhr, status, error) {
                console.error('✗ Erro ao carregar notícias:', {
                    status: status,
                    error: error,
                    xhr: xhr.responseText
                });
            }
        });
        
        console.log('=== FIM DO TESTE ===');
    };
    
    // Função para verificar se os elementos estão no DOM
    window.verificarElementosDOM = function() {
        console.log('=== VERIFICAÇÃO DOS ELEMENTOS DOM ===');
        
        const elementos = [
            '#dt_inicial',
            '#dt_final', 
            '#areas-checkbox-group',
            '#resultado-relatorio',
            '#totalNoticias',
            '#totalSelecionadas',
            '#btnGerarRelatorio',
            'meta[name="csrf-token"]',
            'meta[name="base-url"]'
        ];
        
        elementos.forEach(function(selector) {
            const elemento = $(selector);
            console.log(selector + ':', elemento.length > 0 ? '✓ Encontrado' : '✗ Não encontrado');
        });
        
        console.log('=== FIM DA VERIFICAÇÃO ===');
    };
    
    // Função para verificar variáveis globais
    window.verificarVariaveisGlobais = function() {
        console.log('=== VERIFICAÇÃO DAS VARIÁVEIS GLOBAIS ===');
        
        const variaveis = [
            'host',
            'noticiasCarregadas',
            'noticiasCarregadasCount',
            'mostrarAreas',
            'mostrarSentimento',
            'mostrarRetornoMidia'
        ];
        
        variaveis.forEach(function(varName) {
            if (typeof window[varName] !== 'undefined') {
                console.log(varName + ':', window[varName]);
            } else {
                console.log(varName + ':', '✗ Não definida');
            }
        });
        
        console.log('=== FIM DA VERIFICAÇÃO ===');
    };
    
    // Função para executar todos os testes
    window.debugCompleto = function() {
        verificarElementosDOM();
        verificarVariaveisGlobais();
        testRelatorioDados();
    };
    
})(); 