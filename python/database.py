#!/usr/bin/env python3
"""
Módulo de acesso ao banco de dados MySQL para relatórios de mídia
Adaptado para usar dados reais das tabelas: app_tv, app_radio, app_jornal, app_web
"""

import psycopg2
import pandas as pd
from datetime import datetime
from config import DB_CONFIG

class DatabaseManager:
    def __init__(self):
        self.connection = None
    
    def connect(self):
        """Conecta ao banco de dados MySQL"""
        try:
            self.connection = psycopg2.connect(
                host=DB_CONFIG['host'],
                port=DB_CONFIG['port'],
                user=DB_CONFIG['username'],
                password=DB_CONFIG['password'],
                database=DB_CONFIG['database']
            )
            print(f"✅ Conectado ao banco: {DB_CONFIG['database']}")
            return True
        except Exception as e:
            print(f"❌ Erro ao conectar: {e}")
            return False
    
    def disconnect(self):
        """Desconecta do banco de dados de forma segura"""
        if self.connection:
            try:
                # Tenta fazer commit de qualquer transação pendente
                if hasattr(self.connection, 'commit'):
                    self.connection.commit()
                
                # Fecha a conexão
                self.connection.close()
                print("🔌 Desconectado do banco com segurança")
                
            except Exception as e:
                print(f"⚠️ Erro ao desconectar: {e}")
                try:
                    # Força fechamento mesmo com erro
                    self.connection.close()
                except:
                    pass
            finally:
                # Garante que a referência seja removida
                self.connection = None
    
    def get_noticias_por_midia(self, usuario_id, data_inicio, data_fim, filtros=None):
        """
        Busca quantidade de notícias por tipo de mídia no período com filtros opcionais,
        usando a nova estrutura: noticia_cliente + tabelas por mídia.
        """
        if not self.connection:
            self.connect()

        cursor = self.connection.cursor()
        try:
            resultados = []

            if not filtros:
                filtros = {}

            ids_especificos = filtros.get('ids_especificos', {})
            usar_ids_especificos = bool(ids_especificos and any(ids_especificos.values()))

            if usar_ids_especificos:
                print("🎯 Usando IDs específicos para contagem de notícias")
                for midia, ids in ids_especificos.items():
                    if ids:
                        midia_nome = {
                            'web': 'Web',
                            'impresso': 'Impresso',
                            'tv': 'TV',
                            'radio': 'Rádio'
                        }.get(midia, midia.capitalize())
                        resultados.append({
                            'midia': midia_nome,
                            'quantidade': len(ids)
                        })
                print(f"📊 Notícias por IDs específicos: {resultados}")
                return resultados

            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])
            status_filtros = filtros.get('status', ['1', '-1', '0'])
            areas_filtros = filtros.get('areas', [])

            def _build_sentimento_condition():
                if status_filtros:
                    placeholders = ', '.join(["%s"] * len(status_filtros))
                    return f"AND nc.sentimento IN ({placeholders})", status_filtros
                return "", []

            def _build_area_condition():
                if areas_filtros:
                    placeholders = ', '.join(["%s"] * len(areas_filtros))
                    return f"AND nc.area IN ({placeholders})", areas_filtros
                return "", []

            midias = {
                'impresso': {'tipo_id': 1, 'tabela': 'noticia_impresso', 'campo_data': 'dt_clipagem'},
                'web': {'tipo_id': 2, 'tabela': 'noticias_web', 'campo_data': 'data_noticia'},
                'radio': {'tipo_id': 3, 'tabela': 'noticia_radio', 'campo_data': 'dt_clipagem'},
                'tv': {'tipo_id': 4, 'tabela': 'noticia_tv', 'campo_data': 'dt_noticia'},
            }

            for nome_midia in tipos_midia:
                info = midias.get(nome_midia)
                if not info:
                    continue

                tipo_id = info['tipo_id']
                tabela = info['tabela']
                campo_data = info['campo_data']

                cond_sentimento, valores_sentimento = _build_sentimento_condition()
                cond_area, valores_area = _build_area_condition()

                query = f"""
                    SELECT COUNT(*) FROM noticia_cliente nc
                    JOIN {tabela} nt ON nt.id = nc.noticia_id
                    WHERE nc.cliente_id = %s
                      AND nc.tipo_id = %s
                      AND nc.deleted_at IS NULL
                      AND nt.deleted_at IS NULL
                      AND nt.{campo_data} BETWEEN %s AND %s
                      {cond_sentimento}
                      {cond_area}
                """
                params = [usuario_id, tipo_id, data_inicio, data_fim] + valores_sentimento + valores_area
                cursor.execute(query, params)
                count = cursor.fetchone()[0]

                resultados.append({
                    'midia': nome_midia.capitalize() if nome_midia != 'tv' else 'TV',
                    'quantidade': count
                })

            for item in resultados:
                print(f"   📊 {item['midia']}: {item['quantidade']} notícias")
            return resultados

        except Exception as e:
            print(f"❌ Erro ao buscar notícias por mídia: {e}")
            return [
                {'midia': 'TV', 'quantidade': 0},
                {'midia': 'Rádio', 'quantidade': 0},
                {'midia': 'Impresso', 'quantidade': 0},
                {'midia': 'Web', 'quantidade': 0}
            ]
    
    def get_valores_por_midia(self, usuario_id, data_inicio, data_fim, filtros=None):
        """
        Soma dos valores de retorno das notícias por tipo de mídia no período com filtros opcionais.
        Usa 'valor_retorno' nas tabelas exceto em 'noticias_web', que usa 'nu_valor'.
        """
        if not self.connection:
            self.connect()

        cursor = self.connection.cursor()
        try:
            valores = []

            if not filtros:
                filtros = {}

            ids_especificos = filtros.get('ids_especificos', {})
            usar_ids_especificos = bool(ids_especificos and any(ids_especificos.values()))

            if usar_ids_especificos:
                print("🎯 Usando IDs específicos para cálculo de valores")
                for midia, ids in ids_especificos.items():
                    if ids:
                        midia_nome = {
                            'web': 'Web',
                            'impresso': 'Impresso',
                            'tv': 'TV',
                            'radio': 'Rádio'
                        }.get(midia, midia.capitalize())
                        valor_total = self._calcular_valor_por_ids(midia, ids)
                        valores.append({'midia': midia_nome, 'valor': valor_total})
                
                total_geral = sum(v['valor'] for v in valores)
                for item in valores:
                    item['percentual'] = (item['valor'] / total_geral * 100) if total_geral > 0 else 0
                return valores

            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])
            status_filtros = filtros.get('status', ['1', '-1', '0'])
            areas_filtros = filtros.get('areas', [])

            def _build_sentimento_condition():
                if status_filtros:
                    placeholders = ', '.join(["%s"] * len(status_filtros))
                    return f"AND nc.sentimento IN ({placeholders})", status_filtros
                return "", []

            def _build_area_condition():
                if areas_filtros:
                    placeholders = ', '.join(['%s'] * len(areas_filtros))
                    return f"AND nc.area IN ({placeholders})", areas_filtros
                return "", []

            midias = {
                'impresso': {'tipo_id': 1, 'tabela': 'noticia_impresso', 'campo_data': 'dt_clipagem', 'campo_valor': 'valor_retorno'},
                'web': {'tipo_id': 2, 'tabela': 'noticias_web', 'campo_data': 'data_noticia', 'campo_valor': 'nu_valor'},
                'radio': {'tipo_id': 3, 'tabela': 'noticia_radio', 'campo_data': 'dt_clipagem', 'campo_valor': 'valor_retorno'},
                'tv': {'tipo_id': 4, 'tabela': 'noticia_tv', 'campo_data': 'dt_noticia', 'campo_valor': 'valor_retorno'},
            }

            for nome_midia in tipos_midia:
                info = midias.get(nome_midia)
                if not info:
                    continue

                tipo_id = info['tipo_id']
                tabela = info['tabela']
                campo_data = info['campo_data']
                campo_valor = info['campo_valor']

                cond_sentimento, valores_sent = _build_sentimento_condition()
                cond_area, valores_area = _build_area_condition()

                query = f"""
                    SELECT COALESCE(SUM(nt.{campo_valor}), 0) 
                    FROM noticia_cliente nc
                    JOIN {tabela} nt ON nt.id = nc.noticia_id
                    WHERE nc.cliente_id = %s
                      AND nc.tipo_id = %s
                      AND nc.deleted_at IS NULL
                      AND nt.deleted_at IS NULL
                      AND nt.{campo_data} BETWEEN %s AND %s
                      {cond_sentimento}
                      {cond_area}
                """

                params = [usuario_id, tipo_id, data_inicio, data_fim] + valores_sent + valores_area
                cursor.execute(query, params)
                valor_total = float(cursor.fetchone()[0] or 0.0)

                valores.append({
                    'midia': nome_midia.capitalize() if nome_midia != 'tv' else 'TV',
                    'valor': round(valor_total, 2)
                })

            total_geral = sum(v['valor'] for v in valores)
            for item in valores:
                item['percentual'] = (item['valor'] / total_geral * 100) if total_geral > 0 else 0

            for item in valores:
                print(f"   💰 {item['midia']}: R$ {item['valor']:,.2f} ({item['percentual']:.1f}%)")
            return valores

        except Exception as e:
            print(f"❌ Erro ao calcular valores por mídia: {e}")
            return [
                {'midia': 'TV', 'valor': 0.0, 'percentual': 0.0},
                {'midia': 'Rádio', 'valor': 0.0, 'percentual': 0.0},
                {'midia': 'Impresso', 'valor': 0.0, 'percentual': 0.0},
                {'midia': 'Web', 'valor': 0.0, 'percentual': 0.0}
            ]
        
    def _seconds_to_time_format(self, seconds_value):
        """Converte segundos para formato 00:00:00"""
        if not seconds_value:
            return "00:00:00"
        
        try:
            # Trata tanto valores numéricos quanto string
            if isinstance(seconds_value, str):
                if seconds_value.strip() == '' or seconds_value.strip() == '0':
                    return "00:00:00"
                seconds = int(seconds_value)
            else:
                seconds = int(seconds_value)
            
            if seconds <= 0:
                return "00:00:00"
                
            hours = seconds // 3600
            minutes = (seconds % 3600) // 60
            secs = seconds % 60
            return f"{hours:02d}:{minutes:02d}:{secs:02d}"
        except:
            return "00:00:00"
    
    def get_clipagens_detalhadas(self, usuario_id, data_inicio, data_fim, limite=None, filtros=None):
        """
        Busca clipagens detalhadas de todas as mídias com formato específico e filtros aplicados (TV por enquanto)
        """
        if not self.connection:
            self.connect()

        cursor = self.connection.cursor()

        try:
            if not filtros:
                filtros = {}

            ids_especificos = filtros.get('ids_especificos', {})
            usar_ids_especificos = bool(ids_especificos and any(ids_especificos.values()))

            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])
            status_filtros = filtros.get('status', ['1', '-1', '0'])
            retorno_filtros = filtros.get('retorno', ['com_retorno', 'sem_retorno'])
            areas_filtros = filtros.get('areas', [])

            clipagens = {
                'TV': [],
                'Rádio': [],
                'Impresso': [],
                'Web': []
            }

            # === IMPRESSO ===
            if 'impresso' in tipos_midia or (usar_ids_especificos and 'impresso' in ids_especificos and ids_especificos['impresso']):
                tipo_id = 1  # Impresso

                if usar_ids_especificos and 'impresso' in ids_especificos and ids_especificos['impresso']:
                    ids_impresso = ids_especificos['impresso']
                    ids_placeholder = ','.join(['%s'] * len(ids_impresso))
                    query_impresso = f"""
                        SELECT 
                            ni.dt_clipagem,
                            ni.titulo,
                            COALESCE(f.nome, 'Fonte Não Identificada') AS jornal_nome,
                            COALESCE(s.ds_sessao, 'Seção') AS secao_nome,
                            COALESCE(e.sg_estado, '') AS uf,
                            COALESCE(ni.sinopse, '') AS sinopse,
                            COALESCE(ni.valor_retorno, 0) AS valor,
                            COALESCE(ni.nu_colunas, 1) AS coluna,
                            COALESCE(ni.nu_largura, 1) AS largura,
                            COALESCE(ni.nu_altura, 1) AS altura
                        FROM noticia_impresso ni
                        JOIN noticia_cliente nc ON nc.noticia_id = ni.id AND nc.tipo_id = %s AND nc.deleted_at IS NULL
                        LEFT JOIN fonte_impressa f ON f.id = ni.id_fonte
                        LEFT JOIN sessao_impresso s ON s.id_sessao_impresso = ni.id_sessao_impresso
                        LEFT JOIN estado e ON e.cd_estado = ni.cd_estado
                        WHERE ni.id IN ({ids_placeholder})
                          AND ni.deleted_at IS NULL
                        ORDER BY ni.dt_clipagem ASC
                    """
                    params_impresso = [tipo_id] + ids_impresso
                else:
                    sentimento_map = {'negativo': -1, 'neutro': 0, 'positivo': 1}
                    sentimentos = [sentimento_map[s] for s in status_filtros if s in sentimento_map]
                    sent_cond = f"AND nc.sentimento IN ({','.join(['%s'] * len(sentimentos))})" if sentimentos else ""
                    area_cond = ""
                    params_area = []

                    if areas_filtros:
                        area_cond = f"AND nc.area IN ({','.join(['%s'] * len(areas_filtros))})"
                        params_area = areas_filtros

                    query_impresso = f"""
                        SELECT 
                            ni.dt_clipagem,
                            ni.titulo,
                            COALESCE(f.nome, 'Fonte Não Identificada') AS jornal_nome,
                            COALESCE(s.ds_sessao, 'Seção') AS secao_nome,
                            COALESCE(e.sg_estado, '') AS uf,
                            COALESCE(ni.sinopse, '') AS sinopse,
                            COALESCE(ni.valor_retorno, 0) AS valor,
                            COALESCE(ni.nu_colunas, 1) AS coluna,
                            COALESCE(ni.nu_largura, 1) AS largura,
                            COALESCE(ni.nu_altura, 1) AS altura
                        FROM noticia_impresso ni
                        JOIN noticia_cliente nc ON nc.noticia_id = ni.id AND nc.tipo_id = %s AND nc.deleted_at IS NULL
                        LEFT JOIN fonte_impressa f ON f.id = ni.id_fonte
                        LEFT JOIN sessao_impresso s ON s.id_sessao_impresso = ni.id_sessao_impresso
                        LEFT JOIN estado e ON e.cd_estado = ni.cd_estado
                        WHERE nc.cliente_id = %s
                          AND ni.deleted_at IS NULL
                          AND ni.dt_clipagem BETWEEN %s AND %s
                          {sent_cond}
                          {area_cond}
                        ORDER BY ni.dt_clipagem ASC
                        {f'LIMIT {limite}' if limite else ''}
                    """
                    params_impresso = [tipo_id, usuario_id, data_inicio, data_fim] + sentimentos + params_area

                cursor.execute(query_impresso, params_impresso)
                impresso_results = cursor.fetchall()

                for row in impresso_results:
                    data_clipping, titulo, jornal, secao, uf, sinopse, valor, coluna, largura, altura = row

                    cm_coluna_real = float(altura or 1) * float(coluna or 1)

                    data_str = f"Data da clipagem: {data_clipping.strftime('%d/%m/%Y')}" if data_clipping else "Data não informada"
                    jornal_completo = f"{jornal}" + (f"/{uf}" if uf else "")
                    titulo_formatado = f"{data_str} | {titulo or 'Sem título'} | {jornal_completo} | {secao}"

                    clipagens['Impresso'].append({
                        'data': data_clipping,
                        'titulo_linha1': titulo_formatado,
                        'titulo_linha2': sinopse or '',
                        'arquivo': '',
                        'sinopse': '',
                        'valor': float(valor or 0.0),
                        'coluna': float(coluna or 1),
                        'largura': float(largura or 1),
                        'altura': float(altura or 1),
                        'cm_coluna': cm_coluna_real
                    })

                    # === WEB ===
                    if 'web' in tipos_midia or (usar_ids_especificos and 'web' in ids_especificos and ids_especificos['web']):
                        tipo_id = 2  # Web

                        if usar_ids_especificos and 'web' in ids_especificos and ids_especificos['web']:
                            ids_web = ids_especificos['web']
                            ids_placeholder = ','.join(['%s'] * len(ids_web))
                            query_web = f"""
                                SELECT 
                                    nw.data_noticia,
                                    nw.titulo_noticia,
                                    COALESCE(s.nome, nw.url_noticia, 'Site Não Identificado') AS site_nome,
                                    COALESCE(nw.url_noticia, '') AS domain,
                                    COALESCE(cnw.conteudo, '') AS conteudo,
                                    COALESCE(nw.nu_valor, 0) AS valor
                                FROM noticias_web nw
                                JOIN noticia_cliente nc ON nc.noticia_id = nw.id AND nc.tipo_id = %s AND nc.deleted_at IS NULL
                                LEFT JOIN fonte_web s ON s.id = nw.id_fonte
                                JOIN conteudo_noticia_web cnw ON cnw.id_noticia_web = nw.id
                                WHERE nw.id IN ({ids_placeholder})
                                  AND nw.deleted_at IS NULL
                                ORDER BY nw.data_noticia ASC
                            """
                            params_web = [tipo_id] + ids_web
                            print(f"🎯 WEB: Usando {len(ids_web)} IDs específicos")
                        else:
                            sentimento_map = {'negativo': -1, 'neutro': 0, 'positivo': 1}
                            sentimentos = [sentimento_map[s] for s in status_filtros if s in sentimento_map]
                            sent_cond = f"AND nc.sentimento IN ({','.join(['%s'] * len(sentimentos))})" if sentimentos else ""
                            area_cond = ""
                            params_area = []

                            if areas_filtros:
                                area_cond = f"AND nc.area IN ({','.join(['%s'] * len(areas_filtros))})"
                                params_area = areas_filtros

                            query_web = f"""
                                SELECT 
                                    nw.data_noticia,
                                    nw.titulo_noticia,
                                    COALESCE(s.nome, nw.url_noticia, 'Site Não Identificado') AS site_nome,
                                    COALESCE(nw.url_noticia, '') AS domain,
                                    COALESCE(cnw.conteudo, '') AS conteudo,
                                    COALESCE(nw.nu_valor, 0) AS valor
                                FROM noticias_web nw
                                JOIN noticia_cliente nc ON nc.noticia_id = nw.id AND nc.tipo_id = %s AND nc.deleted_at IS NULL
                                LEFT JOIN fonte_web s ON s.id = nw.id_fonte
                                JOIN conteudo_noticia_web cnw ON cnw.id_noticia_web = nw.id
                                WHERE nc.cliente_id = %s
                                  AND nw.deleted_at IS NULL
                                  AND nw.data_noticia BETWEEN %s AND %s
                                  {sent_cond}
                                  {area_cond}
                                ORDER BY nw.data_noticia ASC
                                {f'LIMIT {limite}' if limite else ''}
                            """
                            params_web = [tipo_id, usuario_id, data_inicio, data_fim] + sentimentos + params_area

                        cursor.execute(query_web, params_web)
                        web_results = cursor.fetchall()

                        for row in web_results:
                            data_clipping, titulo, site_nome, domain, conteudo, valor = row

                            data_str = f"Data da clipagem: {data_clipping.strftime('%d/%m/%Y')}" if data_clipping else "Data não informada"
                            titulo_formatado = f"{data_str} | {titulo or 'Sem título'} - {site_nome or domain}"

                            clipagens['Web'].append({
                                'data': data_clipping,
                                'titulo_linha1': titulo_formatado,
                                'titulo_linha2': conteudo if conteudo else '',
                                'arquivo': '',
                                'sinopse': '',
                                'valor': float(valor or 0.0)
                            })

            # === RÁDIO ===
            if 'radio' in tipos_midia or (usar_ids_especificos and 'radio' in ids_especificos and ids_especificos['radio']):
                tipo_id = 3  # Rádio

                if usar_ids_especificos and 'radio' in ids_especificos and ids_especificos['radio']:
                    ids_radio = ids_especificos['radio']
                    ids_placeholder = ','.join(['%s'] * len(ids_radio))
                    query_radio = f"""
                        SELECT 
                            nr.dt_cadastro,
                            nr.link,
                            nr.sinopse,
                            nr.duracao,
                            nr.valor_retorno,
                            pr.nome_programa AS programa_nome,
                            em.nome_emissora AS emissora_nome
                        FROM noticia_radio nr
                        JOIN noticia_cliente nc ON nc.noticia_id = nr.id AND nc.tipo_id = %s AND nc.deleted_at IS NULL
                        LEFT JOIN programa_emissora_radio pr ON pr.id = nr.programa_id
                        LEFT JOIN emissora_radio em ON em.id = nr.emissora_id
                        WHERE nr.id IN ({ids_placeholder})
                          AND nr.deleted_at IS NULL
                        ORDER BY nr.dt_cadastro ASC
                    """
                    params_radio = [tipo_id] + ids_radio
                else:
                    sentimento_map = {'negativo': -1, 'neutro': 0, 'positivo': 1}
                    sentimentos = [sentimento_map[s] for s in status_filtros if s in sentimento_map]
                    sent_cond = f"AND nc.sentimento IN ({','.join(['%s'] * len(sentimentos))})" if sentimentos else ""
                    area_cond = ""
                    params_area = []

                    if areas_filtros:
                        area_cond = f"AND nc.area IN ({','.join(['%s'] * len(areas_filtros))})"
                        params_area = areas_filtros

                    query_radio = f"""
                        SELECT 
                            nr.dt_cadastro,
                            nr.link,
                            nr.sinopse,
                            nr.duracao,
                            nr.valor_retorno,
                            pr.nome_programa AS programa_nome,
                            em.nome_emissora AS emissora_nome
                        FROM noticia_radio nr
                        JOIN noticia_cliente nc ON nc.noticia_id = nr.id AND nc.tipo_id = %s AND nc.deleted_at IS NULL
                        LEFT JOIN programa_emissora_radio pr ON pr.id = nr.programa_id
                        LEFT JOIN emissora_radio em ON em.id = nr.emissora_id
                        WHERE nc.cliente_id = %s
                          AND nr.deleted_at IS NULL
                          AND nr.dt_cadastro BETWEEN %s AND %s
                          {sent_cond}
                          {area_cond}
                        ORDER BY nr.dt_cadastro ASC
                        {f'LIMIT {limite}' if limite else ''}
                    """
                    params_radio = [tipo_id, usuario_id, data_inicio, data_fim] + sentimentos + params_area

                cursor.execute(query_radio, params_radio)
                radio_results = cursor.fetchall()

                for row in radio_results:
                    data, link, sinopse, duracao, valor, programa, emissora = row

                    data_str = data.strftime('%d/%m/%Y') if data else 'Data não informada'
                    tempo_seg = duracao.hour * 3600 + duracao.minute * 60 + duracao.second if duracao else 0
                    tempo_str = self._seconds_to_time_format(tempo_seg)

                    emissora = emissora or 'Emissora Não Identificada'
                    programa = programa or 'Programa Não Identificado'
                    arquivo = link or "Arquivo não disponível"

                    linha1 = f"{data_str} - Programa: {programa} - Emissora: {emissora} - Tempo Total: {tempo_str}"
                    linha2 = arquivo
                    sinopse_limpa = (sinopse or '').replace('Sinopse 1 - ', '').replace('Sinopse 1', '').strip()
                    linha3 = f"Sinopse: {sinopse_limpa or 'Não informada'} {tempo_str}"

                    clipagens['Rádio'].append({
                        'data': data,
                        'linha1_data_programa_emissora': linha1,
                        'linha2_arquivo': linha2,
                        'linha3_sinopse': linha3,
                        'tempo': tempo_str,
                        'valor': float(valor or 0.0)
                    })

            # === TV ===
            if 'tv' in tipos_midia or (usar_ids_especificos and 'tv' in ids_especificos and ids_especificos['tv']):
                tipo_id = 4  # TV

                if usar_ids_especificos and 'tv' in ids_especificos and ids_especificos['tv']:
                    ids_tv = ids_especificos['tv']
                    ids_placeholder = ','.join(['%s'] * len(ids_tv))
                    query_tv = f"""
                        SELECT 
                            nt.dt_noticia,
                            nt.link,
                            nt.sinopse,
                            nt.duracao,
                            nt.valor_retorno,
                            pr.nome_programa AS programa_nome,
                            em.nome_emissora AS emissora_nome
                        FROM noticia_tv nt
                        JOIN noticia_cliente nc ON nc.noticia_id = nt.id AND nc.tipo_id = %s AND nc.deleted_at IS NULL
                        LEFT JOIN programa_emissora_web pr ON pr.id = nt.programa_id
                        LEFT JOIN emissora_web em ON em.id = nt.emissora_id
                        WHERE nt.id IN ({ids_placeholder})
                          AND nt.deleted_at IS NULL
                        ORDER BY nt.dt_noticia ASC
                    """
                    params_tv = [tipo_id] + ids_tv
                else:
                    sentimento_map = {'negativo': -1, 'neutro': 0, 'positivo': 1}
                    sentimentos = [sentimento_map[s] for s in status_filtros if s in sentimento_map]
                    sent_cond = f"AND nc.sentimento IN ({','.join(['%s'] * len(sentimentos))})" if sentimentos else ""
                    area_cond = ""
                    params_area = []

                    if areas_filtros:
                        area_cond = f"AND nc.area IN ({','.join(['%s'] * len(areas_filtros))})"
                        params_area = areas_filtros

                    query_tv = f"""
                        SELECT 
                            nt.dt_noticia,
                            nt.link,
                            nt.sinopse,
                            nt.duracao,
                            nt.valor_retorno,
                            pr.nome_programa AS programa_nome,
                            em.nome_emissora AS emissora_nome
                        FROM noticia_tv nt
                        JOIN noticia_cliente nc ON nc.noticia_id = nt.id AND nc.tipo_id = %s AND nc.deleted_at IS NULL
                        LEFT JOIN programa_emissora_web pr ON pr.id = nt.programa_id
                        LEFT JOIN emissora_web em ON em.id = nt.emissora_id
                        WHERE nc.cliente_id = %s
                          AND nt.deleted_at IS NULL
                          AND nt.dt_noticia BETWEEN %s AND %s
                          {sent_cond}
                          {area_cond}
                        ORDER BY nt.dt_noticia ASC
                        {f'LIMIT {limite}' if limite else ''}
                    """
                    params_tv = [tipo_id, usuario_id, data_inicio, data_fim] + sentimentos + params_area

                cursor.execute(query_tv, params_tv)
                tv_results = cursor.fetchall()

                for row in tv_results:
                    data, link, sinopse, duracao, valor, programa, emissora = row

                    data_str = data.strftime('%d/%m/%Y') if data else 'Data não informada'
                    tempo_seg = duracao.hour * 3600 + duracao.minute * 60 + duracao.second if duracao else 0
                    tempo_str = self._seconds_to_time_format(tempo_seg)

                    emissora = emissora or 'Emissora Não Identificada'
                    programa = programa or 'Programa Não Identificado'
                    arquivo = link or "Arquivo não disponível"

                    linha1 = f"{data_str} - Programa: {programa} - Emissora: {emissora} - Tempo Total: {tempo_str}"
                    linha2 = arquivo
                    sinopse_limpa = (sinopse or '').replace('Sinopse 1 - ', '').replace('Sinopse 1', '').strip()
                    linha3 = f"Sinopse: {sinopse_limpa or 'Não informada'} {tempo_str}"

                    clipagens['TV'].append({
                        'data': data,
                        'linha1_data_programa_emissora': linha1,
                        'linha2_arquivo': linha2,
                        'linha3_sinopse': linha3,
                        'tempo': tempo_str,
                        'valor': float(valor or 0.0)
                    })

            print(f"📄 Clipagens detalhadas - TV: {len(clipagens['TV'])}, Rádio: {len(clipagens['Rádio'])}, Impresso: {len(clipagens['Impresso'])}, Web: {len(clipagens['Web'])}")
            return clipagens

        except Exception as e:
            print(f"❌ Erro ao buscar clipagens detalhadas: {e}")
            return {
                'TV': [],
                'Rádio': [],
                'Impresso': [],
                'Web': []
            }

    def get_retornos_tv(self, usuario_id, data_inicio, data_fim, filtros=None):
        """
        Busca dados de retorno de TV para a tabela de retorno no PDF (nova estrutura)
        
        Returns:
            pd.DataFrame: DataFrame com colunas: data_clipagem, emissora, programa, valor
        """
        if not self.connection:
            self.connect()

        cursor = self.connection.cursor()

        try:
            import pandas as pd

            if not filtros:
                filtros = {}

            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])
            status_filtros = filtros.get('status', ['positivo', 'negativo', 'neutro'])
            areas_filtros = filtros.get('areas', [])

            if 'tv' not in tipos_midia:
                return pd.DataFrame(columns=['data_clipagem', 'emissora', 'programa', 'valor'])

            tipo_id = 3  # TV

            # Mapeia sentimentos
            sentimento_map = {'negativo': -1, 'neutro': 0, 'positivo': 1}
            sentimentos = [sentimento_map[s] for s in status_filtros if s in sentimento_map]
            sent_cond = f"AND nc.sentimento IN ({','.join(['%s'] * len(sentimentos))})" if sentimentos else ""

            area_cond = ""
            params_area = []
            if areas_filtros:
                area_cond = f"AND nc.area IN ({','.join(['%s'] * len(areas_filtros))})"
                params_area = areas_filtros

            query = f"""
                SELECT 
                    nt.dt_noticia AS data_clipagem,
                    COALESCE(e.nome_emissora, 'Emissora Não Identificada') AS emissora,
                    COALESCE(p.nome_programa, 'Programa Não Identificado') AS programa,
                    COALESCE(nt.valor_retorno, 0) AS valor
                FROM noticia_tv nt
                JOIN noticia_cliente nc ON nc.noticia_id = nt.id AND nc.tipo_id = %s AND nc.deleted_at IS NULL
                LEFT JOIN programa_emissora_web p ON p.id = nt.programa_id
                LEFT JOIN emissora_web e ON e.id = nt.emissora_id
                WHERE nc.cliente_id = %s
                  AND nt.dt_noticia BETWEEN %s AND %s
                  AND nt.deleted_at IS NULL
                  AND nt.valor_retorno IS NOT NULL
                  AND nt.valor_retorno > 0
                  {sent_cond}
                  {area_cond}
                ORDER BY nt.dt_noticia ASC, e.nome_emissora ASC, p.nome_programa ASC
            """

            params = [tipo_id, usuario_id, data_inicio, data_fim] + sentimentos + params_area
            cursor.execute(query, params)
            data = cursor.fetchall()

            df = pd.DataFrame(data, columns=['data_clipagem', 'emissora', 'programa', 'valor'])
            if not df.empty:
                df['valor'] = df['valor'].astype(float)

            print(f"📺 Retornos de TV encontrados: {len(df)}")
            return df

        except Exception as e:
            print(f"❌ Erro ao buscar retornos de TV: {e}")
            return pd.DataFrame(columns=['data_clipagem', 'emissora', 'programa', 'valor'])

    def get_sentimentos_tv(self, usuario_id, data_inicio, data_fim, filtros=None):
        """Busca dados de sentimento de TV agrupados por cidade (nova estrutura)"""
        if not self.connection:
            self.connect()

        cursor = self.connection.cursor()

        try:
            if not filtros:
                filtros = {}

            areas_filtros = filtros.get('areas', [])

            area_cond = ""
            area_params = []
            if areas_filtros:
                area_cond = f"AND nc.area IN ({','.join(['%s'] * len(areas_filtros))})"
                area_params = areas_filtros

            query = f"""
                SELECT 
                    COALESCE(c.nm_cidade, CONCAT('Cidade ID: ', tv.cd_cidade)) AS cidade,
                    nc.sentimento,
                    COUNT(*) AS quantidade,
                    '' AS tempo_segundos,
                    '' AS valor
                FROM noticia_cliente nc
                INNER JOIN noticia_tv tv ON nc.noticia_id = tv.id
                LEFT JOIN cidade c ON tv.cd_cidade = c.cd_cidade
                LEFT JOIN programa_emissora_web prog ON tv.programa_id = prog.id
                WHERE nc.tipo_id = 3
                  AND nc.cliente_id = %s
                  AND nc.deleted_at IS NULL
                  AND tv.dt_noticia BETWEEN %s AND %s
                  {area_cond}
                GROUP BY tv.cd_cidade, c.nm_cidade, nc.sentimento
                ORDER BY cidade ASC, nc.sentimento ASC
            """

            params = [usuario_id, data_inicio, data_fim] + area_params
            cursor.execute(query, params)
            data = cursor.fetchall()

            df = pd.DataFrame(data, columns=['cidade', 'sentimento', 'quantidade', 'tempo_segundos', 'valor'])

            if not df.empty:
                df['quantidade'] = df['quantidade'].astype(int)
                df['tempo_segundos'] = pd.to_numeric(df['tempo_segundos'], errors='coerce').fillna(0).astype(int)
                df['valor'] = pd.to_numeric(df['valor'], errors='coerce').fillna(0).astype(float)
                df['tempo'] = df['tempo_segundos'].apply(self._seconds_to_time_format)

            print(f"📺 Dados de sentimento TV encontrados: {len(df)}")
            return df

        except Exception as e:
            print(f"❌ Erro ao buscar sentimentos TV: {e}")
            return pd.DataFrame(columns=['cidade', 'sentimento', 'quantidade', 'tempo_segundos', 'valor', 'tempo'])

    def get_status_resumo_por_midia(self, usuario_id, data_inicio, data_fim, filtros=None):
        """
        Busca resumo de sentimento por mídia com base no campo inteiro `sentimento` (-1, 0, 1)
        """
        if not self.connection:
            self.connect()

        cursor = self.connection.cursor()

        try:
            resultados = []

            if not filtros:
                filtros = {}

            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])
            status_filtros = filtros.get('status', ['positivo', 'negativo', 'neutro'])
            areas_filtros = filtros.get('areas', [])

            # Mapeamento dos tipos de mídia para seus respectivos IDs
            tipo_ids = {
                'tv': (3, 'TV'),
                'radio': (2, 'Rádio'),
                'impresso': (1, 'Impresso'),
                'web': (4, 'Web'),
            }

            # Mapeamento do filtro textual para os inteiros
            status_map = {
                'positivo': '1',
                'negativo': '-1',
                'neutro': '0'
            }

            status_valores = [status_map[s] for s in status_filtros if s in status_map]

            for chave_midia, (tipo_id, nome_midia) in tipo_ids.items():
                if chave_midia not in tipos_midia:
                    continue

                query = f"""
                    SELECT 
                        SUM(CASE WHEN sentimento = '1' THEN 1 ELSE 0 END) as positivo,
                        SUM(CASE WHEN sentimento = '-1' THEN 1 ELSE 0 END) as negativo,
                        SUM(CASE WHEN sentimento = '0' THEN 1 ELSE 0 END) as neutro,
                        COUNT(*) as total
                    FROM noticia_cliente
                    WHERE tipo_id = %s
                      AND cliente_id = %s
                      AND deleted_at IS NULL
                      AND created_at BETWEEN %s AND %s
                """

                params = [tipo_id, usuario_id, data_inicio, data_fim]

              

                # Adiciona filtro de áreas, se necessário
                if areas_filtros:
                    area_placeholders = ','.join(['%s'] * len(areas_filtros))
                    query += f" AND area IN (0)"
                    params += areas_filtros

                cursor.execute(query, params)
                row = cursor.fetchone()
                resultados.append({
                    'midia': nome_midia,
                    'positivo': row[0] or 0,
                    'negativo': row[1] or 0,
                    'neutro': row[2] or 0,
                    'total': row[3] or 0
                })

            df = pd.DataFrame(resultados)
            print(f"📊 Resumo de sentimento simplificado gerado para {len(df)} mídias")
            return df

        except Exception as e:
            print(f"❌ Erro ao buscar resumo de sentimento simplificado: {e}")
            return pd.DataFrame(columns=['midia', 'positivo', 'negativo', 'neutro', 'total'])
        

