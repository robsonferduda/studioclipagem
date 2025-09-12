<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Cliente;
use App\Models\NoticiaWeb;
use App\Models\NoticiaImpresso;
use App\Models\NoticiaRadio;
use App\Models\NoticiaTv;
use App\Models\Area;
use App\Models\AreaCliente;
use Carbon\Carbon;

class RelatorioService
{
    /**
     * Busca lista de clientes ativos
     */
    public function getClientes()
    {
        return Cliente::where('fl_ativo', true)
            ->orderBy('nome')
            ->get()
            ->map(function ($cliente) {
                return [
                    'id' => $cliente->id,
                    'nome' => $cliente->nome
                ];
            })
            ->toArray();
    }

    /**
     * Verifica se um cliente existe
     */
    public function checkCliente($clienteId)
    {
        return Cliente::where('id', $clienteId)
            ->where('fl_ativo', true)
            ->exists();
    }

    /**
     * Busca áreas de um cliente
     */
    public function getAreasByCliente($clienteId)
    {
        // Busca áreas através da tabela area_cliente
        $areaIds = AreaCliente::where('cliente_id', $clienteId)->pluck('area_id');
        
        return Area::whereIn('id', $areaIds)
            ->get()
            ->map(function ($area) {
                return [
                    'id' => $area->id,
                    'nome' => $area->descricao
                ];
            })
            ->toArray();
    }

