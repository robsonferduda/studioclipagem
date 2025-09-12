<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MonitoramentoSistemaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','monitoramento-sistema');
    }

    public function index()
    {
        return view('monitoramento_sistema/index');
    }

    public function monitoramentoProgramasRadio()
    {
        $sql = "
        WITH parametros AS (
            SELECT 
                5 AS tolerancia_minutos,          -- Tolerância em minutos
                CURRENT_TIMESTAMP AS momento_atual,
                CURRENT_DATE AS data_atual,
                85.0 AS porcentagem_minima,       -- Nova regra: % mínima aceitável
                2 AS max_gravacoes_faltantes      -- Nova regra: máximo de gravações faltantes tolerado
        ),
        programacoes_radio_do_dia AS (
            -- Esta parte permanece igual
            SELECT 
                her.id AS horario_id,
                er.id AS id_emissora,
                er.nome_emissora,
                her.horario_start,
                her.horario_end,
                (SELECT data_atual FROM parametros) + her.horario_start::time AS horario_inicio_completo,
                (SELECT data_atual FROM parametros) + her.horario_end::time AS horario_fim_completo
            FROM 
                horarios_emissora_radio her
            JOIN 
                emissora_radio er ON her.id_emissora = er.id
            WHERE 
                EXTRACT(DOW FROM (SELECT data_atual FROM parametros)) = ANY (string_to_array(her.dias_da_semana, ',')::int[])
                AND her.deleted_at IS NULL
                AND er.deleted_at IS NULL
                AND er.gravar = TRUE
        ),
        -- Cálculos de tempo permanecem similares
        calculo_tempo_atual AS (
            -- Esta parte permanece igual
            SELECT 
                pr.*,
                (SELECT momento_atual FROM parametros) AS momento_atual,
                CASE
                    WHEN (SELECT momento_atual FROM parametros) < pr.horario_inicio_completo THEN 'Não iniciado'
                    WHEN (SELECT momento_atual FROM parametros) > pr.horario_fim_completo THEN 'Finalizado'
                    ELSE 'Em andamento'
                END AS status_programacao,
                CASE
                    WHEN (SELECT momento_atual FROM parametros) < pr.horario_inicio_completo 
                        THEN INTERVAL '0 minutes'
                    WHEN (SELECT momento_atual FROM parametros) > pr.horario_fim_completo 
                        THEN pr.horario_fim_completo - pr.horario_inicio_completo
                    ELSE (SELECT momento_atual FROM parametros) - pr.horario_inicio_completo
                END AS tempo_decorrido
            FROM 
                programacoes_radio_do_dia pr
        ),
        -- O restante dos cálculos permanece similar
        calculo_gravacoes_esperadas AS (
            SELECT 
                cta.*,
                EXTRACT(EPOCH FROM tempo_decorrido) / 60 AS minutos_decorridos,
                CEIL(EXTRACT(EPOCH FROM tempo_decorrido) / 60 / 15) AS gravacoes_esperadas,
                horario_inicio_completo - (INTERVAL '1 minute' * (SELECT tolerancia_minutos FROM parametros)) AS horario_inicio_com_tolerancia,
                CASE
                    WHEN status_programacao = 'Em andamento' 
                        THEN momento_atual + (INTERVAL '1 minute' * (SELECT tolerancia_minutos FROM parametros))
                    ELSE horario_fim_completo + (INTERVAL '1 minute' * (SELECT tolerancia_minutos FROM parametros))
                END AS horario_fim_com_tolerancia
            FROM 
                calculo_tempo_atual cta
        ),
        contagem_gravacoes AS (
            -- Esta parte permanece igual
            SELECT 
                cge.horario_id,
                cge.id_emissora,
                cge.nome_emissora,
                cge.horario_start,
                cge.horario_end,
                cge.status_programacao,
                cge.minutos_decorridos,
                cge.gravacoes_esperadas,
                COUNT(ger.id) AS gravacoes_encontradas
            FROM 
                calculo_gravacoes_esperadas cge
            LEFT JOIN 
                gravacao_emissora_radio ger
                ON cge.id_emissora = ger.id_emissora
                AND ger.data_hora_inicio >= cge.horario_inicio_com_tolerancia
                AND ger.data_hora_inicio <= cge.horario_fim_com_tolerancia
                AND ger.deleted_at IS NULL
            GROUP BY 
                cge.horario_id, cge.id_emissora, cge.nome_emissora, cge.horario_start, cge.horario_end, 
                cge.status_programacao, cge.minutos_decorridos, cge.gravacoes_esperadas
        )
        SELECT 
            nome_emissora AS \"Emissora\",
            horario_start::time AS \"Horário Início\",
            horario_end::time AS \"Horário Fim\",
            status_programacao AS \"Status da Programação\",
            ROUND(minutos_decorridos) AS \"Minutos Decorridos\",
            gravacoes_esperadas AS \"Gravações Esperadas (15min)\",
            gravacoes_encontradas AS \"Gravações Encontradas\",
            gravacoes_esperadas - gravacoes_encontradas AS \"Gravações Faltantes\",
            ROUND((gravacoes_encontradas::numeric / NULLIF(gravacoes_esperadas, 0)) * 100, 1) AS \"Porcentagem Completa\",
            CASE 
                WHEN gravacoes_encontradas = 0 AND gravacoes_esperadas > 0 THEN 'PROBLEMA CRÍTICO'
                WHEN (gravacoes_esperadas - gravacoes_encontradas) > (SELECT max_gravacoes_faltantes FROM parametros) 
                     AND (gravacoes_encontradas::numeric / NULLIF(gravacoes_esperadas, 0)) * 100 < (SELECT porcentagem_minima FROM parametros)
                     AND gravacoes_esperadas > 0 
                THEN 'PROBLEMA DETECTADO'
                ELSE 'OK'
            END AS \"Status\"
        FROM 
            contagem_gravacoes
        WHERE 
            -- Nova condição que implementa as regras de filtragem
            (gravacoes_encontradas = 0 AND gravacoes_esperadas > 0)
            OR (
                (gravacoes_esperadas - gravacoes_encontradas) > (SELECT max_gravacoes_faltantes FROM parametros) 
                AND (gravacoes_encontradas::numeric / NULLIF(gravacoes_esperadas, 0)) * 100 < (SELECT porcentagem_minima FROM parametros)
                AND gravacoes_esperadas > 0
            )
        ORDER BY 
            status_programacao = 'Em andamento' DESC,
            gravacoes_encontradas = 0 DESC,
            (gravacoes_encontradas::numeric / NULLIF(gravacoes_esperadas, 0)) ASC,
            nome_emissora,
            horario_start;
        ";

        try {
            $resultados = DB::select($sql);
            return response()->json([
                'success' => true,
                'data' => $resultados
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao executar consulta: ' . $e->getMessage()
            ]);
        }
    }

    public function monitoramentoProgramasTv()
    {
        $sql = "
        WITH parametros AS (
            SELECT 
                5 AS tolerancia_minutos,
                CURRENT_TIMESTAMP AS momento_atual,
                CURRENT_DATE AS data_atual
        ),
        programas_do_dia AS (
            SELECT 
                hpw.id AS horario_id,
                hpw.horario_start,
                hpw.horario_end,
                hpw.id_programa,
                p.nome_programa,
                p.tipo_programa,
                -- Horários completos com data
                (SELECT data_atual FROM parametros) + hpw.horario_start::time AS horario_inicio_completo,
                (SELECT data_atual FROM parametros) + hpw.horario_end::time AS horario_fim_completo,
                -- Horários com tolerância
                (SELECT data_atual FROM parametros) + hpw.horario_start::time - 
                    (INTERVAL '1 minute' * (SELECT tolerancia_minutos FROM parametros)) AS horario_inicio_com_tolerancia,
                (SELECT data_atual FROM parametros) + hpw.horario_end::time + 
                    (INTERVAL '1 minute' * (SELECT tolerancia_minutos FROM parametros)) AS horario_fim_com_tolerancia
            FROM 
                horarios_programa_emissora_web hpw
            JOIN 
                programa_emissora_web p ON hpw.id_programa = p.id
            WHERE 
                EXTRACT(DOW FROM (SELECT data_atual FROM parametros)) = ANY (string_to_array(hpw.dias_da_semana, ',')::int[])
                AND hpw.deleted_at IS NULL
                AND p.gravar = TRUE
                AND p.tipo_programa IN (1, 2, 4, 6)  -- Filtro para considerar apenas os tipos 1, 2, 4 e 6
        ),
        calculo_videos_esperados AS (
            SELECT 
                pd.*,
                -- Duração total do programa em minutos
                EXTRACT(EPOCH FROM (pd.horario_fim_completo - pd.horario_inicio_completo)) / 60 AS duracao_total_minutos,
                -- Quanto tempo já passou até agora (em minutos)
                CASE 
                    -- Programa ainda não começou
                    WHEN (SELECT momento_atual FROM parametros) < pd.horario_inicio_completo THEN 0
                    -- Programa já terminou
                    WHEN (SELECT momento_atual FROM parametros) > pd.horario_fim_completo THEN 
                        EXTRACT(EPOCH FROM (pd.horario_fim_completo - pd.horario_inicio_completo)) / 60
                    -- Programa em andamento
                    ELSE EXTRACT(EPOCH FROM ((SELECT momento_atual FROM parametros) - pd.horario_inicio_completo)) / 60
                END AS tempo_decorrido_minutos,
                -- Definir duração do vídeo com base no tipo_programa
                CASE 
                    WHEN pd.tipo_programa = 2 THEN 30 -- Para tipo_programa = 2, duração de 30 minutos
                    ELSE 10 -- Para todos os outros tipos, duração de 10 minutos
                END AS duracao_video,
                -- Vídeos esperados até agora (com base na duração específica do tipo de programa)
                CEIL(
                    CASE 
                        -- Programa ainda não começou
                        WHEN (SELECT momento_atual FROM parametros) < pd.horario_inicio_completo THEN 0
                        -- Programa já terminou
                        WHEN (SELECT momento_atual FROM parametros) > pd.horario_fim_completo THEN 
                            EXTRACT(EPOCH FROM (pd.horario_fim_completo - pd.horario_inicio_completo)) / 60 / 
                            (CASE WHEN pd.tipo_programa = 2 THEN 30 ELSE 10 END)
                        -- Programa em andamento
                        ELSE EXTRACT(EPOCH FROM ((SELECT momento_atual FROM parametros) - pd.horario_inicio_completo)) / 60 / 
                            (CASE WHEN pd.tipo_programa = 2 THEN 30 ELSE 10 END)
                    END
                ) AS videos_esperados_agora
            FROM 
                programas_do_dia pd
        ),
        contagem_videos AS (
            SELECT 
                pd.horario_id,
                pd.nome_programa,
                pd.horario_start,
                pd.horario_end,
                pd.tipo_programa,
                pd.duracao_video,
                pd.duracao_total_minutos,
                pd.tempo_decorrido_minutos,
                pd.videos_esperados_agora,
                COUNT(v.id) AS videos_encontrados,
                CASE
                    WHEN (SELECT momento_atual FROM parametros) < pd.horario_inicio_completo THEN 'Não iniciado'
                    WHEN (SELECT momento_atual FROM parametros) > pd.horario_fim_completo THEN 'Finalizado'
                    ELSE 'Em andamento'
                END AS status_programa
            FROM 
                calculo_videos_esperados pd
            LEFT JOIN 
                videos_programa_emissora_web v 
                ON pd.id_programa = v.id_programa_emissora_web
                AND v.horario_start_gravacao >= pd.horario_inicio_com_tolerancia
                AND v.horario_end_gravacao <= (
                    CASE
                        -- Se o programa ainda está em andamento, considere apenas até o momento atual
                        WHEN (SELECT momento_atual FROM parametros) < pd.horario_fim_completo 
                        THEN LEAST((SELECT momento_atual FROM parametros), pd.horario_fim_com_tolerancia)
                        -- Se já terminou, considere até o fim
                        ELSE pd.horario_fim_com_tolerancia
                    END
                )
            GROUP BY 
                pd.horario_id, pd.nome_programa, pd.horario_start, pd.horario_end, pd.tipo_programa, pd.duracao_video,
                pd.duracao_total_minutos, pd.tempo_decorrido_minutos, pd.videos_esperados_agora,
                pd.horario_inicio_completo, pd.horario_fim_completo
        )
        SELECT 
            nome_programa AS \"Programa\",
            horario_start::time AS \"Horário Início\",
            horario_end::time AS \"Horário Fim\",
            status_programa AS \"Status do Programa\",
            ROUND(tempo_decorrido_minutos) AS \"Minutos Decorridos\",
            duracao_video AS \"Duração de Cada Vídeo (min)\",
            videos_esperados_agora AS \"Vídeos Esperados Até Agora\",
            videos_encontrados AS \"Vídeos Encontrados\",
            CASE 
                WHEN videos_encontrados < videos_esperados_agora AND videos_esperados_agora > 0 THEN 'PROBLEMA DETECTADO'
                ELSE 'OK'
            END AS \"Status\"
        FROM 
            contagem_videos
        WHERE 
            videos_encontrados < videos_esperados_agora AND videos_esperados_agora > 0
        ORDER BY 
            \"Vídeos Encontrados\" asc;
        ";

        try {
            $resultados = DB::select($sql);
            return response()->json([
                'success' => true,
                'data' => $resultados
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao executar consulta: ' . $e->getMessage()
            ]);
        }
    }
}