def test_database():
    """Testa as consultas do banco de dados"""
    print("🧪 TESTANDO CONSULTAS DO BANCO DE DADOS")
    print("=" * 50)
    
    db = DatabaseManager()
    
    if not db.connect():
        print("❌ Não foi possível conectar ao banco")
        return
    
    # Teste com cliente 418 no período de março de 2025
    usuario_id = 418 
    data_inicio = '2025-03-01'
    data_fim = '2025-03-31'
    
    print(f"🔍 Testando consultas para usuário {usuario_id}")
    print(f"📅 Período: {data_inicio} até {data_fim}")
    
    # Teste 1: Notícias por mídia
    print(f"\n1️⃣ Testando notícias por mídia...")
    noticias = db.get_noticias_por_midia(usuario_id, data_inicio, data_fim)
    for item in noticias:
        print(f"   📊 {item['midia']}: {item['quantidade']} notícias")
    
    # Teste 2: Valores por mídia
    print(f"\n2️⃣ Testando valores por mídia...")
    valores = db.get_valores_por_midia(usuario_id, data_inicio, data_fim)
    for item in valores:
        print(f"   💰 {item['midia']}: R$ {item['valor']:,.2f} ({item['percentual']:.1f}%)")
    
    # Teste 3: Clipagens detalhadas
    print(f"\n3️⃣ Testando clipagens detalhadas...")
    clipagens = db.get_clipagens_detalhadas(usuario_id, data_inicio, data_fim)
    for midia, lista in clipagens.items():
        print(f"   📺 {midia}: {len(lista)} clipagens")
        for clip in lista[:2]:  # Mostra apenas 2 para não poluir
            if 'linha1_data_programa_emissora' in clip:
                print(f"     - {clip['data']}: {clip['linha1_data_programa_emissora'][:50]}...")
            elif 'titulo_linha1' in clip:
                print(f"     - {clip['data']}: {clip['titulo_linha1'][:50]}...")
    
    db.disconnect()
    print(f"\n✅ Teste concluído!")

if __name__ == "__main__":
    test_database() 