    /**
     * Lista notícias por período com filtros aplicados
     */
    public function listarNoticiasPorPeriodoComFiltros($clienteId, $dataInicio, $dataFim, $filtros = [], $termo = null, $tipoFiltroData = 'coleta')
    {
        Log::info('=== INICIANDO listarNoticiasPorPeriodoComFiltros ===', [
            'clienteId' => $clienteId,
            'dataInicio' => $dataInicio,
            'dataFim' => $dataFim,
            'tipoFiltroData' => $tipoFiltroData,
            'filtros' => $filtros,
            'termo' => $termo
        ]);
        
        // Converte datas para o formato correto incluindo horário
        $dataInicio = Carbon::parse($dataInicio)->format('Y-m-d') . ' 00:00:00';
        $dataFim = Carbon::parse($dataFim)->format('Y-m-d') . ' 23:59:59';

        $tiposMidia = $filtros['tipos_midia'] ?? ['web', 'tv', 'radio', 'impresso'];
        $statusFiltros = $filtros['status'] ?? ['positivo', 'negativo', 'neutro'];
        $retornoFiltros = $filtros['retorno'] ?? ['com_retorno'];
        $valorFiltros = $filtros['valor'] ?? ['com_valor', 'sem_valor'];
        $areasFiltros = $filtros['areas'] ?? [];
        $semAreaFiltro = $filtros['sem_area'] ?? false;
        $tagsFiltros = $filtros['tags_filtro'] ?? [];
        $fontesFiltros = $filtros['fontes_filtro'] ?? [];
        
        Log::info('=== FILTROS PROCESSADOS RELATÓRIO SERVICE ===', [
            'tiposMidia' => $tiposMidia,
            'statusFiltros' => $statusFiltros,
            'retornoFiltros' => $retornoFiltros,
            'valorFiltros' => $valorFiltros,
            'areasFiltros' => $areasFiltros,
            'areasFiltros_empty' => empty($areasFiltros),
            'semAreaFiltro' => $semAreaFiltro,
            'semAreaFiltro_type' => gettype($semAreaFiltro),
            'semAreaFiltro_bool' => (bool)$semAreaFiltro,
            'tagsFiltros' => $tagsFiltros,
            'tags_count' => count($tagsFiltros),
            'tem_tags' => !empty($tagsFiltros),
            'fontesFiltros' => $fontesFiltros,
            'tem_fontes' => !empty($fontesFiltros)
        ]);

        $resultado = [
            'web' => [],
            'tv' => [],
            'radio' => [],
            'impresso' => []
        ];

        // Função auxiliar para obter coluna de data baseada no tipo de filtro
        $getDataColumn = function($tipoMidia) use ($tipoFiltroData) {
            if ($tipoFiltroData === 'coleta') {
                // Para data de coleta, sempre usa created_at
                return 'created_at';
            }
            
            // Para data de clipagem, usa as colunas específicas de cada tipo
            switch ($tipoMidia) {
                case 'web':
                    return 'data_noticia';
                case 'tv':
                    return 'dt_noticia';
                case 'radio':
                    return 'dt_clipagem';
                case 'impresso':
                    return 'dt_clipagem';
                default:
                    return 'created_at';
            }
        };

        Log::info('=== CONFIGURAÇÃO DE COLUNAS DE DATA ===', [
            'tipoFiltroData' => $tipoFiltroData,
            'coluna_web' => $getDataColumn('web'),
            'coluna_tv' => $getDataColumn('tv'),
            'coluna_radio' => $getDataColumn('radio'),
            'coluna_impresso' => $getDataColumn('impresso')
        ]);

        // Funções auxiliares para construir condições SQL
        $buildStatusCondition = function($tablePrefix = "") use ($statusFiltros) {
            if (count($statusFiltros) == 3) {  // Todos selecionados
                return "";
            }
            
            $conditions = [];
            foreach ($statusFiltros as $status) {
                switch ($status) {
                    case 'positivo':
                        $conditions[] = "{$tablePrefix}sentimento IN ('1', 'positivo', 'Positivo')";
                        break;
                    case 'negativo':
                        $conditions[] = "{$tablePrefix}sentimento IN ('-1', 'negativo', 'Negativo')";
                        break;
                    case 'neutro':
                        $conditions[] = "{$tablePrefix}sentimento IN ('0', 'neutro', 'Neutro', 'Sem Classificação', '')";
                        break;
                }
            }
            
            if (!empty($conditions)) {
                return " AND (" . implode(' OR ', $conditions) . ")";
            }
            return "";
        };

        $buildValorCondition = function($tablePrefix = "", $valorColumn = "") use ($valorFiltros) {
            if (count($valorFiltros) == 2) {  // Todos selecionados
                return "";
            }
            
            $conditions = [];
            foreach ($valorFiltros as $valor) {
                if ($valor == 'com_valor') {
                    $conditions[] = "{$tablePrefix}{$valorColumn} > 0";
                } elseif ($valor == 'sem_valor') {
                    $conditions[] = "({$tablePrefix}{$valorColumn} IS NULL OR {$tablePrefix}{$valorColumn} = 0)";
                }
            }
            
            if (!empty($conditions)) {
                return " AND (" . implode(' OR ', $conditions) . ")";
            }
            return "";
        };

        $buildTermoCondition = function($tablePrefix = "", $termo = null) {
            
            if (empty($termo)) {
                return "";
            }
            // Escapa o termo para evitar SQL injection (usando parâmetros depois)
            $termoLike = '%' . addcslashes($termo, '%_') . '%';

            // Adapte os campos conforme cada mídia/tabela

            switch ($tablePrefix) {
                case 'w':
                    $campos = [
                        "{$tablePrefix}sinopse",
                        "{$tablePrefix}titulo_noticia",
                    ];
                    break;

                case 'r':
                    $campos = [
                        "{$tablePrefix}sinopse",
                    ];
                    break;

                case 'j':
                    $campos = [
                        "{$tablePrefix}sinopse",
                        "{$tablePrefix}titulo",
                    ];
                    break;

                case 't':
                    $campos = [
                        "{$tablePrefix}sinopse",
                    ];
                    break;
                
                default:
                    $campos = [
                        "{$tablePrefix}sinopse",
                    ];
                    break;
            }
            
            $conditions = [];
            foreach ($campos as $campo) {
                $conditions[] = "$campo ILIKE :termo";
            }

            return " AND (" . implode(' OR ', $conditions) . ")";
        };

        $buildAreaCondition = function($tablePrefix = "") use ($areasFiltros, $semAreaFiltro) {
            $conditions = [];
            
            Log::info('🔍 DEBUG buildAreaCondition:', [
                'tablePrefix' => $tablePrefix,
                'areasFiltros' => $areasFiltros,
                'areasFiltros_empty' => empty($areasFiltros),
                'areasFiltros_count' => count($areasFiltros),
                'semAreaFiltro' => $semAreaFiltro,
                'semAreaFiltro_type' => gettype($semAreaFiltro),
                'semAreaFiltro_bool_check' => (bool)$semAreaFiltro
            ]);
            
            // Filtro por áreas específicas
            if (!empty($areasFiltros)) {
                $areaIds = implode(',', $areasFiltros);
                $conditions[] = "{$tablePrefix}area IN ({$areaIds})";
                Log::info('✅ Adicionando filtro áreas específicas:', [
                    'condition' => "{$tablePrefix}area IN ({$areaIds})"
                ]);
            }
            
            // Filtro para notícias sem área
            if ($semAreaFiltro) {
                $conditions[] = "({$tablePrefix}area IS NULL OR {$tablePrefix}area = 0)";
                Log::info('✅ Adicionando filtro SEM ÁREA:', [
                    'condition' => "({$tablePrefix}area IS NULL OR {$tablePrefix}area = 0)"
                ]);
            }
            
            // Se ambos os filtros estão ativos, usar OR
            if (!empty($conditions)) {
                if (count($conditions) > 1) {
                    $result = " AND (" . implode(' OR ', $conditions) . ")";
                    Log::info('🎯 Condição COMBINADA:', ['result' => $result]);
                    return $result;
                } else {
                    $result = " AND " . $conditions[0];
                    Log::info('🎯 Condição SIMPLES:', ['result' => $result]);
                    return $result;
                }
            }
            
            Log::info('❌ NENHUMA condição de área - mostrando TODAS as notícias');
            return "";
        };

        $buildTagsCondition = function($clienteId, $tipoId) use ($tagsFiltros) {
            if (empty($tagsFiltros)) {
                Log::info('buildTagsCondition: Nenhuma tag para filtrar', [
                    'cliente_id' => $clienteId,
                    'tipo_id' => $tipoId
                ]);
                return "";
            }
            
            Log::info('=== CONSTRUINDO FILTRO TAGS ===', [
                'cliente_id' => $clienteId,
                'tipo_id' => $tipoId,
                'tags_filtros' => $tagsFiltros,
                'count_tags' => count($tagsFiltros)
            ]);
            
            // Usando abordagem mais simples com LIKE em PostgreSQL
            $tagConditions = [];
            foreach ($tagsFiltros as $tag) {
                // Escape caracteres especiais para LIKE
                $tagEscaped = str_replace(['\\', '%', '_', '"'], ['\\\\', '\\%', '\\_', '\\"'], $tag);
                // Verificar se a tag existe como string dentro do array JSON
                $tagConditions[] = "nc.misc_data::text LIKE '%\"$tagEscaped\"%'";
            }
            
            if (!empty($tagConditions)) {
                // IMPORTANTE: Todas as tags devem estar presentes (AND) ou pelo menos uma (OR)?
                // Para filtro funcional, usamos OR (mostrar notícias que tenham pelo menos uma das tags selecionadas)
                $condition = " AND (" . implode(' OR ', $tagConditions) . ")";
                
                Log::info('=== CONDIÇÃO SQL TAGS GERADA ===', [
                    'condition' => $condition,
                    'tag_conditions_array' => $tagConditions
                ]);
                
                return $condition;
            }
            return "";
        };

        // Função auxiliar para construir condições de fontes
        $buildFontesCondition = function($tipoMidia) use ($fontesFiltros) {
            if (empty($fontesFiltros)) {
                return "";
            }
            
            $fontesTipo = $fontesFiltros[$tipoMidia] ?? [];
            
            switch ($tipoMidia) {
                case 'web':
                    if (!empty($fontesTipo)) {
                        $fontesIds = implode(',', $fontesTipo);
                        return " AND w.id_fonte IN ({$fontesIds})";
                    }
                    break;
                    
                case 'impresso':
                    if (!empty($fontesTipo)) {
                        $fontesIds = implode(',', $fontesTipo);
                        return " AND j.id_fonte IN ({$fontesIds})";
                    }
                    break;
                    
                case 'tv':
                    $conditions = [];
                    if (!empty($fontesTipo['emissoras'])) {
                        $emissorasIds = implode(',', $fontesTipo['emissoras']);
                        $conditions[] = "t.emissora_id IN ({$emissorasIds})";
                    }
                    if (!empty($fontesTipo['programas'])) {
                        $programasIds = implode(',', $fontesTipo['programas']);
                        $conditions[] = "t.programa_id IN ({$programasIds})";
                    }
                    if (!empty($conditions)) {
                        return " AND (" . implode(' OR ', $conditions) . ")";
                    }
                    break;
                    
                case 'radio':
                    $conditions = [];
                    if (!empty($fontesTipo['emissoras'])) {
                        $emissorasIds = implode(',', $fontesTipo['emissoras']);
                        $conditions[] = "r.emissora_id IN ({$emissorasIds})";
                    }
                    if (!empty($fontesTipo['programas'])) {
                        $programasIds = implode(',', $fontesTipo['programas']);
                        $conditions[] = "r.programa_id IN ({$programasIds})";
                    }
                    if (!empty($conditions)) {
                        return " AND (" . implode(' OR ', $conditions) . ")";
                    }
                    break;
            }
            
            return "";
        };

        // Função auxiliar para converter sentimento string/numérico para inteiro
        $convertSentimento = function($sentimento) {
            $sentimentoInt = 0;
            if (is_numeric($sentimento)) {
                $sentimentoInt = (int)$sentimento;
            } elseif (is_string($sentimento)) {
                switch (strtolower(trim($sentimento))) {
                    case 'positivo':
                    case '1':
                        $sentimentoInt = 1;
                        break;
                    case 'negativo':
                    case '-1':
                        $sentimentoInt = -1;
                        break;
                    case 'neutro':
                    case 'sem classificação':
                    case '0':
                    case '':
                    default:
                        $sentimentoInt = 0;
                        break;
                }
            }
            return $sentimentoInt;
        };

        // Buscar notícias Web
        if (in_array('web', $tiposMidia)) {
            $colunaDataWeb = $getDataColumn('web');
            Log::info('Buscando notícias WEB...', ['coluna_data' => $colunaDataWeb]);
            try {
                $sql = "
                    SELECT 
                        w.id,
                        w.data_noticia as data,
                        COALESCE(w.titulo_noticia, 'Título não informado') as titulo,
                        COALESCE(fw.nome, 'Site Não Identificado') as veiculo,
                        COALESCE(w.url_noticia, '') as link,
                        COALESCE(w.sinopse, '') as texto,
                        COALESCE(w.nu_valor, 0) as valor,
                        COALESCE(nc.sentimento, '0') as sentimento,
                        COALESCE(nc.area, 0) as area_id,
                        COALESCE(a.descricao, 'Sem área') as area_nome,
                        nc.id as vinculo_id,
                        nc.misc_data
                    FROM noticias_web w
                    LEFT JOIN fonte_web fw ON w.id_fonte = fw.id
                    JOIN noticia_cliente nc ON w.id = nc.noticia_id AND nc.tipo_id = 2
                    LEFT JOIN area a ON nc.area = a.id
                    WHERE nc.cliente_id = :clienteId
                    AND w.$colunaDataWeb BETWEEN :dataInicio AND :dataFim
                    AND w.deleted_at IS NULL
                    {$buildStatusCondition('nc.')}
                    {$buildValorCondition('w.', 'nu_valor')}
                    {$buildAreaCondition('nc.')}
                    {$buildTermoCondition('w.', $termo)}
                    {$buildTagsCondition($clienteId, 2)}
                    {$buildFontesCondition('web')}
                    ORDER BY w.$colunaDataWeb ASC, w.titulo_noticia ASC
                ";

                $params = [
                    'clienteId' => $clienteId,
                    'dataInicio' => $dataInicio,
                    'dataFim' => $dataFim
                ];

                if (!empty($termo)) {
                    $params['termo'] = '%' . $termo . '%';
                }
                
                // Log da query completa ANTES da execução
                Log::info('=== EXECUTANDO QUERY WEB ===', [
                    'sql_completa' => $sql,
                    'params' => $params,
                    'tem_filtro_tags' => !empty($tagsFiltros),
                    'tags_solicitadas' => $tagsFiltros
                ]);

                $noticiasWeb = DB::select($sql, $params);
                
                Log::info('=== QUERY WEB EXECUTADA ===', [
                    'count' => count($noticiasWeb),
                    'tem_filtro_tags' => !empty($tagsFiltros),
                    'tags_solicitadas' => $tagsFiltros
                ]);
                
                // Log de algumas notícias para debug
                if (!empty($noticiasWeb)) {
                    Log::info('=== AMOSTRA NOTÍCIAS WEB ===', [
                        'primeira_noticia' => [
                            'id' => $noticiasWeb[0]->id ?? 'N/A',
                            'titulo' => substr($noticiasWeb[0]->titulo ?? 'N/A', 0, 50),
                            'misc_data' => $noticiasWeb[0]->misc_data ?? 'N/A'
                        ],
                        'total_com_misc_data' => count(array_filter($noticiasWeb, function($n) { return !empty($n->misc_data); }))
                    ]);
                }
                
                foreach ($noticiasWeb as $noticia) {
                    // Buscar tags do misc_data
                    $tags = [];
                    if (!empty($noticia->misc_data)) {
                        $miscData = json_decode($noticia->misc_data, true);
                        $tags = $miscData['tags_noticia'] ?? [];
                    }
                    
                    $resultado['web'][] = [
                        'id' => $noticia->id,
                        'vinculo_id' => $noticia->vinculo_id,
                        'titulo' => $noticia->titulo,
                        'texto' => $noticia->texto,
                        'veiculo' => $noticia->veiculo,
                        'data' => $noticia->data,
                        'data_formatada' => Carbon::parse($noticia->data)->format('d/m/Y'),
                        'link' => $noticia->link,
                        'sentimento' => $convertSentimento($noticia->sentimento),
                        'valor' => (float)$noticia->valor,
                        'area' => $noticia->area_nome ?? 'Sem área',
                        'area_id' => $noticia->area_id,
                        'tags' => $tags,
                        'tipo_midia' => 'web'
                    ];
                }
                
                // Ordenar explicitamente por data no PHP como garantia
                usort($resultado['web'], function($a, $b) {
                    return strtotime($a['data']) - strtotime($b['data']);
                });

                Log::info('Processamento WEB concluído:', [
                    'quantidade' => count($resultado['web'])
                ]);
                
            } catch (\Exception $e) {
                Log::error('Erro ao processar notícias WEB:', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                throw $e;
            }
        }

        // Buscar notícias TV
        if (in_array('tv', $tiposMidia)) {
            $colunaDataTv = $getDataColumn('tv');
            Log::info('Buscando notícias TV...', ['coluna_data' => $colunaDataTv]);
            try {
                $sql = "
                    SELECT 
                        t.id,
                        t.dt_noticia as data,
                        COALESCE(t.sinopse, 'Sem título') as titulo,
                        COALESCE(e.nome_emissora, 'Emissora Não Identificada') as veiculo,
                        COALESCE(p.nome_programa, 'Programa Não Identificado') as programa,
                        COALESCE(t.horario, '00:00:00') as horario,
                        COALESCE(t.duracao, '00:00:00') as duracao,
                        COALESCE(t.sinopse, '') as texto,
                        COALESCE(t.valor_retorno, 0) as valor,
                        COALESCE(nc.sentimento, '0') as sentimento,
                        COALESCE(nc.area, 0) as area_id,
                        COALESCE(a.descricao, 'Sem área') as area_nome,
                        nc.id as vinculo_id,
                        nc.misc_data
                    FROM noticia_tv t
                    LEFT JOIN emissora_web e ON t.emissora_id = e.id
                    LEFT JOIN programa_emissora_web p ON t.programa_id = p.id
                    JOIN noticia_cliente nc ON t.id = nc.noticia_id AND nc.tipo_id = 4
                    LEFT JOIN area a ON nc.area = a.id
                    WHERE nc.cliente_id = :clienteId
                    AND t.$colunaDataTv BETWEEN :dataInicio AND :dataFim
                    AND t.deleted_at IS NULL
                    AND t.duracao IS NOT NULL -- Filtro obrigatório: apenas notícias TV editadas (com duração)
                    {$buildStatusCondition('nc.')}
                    {$buildValorCondition('t.', 'valor_retorno')}
                    {$buildAreaCondition('nc.')}
                    {$buildTermoCondition('t.', $termo)}
                    {$buildTagsCondition($clienteId, 4)}
                    {$buildFontesCondition('tv')}
                    ORDER BY t.$colunaDataTv ASC, t.sinopse ASC
                ";

                $params = [
                    'clienteId' => $clienteId,
                    'dataInicio' => $dataInicio,
                    'dataFim' => $dataFim
                ];

                if (!empty($termo)) {
                    $params['termo'] = '%' . $termo . '%';
                }
                
                $noticiasTV = DB::select($sql, $params);
                
                Log::info('Query TV executada:', [
                    'count' => count($noticiasTV)
                ]);
                
                foreach ($noticiasTV as $noticia) {
                    // Buscar tags do misc_data
                    $tags = [];
                    if (!empty($noticia->misc_data)) {
                        $miscData = json_decode($noticia->misc_data, true);
                        $tags = $miscData['tags_noticia'] ?? [];
                    }
                    
                    $resultado['tv'][] = [
                        'id' => $noticia->id,
                        'vinculo_id' => $noticia->vinculo_id,
                        'titulo' => $noticia->titulo,
                        'texto' => $noticia->texto,
                        'veiculo' => $noticia->veiculo,
                        'data' => $noticia->data,
                        'data_formatada' => Carbon::parse($noticia->data)->format('d/m/Y'),
                        'programa' => $noticia->programa,
                        'horario' => $noticia->horario,
                        'duracao' => $noticia->duracao,
                        'sentimento' => $convertSentimento($noticia->sentimento),
                        'valor' => (float)$noticia->valor,
                        'area' => $noticia->area_nome ?? 'Sem área',
                        'area_id' => $noticia->area_id,
                        'tags' => $tags,
                        'tipo_midia' => 'tv'
                    ];
                }
                
                // Ordenar explicitamente por data no PHP como garantia
                usort($resultado['tv'], function($a, $b) {
                    return strtotime($a['data']) - strtotime($b['data']);
                });
                
            } catch (\Exception $e) {
                Log::error('Erro ao processar notícias TV:', [
                    'message' => $e->getMessage()
                ]);
                throw $e;
            }
        }

        // Buscar notícias Rádio
        if (in_array('radio', $tiposMidia)) {
            $colunaDataRadio = $getDataColumn('radio');
            Log::info('Buscando notícias RÁDIO...', ['coluna_data' => $colunaDataRadio]);
            try {
                $sql = "
                    SELECT 
                        r.id,
                        r.dt_clipagem as data,
                        COALESCE(r.titulo, 'Sem título') as titulo,
                        CASE 
                            WHEN e.nome_emissora IS NOT NULL THEN e.nome_emissora
                            WHEN p.nome_programa IS NOT NULL AND pe.nome_emissora IS NOT NULL THEN pe.nome_emissora
                            ELSE 'Emissora Não Identificada'
                        END as veiculo,
                        COALESCE(p.nome_programa, 'Programa Não Identificado') as programa,
                        COALESCE(r.horario, '00:00:00') as horario,
                        COALESCE(r.duracao, '00:00:00') as duracao,
                        COALESCE(r.sinopse, '') as texto,
                        COALESCE(r.valor_retorno, 0) as valor,
                        COALESCE(nc.sentimento, '0') as sentimento,
                        COALESCE(nc.area, 0) as area_id,
                        COALESCE(a.descricao, 'Sem área') as area_nome,
                        nc.id as vinculo_id,
                        nc.misc_data
                    FROM noticia_radio r
                    LEFT JOIN emissora_radio e ON r.emissora_id = e.id
                    LEFT JOIN programa_emissora_radio p ON r.programa_id = p.id
                    LEFT JOIN emissora_radio pe ON p.id_emissora = pe.id
                    JOIN noticia_cliente nc ON r.id = nc.noticia_id AND nc.tipo_id = 3
                    LEFT JOIN area a ON nc.area = a.id
                    WHERE nc.cliente_id = :clienteId
                    AND r.$colunaDataRadio BETWEEN :dataInicio AND :dataFim
                    AND r.deleted_at IS NULL
                    AND r.duracao IS NOT NULL -- Filtro obrigatório: apenas notícias Rádio editadas (com duração)
                    {$buildStatusCondition('nc.')}
                    {$buildValorCondition('r.', 'valor_retorno')}
                    {$buildAreaCondition('nc.')}
                    {$buildTermoCondition('r.', $termo)}
                    {$buildTagsCondition($clienteId, 3)}
                    {$buildFontesCondition('radio')}
                    ORDER BY r.$colunaDataRadio ASC, r.titulo ASC
                ";

                $params = [
                    'clienteId' => $clienteId,
                    'dataInicio' => $dataInicio,
                    'dataFim' => $dataFim
                ];

                if (!empty($termo)) {
                    $params['termo'] = '%' . $termo . '%';
                }
                
                $noticiasRadio = DB::select($sql, $params);
                
                Log::info('Query RÁDIO executada:', [
                    'count' => count($noticiasRadio)
                ]);
                
                foreach ($noticiasRadio as $noticia) {
                    // Buscar tags do misc_data
                    $tags = [];
                    if (!empty($noticia->misc_data)) {
                        $miscData = json_decode($noticia->misc_data, true);
                        $tags = $miscData['tags_noticia'] ?? [];
                    }
                    
                    $resultado['radio'][] = [
                        'id' => $noticia->id,
                        'vinculo_id' => $noticia->vinculo_id,
                        'titulo' => $noticia->titulo,
                        'texto' => $noticia->texto,
                        'veiculo' => $noticia->veiculo,
                        'data' => $noticia->data,
                        'data_formatada' => Carbon::parse($noticia->data)->format('d/m/Y'),
                        'programa' => $noticia->programa,
                        'horario' => $noticia->horario,
                        'duracao' => $noticia->duracao,
                        'sentimento' => $convertSentimento($noticia->sentimento),
                        'valor' => (float)$noticia->valor,
                        'area' => $noticia->area_nome ?? 'Sem área',
                        'area_id' => $noticia->area_id,
                        'tags' => $tags,
                        'tipo_midia' => 'radio'
                    ];
                }
                
                // Ordenar explicitamente por data no PHP como garantia
                usort($resultado['radio'], function($a, $b) {
                    return strtotime($a['data']) - strtotime($b['data']);
                });
                
            } catch (\Exception $e) {
                Log::error('Erro ao processar notícias RÁDIO:', [
                    'message' => $e->getMessage()
                ]);
                throw $e;
            }
        }

        // Buscar notícias Impresso
        if (in_array('impresso', $tiposMidia)) {
            $colunaDataImpresso = $getDataColumn('impresso');
            Log::info('Buscando notícias IMPRESSO...', ['coluna_data' => $colunaDataImpresso]);
            try {
                $sql = "
                    SELECT 
                        j.id,
                        j.dt_clipagem as data,
                        COALESCE(j.titulo, 'Sem título') as titulo,
                        COALESCE(ji.nome, 'Jornal Não Identificado') as veiculo,
                        COALESCE(j.sinopse, '') as texto,
                        COALESCE(j.valor_retorno, 0) as valor,
                        COALESCE(nc.sentimento, '0') as sentimento,
                        COALESCE(nc.area, 0) as area_id,
                        COALESCE(a.descricao, 'Sem área') as area_nome,
                        nc.id as vinculo_id,
                        nc.misc_data
                    FROM noticia_impresso j
                    LEFT JOIN jornal_online ji ON j.id_fonte = ji.id
                    JOIN noticia_cliente nc ON j.id = nc.noticia_id AND nc.tipo_id = 1
                    LEFT JOIN area a ON nc.area = a.id
                    WHERE nc.cliente_id = :clienteId
                    AND j.$colunaDataImpresso BETWEEN :dataInicio AND :dataFim
                    AND j.deleted_at IS NULL
                    AND j.valor_retorno IS NOT NULL -- Filtro obrigatório: apenas notícias Impressas editadas (com retorno de mídia)
                    {$buildStatusCondition('nc.')}
                    {$buildValorCondition('j.', 'valor_retorno')}
                    {$buildAreaCondition('nc.')}
                    {$buildTermoCondition('j.', $termo)}
                    {$buildTagsCondition($clienteId, 1)}
                    {$buildFontesCondition('impresso')}
                    ORDER BY j.$colunaDataImpresso ASC, j.titulo ASC
                ";

                $params = [
                    'clienteId' => $clienteId,
                    'dataInicio' => $dataInicio,
                    'dataFim' => $dataFim
                ];

                if (!empty($termo)) {
                    $params['termo'] = '%' . $termo . '%';
                }
                
                $noticiasImpresso = DB::select($sql, $params);
                
                Log::info('Query IMPRESSO executada (após remover filtro restritivo de sinopse):', [
                    'count' => count($noticiasImpresso),
                    'sql' => $sql,
                    'params' => $params
                ]);
                
                foreach ($noticiasImpresso as $noticia) {
                    // Buscar tags do misc_data
                    $tags = [];
                    if (!empty($noticia->misc_data)) {
                        $miscData = json_decode($noticia->misc_data, true);
                        $tags = $miscData['tags_noticia'] ?? [];
                    }
                    
                    $resultado['impresso'][] = [
                        'id' => $noticia->id,
                        'vinculo_id' => $noticia->vinculo_id,
                        'titulo' => $noticia->titulo,
                        'texto' => $noticia->texto,
                        'veiculo' => $noticia->veiculo,
                        'data' => $noticia->data,
                        'data_formatada' => Carbon::parse($noticia->data)->format('d/m/Y'),
                        'sentimento' => $convertSentimento($noticia->sentimento),
                        'valor' => (float)$noticia->valor,
                        'area' => $noticia->area_nome ?? 'Sem área',
                        'area_id' => $noticia->area_id,
                        'tags' => $tags,
                        'tipo_midia' => 'impresso'
                    ];
                }
                
                // Ordenar explicitamente por data no PHP como garantia
                usort($resultado['impresso'], function($a, $b) {
                    return strtotime($a['data']) - strtotime($b['data']);
                });
                
            } catch (\Exception $e) {
                Log::error('Erro ao processar notícias IMPRESSO:', [
                    'message' => $e->getMessage()
                ]);
                throw $e;
            }
        }

        Log::info('=== FINALIZANDO listarNoticiasPorPeriodoComFiltros ===', [
            'resultado' => [
                'web' => count($resultado['web']),
                'tv' => count($resultado['tv']),
                'radio' => count($resultado['radio']),
                'impresso' => count($resultado['impresso'])
            ]
        ]);
        
        return $resultado;
    }

    /**
     * Busca notícia por ID e tipo
     */
    public function getNoticiaById($noticiaId, $tipo)
    {
        switch ($tipo) {
            case 'web':
                return NoticiaWeb::with(['clientes', 'area', 'conteudo'])->find($noticiaId);
            case 'tv':
                return NoticiaTv::with(['clientes', 'area', 'emissora', 'programa'])->find($noticiaId);
            case 'radio':
                return NoticiaRadio::with(['clientes', 'area', 'emissora', 'programa'])->find($noticiaId);
            case 'impresso':
                return NoticiaImpresso::with(['clientes', 'area', 'fonte'])->find($noticiaId);
            default:
                return null;
        }
    }

    /**
     * Adiciona uma nova notícia
     */
    public function adicionarNoticia($dados)
    {
        try {
            DB::beginTransaction();

            $noticia = null;
            $clienteId = $dados['cliente_id'];

            switch ($dados['tipo']) {
                case 'WEB':
                    $noticia = NoticiaWeb::create([
                        'titulo' => $dados['titulo'],
                        'texto' => $dados['texto'],
                        'link' => $dados['link'] ?? '',
                        'valor' => $dados['valor'] ?? 0,
                        'tags' => $dados['tags'] ?? '',
                        'created_at' => $dados['data'],
                        'fonte_id' => 1 // Ajustar conforme necessário
                    ]);
                    break;

                case 'TV':
                    $noticia = NoticiaTv::create([
                        'titulo' => $dados['titulo'],
                        'texto' => $dados['texto'],
                        'programa' => $dados['programa'] ?? '',
                        'horario' => $dados['horario'] ?? '',
                        'valor' => $dados['valor'] ?? 0,
                        'tags' => $dados['tags'] ?? '',
                        'created_at' => $dados['data'],
                        'emissora_id' => 1 // Ajustar conforme necessário
                    ]);
                    break;

                case 'RADIO':
                    $noticia = NoticiaRadio::create([
                        'titulo' => $dados['titulo'],
                        'texto' => $dados['texto'],
                        'programa' => $dados['programa_radio'] ?? '',
                        'horario' => $dados['horario_radio'] ?? '',
                        'valor' => $dados['valor'] ?? 0,
                        'tags' => $dados['tags'] ?? '',
                        'created_at' => $dados['data'],
                        'emissora_id' => 1 // Ajustar conforme necessário
                    ]);
                    break;

                case 'JORNAL':
                    $noticia = NoticiaImpresso::create([
                        'titulo' => $dados['titulo'],
                        'texto' => $dados['texto'],
                        'valor' => $dados['valor'] ?? 0,
                        'tags' => $dados['tags'] ?? '',
                        'created_at' => $dados['data'],
                        'fonte_id' => 1 // Ajustar conforme necessário
                    ]);
                    break;

                default:
                    throw new \Exception('Tipo de notícia inválido');
            }

            if ($noticia) {
                // Criar vínculo com o cliente
                $noticia->clientes()->attach($clienteId);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Notícia adicionada com sucesso',
                'noticia_id' => $noticia->id
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao adicionar notícia: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erro ao adicionar notícia: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Edita uma notícia existente
     */
    public function editarNoticia($noticiaId, $dados)
    {
        try {
            DB::beginTransaction();

            $noticia = null;
            $tipo = strtolower($dados['tipo']); // Converter para minúsculo

            // Buscar a notícia pelo tipo
            $noticia = $this->getNoticiaById($noticiaId, $tipo);

            if (!$noticia) {
                throw new \Exception('Notícia não encontrada');
            }

            // Converter data se fornecida
            $dataFormatada = null;
            if (!empty($dados['data'])) {
                try {
                    if (strpos($dados['data'], '/') !== false) {
                        // Formato DD/MM/YYYY para YYYY-MM-DD
                        $dataFormatada = Carbon::createFromFormat('d/m/Y', $dados['data'])->format('Y-m-d');
                    } else {
                        $dataFormatada = $dados['data'];
                    }
                } catch (\Exception $e) {
                    Log::warning('Erro ao converter data: ' . $e->getMessage());
                }
            }

            // Atualizar campos específicos por tipo
            switch ($tipo) {
                case 'web':
                    if (!empty($dados['titulo'])) {
                        $noticia->titulo_noticia = $dados['titulo'];
                    }
                    if ($dataFormatada) {
                        $noticia->data_noticia = $dataFormatada;
                    }
                    if (isset($dados['valor'])) {
                        $noticia->nu_valor = $dados['valor'] ?? 0;
                    }
                    if (!empty($dados['link'])) {
                        $noticia->url_noticia = $dados['link'];
                    }
                    // Para web, atualizar conteúdo na tabela relacionada
                    if (!empty($dados['texto']) && $noticia->conteudo) {
                        $noticia->conteudo->conteudo = $dados['texto'];
                        $noticia->conteudo->save();
                    }
                    break;

                case 'impresso':
                    if (!empty($dados['titulo'])) {
                        $noticia->titulo = $dados['titulo'];
                    }
                    if (!empty($dados['texto'])) {
                        $noticia->texto = $dados['texto'];
                        $noticia->sinopse = $dados['texto']; // Backup na sinopse também
                    }
                    if ($dataFormatada) {
                        $noticia->dt_clipagem = $dataFormatada;
                    }
                    if (isset($dados['valor'])) {
                        $noticia->valor_retorno = $dados['valor'] ?? 0;
                    }
                    break;

                case 'tv':
                    if (!empty($dados['titulo'])) {
                        $noticia->titulo = $dados['titulo'];
                    }
                    if (!empty($dados['texto'])) {
                        $noticia->sinopse = $dados['texto'];
                    }
                    if ($dataFormatada) {
                        $noticia->dt_noticia = $dataFormatada;
                    }
                    if (isset($dados['valor'])) {
                        $noticia->valor_retorno = $dados['valor'] ?? 0;
                    }
                    if (!empty($dados['programa'])) {
                        // Note: pode precisar criar/atualizar registro na tabela programa_emissora_tv
                        $noticia->programa = $dados['programa'];
                    }
                    if (!empty($dados['horario'])) {
                        $noticia->horario = $dados['horario'];
                    }
                    break;

                case 'radio':
                    if (!empty($dados['titulo'])) {
                        $noticia->titulo = $dados['titulo'];
                    }
                    if (!empty($dados['texto'])) {
                        $noticia->sinopse = $dados['texto'];
                    }
                    if ($dataFormatada) {
                        $noticia->dt_clipagem = $dataFormatada;
                    }
                    if (isset($dados['valor'])) {
                        $noticia->valor_retorno = $dados['valor'] ?? 0;
                    }
                    if (!empty($dados['programa'])) {
                        $noticia->programa = $dados['programa'];
                    }
                    if (!empty($dados['horario'])) {
                        $noticia->horario = $dados['horario'];
                    }
                    break;

                default:
                    throw new \Exception('Tipo de notícia inválido: ' . $tipo);
            }

            $noticia->save();
            
            // Atualizar o sentimento na tabela noticia_cliente
            if (isset($dados['sentimento']) && $dados['sentimento'] !== null && isset($dados['cliente_id'])) {
                $tipoIdMap = ['web' => 2, 'impresso' => 1, 'tv' => 4, 'radio' => 3];
                $tipoId = $tipoIdMap[$tipo] ?? null;
                
                if ($tipoId) {
                    DB::table('noticia_cliente')
                        ->where('noticia_id', $noticiaId)
                        ->where('tipo_id', $tipoId)
                        ->where('cliente_id', $dados['cliente_id'])
                        ->update(['sentimento' => $dados['sentimento']]);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Notícia editada com sucesso',
                'noticia_id' => $noticia->id
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao editar notícia: ' . $e->getMessage(), [
                'noticia_id' => $noticiaId,
                'dados' => $dados,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao editar notícia: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Exclui uma notícia (remove vínculo com cliente)
     */
    public function excluirNoticia($vinculoId)
    {
        try {
            DB::beginTransaction();

            // Buscar o vínculo na tabela noticia_cliente
            $vinculo = DB::table('noticia_cliente')->where('id', $vinculoId)->first();
            
            if (!$vinculo) {
                throw new \Exception('Vínculo não encontrado');
            }

            // Remover o vínculo
            $rowsAffected = DB::table('noticia_cliente')->where('id', $vinculoId)->delete();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Notícia excluída com sucesso',
                'noticia_info' => [
                    'vinculo_id' => $vinculoId,
                    'noticia_id' => $vinculo->noticia_id,
                    'cliente_id' => $vinculo->cliente_id,
                    'tipo_midia' => $vinculo->tipo_midia,
                    'rows_affected' => $rowsAffected
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir notícia: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erro ao excluir notícia: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Aplica tags a múltiplas notícias
     */
    public function aplicarTagsLote($noticiasIds, $tags, $acao = 'adicionar')
    {
        try {
            DB::beginTransaction();

            $noticiasAtualizadas = 0;

            foreach ($noticiasIds as $noticiaData) {
                $noticiaId = $noticiaData['id'];
                $tipoMidia = $noticiaData['tipo'];

                $noticia = $this->getNoticiaById($noticiaId, $tipoMidia);
                
                if ($noticia) {
                    $tagsAtuais = $noticia->tags ?? '';
                    
                    switch ($acao) {
                        case 'adicionar':
                            $novasTags = $tagsAtuais ? $tagsAtuais . ', ' . $tags : $tags;
                            break;
                        case 'substituir':
                            $novasTags = $tags;
                            break;
                        case 'remover':
                            $novasTags = str_replace($tags, '', $tagsAtuais);
                            $novasTags = trim(str_replace(',,', ',', $novasTags), ', ');
                            break;
                        default:
                            $novasTags = $tagsAtuais;
                    }

                    $noticia->tags = $novasTags;
                    $noticia->save();
                    $noticiasAtualizadas++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Tags aplicadas com sucesso a $noticiasAtualizadas notícias",
                'noticias_atualizadas' => $noticiasAtualizadas
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao aplicar tags em lote: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erro ao aplicar tags: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Vincula notícia a uma área
     */
    public function vincularNoticiaArea($noticiaId, $tipoMidia, $areaId)
    {
        try {
            $noticia = $this->getNoticiaById($noticiaId, $tipoMidia);
            
            if (!$noticia) {
                return false;
            }

            // Para TV e Radio, que têm area_id diretamente na tabela
            if (in_array($tipoMidia, ['tv', 'radio'])) {
                $noticia->area_id = $areaId;
                $noticia->save();
            } else {
                // Para Web e Impresso, que usam a tabela noticia_cliente
                $tipoIdMap = ['web' => 2, 'impresso' => 1, 'tv' => 4, 'radio' => 3];
                $tipoId = $tipoIdMap[$tipoMidia];
                
                DB::table('noticia_cliente')
                    ->where('noticia_id', $noticiaId)
                    ->where('tipo_id', $tipoId)
                    ->update(['area' => $areaId]);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao vincular notícia à área: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca detalhes de uma notícia específica
     */
    public function buscarNoticia($noticiaId, $tipo, $clienteId)
    {
        try {
            $noticia = $this->getNoticiaById($noticiaId, $tipo);
            
            if (!$noticia) {
                return null;
            }

            // Buscar informações do vínculo com o cliente
            $tipoIdMap = ['web' => 2, 'impresso' => 1, 'tv' => 4, 'radio' => 3];
            $tipoId = $tipoIdMap[$tipo];
            
            $vinculo = DB::table('noticia_cliente')
                ->where('noticia_id', $noticiaId)
                ->where('tipo_id', $tipoId)
                ->where('cliente_id', $clienteId)
                ->first();

            if (!$vinculo) {
                return null;
            }

            // Buscar nome da área
            $areaTexto = 'Sem área';
            if ($vinculo->area) {
                $area = DB::table('area')->where('id', $vinculo->area)->first();
                if ($area) {
                    $areaTexto = $area->nome;
                }
            }

            // Buscar nome do veículo/fonte e preparar dados específicos por tipo
            $veiculoTexto = 'Sem veículo';
            $resultado = [
                'id' => $noticia->id,
                'area' => $areaTexto,
                'sentimento' => $vinculo->sentimento ?? 0,
                'vinculo_id' => $vinculo->id,
                'tipo' => $tipo
            ];

            switch ($tipo) {
                case 'web':
                    $veiculoTexto = $noticia->fonte ? $noticia->fonte->nome : 'Sem fonte';
                    
                    $resultado = array_merge($resultado, [
                        'titulo' => $noticia->titulo_noticia ?? 'Sem título',
                        'texto' => $noticia->conteudo ? $noticia->conteudo->conteudo : ($noticia->sinopse ?? 'Sem texto'),
                        'veiculo' => $veiculoTexto,
                        'data_formatada' => $noticia->data_noticia ? date('d/m/Y', strtotime($noticia->data_noticia)) : 'Sem data',
                        'data_noticia' => $noticia->data_noticia,
                        'valor' => $noticia->nu_valor ?? 0,
                        'tags' => '', // Web não tem tags diretas
                        'link' => $noticia->url_noticia ?? '',
                        'midia' => $noticia->ds_caminho_img ?? null
                    ]);
                    break;

                case 'impresso':
                    $veiculoTexto = $noticia->fonte ? $noticia->fonte->nome : 'Sem fonte';
                    
                    $resultado = array_merge($resultado, [
                        'titulo' => $noticia->titulo ?? 'Sem título',
                        'texto' => $noticia->texto ?? $noticia->sinopse ?? 'Sem texto',
                        'veiculo' => $veiculoTexto,
                        'data_formatada' => $noticia->dt_clipagem ? date('d/m/Y', strtotime($noticia->dt_clipagem)) : 'Sem data',
                        'data_noticia' => $noticia->dt_clipagem,
                        'valor' => $noticia->valor_retorno ?? 0,
                        'tags' => '', // Impresso não tem tags diretas
                        'pagina' => $noticia->nu_pagina_atual ?? '',
                        'midia' => $noticia->ds_caminho_img ?? null
                    ]);
                    break;

                case 'tv':
                    $veiculoTexto = $noticia->emissora ? $noticia->emissora->nome_emissora : 'Sem emissora';
                    $programaTexto = $noticia->programa ? $noticia->programa->nome_programa : '';
                    
                    $resultado = array_merge($resultado, [
                        'titulo' => $noticia->titulo ?? 'Sem título',
                        'texto' => $noticia->sinopse ?? 'Sem texto',
                        'veiculo' => $veiculoTexto,
                        'data_formatada' => $noticia->dt_noticia ? date('d/m/Y', strtotime($noticia->dt_noticia)) : 'Sem data',
                        'data_noticia' => $noticia->dt_noticia,
                        'valor' => $noticia->valor_retorno ?? 0,
                        'tags' => '', // TV não tem tags diretas
                        'programa' => $programaTexto,
                        'horario' => $noticia->horario ?? '',
                        'duracao' => $noticia->duracao ?? '',
                        'midia' => $noticia->ds_caminho_video ?? null
                    ]);
                    break;

                case 'radio':
                    $veiculoTexto = $noticia->emissora ? $noticia->emissora->nome_emissora : 'Sem emissora';
                    $programaTexto = $noticia->programa ? $noticia->programa->nome_programa : '';
                    
                    $resultado = array_merge($resultado, [
                        'titulo' => $noticia->titulo ?? 'Sem título',
                        'texto' => $noticia->sinopse ?? 'Sem texto',
                        'veiculo' => $veiculoTexto,
                        'data_formatada' => $noticia->dt_clipagem ? date('d/m/Y', strtotime($noticia->dt_clipagem)) : 'Sem data',
                        'data_noticia' => $noticia->dt_clipagem,
                        'valor' => $noticia->valor_retorno ?? 0,
                        'tags' => '', // Radio não tem tags diretas
                        'programa' => $programaTexto,
                        'horario' => $noticia->horario ?? '',
                        'duracao' => $noticia->duracao ?? '',
                        'midia' => $noticia->ds_caminho_audio ?? null
                    ]);
                    break;
            }

            return $resultado;

        } catch (\Exception $e) {
            Log::error('Erro ao buscar notícia: ' . $e->getMessage());
            return null;
        }
    }
} 