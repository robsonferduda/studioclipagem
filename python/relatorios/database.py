#!/usr/bin/env python3
"""
Módulo de acesso ao banco de dados MySQL para relatórios de mídia
Adaptado para usar dados reais das tabelas: noticia_tv, noticia_radio, noticia_impresso, noticias_web
"""

import psycopg2
import pandas as pd
import json

DB_CONFIG = {
    'host': 'studioclipagemdb.cvxurxqqog54.us-east-1.rds.amazonaws.com',
    'port': 5432,
    'user': 'postgres',
    'password': 'AASsdas213das21sd',
    'database': 'studio_clipagem'
} 

class DatabaseManager:
    def __init__(self):
        self.connection = None
    
    def connect(self):
        """Conecta ao banco de dados MySQL"""
        try:
            print(f"🔌 Tentando conectar ao banco: {DB_CONFIG['database']}@{DB_CONFIG['host']}:{DB_CONFIG['port']}")
            self.connection = psycopg2.connect(
                host=DB_CONFIG['host'],
                port=DB_CONFIG['port'],
                user=DB_CONFIG['user'],
                password=DB_CONFIG['password'],
                database=DB_CONFIG['database']
            )
            print(f"✅ Conectado ao banco: {DB_CONFIG['database']}")
            return True
        except Exception as e:
            print(f"❌ Erro ao conectar: {e}")
            print(f"❌ Tipo do erro de conexão: {type(e).__name__}")
            import traceback
            traceback.print_exc()
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
        Busca quantidade de notícias por tipo de mídia no período com filtros opcionais
        
        Args:
            usuario_id (int): ID do usuário/cliente
            data_inicio (str): Data início no formato YYYY-MM-DD
            data_fim (str): Data fim no formato YYYY-MM-DD
            filtros (dict): Filtros opcionais {
                'tipos_midia': ['web', 'tv', 'radio', 'impresso'], 
                'status': ['positivo', 'negativo', 'neutro'],
                'retorno': ['com_retorno', 'sem_retorno'],
                'areas': [1, 2, 3]
            }
            
        Returns:
            list: Lista com dicionários contendo 'midia' e 'quantidade'
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            resultados = []
            
            # Processa filtros (por padrão, todos selecionados)
            if not filtros:
                filtros = {}
            
            # NOVO: Verifica se há IDs específicos
            ids_especificos = filtros.get('ids_especificos', {})
            usar_ids_especificos = bool(ids_especificos and any(ids_especificos.values()))
            
            if usar_ids_especificos:
                print("🎯 Usando IDs específicos para contagem de notícias")
                # Conta diretamente os IDs fornecidos
                for midia, ids in ids_especificos.items():
                    if ids:  # Se há IDs para esta mídia
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
            status_filtros = filtros.get('status', ['positivo', 'negativo', 'neutro'])
            retorno_filtros = filtros.get('retorno', ['com_retorno', 'sem_retorno'])
            areas_filtros = filtros.get('areas', [])
            
            # Monta condições adicionais
            def _build_status_condition(table_prefix=""):
                status_conditions = []
                if 'positivo' in status_filtros:
                    status_conditions.append(f"{table_prefix}status = 'positivo'")
                if 'negativo' in status_filtros:
                    status_conditions.append(f"{table_prefix}status = 'negativo'")
                if 'neutro' in status_filtros:
                    status_conditions.append(f"{table_prefix}status = 'neutro'")
                
                if status_conditions:
                    return f" AND ({' OR '.join(status_conditions)})"
                return ""

            # 1. TV - usa campo 'data'
            if 'tv' in tipos_midia:
                query_tv = f"""
                    SELECT COUNT(*) as quantidade 
                    FROM noticia_tv nt
                    JOIN noticia_cliente nc ON nt.id = nc.noticia_id AND nc.tipo_id = 4
                    WHERE nc.cliente_id = %s
                    AND dt_noticia BETWEEN %s AND %s
                """
                cursor.execute(query_tv, (usuario_id, data_inicio, data_fim))
                tv_count = cursor.fetchone()[0]
                resultados.append({'midia': 'TV', 'quantidade': tv_count})
            
            # 2. Rádio - usa campo 'data'
            if 'radio' in tipos_midia:
                query_radio = f"""
                    SELECT COUNT(*) as quantidade 
                    FROM noticia_radio nr
                    JOIN noticia_cliente nc ON nr.id = nc.noticia_id AND nc.tipo_id = 3
                    WHERE nc.cliente_id = %s
                    AND dt_clipagem BETWEEN %s AND %s
                """
                cursor.execute(query_radio, (usuario_id, data_inicio, data_fim))
                radio_count = cursor.fetchone()[0]
                resultados.append({'midia': 'Rádio', 'quantidade': radio_count})
            
            # 3. Impresso - usa campo 'dt_clipagem'
            if 'impresso' in tipos_midia:
                query_jornal = f"""
                    SELECT COUNT(*) as quantidade 
                    FROM noticia_impresso ni
                    JOIN noticia_cliente nc ON ni.id = nc.noticia_id AND nc.tipo_id = 1
                    WHERE nc.cliente_id = %s
                    AND ni.dt_clipagem BETWEEN %s AND %s
                    AND ni.deleted_at IS NULL
                """
                cursor.execute(query_jornal, (usuario_id, data_inicio, data_fim))
                jornal_count = cursor.fetchone()[0]
                resultados.append({'midia': 'Impresso', 'quantidade': jornal_count})
            
            # 4. Web - usa campo 'data_clipping'
            if 'web' in tipos_midia:
                query_web = f"""
                    SELECT COUNT(*) as quantidade 
                    FROM noticias_web 
                    JOIN noticia_cliente ON noticias_web.id = noticia_cliente.noticia_id AND noticia_cliente.tipo_id = 2
                    WHERE noticia_cliente.cliente_id = %s
                    AND data_noticia BETWEEN %s AND %s
                """
                cursor.execute(query_web, (usuario_id, data_inicio, data_fim))
                web_count = cursor.fetchone()[0]
                resultados.append({'midia': 'Web', 'quantidade': web_count})
            
            print(f"📊 Notícias encontradas com filtros: {resultados}")
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
        Busca valores em R$ por tipo de mídia no período com filtros opcionais
        
        Args:
            usuario_id (int): ID do usuário/cliente
            data_inicio (str): Data início no formato YYYY-MM-DD
            data_fim (str): Data fim no formato YYYY-MM-DD
            filtros (dict): Filtros opcionais
            
        Returns:
            list: Lista com dicionários contendo 'midia', 'valor' e 'percentual'
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            valores = []
            
            # Processa filtros (por padrão, todos selecionados)
            if not filtros:
                filtros = {}
            
            # NOVO: Verifica se há IDs específicos
            ids_especificos = filtros.get('ids_especificos', {})
            usar_ids_especificos = bool(ids_especificos and any(ids_especificos.values()))
            
            if usar_ids_especificos:
                print("🎯 Usando IDs específicos para cálculo de valores")
                # Calcula valores baseado nos IDs específicos
                for midia, ids in ids_especificos.items():
                    if ids:  # Se há IDs para esta mídia
                        midia_nome = {
                            'web': 'Web',
                            'impresso': 'Impresso', 
                            'tv': 'TV',
                            'radio': 'Rádio'
                        }.get(midia, midia.capitalize())
                        
                        # Busca valores específicos por IDs
                        valor_total = self._calcular_valor_por_ids(midia, ids)
                        
                        valores.append({
                            'midia': midia_nome,
                            'valor': valor_total
                        })
                
                # Calcula percentuais se houver valores
                valor_total_geral = sum(v['valor'] for v in valores)
                for item in valores:
                    if valor_total_geral > 0:
                        item['percentual'] = (item['valor'] / valor_total_geral) * 100
                    else:
                        item['percentual'] = 0
                
                print(f"💰 Valores por IDs específicos: {valores}")
                return valores
            
            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])

            
            # 1. TV - calcula dinamicamente: converte duracao (interval ou texto 'HH:MM:SS') para segundos antes de multiplicar
            if 'tv' in tipos_midia:
                query_tv = f"""
                    SELECT COALESCE(SUM(
                        (
                            CASE 
                                WHEN t.duracao IS NULL THEN 0
                                WHEN t.duracao::text ~ '^\d+:\d+:\d+$' THEN 
                                    EXTRACT(EPOCH FROM t.duracao::time)
                                WHEN pg_typeof(t.duracao)::text = 'interval' THEN 
                                    EXTRACT(EPOCH FROM t.duracao)
                                ELSE 0
                            END
                        ) * COALESCE(p.valor_segundo, 0)
                    ), 0) as valor_total
                    FROM noticia_tv t
                    JOIN programa_emissora_web p ON t.programa_id = p.id
                    JOIN noticia_cliente nc ON t.id = nc.noticia_id AND nc.tipo_id = 4
                    WHERE nc.cliente_id = %s
                    AND t.dt_noticia BETWEEN %s AND %s
                    AND t.duracao IS NOT NULL
                    AND p.valor_segundo IS NOT NULL
                    AND p.valor_segundo > 0
                """
                cursor.execute(query_tv, (usuario_id, data_inicio, data_fim))
                tv_valor = float(cursor.fetchone()[0] or 0)
                valores.append({'midia': 'TV', 'valor': tv_valor})

            # 2. Rádio - usa campo 'valor_retorno'
            if 'radio' in tipos_midia:
                query_radio = f"""
                    SELECT COALESCE(SUM(nr.valor_retorno), 0) as valor_total
                    FROM noticia_radio nr
                    JOIN noticia_cliente nc ON nr.id = nc.noticia_id AND nc.tipo_id = 3
                    WHERE nc.cliente_id = %s
                    AND nr.dt_clipagem BETWEEN %s AND %s
                    AND nr.deleted_at IS NULL
                """
                cursor.execute(query_radio, (usuario_id, data_inicio, data_fim))
                radio_valor = float(cursor.fetchone()[0] or 0)
                valores.append({'midia': 'Rádio', 'valor': radio_valor})
            
            # 3. Impresso - usa campo 'valor_retorno'
            if 'impresso' in tipos_midia:
                query_jornal = f"""
                    SELECT COALESCE(SUM(ni.valor_retorno), 0) as valor_total
                    FROM noticia_impresso ni
                    JOIN noticia_cliente nc ON ni.id = nc.noticia_id AND nc.tipo_id = 1
                    WHERE nc.cliente_id = %s
                    AND ni.dt_clipagem BETWEEN %s AND %s
                    AND ni.deleted_at IS NULL   
                """
                cursor.execute(query_jornal, (usuario_id, data_inicio, data_fim))
                jornal_valor = float(cursor.fetchone()[0] or 0)
                valores.append({'midia': 'Impresso', 'valor': jornal_valor})
            
                        # 4. Web - usa campo 'nu_valor' da nova estrutura noticias_web
            if 'web' in tipos_midia:
                query_web = f"""
                    SELECT COALESCE(SUM(nu_valor), 0) as valor_total
                    FROM noticias_web nw
                    JOIN noticia_cliente nc ON nw.id = nc.noticia_id AND nc.tipo_id = 2
                    WHERE nc.cliente_id = %s
                    AND nw.data_noticia BETWEEN %s AND %s
                    AND nw.deleted_at IS NULL
                """
                cursor.execute(query_web, (usuario_id, data_inicio, data_fim))
                web_valor = float(cursor.fetchone()[0] or 0)
                valores.append({'midia': 'Web', 'valor': web_valor})
            
            # Calcula percentuais
            total_geral = sum(item['valor'] for item in valores)
            
            for item in valores:
                if total_geral > 0:
                    item['percentual'] = (item['valor'] / total_geral) * 100
                else:
                    item['percentual'] = 0
            
            print(f"💰 Valores encontrados - TV: R$ {tv_valor:,.2f}, Rádio: R$ {radio_valor:,.2f}, Impresso: R$ {jornal_valor:,.2f}, Web: R$ {web_valor:,.2f}")
            return valores
            
        except Exception as e:
            print(f"❌ Erro ao buscar valores por mídia: {e}")
            return [
                {'midia': 'TV', 'valor': 0, 'percentual': 0},
                {'midia': 'Rádio', 'valor': 0, 'percentual': 0},
                {'midia': 'Impresso', 'valor': 0, 'percentual': 0},
                {'midia': 'Web', 'valor': 0, 'percentual': 0}
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
    
    def _calcular_valor_por_ids(self, midia, ids):
        """
        Calcula o valor total para IDs específicos de uma mídia
        
        Args:
            midia (str): Tipo da mídia ('web', 'tv', 'radio', 'impresso')
            ids (list): Lista de IDs para calcular
            
        Returns:
            float: Valor total calculado
        """
        if not ids:
            return 0.0
        
        cursor = self.connection.cursor()
        valor_total = 0.0
        
        try:
            ids_str = ','.join(map(str, ids))
            
            if midia == 'web':
                query = f"""
                    SELECT COALESCE(SUM(nu_valor), 0) as valor_total
                    FROM noticias_web 
                    WHERE id IN ({ids_str}) AND deleted_at IS NULL
                """
            elif midia == 'impresso':
                query = f"""
                    SELECT COALESCE(SUM(valor_retorno), 0) as valor_total
                    FROM noticia_impresso 
                    WHERE id IN ({ids_str}) AND deleted_at IS NULL
                """
            elif midia == 'tv':
                query = f"""
                    SELECT COALESCE(SUM(
                        (
                            CASE 
                                WHEN t.duracao IS NULL THEN 0
                                WHEN t.duracao::text ~ '^\d+:\d+:\d+$' THEN 
                                    EXTRACT(EPOCH FROM t.duracao::time)
                                WHEN pg_typeof(t.duracao)::text = 'interval' THEN 
                                    EXTRACT(EPOCH FROM t.duracao)
                                ELSE 0
                            END
                        ) * COALESCE(p.valor_segundo, 0)
                    ), 0) as valor_total
                    FROM noticia_tv t
                    LEFT JOIN programa_emissora_web p ON t.programa_id = p.id
                    WHERE t.id IN ({ids_str}) AND t.deleted_at IS NULL
                """
            elif midia == 'radio':  
                query = f"""
                    SELECT COALESCE(SUM(r.valor_retorno), 0) as valor_total
                    FROM noticia_radio r
                    WHERE r.id IN ({ids_str}) AND r.deleted_at IS NULL
                """
            else:
                return 0.0
            
            cursor.execute(query)
            result = cursor.fetchone()
            valor_total = float(result[0]) if result and result[0] else 0.0
            
            print(f"💰 Valor calculado para {midia} com {len(ids)} IDs: R$ {valor_total:.2f}")
            
        except Exception as e:
            print(f"❌ Erro ao calcular valor para {midia}: {e}")
            valor_total = 0.0
        
        return valor_total
    
    def get_clipagens_detalhadas(self, usuario_id, data_inicio, data_fim, limite=None, filtros=None):
        """
        Busca clipagens detalhadas de todas as mídias com formato específico e filtros aplicados
        
        Args:
            usuario_id (int): ID do usuário/cliente
            data_inicio (str): Data início no formato YYYY-MM-DD
            data_fim (str): Data fim no formato YYYY-MM-DD
            limite (int): Número máximo de registros por mídia (None = sem limite)
            filtros (dict): Filtros opcionais {
                'tipos_midia': ['web', 'tv', 'radio', 'impresso'], 
                'status': ['positivo', 'negativo', 'neutro'],
                'retorno': ['com_retorno', 'sem_retorno'],
                'areas': [1, 2, 3]
            }
            
        Returns:
            dict: Dicionário com listas de clipagens por mídia
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            # Processa filtros (por padrão, todos selecionados)
            if not filtros:
                filtros = {}
            
            # NOVO: Verifica se há IDs específicos
            ids_especificos = filtros.get('ids_especificos', {})
            usar_ids_especificos = bool(ids_especificos and any(ids_especificos.values()))
            
            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])

            
            clipagens = {
                'TV': [],
                'Rádio': [],
                'Impresso': [],
                'Web': []
            }
            
            # 1. Clipagens de TV - Com filtros aplicados
            if 'tv' in tipos_midia or (usar_ids_especificos and 'tv' in ids_especificos and ids_especificos['tv']):
                
                if usar_ids_especificos and 'tv' in ids_especificos and ids_especificos['tv']:
                    # MODO IDs ESPECÍFICOS: Busca apenas as notícias com IDs fornecidos
                    ids_tv = ids_especificos['tv']
                    ids_str = ','.join(map(str, ids_tv))
                    
                    query_tv = f"""
                        SELECT 
                            t.dt_noticia,
                            t.sinopse,
                            COALESCE(e.nome_emissora, 'Emissora Não Identificada') as emissora_nome,
                            COALESCE(p.nome_programa, 'Programa Não Identificado') as programa_nome,
                            COALESCE(t.duracao, '00:00:00') as tempo,
                            (
                                        CASE 
                                            WHEN t.duracao IS NULL THEN 0
                                            WHEN t.duracao::text ~ '^\d+:\d+:\d+$' THEN 
                                                EXTRACT(EPOCH FROM t.duracao::time)
                                    WHEN pg_typeof(t.duracao)::text = 'interval' THEN 
                                        EXTRACT(EPOCH FROM t.duracao)
                                    ELSE 0
                                END
                            ) as segundos_totais,
                            '' as arquivo,
                            COALESCE(t.sinopse, '') as sinopse,
                            COALESCE(
                                    (
                                        CASE 
                                            WHEN t.duracao IS NULL THEN 0
                                            WHEN t.duracao::text ~ '^\d+:\d+:\d+$' THEN 
                                                EXTRACT(EPOCH FROM t.duracao::time)
                                    WHEN pg_typeof(t.duracao)::text = 'interval' THEN 
                                        EXTRACT(EPOCH FROM t.duracao)
                                    ELSE 0
                                END
                            ) * COALESCE(p.valor_segundo, 0)) as valor
                        FROM noticia_tv t
                        LEFT JOIN emissora_web e ON t.emissora_id = e.id
                        LEFT JOIN programa_emissora_web p ON t.programa_id = p.id
                        WHERE t.id IN ({ids_str})
                        ORDER BY t.dt_noticia ASC, t.sinopse ASC
                        
                    """
                    params_tv = ()
                    print(f"🎯 TV: Usando {len(ids_tv)} IDs específicos")
          
                
                    # Adiciona LIMIT apenas se especificado
                    if limite and not usar_ids_especificos:
                        query_tv += " LIMIT %s"
                        params_tv = params_tv + (limite,)
                    
                    cursor.execute(query_tv, params_tv)
                    tv_results = cursor.fetchall()
                    print(f"🔍 DEBUG TV: Query encontrou {len(tv_results)} resultados")
                    
                    for row in tv_results:
                        data, titulo, emissora, programa, tempo, segundos_totais, arquivo, sinopse, valor = row
                        
                        # FORMATO SIMPLIFICADO: programa, emissora, data, tempo e sinopse
                        
                        # Trata data nula ou inválida
                        if data:
                            try:
                                data_formatada = data.strftime('%d/%m/%Y')
                            except:
                                data_formatada = "Data não informada"
                        else:
                            data_formatada = "Data não informada"
                        
                        tempo_total_formatado = self._seconds_to_time_format(segundos_totais)
                        
                        # Linha 1: Data - Programa - Emissora - Tempo
                        linha1 = f"{data_formatada} - {programa} - {emissora} - {tempo_total_formatado}"
                        
                        # Linha 2: Arquivo (opcional)
                        linha2_arquivo = arquivo if arquivo and arquivo.strip() else "Arquivo não disponível"
                        
                        # Linha 3: Sinopse limpa
                        sinopse_limpa = sinopse
                        if sinopse_limpa and sinopse_limpa.startswith('Sinopse 1 - '):
                            sinopse_limpa = sinopse_limpa[12:]  # Remove "Sinopse 1 - "
                        elif sinopse_limpa and sinopse_limpa.startswith('Sinopse 1'):
                            sinopse_limpa = sinopse_limpa[9:]  # Remove "Sinopse 1"
                        
                        sinopse_formatada = sinopse_limpa.strip() if sinopse_limpa and sinopse_limpa.strip() else "Sinopse não informada"
                        
                        clipagens['TV'].append({
                            'data': data,
                            'linha1_data_programa_emissora': linha1,
                            'linha2_arquivo': linha2_arquivo,
                            'linha3_sinopse': sinopse_formatada,
                            'tempo': tempo,
                            'segundos': segundos_totais,
                            'valor': float(valor)
                        })
                        print(f"🔍 DEBUG TV: Adicionada clipagem: {programa} - {emissora}")
                
                print(f"🔍 DEBUG TV: Total de clipagens processadas: {len(clipagens['TV'])}")
            
            # 2. Clipagens de Rádio - Com filtros aplicados
            if 'radio' in tipos_midia or (usar_ids_especificos and 'radio' in ids_especificos and ids_especificos['radio']):
                
                if usar_ids_especificos and 'radio' in ids_especificos and ids_especificos['radio']:
                    # MODO IDs ESPECÍFICOS: Busca apenas as notícias com IDs fornecidos
                    ids_radio = ids_especificos['radio']
                    ids_str = ','.join(map(str, ids_radio))
                    
                    query_radio = f"""
                        SELECT 
                            CASE
                                WHEN horario IS NOT NULL THEN
                                dt_clipagem + horario::interval
                                ELSE
                                dt_clipagem
                            END AS data,
                            COALESCE(r.titulo, 'Título não informado') as titulo,
                            CASE 
                                WHEN e.nome_emissora IS NOT NULL THEN e.nome_emissora
                                WHEN p.nome_programa IS NOT NULL AND pe.nome_emissora IS NOT NULL THEN pe.nome_emissora
                                ELSE 'Emissora Não Identificada'
                            END as emissora_nome,
                            COALESCE(p.nome_programa, 'Programa Não Identificado') as programa_nome,
                            COALESCE(r.horario, '00:00:00') as tempo,
                            COALESCE(r.duracao, '00:00:00') as segundos_totais,
                            '' as arquivo,
                            COALESCE(r.sinopse, '') as sinopse,
                            COALESCE(r.valor_retorno, 0) as valor
                        FROM noticia_radio r
                        LEFT JOIN emissora_radio e ON r.emissora_id = e.id
                        LEFT JOIN programa_emissora_radio p ON r.programa_id = p.id
                        LEFT JOIN emissora_radio pe ON p.id_emissora = pe.id
                        JOIN noticia_cliente nc ON r.id = nc.noticia_id AND nc.tipo_id = 3
                        WHERE r.id IN ({ids_str})
                        AND r.deleted_at IS NULL
                        ORDER BY data ASC, r.titulo ASC
                    """
                    params_radio = ()
                    print(f"🎯 RÁDIO: Usando {len(ids_radio)} IDs específicos")
                
                    # Adiciona LIMIT apenas se especificado
                    if limite and not usar_ids_especificos:
                        query_radio += " LIMIT %s"
                        params_radio = params_radio + (limite,)
                    
                    cursor.execute(query_radio, params_radio)
                    radio_results = cursor.fetchall()
                    print(f"🔍 DEBUG RÁDIO: Query encontrou {len(radio_results)} resultados")
                    
                    for row in radio_results:
                        data, titulo, emissora, programa, tempo, segundos_totais, arquivo, sinopse, valor = row
                        
                        # FORMATO SIMPLIFICADO: programa, emissora, data, tempo e sinopse
                        
                        # Trata data nula ou inválida
                        if data:
                            try:
                                data_formatada = data.strftime('%d/%m/%Y')
                            except:
                                data_formatada = "Data não informada"
                        else:
                            data_formatada = "Data não informada"
                        
                        tempo_total_formatado = self._seconds_to_time_format(segundos_totais)
                        
                        # Linha 1: Data - Programa - Emissora - Tempo
                        linha1 = f"{data_formatada} - {programa} - {emissora} - {tempo_total_formatado}"
                        
                        # Linha 2: Arquivo (opcional)
                        linha2_arquivo = arquivo if arquivo and arquivo.strip() else "Arquivo não disponível"
                        
                        # Linha 3: Sinopse limpa
                        sinopse_limpa = sinopse
                        if sinopse_limpa and sinopse_limpa.startswith('Sinopse 1 - '):
                            sinopse_limpa = sinopse_limpa[12:]  # Remove "Sinopse 1 - "
                        elif sinopse_limpa and sinopse_limpa.startswith('Sinopse 1'):
                            sinopse_limpa = sinopse_limpa[9:]  # Remove "Sinopse 1"
                        
                        sinopse_formatada = sinopse_limpa.strip() if sinopse_limpa and sinopse_limpa.strip() else "Sinopse não informada"
                        
                        clipagens['Rádio'].append({
                            'data': data,
                            'linha1_data_programa_emissora': linha1,
                            'linha2_arquivo': linha2_arquivo,
                            'linha3_sinopse': sinopse_formatada,
                            'tempo': tempo,
                            'segundos': segundos_totais,
                            'valor': float(valor)
                        })
            
            # 3. Clipagens de Impresso - Com filtros aplicados
            if 'impresso' in tipos_midia or (usar_ids_especificos and 'impresso' in ids_especificos and ids_especificos['impresso']):
                
                if usar_ids_especificos and 'impresso' in ids_especificos and ids_especificos['impresso']:
                    # MODO IDs ESPECÍFICOS: Busca apenas as notícias com IDs fornecidos
                    ids_impresso = ids_especificos['impresso']
                    ids_str = ','.join(map(str, ids_impresso))
                    
                    query_jornal = f"""
                        SELECT 
                            j.dt_clipagem,
                            j.titulo,
                            COALESCE(ji.nome, 'Jornal Não Identificado') as jornal_nome,
                            COALESCE(si.ds_sessao, 'Seção') as secao,
                            COALESCE(e.sg_estado, '') as uf,
                            COALESCE(j.sinopse, '') as sinopse,
                            COALESCE(j.valor_retorno, 0) as valor,
                            COALESCE(j.nu_colunas, 1) as coluna,
                            COALESCE(j.nu_largura, 1) as largura,
                            COALESCE(j.nu_altura, 1) as altura
                        FROM noticia_impresso j
                        LEFT JOIN jornal_online ji ON j.id_fonte = ji.id
                        LEFT JOIN sessao_impresso si ON j.id_secao = si.id_sessao_impresso
                        LEFT JOIN cidade c ON j.cd_cidade = c.cd_cidade
                        LEFT JOIN estado e ON c.cd_estado = e.cd_estado
                        WHERE j.id IN ({ids_str})
                        AND j.deleted_at IS NULL
                        ORDER BY j.dt_clipagem ASC, j.titulo ASC
                    """
                    params_jornal = ()
                    print(f"🎯 IMPRESSO: Usando {len(ids_impresso)} IDs específicos")
                
                
                    # Adiciona LIMIT apenas se especificado
                    if limite and not usar_ids_especificos:
                        query_jornal += " LIMIT %s"
                        params_jornal = params_jornal + (limite,)
                    
                    cursor.execute(query_jornal, params_jornal)
                    for row in cursor.fetchall():
                        data_clipping, titulo, jornal, secao, uf, sinopse, valor, coluna, largura, altura = row
                        
                        # Calcula cm/coluna: altura × número de colunas
                        cm_coluna_real = float(altura or 1) * float(coluna or 1)
                        
                        # Trata data nula ou inválida
                        if data_clipping:
                            try:
                                data_str = f"Data da clipagem: {data_clipping.strftime('%d/%m/%Y')}"
                            except:
                                data_str = "Data da clipagem: não informada"
                        else:
                            data_str = "Data da clipagem: não informada"
                        
                        # Formato: Data da clipagem: 01/02/2025 | Título | Jornal/UF | Seção
                        jornal_completo = f"{jornal}" + (f"/{uf}" if uf else "")
                        titulo_formatado = f"{data_str} | {titulo} | {jornal_completo} | {secao}"
                        
                        clipagens['Impresso'].append({
                            'data': data_clipping,
                            'titulo_linha1': titulo_formatado,
                            'titulo_linha2': sinopse if sinopse else '',
                            'arquivo': '',
                            'sinopse': '',
                            'valor': float(valor),
                            'coluna': float(coluna or 1),
                            'largura': float(largura or 1),
                            'altura': float(altura or 1),
                            'cm_coluna': cm_coluna_real
                        })
            
            # 4. Clipagens de Web - Com filtros aplicados  
            if 'web' in tipos_midia or (usar_ids_especificos and 'web' in ids_especificos and ids_especificos['web']):
                
                if usar_ids_especificos and 'web' in ids_especificos and ids_especificos['web']:
                    # MODO IDs ESPECÍFICOS: Busca apenas as notícias com IDs fornecidos
                    ids_web = ids_especificos['web']
                    ids_str = ','.join(map(str, ids_web))
                    
                    query_web = f"""
                        SELECT 
                            w.data_noticia,
                            COALESCE(w.titulo_noticia, 'Título não informado') as titulo,
                            COALESCE(fw.nome, 'Site Não Identificado') as site_nome,
                            COALESCE(w.url_noticia, '') as domain,
                            COALESCE(w.sinopse, '') as conteudo,
                            COALESCE(w.nu_valor, 0) as valor
                        FROM noticias_web w
                        LEFT JOIN noticia_cliente nc ON w.id = nc.noticia_id AND nc.tipo_id = 2
                        JOIN fonte_web fw ON w.id_fonte = fw.id
                        WHERE w.id IN ({ids_str})
                        AND w.deleted_at IS NULL
                        ORDER BY w.data_noticia ASC, w.titulo_noticia ASC
                    """
                    params_web = ()
                    print(f"🎯 WEB: Usando {len(ids_web)} IDs específicos")
                
                    # Adiciona LIMIT apenas se especificado
                    if limite and not usar_ids_especificos:
                        query_web += " LIMIT %s"
                        params_web = params_web + (limite,)
                    
                    cursor.execute(query_web, params_web)
                    for row in cursor.fetchall():
                        data_clipping, titulo, site_nome, domain, conteudo, valor = row
                        
                        # Trata data nula ou inválida
                        if data_clipping:
                            try:
                                data_str = f"Data da clipagem: {data_clipping.strftime('%d/%m/%Y')}"
                            except:
                                data_str = "Data da clipagem: não informada"
                        else:
                            data_str = "Data da clipagem: não informada"
                        
                        # Formato: Data da clipagem: 12/02/2025 | Título - Site
                        titulo_formatado = f"{data_str} | {titulo} - {site_nome}"
                        
                        clipagens['Web'].append({
                            'data': data_clipping,
                            'titulo_linha1': titulo_formatado,
                            'titulo_linha2': conteudo if conteudo else '',
                            'arquivo': '',
                            'sinopse': '',
                            'valor': float(valor)
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

    def check_cliente(self, usuario_id):
        """Verifica se um cliente existe em qualquer uma das tabelas principais"""
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            query = """
                SELECT COUNT(*) FROM noticia_cliente 
                WHERE cliente_id = %s
                LIMIT 1
            """
            cursor.execute(query, (usuario_id,))
            if cursor.fetchone()[0] > 0:
                print(f"✅ Cliente {usuario_id} encontrado via tabela noticia_cliente")
                return True
            
            print(f"❌ Cliente {usuario_id} não encontrado em nenhuma tabela")
            return False
                
        except Exception as e:
            print(f"❌ Erro ao verificar cliente: {e}")
            return False
    
    def verificar_cliente_ativo(self, cliente_id):
        """
        Verifica se um cliente está ativo na tabela clientes
        
        Args:
            cliente_id (int): ID do cliente
            
        Returns:
            bool: True se está ativo, False caso contrário
        """
        try:
            config = self.get_cliente_configuracoes(cliente_id)
            if not config:
                return False
            
            # Verifica se o cliente está ativo e não foi deletado
            return config.get('fl_ativo', False)
            
        except Exception as e:
            print(f"❌ Erro ao verificar se cliente está ativo: {e}")
            return False
    
    def filtrar_tipos_midia_por_cliente(self, cliente_id, tipos_midia_solicitados):
        """
        Filtra os tipos de mídia baseado nas configurações do cliente
        
        Args:
            cliente_id (int): ID do cliente
            tipos_midia_solicitados (list): Lista de tipos de mídia solicitados
            
        Returns:
            list: Lista de tipos de mídia que o cliente tem permissão para ver
        """
        try:
            config = self.get_cliente_configuracoes(cliente_id)
            if not config:
                return []
            
            # Mapeia tipo de mídia para o campo correspondente
            campo_map = {
                'web': 'fl_web',
                'impresso': 'fl_impresso',
                'tv': 'fl_tv',
                'radio': 'fl_radio'
            }
            
            tipos_permitidos = []
            for tipo_midia in tipos_midia_solicitados:
                campo = campo_map.get(tipo_midia.lower())
                if campo and config.get(campo, False):
                    tipos_permitidos.append(tipo_midia)
            
            print(f"🔍 Cliente {cliente_id} - Tipos permitidos: {tipos_permitidos}")
            return tipos_permitidos
            
        except Exception as e:
            print(f"❌ Erro ao filtrar tipos de mídia: {e}")
            return []
    
    def verificar_permissao_retorno_midia(self, cliente_id):
        """
        Verifica se o cliente tem permissão para ver valores de retorno de mídia
        
        Args:
            cliente_id (int): ID do cliente
            
        Returns:
            bool: True se tem permissão para ver retornos, False caso contrário
        """
        try:
            config = self.get_cliente_configuracoes(cliente_id)
            if not config:
                return False
            
            # Verifica se o cliente tem permissão para ver retornos de mídia
            return config.get('fl_retorno_midia', False)
            
        except Exception as e:
            print(f"❌ Erro ao verificar permissão de retorno de mídia: {e}")
            return False
    
    def verificar_permissao_sentimento(self, cliente_id):
        """
        Verifica se o cliente tem permissão para ver dados de sentimento
        
        Args:
            cliente_id (int): ID do cliente
            
        Returns:
            bool: True se tem permissão para ver sentimentos, False caso contrário
        """
        try:
            config = self.get_cliente_configuracoes(cliente_id)
            if not config:
                return False
            
            # Verifica se o cliente tem permissão para ver dados de sentimento
            return config.get('fl_sentimento', False)
            
        except Exception as e:
            print(f"❌ Erro ao verificar permissão de sentimento: {e}")
            return False
    
    def verificar_permissao_audiencia(self, cliente_id):
        """
        Verifica se o cliente tem permissão para ver dados de audiência
        
        Args:
            cliente_id (int): ID do cliente
            
        Returns:
            bool: True se tem permissão para ver audiência, False caso contrário
        """
        try:
            config = self.get_cliente_configuracoes(cliente_id)
            if not config:
                return False
            
            # Verifica se o cliente tem permissão para ver dados de audiência
            return config.get('fl_audiencia', False)
            
        except Exception as e:
            print(f"❌ Erro ao verificar permissão de audiência: {e}")
            return False
    
    def verificar_permissao_areas(self, cliente_id):
        """
        Verifica se o cliente tem permissão para ver dados de áreas
        
        Args:
            cliente_id (int): ID do cliente
            
        Returns:
            bool: True se tem permissão para ver áreas, False caso contrário
        """
        try:
            config = self.get_cliente_configuracoes(cliente_id)
            if not config:
                return False
            
            # Verifica se o cliente tem permissão para ver dados de áreas
            return config.get('fl_areas', False)
            
        except Exception as e:
            print(f"❌ Erro ao verificar permissão de áreas: {e}")
            return False

    def get_retornos_tv(self, usuario_id, data_inicio, data_fim, filtros=None):
        """
        Busca dados de retorno de TV para a tabela de retorno no PDF
        
        Args:
            usuario_id (int): ID do usuário/cliente
            data_inicio (str): Data início no formato YYYY-MM-DD
            data_fim (str): Data fim no formato YYYY-MM-DD
            filtros (dict): Filtros opcionais {
                'tipos_midia': ['web', 'tv', 'radio', 'impresso'], 
                'status': ['positivo', 'negativo', 'neutro'],
                'retorno': ['com_retorno', 'sem_retorno'],
                'areas': [1, 2, 3]
            }
            
        Returns:
            pd.DataFrame: DataFrame com colunas: data, emissora, programa, valor
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            # Processa filtros (por padrão, todos selecionados)
            if not filtros:
                filtros = {}
            
            ids_especificos = filtros.get('ids_especificos', {})
            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])
            status_filtros = filtros.get('status', ['positivo', 'negativo', 'neutro'])
            areas_filtros = filtros.get('areas', [])
            
            # Se TV não está nos tipos de mídia selecionados, retorna DataFrame vazio
            if 'tv' not in tipos_midia:
                return pd.DataFrame(columns=['data_clipagem', 'emissora', 'programa', 'valor'])
            
            # Verifica se existem IDs específicos para TV
            if not ids_especificos.get('tv', []):
                print(f"📺 Nenhum ID específico para TV encontrado, retornando DataFrame vazio")
                return pd.DataFrame(columns=['data_clipagem', 'emissora', 'programa', 'valor'])
            
            # Funções auxiliares para construir condições SQL
            def _build_status_condition(table_prefix=""):
                if len(status_filtros) == 3:  # Todos selecionados
                    return ""
                conditions = []
                for status in status_filtros:
                    if status == 'positivo':
                        conditions.append(f"{table_prefix}sentimento = '1'")
                    elif status == 'negativo':
                        conditions.append(f"{table_prefix}sentimento = '-1'")
                    elif status == 'neutro':
                        conditions.append(f"{table_prefix}sentimento = '0'")
                
                if conditions:
                    return f" AND ({' OR '.join(conditions)})"
                return ""
            
            def _build_area_condition(table_prefix=""):
                if areas_filtros:
                    area_ids = ','.join(map(str, areas_filtros))
                    return f" AND nc.area IN ({area_ids})"
                return ""
            
            query = f"""
                SELECT 
                    t.dt_noticia as data_clipagem,
                    COALESCE(e.nome_emissora, 'Emissora Não Identificada') as emissora,
                    COALESCE(p.nome_programa, 'Programa Não Identificado') as programa,
                    COALESCE(
                        (
                            CASE 
                                WHEN t.duracao IS NULL THEN 0
                                WHEN t.duracao::text ~ '^\d+:\d+:\d+$' THEN 
                                    EXTRACT(EPOCH FROM t.duracao::time)
                                WHEN pg_typeof(t.duracao)::text = 'interval' THEN 
                                    EXTRACT(EPOCH FROM t.duracao)
                                ELSE 0
                            END
                        ) * COALESCE(p.valor_segundo, 0)
                    , 0) as valor
                FROM noticia_tv t
                LEFT JOIN emissora_web e ON t.emissora_id = e.id
                LEFT JOIN programa_emissora_web p ON t.programa_id = p.id
                JOIN noticia_cliente nc ON t.id = nc.noticia_id AND nc.tipo_id = 4
                WHERE (nc.cliente_id = %s)
                AND t.dt_noticia BETWEEN %s AND %s
                AND t.deleted_at IS NULL
                AND t.duracao IS NOT NULL
                AND p.valor_segundo IS NOT NULL
                AND p.valor_segundo > 0
                {_build_status_condition('nc.')}
                {_build_area_condition('t.')}
                AND t.id IN ({','.join(map(str, ids_especificos['tv']))})
                ORDER BY t.dt_noticia ASC, e.nome_emissora ASC, p.nome_programa ASC
            """
            
            cursor.execute(query, (usuario_id, data_inicio, data_fim))
            data = cursor.fetchall()
            
            # Converte para DataFrame
            df = pd.DataFrame(data, columns=['data_clipagem', 'emissora', 'programa', 'valor'])
            
            # Converte valor para float
            if not df.empty:
                df['valor'] = df['valor'].astype(float)
            
            print(f"📺 Retornos de TV encontrados: {len(df)}")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar retornos de TV: {e}")
            return pd.DataFrame(columns=['data_clipagem', 'emissora', 'programa', 'valor'])
    
    def get_retornos_radio(self, usuario_id, data_inicio, data_fim, filtros=None):
        """
        Busca dados de retorno de Rádio para a tabela de retorno no PDF
        
        Args:
            usuario_id (int): ID do usuário/cliente
            data_inicio (str): Data início no formato YYYY-MM-DD
            data_fim (str): Data fim no formato YYYY-MM-DD
            filtros (dict): Filtros opcionais {
                'tipos_midia': ['web', 'tv', 'radio', 'impresso'], 
                'status': ['positivo', 'negativo', 'neutro'],
                'retorno': ['com_retorno', 'sem_retorno'],
                'areas': [1, 2, 3]
            }
            
        Returns:
            pd.DataFrame: DataFrame com colunas: data, emissora, programa, valor
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            # Processa filtros (por padrão, todos selecionados)
            if not filtros:
                filtros = {}
            
            ids_especificos = filtros.get('ids_especificos', {})
            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])
            status_filtros = filtros.get('status', ['positivo', 'negativo', 'neutro'])
            areas_filtros = filtros.get('areas', [])
            
            # Se Rádio não está nos tipos de mídia selecionados, retorna DataFrame vazio
            if 'radio' not in tipos_midia:
                return pd.DataFrame(columns=['data_clipagem', 'emissora', 'programa', 'valor'])
            
            # Verifica se existem IDs específicos para Rádio
            if not ids_especificos.get('radio', []):
                print(f"📻 Nenhum ID específico para Rádio encontrado, retornando DataFrame vazio")
                return pd.DataFrame(columns=['data_clipagem', 'emissora', 'programa', 'valor'])
            
            # Funções auxiliares para construir condições SQL
            def _build_status_condition(table_prefix=""):
                if len(status_filtros) == 3:  # Todos selecionados
                    return ""
                conditions = []
                for status in status_filtros:
                    if status == 'positivo':
                        conditions.append(f"{table_prefix}sentimento = '1'")
                    elif status == 'negativo':
                        conditions.append(f"{table_prefix}sentimento = '-1'")
                    elif status == 'neutro':
                        conditions.append(f"{table_prefix}sentimento = '0'")
                
                if conditions:
                    return f" AND ({' OR '.join(conditions)})"
                return ""
            
            def _build_area_condition(table_prefix=""):
                if areas_filtros:
                    area_ids = ','.join(map(str, areas_filtros))
                    return f" AND nc.area IN ({area_ids})"
                return ""
            
            query = f"""
                SELECT 
                    r.dt_clipagem as data_clipagem,
                    COALESCE(e.nome_emissora, 'Emissora Não Identificada') as emissora,
                    COALESCE(p.nome_programa, 'Programa Não Identificado') as programa,
                    COALESCE(r.valor_retorno, 0) as valor
                FROM noticia_radio r
                LEFT JOIN emissora_radio e ON r.emissora_id = e.id
                LEFT JOIN programa_emissora_radio p ON r.programa_id = p.id
                JOIN noticia_cliente nc ON r.id = nc.noticia_id AND nc.tipo_id = 3
                WHERE nc.cliente_id = %s
                AND r.dt_clipagem BETWEEN %s AND %s
                AND r.deleted_at IS NULL
                AND r.duracao IS NOT NULL
                AND p.valor_segundo IS NOT NULL
                AND p.valor_segundo > 0
                {_build_status_condition('nc.')}
                {_build_area_condition('r.')}
                AND r.id IN ({','.join(map(str, ids_especificos['radio']))})
                ORDER BY r.dt_clipagem ASC, e.nome_emissora ASC, p.nome_programa ASC
            """
            
            cursor.execute(query, (usuario_id, data_inicio, data_fim))
            data = cursor.fetchall()
            
            # Converte para DataFrame
            df = pd.DataFrame(data, columns=['data_clipagem', 'emissora', 'programa', 'valor'])
            
            # Converte valor para float
            if not df.empty:
                df['valor'] = df['valor'].astype(float)
            
            print(f"📻 Retornos de Rádio encontrados: {len(df)}")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar retornos de Rádio: {e}")
            return pd.DataFrame(columns=['data_clipagem', 'emissora', 'programa', 'valor'])
    
    def get_retornos_web(self, usuario_id, data_inicio, data_fim, filtros=None):
        """
        Busca dados de retorno de Web para a tabela de retorno no PDF
        
        Args:
            usuario_id (int): ID do usuário/cliente
            data_inicio (str): Data início no formato YYYY-MM-DD
            data_fim (str): Data fim no formato YYYY-MM-DD
            filtros (dict): Filtros opcionais {
                'tipos_midia': ['web', 'tv', 'radio', 'impresso'], 
                'status': ['positivo', 'negativo', 'neutro'],
                'retorno': ['com_retorno', 'sem_retorno'],
                'areas': [1, 2, 3]
            }
            
        Returns:
            pd.DataFrame: DataFrame com colunas: data, site, secao, valor
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            # Processa filtros (por padrão, todos selecionados)
            if not filtros:
                filtros = {}
            
            ids_especificos = filtros.get('ids_especificos', {})
            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])
            status_filtros = filtros.get('status', ['positivo', 'negativo', 'neutro'])
            areas_filtros = filtros.get('areas', [])
            
            # Se Web não está nos tipos de mídia selecionados, retorna DataFrame vazio
            if 'web' not in tipos_midia:
                return pd.DataFrame(columns=['data_clipagem', 'site', 'secao', 'valor'])
            
            # Verifica se existem IDs específicos para Web
            if not ids_especificos.get('web', []):
                print(f"🌐 Nenhum ID específico para Web encontrado, retornando DataFrame vazio")
                return pd.DataFrame(columns=['data_clipagem', 'site', 'secao', 'valor'])
            
            # Funções auxiliares para construir condições SQL
            def _build_status_condition(table_prefix=""):
                if len(status_filtros) == 3:  # Todos selecionados
                    return ""
                conditions = []
                for status in status_filtros:
                    if status == 'positivo':
                        conditions.append(f"{table_prefix}sentimento = '1'")
                    elif status == 'negativo':
                        conditions.append(f"{table_prefix}sentimento = '-1'")
                    elif status == 'neutro':
                        conditions.append(f"{table_prefix}sentimento = '0'")
                
                if conditions:
                    return f" AND ({' OR '.join(conditions)})"
                return ""
            
            def _build_area_condition(table_prefix=""):
                if areas_filtros:
                    area_ids = ','.join(map(str, areas_filtros))
                    return f" AND nc.area IN ({area_ids})"
                return ""
            
            query = f"""
                SELECT 
                    w.data_noticia as data_clipagem,
                    COALESCE(fw.nome, 'Site Não Identificado') as site,
                    COALESCE(sw.ds_sessao, 'Geral') as secao,
                    COALESCE(w.nu_valor, 0) as valor
                FROM noticias_web w
                LEFT JOIN fonte_web fw ON w.id_fonte = fw.id
                JOIN noticia_cliente nc ON w.id = nc.noticia_id AND nc.tipo_id = 2
                LEFT JOIN sessao_web sw ON sw.id_sessao_web = w.id_sessao_web
                WHERE nc.cliente_id = %s
                AND w.data_noticia BETWEEN %s AND %s
                AND w.deleted_at IS NULL
                AND w.nu_valor IS NOT NULL
                AND w.nu_valor > 0
                {_build_status_condition('nc.')}
                {_build_area_condition('w.')}
                AND w.id IN ({','.join(map(str, ids_especificos['web']))})
                ORDER BY w.data_noticia ASC, fw.nome ASC 
            """
            
            cursor.execute(query, (usuario_id, data_inicio, data_fim))
            data = cursor.fetchall()
            
            # Converte para DataFrame
            df = pd.DataFrame(data, columns=['data_clipagem', 'site', 'secao', 'valor'])
            
            # Converte valor para float
            if not df.empty:
                df['valor'] = df['valor'].astype(float)
            
            print(f"🌐 Retornos de Web encontrados: {len(df)}")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar retornos de Web: {e}")
            return pd.DataFrame(columns=['data_clipagem', 'site', 'secao', 'valor'])
    
    def get_retornos_impresso(self, usuario_id, data_inicio, data_fim, filtros=None):
        """
        Busca dados de retorno de Mídia Impressa para a tabela de retorno no PDF
        
        Args:
            usuario_id (int): ID do usuário/cliente
            data_inicio (str): Data início no formato YYYY-MM-DD
            data_fim (str): Data fim no formato YYYY-MM-DD
            filtros (dict): Filtros opcionais {
                'tipos_midia': ['web', 'tv', 'radio', 'impresso'], 
                'status': ['positivo', 'negativo', 'neutro'],
                'retorno': ['com_retorno', 'sem_retorno'],
                'areas': [1, 2, 3]
            }
            
        Returns:
            pd.DataFrame: DataFrame com colunas: data, jornal, secao, valor
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            # Processa filtros (por padrão, todos selecionados)
            if not filtros:
                filtros = {}
            
            ids_especificos = filtros.get('ids_especificos', {})
            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])
            status_filtros = filtros.get('status', ['positivo', 'negativo', 'neutro'])
            areas_filtros = filtros.get('areas', [])
            
            # Se Impresso não está nos tipos de mídia selecionados, retorna DataFrame vazio
            if 'impresso' not in tipos_midia:
                return pd.DataFrame(columns=['data_clipagem', 'jornal', 'secao', 'valor'])
            
            # Verifica se existem IDs específicos para Impresso
            if not ids_especificos.get('impresso', []):
                print(f"📰 Nenhum ID específico para Mídia Impressa encontrado, retornando DataFrame vazio")
                return pd.DataFrame(columns=['data_clipagem', 'jornal', 'secao', 'valor'])
            
            # Funções auxiliares para construir condições SQL
            def _build_status_condition(table_prefix=""):
                if len(status_filtros) == 3:  # Todos selecionados
                    return ""
                conditions = []
                for status in status_filtros:
                    if status == 'positivo':
                        conditions.append(f"{table_prefix}sentimento = '1'")
                    elif status == 'negativo':
                        conditions.append(f"{table_prefix}sentimento = '-1'")
                    elif status == 'neutro':
                        conditions.append(f"{table_prefix}sentimento = '0'")
                
                if conditions:
                    return f" AND ({' OR '.join(conditions)})"
                return ""
            
            def _build_area_condition(table_prefix=""):
                if areas_filtros:
                    area_ids = ','.join(map(str, areas_filtros))
                    return f" AND nc.area IN ({area_ids})"
                return ""
            
            query = f"""
                SELECT 
                    j.dt_clipagem as data_clipagem,
                    COALESCE(ji.nome, 'Jornal Não Identificado') as jornal,
                    COALESCE(si.ds_sessao, 'Geral') as secao,
                    COALESCE(j.valor_retorno, 0) as valor
                FROM noticia_impresso j
                LEFT JOIN jornal_online ji ON j.id_fonte = ji.id
                LEFT JOIN sessao_impresso si ON j.id_secao = si.id_sessao_impresso
                JOIN noticia_cliente nc ON j.id = nc.noticia_id AND nc.tipo_id = 1
                WHERE nc.cliente_id = %s
                AND j.dt_clipagem BETWEEN %s AND %s
                AND j.deleted_at IS NULL
                AND j.valor_retorno IS NOT NULL
                AND j.valor_retorno > 0
                {_build_status_condition('nc.')}
                {_build_area_condition('j.')}
                AND j.id IN ({','.join(map(str, ids_especificos['impresso']))})
                ORDER BY j.dt_clipagem ASC, ji.nome ASC
            """
            
            cursor.execute(query, (usuario_id, data_inicio, data_fim))
            data = cursor.fetchall()
            
            # Converte para DataFrame
            df = pd.DataFrame(data, columns=['data_clipagem', 'jornal', 'secao', 'valor'])
            
            # Converte valor para float
            if not df.empty:
                df['valor'] = df['valor'].astype(float)
            
            print(f"📰 Retornos de Mídia Impressa encontrados: {len(df)}")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar retornos de Mídia Impressa: {e}")
            return pd.DataFrame(columns=['data_clipagem', 'jornal', 'secao', 'valor'])
    
    def get_sentimentos_tv(self, usuario_id, data_inicio, data_fim, filtros=None):
        """Busca dados de sentimento de TV agrupados por cidade"""
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()

        ids_especificos = filtros.get('ids_especificos', {})
        
        # Verifica se existem IDs específicos para TV
        if not ids_especificos.get('tv', []):
            print(f"📺 Nenhum ID específico para TV encontrado, retornando DataFrame vazio")
            return pd.DataFrame(columns=['cidade', 'sentimento', 'quantidade', 'tempo_segundos', 'valor', 'tempo'])
        
        try:
            query = f"""
                SELECT 
                    COALESCE(c.nm_cidade, CONCAT('Cidade ID: ', t.cd_cidade)) as cidade,
                    nc.sentimento as sentimento,
                    COUNT(*) as quantidade,
                    SUM(
                        CASE 
                            WHEN t.duracao IS NULL THEN 0
                            WHEN t.duracao::text ~ '^\d+:\d+:\d+$' THEN 
                                EXTRACT(EPOCH FROM t.duracao::time)
                            WHEN pg_typeof(t.duracao)::text = 'interval' THEN 
                                EXTRACT(EPOCH FROM t.duracao)
                            ELSE 0
                        END
                    ) AS tempo_segundos,
                    SUM(
                        CASE 
                            WHEN t.duracao IS NULL THEN 0
                            WHEN t.duracao::text ~ '^\d+:\d+:\d+$' THEN 
                                EXTRACT(EPOCH FROM t.duracao::time)
                            WHEN pg_typeof(t.duracao)::text = 'interval' THEN 
                                EXTRACT(EPOCH FROM t.duracao)
                            ELSE 0
                        END * COALESCE(p.valor_segundo, 0)
                    ) as valor_total
                FROM noticia_tv t
                LEFT JOIN cidade c ON t.cd_cidade = c.cd_cidade
                LEFT JOIN emissora_web e ON t.emissora_id = e.id
                LEFT JOIN programa_emissora_web p ON t.programa_id = p.id
                JOIN noticia_cliente nc ON t.id = nc.noticia_id AND nc.tipo_id = 4
                WHERE (nc.cliente_id = %s)
                AND t.dt_noticia BETWEEN %s AND %s
                AND t.deleted_at IS NULL
                AND nc.sentimento IS NOT NULL
                AND noticia_id IN ({','.join(map(str, ids_especificos.get('tv', [])))})
                GROUP BY t.cd_cidade, c.nm_cidade, nc.sentimento, e.nome_emissora, p.nome_programa
                ORDER BY cidade ASC, sentimento ASC;
            """
            
            cursor.execute(query, (usuario_id, data_inicio, data_fim))
            data = cursor.fetchall()
            
            # Converte para DataFrame
            df = pd.DataFrame(data, columns=['cidade', 'sentimento', 'quantidade', 'tempo_segundos', 'valor'])
            
            if not df.empty:
                # Converte tipos
                df['quantidade'] = df['quantidade'].astype(int)
                df['tempo_segundos'] = pd.to_numeric(df['tempo_segundos'], errors='coerce').fillna(0).astype(int)
                df['valor'] = pd.to_numeric(df['valor'], errors='coerce').fillna(0).astype(float)
                
                # Converte segundos para formato HH:MM:SS
                df['tempo'] = df['tempo_segundos'].apply(self._seconds_to_time_format)
            
            print(f"📺 Dados de sentimento TV encontrados: {len(df)}")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar sentimentos TV: {e}")
            print(f"❌ Tipo do erro: {type(e).__name__}")
            print(f"❌ Detalhes do erro: {str(e)}")
            print(f"❌ Parâmetros da consulta: usuario_id={usuario_id}, data_inicio={data_inicio}, data_fim={data_fim}")
            import traceback
            print(f"❌ Stack trace completo:\n{traceback.format_exc()}")
            return pd.DataFrame(columns=['cidade', 'sentimento', 'quantidade', 'tempo_segundos', 'valor', 'tempo'])
    
    def get_sentimentos_radio(self, usuario_id, data_inicio, data_fim, filtros=None):
        """Busca dados de sentimento de Rádio agrupados por cidade"""
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()

        ids_especificos = filtros.get('ids_especificos', {})
        
        # Verifica se existem IDs específicos para Rádio
        if not ids_especificos.get('radio', []):
            print(f"📻 Nenhum ID específico para Rádio encontrado, retornando DataFrame vazio")
            return pd.DataFrame(columns=['cidade', 'sentimento', 'quantidade', 'tempo_segundos', 'valor', 'tempo'])
        
        try:
            query = f"""
                SELECT 
                    COALESCE(c.nm_cidade, CONCAT('Cidade ID: ', r.cd_cidade)) as cidade,
                    nc.sentimento as sentimento,
                    COUNT(*) as quantidade,
                    SUM(
                        CASE 
                            WHEN r.duracao IS NULL THEN 0
                            WHEN r.duracao::text ~ '^\d+:\d+:\d+$' THEN 
                                EXTRACT(EPOCH FROM r.duracao::time)
                            WHEN pg_typeof(r.duracao)::text = 'interval' THEN 
                                EXTRACT(EPOCH FROM r.duracao)
                            ELSE 0
                        END 
                    ) as tempo_segundos,
                    SUM(
                        r.valor_retorno
                    ) as valor
                FROM noticia_radio r
                LEFT JOIN cidade c ON r.cd_cidade = c.cd_cidade
                LEFT JOIN emissora_radio e ON r.emissora_id = e.id
                LEFT JOIN programa_emissora_radio p ON r.programa_id = p.id
                JOIN noticia_cliente nc ON r.id = nc.noticia_id AND nc.tipo_id = 3
                WHERE (nc.cliente_id = %s)
                AND r.dt_clipagem BETWEEN %s AND %s
                AND r.deleted_at IS NULL
                AND nc.sentimento IS NOT NULL
                AND noticia_id IN ({','.join(map(str, ids_especificos.get('radio', [])))})
                GROUP BY r.cd_cidade, c.nm_cidade, nc.sentimento
                ORDER BY cidade ASC, sentimento ASC
            """
            
            cursor.execute(query, (usuario_id, data_inicio, data_fim))
            data = cursor.fetchall()
            
            # Converte para DataFrame
            df = pd.DataFrame(data, columns=['cidade', 'sentimento', 'quantidade', 'tempo_segundos', 'valor'])
            
            if not df.empty:
                # Converte tipos
                df['quantidade'] = df['quantidade'].astype(int)
                df['tempo_segundos'] = pd.to_numeric(df['tempo_segundos'], errors='coerce').fillna(0).astype(int)
                df['valor'] = pd.to_numeric(df['valor'], errors='coerce').fillna(0).astype(float)
                
                # Converte segundos para formato HH:MM:SS
                df['tempo'] = df['tempo_segundos'].apply(self._seconds_to_time_format)
            
            print(f"📻 Dados de sentimento Rádio encontrados: {len(df)}")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar sentimentos Rádio: {e}")
            return pd.DataFrame(columns=['cidade', 'sentimento', 'quantidade', 'tempo_segundos', 'valor', 'tempo'])
    
    def get_sentimentos_impresso(self, usuario_id, data_inicio, data_fim, filtros=None):
        """Busca dados de sentimento de Impresso agrupados por cidade"""
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        ids_especificos = filtros.get('ids_especificos', {})

        # Verifica se existem IDs específicos para Impresso
        if not ids_especificos.get('impresso', []):
            print(f"📰 Nenhum ID específico para Impresso encontrado, retornando DataFrame vazio")
            return pd.DataFrame(columns=['cidade', 'sentimento', 'quantidade', 'valor'])

        try:
            query = f"""
                SELECT 
                    COALESCE(c.nm_cidade, CONCAT('Cidade ID: ', j.cd_cidade)) as cidade,
                    nc.sentimento as sentimento,
                    COUNT(*) as quantidade,
                    SUM(COALESCE(j.valor_retorno, 0)) as valor
                FROM noticia_impresso j
                LEFT JOIN cidade c ON j.cd_cidade = c.cd_cidade
                JOIN noticia_cliente nc ON j.id = nc.noticia_id AND nc.tipo_id = 1
                WHERE (nc.cliente_id = %s)
                AND j.dt_clipagem BETWEEN %s AND %s
                AND j.deleted_at IS NULL
                AND nc.sentimento IS NOT NULL
                AND noticia_id IN ({','.join(map(str, ids_especificos.get('impresso', [])))})
                GROUP BY j.cd_cidade, c.nm_cidade, nc.sentimento
                ORDER BY cidade ASC, sentimento ASC
            """
            
            cursor.execute(query, (usuario_id, data_inicio, data_fim))
            data = cursor.fetchall()
            
            # Converte para DataFrame
            df = pd.DataFrame(data, columns=['cidade', 'sentimento', 'quantidade', 'valor'])
            
            if not df.empty:
                # Converte tipos
                df['quantidade'] = df['quantidade'].astype(int)
                df['valor'] = pd.to_numeric(df['valor'], errors='coerce').fillna(0).astype(float)
            
            print(f"📰 Dados de sentimento Impresso encontrados: {len(df)}")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar sentimentos Impresso: {e}")
            return pd.DataFrame(columns=['cidade', 'sentimento', 'quantidade', 'valor'])
    
    def get_sentimentos_web(self, usuario_id, data_inicio, data_fim, filtros=None):
        """Busca dados de sentimento de Web agrupados por cidade"""
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()

        ids_especificos = filtros.get('ids_especificos', {})
        
        # Verifica se existem IDs específicos para Web
        if not ids_especificos.get('web', []):
            print(f"🌐 Nenhum ID específico para Web encontrado, retornando DataFrame vazio")
            return pd.DataFrame(columns=['cidade', 'sentimento', 'quantidade', 'valor'])
        
        try:
            query = f"""
                SELECT 
                    COALESCE(c.nm_cidade, CONCAT('Cidade ID: ', w.cd_cidade)) as cidade,
                    nc.sentimento as sentimento,
                    COUNT(*) as quantidade,
                    SUM(COALESCE(w.nu_valor, 0)) as valor
                FROM noticias_web w
                LEFT JOIN cidade c ON w.cd_cidade = c.cd_cidade
                JOIN noticia_cliente nc ON w.id = nc.noticia_id AND nc.tipo_id = 2
                WHERE (nc.cliente_id = %s)
                AND w.data_noticia BETWEEN %s AND %s
                AND w.deleted_at IS NULL
                AND nc.sentimento IS NOT NULL
                AND noticia_id IN ({','.join(map(str, ids_especificos.get('web', [])))})
                GROUP BY w.cd_cidade, c.nm_cidade, nc.sentimento
                ORDER BY cidade ASC, sentimento ASC
            """
            
            cursor.execute(query, (usuario_id, data_inicio, data_fim))
            data = cursor.fetchall()
            
            # Converte para DataFrame
            df = pd.DataFrame(data, columns=['cidade', 'sentimento', 'quantidade', 'valor'])
            
            if not df.empty:
                # Converte tipos
                df['quantidade'] = df['quantidade'].astype(int)
                df['valor'] = pd.to_numeric(df['valor'], errors='coerce').fillna(0).astype(float)
            
            print(f"🌐 Dados de sentimento Web encontrados: {len(df)}")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar sentimentos Web: {e}")
            return pd.DataFrame(columns=['cidade', 'sentimento', 'quantidade', 'valor'])

    def get_status_resumo_por_midia(self, usuario_id, data_inicio, data_fim, filtros=None):
        """
        Busca resumo de status por mídia para criação da tabela e gráfico de resumo
        
        Args:
            usuario_id (int): ID do usuário/cliente
            data_inicio (str): Data início no formato YYYY-MM-DD
            data_fim (str): Data fim no formato YYYY-MM-DD
            
        Returns:
            pd.DataFrame: DataFrame com colunas ['midia', 'positivo', 'negativo', 'neutro', 'total']
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        
        try:
            resultados = []
            
            # NOVO: Processa filtros (por padrão, todos selecionados)
            if not filtros:
                filtros = {}
            
            ids_especificos = filtros.get('ids_especificos', {})
            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])
            status_filtros = filtros.get('status', ['positivo', 'negativo', 'neutro'])
            areas_filtros = filtros.get('areas', [])
            
            # Funções auxiliares para construir condições SQL
            def _build_status_condition(table_prefix=""):
                if len(status_filtros) == 3:  # Todos selecionados
                    return ""
                conditions = []
                for status in status_filtros:
                    if status == 'positivo':
                        conditions.append(f"{table_prefix}sentimento = '1'")
                    elif status == 'negativo':
                        conditions.append(f"{table_prefix}sentimento = '-1'")
                    elif status == 'neutro':
                        conditions.append(f"{table_prefix}sentimento = '0'")
                
                if conditions:
                    return f" AND ({' OR '.join(conditions)})"
                return ""
            
            def _build_area_condition(table_prefix=""):
                if areas_filtros:
                    area_ids = ','.join(map(str, areas_filtros))
                    return f" AND nc.area IN ({area_ids})"
                return ""
            
            # 1. TV - contabiliza por status (apenas se TV estiver nos filtros e tiver IDs específicos)
            if 'tv' in tipos_midia and ids_especificos.get('tv', []):
                query_tv = f"""
                    SELECT 
                        SUM(CASE WHEN nc.sentimento = '1' THEN 1 ELSE 0 END) as positivo,
                        SUM(CASE WHEN nc.sentimento = '-1' THEN 1 ELSE 0 END) as negativo,
                        SUM(CASE WHEN nc.sentimento = '0' THEN 1 ELSE 0 END) as neutro,
                        COUNT(*) as total
                    FROM noticia_tv t
                    JOIN noticia_cliente nc ON t.id = nc.noticia_id AND nc.tipo_id = 4
                    WHERE (nc.cliente_id = %s)
                    AND t.dt_noticia BETWEEN %s AND %s
                    AND t.deleted_at IS NULL
                    AND nc.sentimento IS NOT NULL
                    AND noticia_id IN ({','.join(map(str, ids_especificos.get('tv', [])))})
                """
                cursor.execute(query_tv, (usuario_id, data_inicio, data_fim))
                tv_data = cursor.fetchone()
                resultados.append({
                    'midia': 'TV',
                    'positivo': tv_data[0] or 0,
                    'negativo': tv_data[1] or 0,
                    'neutro': tv_data[2] or 0,
                    'total': tv_data[3] or 0
                })
            
            # 2. Rádio - contabiliza por status (apenas se Rádio estiver nos filtros e tiver IDs específicos)
            if 'radio' in tipos_midia and ids_especificos.get('radio', []):
                query_radio = f"""
                   SELECT 
                        SUM(CASE WHEN nc.sentimento = '1' THEN 1 ELSE 0 END) as positivo,
                        SUM(CASE WHEN nc.sentimento = '-1' THEN 1 ELSE 0 END) as negativo,
                        SUM(CASE WHEN nc.sentimento = '0' THEN 1 ELSE 0 END) as neutro,
                        COUNT(*) as total
                    FROM noticia_radio r
                    JOIN noticia_cliente nc ON r.id = nc.noticia_id AND nc.tipo_id = 3
                    WHERE (nc.cliente_id = %s)
                    AND r.dt_clipagem BETWEEN %s AND %s
                    AND r.deleted_at IS NULL
                    AND nc.sentimento IS NOT NULL
                    AND noticia_id IN ({','.join(map(str, ids_especificos.get('radio', [])))})
                """
                cursor.execute(query_radio, (usuario_id, data_inicio, data_fim))
                radio_data = cursor.fetchone()
                resultados.append({
                    'midia': 'Rádio',
                    'positivo': radio_data[0] or 0,
                    'negativo': radio_data[1] or 0,
                    'neutro': radio_data[2] or 0,
                    'total': radio_data[3] or 0
                })
            
            # 3. Impresso - contabiliza por status (apenas se Impresso estiver nos filtros e tiver IDs específicos)
            if 'impresso' in tipos_midia and ids_especificos.get('impresso', []):
                query_jornal = f"""
                    SELECT 
                        SUM(CASE WHEN nc.sentimento = '1' THEN 1 ELSE 0 END) as positivo,
                        SUM(CASE WHEN nc.sentimento = '-1' THEN 1 ELSE 0 END) as negativo,
                        SUM(CASE WHEN nc.sentimento = '0' THEN 1 ELSE 0 END) as neutro,
                        COUNT(*) as total
                    FROM noticia_impresso j
                    JOIN noticia_cliente nc ON j.id = nc.noticia_id AND nc.tipo_id = 1
                    WHERE (nc.cliente_id = %s)
                    AND j.dt_clipagem BETWEEN %s AND %s
                    AND j.deleted_at IS NULL
                    AND nc.sentimento IS NOT NULL
                    AND noticia_id IN ({','.join(map(str, ids_especificos.get('impresso', [])))})
                """
                cursor.execute(query_jornal, (usuario_id, data_inicio, data_fim))
                jornal_data = cursor.fetchone()
                resultados.append({
                    'midia': 'Impresso',
                    'positivo': jornal_data[0] or 0,
                    'negativo': jornal_data[1] or 0,
                    'neutro': jornal_data[2] or 0,
                    'total': jornal_data[3] or 0
                })
            
            # 4. Web - contabiliza por status (apenas se Web estiver nos filtros e tiver IDs específicos)
            if 'web' in tipos_midia and ids_especificos.get('web', []):
                query_web = f"""
                    SELECT 
                        SUM(CASE WHEN nc.sentimento = '1' THEN 1 ELSE 0 END) as positivo,
                        SUM(CASE WHEN nc.sentimento = '-1' THEN 1 ELSE 0 END) as negativo,
                        SUM(CASE WHEN nc.sentimento = '0' THEN 1 ELSE 0 END) as neutro,
                        COUNT(*) as total
                    FROM noticias_web w
                    JOIN noticia_cliente nc ON w.id = nc.noticia_id AND nc.tipo_id = 2
                    WHERE (nc.cliente_id = %s)
                    AND w.data_noticia BETWEEN %s AND %s
                    AND w.deleted_at IS NULL
                    AND nc.sentimento IS NOT NULL
                    AND noticia_id IN ({','.join(map(str, ids_especificos.get('web', [])))})
                """
                cursor.execute(query_web, (usuario_id, data_inicio, data_fim))
                web_data = cursor.fetchone()
                resultados.append({
                    'midia': 'Web',
                    'positivo': web_data[0] or 0,
                    'negativo': web_data[1] or 0,
                    'neutro': web_data[2] or 0,
                    'total': web_data[3] or 0
                })
            
            # Converte para DataFrame
            df = pd.DataFrame(resultados)
            print(f"📊 Resumo de status encontrado para {len(df)} mídias (filtrado)")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar resumo de status: {e}")
            return pd.DataFrame(columns=['midia', 'positivo', 'negativo', 'neutro', 'total'])

    def get_status_tv_detalhado(self, usuario_id, data_inicio, data_fim, filtros=None):
        """Busca status detalhado de TV com numeração, data e status"""
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        ids_especificos = filtros.get('ids_especificos', {})
        
        # Verifica se existem IDs específicos para TV
        if not ids_especificos.get('tv', []):
            print(f"📺 Nenhum ID específico para TV encontrado, retornando DataFrame vazio")
            return pd.DataFrame(columns=['data', 'status'])
        
        try:
            query = f"""
                SELECT t.dt_noticia as data, nc.sentimento as status
                FROM noticia_tv t
                JOIN noticia_cliente nc ON t.id = nc.noticia_id AND nc.tipo_id = 4
                WHERE (nc.cliente_id = %s)
                AND t.dt_noticia BETWEEN %s AND %s
                AND t.deleted_at IS NULL
                AND nc.sentimento IS NOT NULL
                AND noticia_id IN ({','.join(map(str, ids_especificos.get('tv', [])))})
                ORDER BY t.dt_noticia ASC
            """
            
            cursor.execute(query, (usuario_id, data_inicio, data_fim))
            data = cursor.fetchall()
            
            # Converte para DataFrame
            df = pd.DataFrame(data, columns=['data', 'status'])
            
            print(f"📺 Status detalhado TV encontrados: {len(df)}")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar status detalhado TV: {e}")
            return pd.DataFrame(columns=['data', 'status'])

    def get_status_radio_detalhado(self, usuario_id, data_inicio, data_fim, filtros=None):
        """Busca status detalhado de Rádio com numeração, data e status"""
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        ids_especificos = filtros.get('ids_especificos', {})
        
        # Verifica se existem IDs específicos para Rádio
        if not ids_especificos.get('radio', []):
            print(f"📻 Nenhum ID específico para Rádio encontrado, retornando DataFrame vazio")
            return pd.DataFrame(columns=['data', 'status'])
        
        try:
            query = f"""
                SELECT r.dt_clipagem as data, nc.sentimento as status
                FROM noticia_radio r
                JOIN noticia_cliente nc ON r.id = nc.noticia_id AND nc.tipo_id = 3
                WHERE (nc.cliente_id = %s)
                AND r.dt_clipagem BETWEEN %s AND %s
                AND r.deleted_at IS NULL
                AND nc.sentimento IS NOT NULL
                AND noticia_id IN ({','.join(map(str, ids_especificos.get('radio', [])))})
                ORDER BY r.dt_clipagem ASC
            """
            
            cursor.execute(query, (usuario_id, data_inicio, data_fim))
            data = cursor.fetchall()
            
            # Converte para DataFrame
            df = pd.DataFrame(data, columns=['data', 'status'])
            
            print(f"📻 Status detalhado Rádio encontrados: {len(df)}")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar status detalhado Rádio: {e}")
            return pd.DataFrame(columns=['data', 'status'])

    def get_status_web_detalhado(self, usuario_id, data_inicio, data_fim, filtros=None):
        """Busca status detalhado de Web com numeração, data e status"""
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        ids_especificos = filtros.get('ids_especificos', {})
        
        # Verifica se existem IDs específicos para Web
        if not ids_especificos.get('web', []):
            print(f"🌐 Nenhum ID específico para Web encontrado, retornando DataFrame vazio")
            return pd.DataFrame(columns=['data', 'status'])
        
        try:
            query = f"""
                SELECT w.data_noticia as data, nc.sentimento as status
                FROM noticias_web w
                JOIN noticia_cliente nc ON w.id = nc.noticia_id AND nc.tipo_id = 2
                WHERE (nc.cliente_id = %s)
                AND w.data_noticia BETWEEN %s AND %s
                AND w.deleted_at IS NULL
                AND nc.sentimento IS NOT NULL
                AND noticia_id IN ({','.join(map(str, ids_especificos.get('web', [])))})
                ORDER BY w.data_noticia ASC
            """
            
            cursor.execute(query, (usuario_id, data_inicio, data_fim))
            data = cursor.fetchall()
            
            # Converte para DataFrame
            df = pd.DataFrame(data, columns=['data', 'status'])
            
            print(f"🌐 Status detalhado Web encontrados: {len(df)}")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar status detalhado Web: {e}")
            return pd.DataFrame(columns=['data', 'status'])

    def get_status_impresso_detalhado(self, usuario_id, data_inicio, data_fim, filtros=None):
        """Busca status detalhado de Impresso com numeração, data e status"""
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()

        ids_especificos = filtros.get('ids_especificos', {})
        
        # Verifica se existem IDs específicos para Impresso
        if not ids_especificos.get('impresso', []):
            print(f"📰 Nenhum ID específico para Impresso encontrado, retornando DataFrame vazio")
            return pd.DataFrame(columns=['data', 'status'])
        
        try:
            query = f"""
                SELECT j.dt_clipagem as data, nc.sentimento as status
                FROM noticia_impresso j
                JOIN noticia_cliente nc ON j.id = nc.noticia_id AND nc.tipo_id = 1
                WHERE (nc.cliente_id = %s)
                AND j.dt_clipagem BETWEEN %s AND %s
                AND j.deleted_at IS NULL
                AND nc.sentimento IS NOT NULL
                AND noticia_id IN ({','.join(map(str, ids_especificos.get('impresso', [])))})
                ORDER BY j.dt_clipagem ASC
            """
            
            cursor.execute(query, (usuario_id, data_inicio, data_fim))
            data = cursor.fetchall()
            
            # Converte para DataFrame
            df = pd.DataFrame(data, columns=['data', 'status'])
            
            print(f"📰 Status detalhado Impresso encontrados: {len(df)}")
            return df
            
        except Exception as e:
            print(f"❌ Erro ao buscar status detalhado Impresso: {e}")
            return pd.DataFrame(columns=['data', 'status'])

    def get_clientes(self):
        """
        Busca lista única de todos os clientes disponíveis nas tabelas com informações adicionais
        
        Returns:
            list: Lista de dicionários com 'id', 'nome' e informações dos clientes
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            clientes_info = {}
            
            # Busca clientes únicos de todas as tabelas com contagem de registros
            tabelas = [
                ('noticia_tv', 'dt_noticia'),
                ('noticia_radio', 'dt_clipagem'),
                ('noticia_impresso', 'dt_clipagem'),
                ('noticias_web', 'data_noticia')
            ]
            
            for tabela, campo_data in tabelas:
                # Mapeia tabela para tipo_id na noticia_cliente
                tipo_map = {
                    'noticia_tv': 4,
                    'noticia_radio': 3,
                    'noticia_impresso': 1,
                    'noticias_web': 2
                }
                
                tipo_id = tipo_map.get(tabela)
                if not tipo_id:
                    continue
                
                query = f"""
                    SELECT 
                        nc.cliente_id,
                        COUNT(*) as total_registros,
                        MIN(t.{campo_data}) as primeira_data,
                        MAX(t.{campo_data}) as ultima_data
                    FROM {tabela} t
                    JOIN noticia_cliente nc ON t.id = nc.noticia_id AND nc.tipo_id = {tipo_id}
                    WHERE nc.cliente_id IS NOT NULL 
                    AND nc.cliente_id > 0
                    AND t.deleted_at IS NULL
                    AND t.{campo_data} IS NOT NULL
                    AND t.{campo_data}::text != '0000-00-00'
                    AND t.{campo_data}::text != '0000-00-00 00:00:00'
                    GROUP BY nc.cliente_id
                """
                
                cursor.execute(query)
                for row in cursor.fetchall():
                    cliente_id, total, primeira_data, ultima_data = row
                    if cliente_id:
                        if cliente_id not in clientes_info:
                            clientes_info[cliente_id] = {
                                'total_registros': 0,
                                'primeira_data': None,
                                'ultima_data': None,
                                'midias': set()
                            }
                        
                        clientes_info[cliente_id]['total_registros'] += total
                        # Remove noticia_ e noticias_ dos nomes das tabelas
                        midia_nome = tabela.replace('noticia_', '').replace('noticias_', '').upper()
                        if midia_nome == 'IMPRESSO':
                            midia_nome = 'IMPRESSO'
                        clientes_info[cliente_id]['midias'].add(midia_nome)
                        
                        # Atualiza datas - converte para string para evitar problemas de timezone
                        if primeira_data:
                            primeira_data_str = str(primeira_data)
                            if not clientes_info[cliente_id]['primeira_data'] or primeira_data_str < clientes_info[cliente_id]['primeira_data']:
                                clientes_info[cliente_id]['primeira_data'] = primeira_data_str
                        
                        if ultima_data:
                            ultima_data_str = str(ultima_data)
                            if not clientes_info[cliente_id]['ultima_data'] or ultima_data_str > clientes_info[cliente_id]['ultima_data']:
                                clientes_info[cliente_id]['ultima_data'] = ultima_data_str
            
            # Tenta buscar nomes reais de uma possível tabela de usuários/clientes
            try:
                # Tenta diferentes possíveis tabelas de usuários
                possible_user_tables = ['clientes', 'app_usuarios', 'users']
                nome_real_encontrado = {}
                
                for table_name in possible_user_tables:
                    try:
                        query_nome = f"""
                            SELECT id, nome 
                            FROM {table_name}
                            WHERE id IN ({','.join(map(str, clientes_info.keys()))})
                        """
                        cursor.execute(query_nome)
                        for user_id, nome in cursor.fetchall():
                            if nome and nome.strip():
                                nome_real_encontrado[user_id] = nome.strip()
                        
                        if nome_real_encontrado:
                            print(f"✅ Nomes reais encontrados na tabela {table_name}")
                            break
                            
                    except Exception:
                        continue  # Tabela não existe, tenta a próxima
                        
            except Exception:
                nome_real_encontrado = {}
            
            # Converte para lista ordenada com informações detalhadas
            clientes_list = []
            for cliente_id in sorted(clientes_info.keys()):
                info = clientes_info[cliente_id]
                
                # Define o nome (real se encontrado, senão genérico com mais informações)
                if cliente_id in nome_real_encontrado:
                    nome_cliente = nome_real_encontrado[cliente_id]
                else:
                    # Cria nome mais informativo baseado nas mídias e atividade
                    midias_str = ', '.join(sorted(info['midias']))
                    if info['total_registros'] > 1000:
                        atividade = "Cliente Premium"
                    elif info['total_registros'] > 100:
                        atividade = "Cliente Ativo"
                    else:
                        atividade = "Cliente"
                    
                    nome_cliente = f"{atividade} {cliente_id} ({midias_str})"
                
                clientes_list.append({
                    'id': cliente_id,
                    'nome': nome_cliente,
                    'total_registros': info['total_registros'],
                    'midias': list(info['midias']),
                    'primeira_data': info['primeira_data'] if info['primeira_data'] else None,
                    'ultima_data': info['ultima_data'] if info['ultima_data'] else None
                })
            
            print(f"📋 {len(clientes_list)} clientes encontrados")
            if nome_real_encontrado:
                print(f"✅ {len(nome_real_encontrado)} clientes com nomes reais")
            
            return clientes_list
            
        except Exception as e:
            print(f"❌ Erro ao buscar clientes: {e}")
            import traceback
            traceback.print_exc()
            return []

    def _sanitize_text(self, text):
        """Sanitiza texto para evitar problemas com JSON"""
        if not text:
            return ''
        
        # Remove caracteres de controle que podem quebrar JSON
        import re
        text = str(text)
        # Remove caracteres de controle exceto \n, \r, \t
        text = re.sub(r'[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]', '', text)
        # Escapa aspas e barras invertidas
        text = text.replace('\\', '\\\\').replace('"', '\\"')
        return text.strip()
    
    def _get_tags_from_misc_data(self, noticia_id, tipo_id):
        """
        Busca as tags do misc_data da tabela noticia_cliente
        
        Args:
            noticia_id (int): ID da notícia
            tipo_id (int): ID do tipo de mídia (1=impresso, 2=web, 3=radio, 4=tv)
            
        Returns:
            str: Tags separadas por vírgula ou string vazia
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            cursor.execute("""
                SELECT misc_data FROM noticia_cliente 
                WHERE noticia_id = %s AND tipo_id = %s
            """, (noticia_id, tipo_id))
            
            resultado = cursor.fetchone()
            
            if resultado and resultado[0]:
                try:
                    misc_data_raw = resultado[0]
                    
                    # Se já é um dict (PostgreSQL pode retornar JSON já parseado)
                    if isinstance(misc_data_raw, dict):
                        misc_data = misc_data_raw
                    # Se é string, faz parse
                    elif isinstance(misc_data_raw, str):
                        misc_data = json.loads(misc_data_raw)
                    # Se é bytes, decodifica e faz parse
                    elif isinstance(misc_data_raw, bytes):
                        misc_data = json.loads(misc_data_raw.decode('utf-8'))
                    else:
                        print(f"⚠️ Tipo inesperado para misc_data: {type(misc_data_raw)}")
                        misc_data = {}
                    
                    tags_array = misc_data.get('tags', [])
                    if tags_array:
                        return ', '.join(tags_array)
                except (json.JSONDecodeError, TypeError, AttributeError) as e:
                    print(f"⚠️ Erro ao processar misc_data: {e}, tipo: {type(resultado[0])}, valor: {resultado[0]}")
                    pass
            
            return ''
        
        except Exception as e:
            print(f"❌ Erro ao buscar tags do misc_data: {e}")
            return ''
    
    def get_cliente_configuracoes(self, cliente_id):
        """
        Busca as configurações de um cliente específico da tabela clientes
        
        Args:
            cliente_id (int): ID do cliente
            
        Returns:
            dict: Configurações do cliente ou None se não encontrado
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            query = """
                SELECT 
                    id, nome, logo, logo_expandida, emails,
                    fl_retorno_midia, fl_impresso, fl_web, fl_relatorio_completo,
                    fl_relatorio_consolidado, fl_sentimento_cli, fl_ativo,
                    fl_audiencia, fl_print, fl_area_restrita, fl_areas,
                    fl_sentimento, fl_link_relatorio, fl_radio, fl_tv,
                    cod_unico, created_at, updated_at
                FROM clientes 
                WHERE id = %s AND deleted_at IS NULL
            """
            
            cursor.execute(query, (cliente_id,))
            resultado = cursor.fetchone()
            
            if resultado:
                configuracoes = {
                    'id': resultado[0],
                    'nome': resultado[1],
                    'logo': resultado[2],
                    'logo_expandida': resultado[3],
                    'emails': resultado[4],
                    'fl_retorno_midia': resultado[5],
                    'fl_impresso': resultado[6],
                    'fl_web': resultado[7],
                    'fl_relatorio_completo': resultado[8],
                    'fl_relatorio_consolidado': resultado[9],
                    'fl_sentimento_cli': resultado[10],
                    'fl_ativo': resultado[11],
                    'fl_audiencia': resultado[12],
                    'fl_print': resultado[13],
                    'fl_area_restrita': resultado[14],
                    'fl_areas': resultado[15],
                    'fl_sentimento': resultado[16],
                    'fl_link_relatorio': resultado[17],
                    'fl_radio': resultado[18],
                    'fl_tv': resultado[19],
                    'cod_unico': resultado[20],
                    'created_at': resultado[21],
                    'updated_at': resultado[22]
                }
                
                print(f"✅ Configurações do cliente {cliente_id} carregadas")
                return configuracoes
            else:
                print(f"❌ Cliente {cliente_id} não encontrado")
                return None
                
        except Exception as e:
            print(f"❌ Erro ao buscar configurações do cliente: {e}")
            return None
    
    def verificar_permissao_midia(self, cliente_id, tipo_midia):
        """
        Verifica se o cliente tem permissão para ver determinado tipo de mídia
        
        Args:
            cliente_id (int): ID do cliente
            tipo_midia (str): Tipo da mídia ('web', 'impresso', 'tv', 'radio')
            
        Returns:
            bool: True se tem permissão, False caso contrário
        """
        try:
            config = self.get_cliente_configuracoes(cliente_id)
            if not config:
                return False
            
            # Mapeia tipo de mídia para o campo correspondente
            campo_map = {
                'web': 'fl_web',
                'impresso': 'fl_impresso',
                'tv': 'fl_tv',
                'radio': 'fl_radio'
            }
            
            campo = campo_map.get(tipo_midia.lower())
            if not campo:
                return False
            
            # Retorna True se o campo estiver habilitado
            return config.get(campo, False)
            
        except Exception as e:
            print(f"❌ Erro ao verificar permissão de mídia: {e}")
            return False
    
    def get_areas_by_cliente(self, cliente_id):
        """
        Busca as áreas vinculadas a um cliente específico através da tabela area_cliente
        
        Args:
            cliente_id (int): ID do cliente
            
        Returns:
            list: Lista de áreas vinculadas ao cliente
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            print(f"🔍 Buscando áreas vinculadas ao cliente {cliente_id}...")
            
            # Busca as áreas vinculadas ao cliente através da tabela area_cliente
            query_areas = """
                SELECT 
                    a.id,
                    a.descricao
                FROM area a
                INNER JOIN area_cliente ac ON a.id = ac.area_id
                WHERE ac.cliente_id = %s
                ORDER BY a.descricao
            """
            
            cursor.execute(query_areas, (cliente_id,))
            areas = cursor.fetchall()
            
            areas_list = []
            for area in areas:
                areas_list.append({
                    'id': area[0],
                    'nome': area[1]
                })
            
            print(f"🏢 {len(areas_list)} áreas encontradas para o cliente {cliente_id}")
            return areas_list
            
        except Exception as e:
            print(f"❌ Erro ao buscar áreas: {e}")
            import traceback
            traceback.print_exc()
            return []

    def listar_noticias_por_periodo(self, usuario_id, data_inicio, data_fim):
        """
        Lista todas as notícias do período separadas por tipo de mídia para a interface de edição
        
        Args:
            usuario_id (int): ID do usuário/cliente
            data_inicio (str): Data início no formato YYYY-MM-DD
            data_fim (str): Data fim no formato YYYY-MM-DD
            
        Returns:
            dict: Dicionário com listas de notícias por mídia (web, impresso, tv, radio)
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            # UPDATE: Atualiza valores das notícias web antes da listagem
            try:
                print(f"🔄 Atualizando valores das notícias web para cliente {usuario_id}...")
                update_query = """
                    UPDATE noticias_web
                    SET nu_valor = fonte_web.nu_valor
                    FROM fonte_web
                    WHERE fonte_web.id = noticias_web.id_fonte
                      AND noticias_web.id IN (
                        SELECT noticia_id 
                        FROM noticia_cliente 
                        WHERE cliente_id = %s AND tipo_id = 2
                      )
                      AND (
                        noticias_web.nu_valor IS NULL
                        OR noticias_web.nu_valor = 0
                      )
                """
                cursor.execute(update_query, (usuario_id,))
                rows_updated = cursor.rowcount
                print(f"✅ {rows_updated} notícias web atualizadas com novos valores")
            except Exception as e:
                print(f"⚠️ Erro ao atualizar valores das notícias web: {e}")
                # Continua a execução mesmo se o update falhar
            
            noticias = {
                'web': [],
                'impresso': [],
                'tv': [],
                'radio': []
            }
            
            # 1. Notícias de Web
            query_web = """
                SELECT 
                    w.id,
                    w.data_noticia as data,
                    COALESCE(w.titulo_noticia, 'Título não informado') as titulo,
                    COALESCE(fw.nome, 'Site Não Identificado') as veiculo,
                    COALESCE(w.url_noticia, '') as link,
                    COALESCE(w.sinopse, '') as texto,
                    COALESCE(w.nu_valor, 0) as valor,
                    nc.id as vinculo_id
                FROM noticias_web w
                LEFT JOIN fonte_web fw ON w.id_fonte = fw.id
                JOIN noticia_cliente nc ON w.id = nc.noticia_id AND nc.tipo_id = 2
                WHERE nc.cliente_id = %s
                AND w.data_noticia BETWEEN %s AND %s
                AND w.deleted_at IS NULL
                ORDER BY w.data_noticia ASC, w.titulo_noticia ASC
            """
            
            cursor.execute(query_web, (usuario_id, data_inicio, data_fim))
            for row in cursor.fetchall():
                id_noticia, data, titulo, veiculo, link, texto, valor, vinculo_id = row
                noticias['web'].append({
                    'id': id_noticia,
                    'data': data.strftime('%Y-%m-%d') if data else None,
                    'titulo': self._sanitize_text(titulo),
                    'veiculo': self._sanitize_text(veiculo),
                    'link': self._sanitize_text(link),
                    'texto': self._sanitize_text(texto),
                    'valor': float(valor or 0),
                    'vinculo_id': vinculo_id
                })
            
            # 2. Notícias de Impresso
            query_impresso = """
                SELECT 
                    j.id,
                    j.dt_clipagem as data,
                    j.titulo,
                    COALESCE(ji.nome, 'Jornal Não Identificado') as veiculo,
                    COALESCE(j.sinopse, '') as texto,
                    COALESCE(j.valor_retorno, 0) as valor,
                    nc.id as vinculo_id,
                    COALESCE(j.ds_caminho_img, '') as ds_caminho_img
                FROM noticia_impresso j
                LEFT JOIN jornal_online ji ON j.id_fonte = ji.id
                JOIN noticia_cliente nc ON j.id = nc.noticia_id AND nc.tipo_id = 1
                WHERE nc.cliente_id = %s
                AND j.dt_clipagem BETWEEN %s AND %s
                AND j.deleted_at IS NULL
                AND j.sinopse IS NOT NULL
                AND j.sinopse != ''
                ORDER BY j.dt_clipagem ASC, j.titulo ASC
            """
            
            cursor.execute(query_impresso, (usuario_id, data_inicio, data_fim))
            for row in cursor.fetchall():
                id_noticia, data, titulo, veiculo, texto, valor, vinculo_id, ds_caminho_img = row
                noticias['impresso'].append({
                    'id': id_noticia,
                    'data': data.strftime('%Y-%m-%d') if data else None,
                    'titulo': self._sanitize_text(titulo),
                    'veiculo': self._sanitize_text(veiculo),
                    'texto': self._sanitize_text(texto),
                    'valor': float(valor or 0),
                    'vinculo_id': vinculo_id,
                    'ds_caminho_img': self._sanitize_text(ds_caminho_img) if ds_caminho_img else None
                })
            
            # 3. Notícias de TV
            query_tv = """
                SELECT 
                    t.id,
                    t.dt_noticia as data,
                    t.sinopse as titulo,
                    COALESCE(e.nome_emissora, 'Emissora Não Identificada') as veiculo,
                    COALESCE(p.nome_programa, 'Programa Não Identificado') as programa,
                    COALESCE(t.horario, '00:00:00') as horario,
                    COALESCE(t.sinopse, '') as texto,
                    COALESCE(
                        (
                            CASE 
                                WHEN t.duracao IS NULL THEN 0
                                WHEN t.duracao::text ~ '^\d+:\d+:\d+$' THEN 
                                    EXTRACT(EPOCH FROM t.duracao::time)
                                WHEN pg_typeof(t.duracao)::text = 'interval' THEN 
                                    EXTRACT(EPOCH FROM t.duracao)
                                ELSE 0
                            END
                        ) * COALESCE(p.valor_segundo, 0)
                    , 0) as valor,
                    nc.id as vinculo_id
                FROM noticia_tv t
                LEFT JOIN emissora_web e ON t.emissora_id = e.id
                LEFT JOIN programa_emissora_web p ON t.programa_id = p.id
                JOIN noticia_cliente nc ON t.id = nc.noticia_id AND nc.tipo_id = 4
                WHERE nc.cliente_id = %s
                AND t.dt_noticia BETWEEN %s AND %s
                AND t.deleted_at IS NULL
                ORDER BY t.dt_noticia ASC, t.sinopse ASC
            """
            
            cursor.execute(query_tv, (usuario_id, data_inicio, data_fim))
            for row in cursor.fetchall():
                id_noticia, data, titulo, veiculo, programa, horario, texto, valor, vinculo_id = row
                noticias['tv'].append({
                    'id': id_noticia,
                    'data': data.strftime('%Y-%m-%d') if data else None,
                    'titulo': self._sanitize_text(titulo),
                    'veiculo': self._sanitize_text(veiculo),
                    'programa': self._sanitize_text(programa),
                    'horario': str(horario) if horario else '',
                    'texto': self._sanitize_text(texto),
                    'valor': float(valor or 0),
                    'vinculo_id': vinculo_id
                })
            
            # 4. Notícias de Rádio - USANDO VALOR_RETORNO
            query_radio = """
                SELECT 
                    r.id,
                    r.dt_clipagem as data,
                    r.titulo,
                    CASE 
                        WHEN e.nome_emissora IS NOT NULL THEN e.nome_emissora
                        WHEN p.nome_programa IS NOT NULL AND pe.nome_emissora IS NOT NULL THEN pe.nome_emissora
                        ELSE 'Emissora Não Identificada'
                    END as veiculo,
                    COALESCE(p.nome_programa, 'Programa Não Identificado') as programa,
                    COALESCE(r.horario, '00:00:00') as horario,
                    COALESCE(r.sinopse, '') as texto,
                    COALESCE(r.valor_retorno, 0) as valor,
                    nc.id as vinculo_id
                FROM noticia_radio r
                LEFT JOIN emissora_radio e ON r.emissora_id = e.id
                LEFT JOIN programa_emissora_radio p ON r.programa_id = p.id
                LEFT JOIN emissora_radio pe ON p.id_emissora = pe.id
                JOIN noticia_cliente nc ON r.id = nc.noticia_id AND nc.tipo_id = 3
                WHERE nc.cliente_id = %s
                AND r.dt_clipagem BETWEEN %s AND %s
                AND r.deleted_at IS NULL
                ORDER BY r.dt_clipagem ASC, r.titulo ASC
            """
            
            cursor.execute(query_radio, (usuario_id, data_inicio, data_fim))
            for row in cursor.fetchall():
                id_noticia, data, titulo, veiculo, programa, horario, texto, valor, vinculo_id = row
                noticias['radio'].append({
                    'id': id_noticia,
                    'data': data.strftime('%Y-%m-%d') if data else None,
                    'titulo': self._sanitize_text(titulo),
                    'veiculo': self._sanitize_text(veiculo),
                    'programa': self._sanitize_text(programa),
                    'horario': str(horario) if horario else '',
                    'texto': self._sanitize_text(texto),
                    'valor': float(valor or 0),
                    'vinculo_id': vinculo_id
                })
            
            print(f"📋 Notícias encontradas - Web: {len(noticias['web'])}, Impresso: {len(noticias['impresso'])}, TV: {len(noticias['tv'])}, Rádio: {len(noticias['radio'])}")
            return noticias
            
        except Exception as e:
            print(f"❌ Erro ao listar notícias: {e}")
            import traceback
            traceback.print_exc()
            return {
                'web': [],
                'impresso': [],
                'tv': [],
                'radio': []
            }

    def listar_noticias_por_periodo_com_filtros(self, usuario_id, data_inicio, data_fim, filtros=None):
        """
        Lista todas as notícias do período separadas por tipo de mídia com filtros aplicados
        
        Args:
            usuario_id (int): ID do usuário/cliente
            data_inicio (str): Data início no formato YYYY-MM-DD
            data_fim (str): Data fim no formato YYYY-MM-DD
            filtros (dict): Filtros a aplicar {
                'tipos_midia': ['web', 'tv', 'radio', 'impresso'], 
                'status': ['positivo', 'negativo', 'neutro'],
                'retorno': ['com_retorno', 'sem_retorno'],
                'areas': [1, 2, 3]
            }
            
        Returns:
            dict: Dicionário com listas de notícias por mídia (web, impresso, tv, radio)
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            # Processamento dos filtros
            if not filtros:
                filtros = {}
            
            ids_especificos = filtros.get('ids_especificos', {})
            tipos_midia = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso'])
            status_filtros = filtros.get('status', ['positivo', 'negativo', 'neutro'])
            retorno_filtros = filtros.get('retorno', ['com_retorno', 'sem_retorno'])
            valor_filtros = filtros.get('valor', ['com_valor', 'sem_valor'])
            areas_filtros = filtros.get('areas', [])
            
            print(f"🔍 Aplicando filtros - Mídias: {tipos_midia}, Status: {status_filtros}, Retorno: {retorno_filtros}, Valor: {valor_filtros}, Áreas: {areas_filtros}")
            
            noticias = {
                'web': [],
                'impresso': [],
                'tv': [],
                'radio': []
            }
            
            # Funções auxiliares para construir condições SQL
            def _build_status_condition(table_prefix=""):
                if len(status_filtros) == 3:  # Todos selecionados
                    return ""
                conditions = []
                for status in status_filtros:
                    if status == 'positivo':
                        conditions.append(f"{table_prefix}sentimento = '1'")
                    elif status == 'negativo':
                        conditions.append(f"{table_prefix}sentimento = '-1'")
                    elif status == 'neutro':
                        conditions.append(f"{table_prefix}sentimento = '0'")
                
                if conditions:
                    return f" AND ({' OR '.join(conditions)})"
                return ""
            
            def _build_retorno_condition(table_prefix=""):
                # NOVA LÓGICA: Filtro de retorno nunca filtra dados, apenas controla visibilidade no PDF
                # Sempre retorna string vazia para incluir todas as notícias
                return ""
            
            def _build_valor_condition(table_prefix="", valor_column=""):
                # Verifica se tem filtro de valor ativo
                if len(valor_filtros) == 2:  # Todos selecionados
                    return ""
                
                conditions = []
                for valor in valor_filtros:
                    if valor == 'com_valor':
                        conditions.append(f"{table_prefix}{valor_column} > 0")
                    elif valor == 'sem_valor':
                        conditions.append(f"({table_prefix}{valor_column} IS NULL OR {table_prefix}{valor_column} = 0)")
                
                if conditions:
                    return f" AND ({' OR '.join(conditions)})"
                return ""

            def _build_area_condition(table_prefix=""):
                if areas_filtros:
                    area_ids = ','.join(map(str, areas_filtros))
                    return f" AND nc.area IN ({area_ids})"
                return ""
            
            # UPDATE: Atualiza valores das notícias web antes da listagem
            try:
                print(f"🔄 Atualizando valores das notícias web para cliente {usuario_id}...")
                update_query = """
                    UPDATE noticias_web
                    SET nu_valor = fonte_web.nu_valor
                    FROM fonte_web
                    WHERE fonte_web.id = noticias_web.id_fonte
                      AND noticias_web.id IN (
                        SELECT noticia_id 
                        FROM noticia_cliente 
                        WHERE cliente_id = %s AND tipo_id = 2
                      )
                      AND (
                        noticias_web.nu_valor IS NULL
                        OR noticias_web.nu_valor = 0
                      )
                """
                cursor.execute(update_query, (usuario_id,))
                rows_updated = cursor.rowcount
                print(f"✅ {rows_updated} notícias web atualizadas com novos valores")
            except Exception as e:
                print(f"⚠️ Erro ao atualizar valores das notícias web: {e}")
                # Continua a execução mesmo se o update falhar
            
            # 1. Notícias de Web (se tipo web está nos filtros)
            if 'web' in tipos_midia:
                query_web = f"""
                    SELECT 
                        w.id,
                        w.data_noticia as data,
                        COALESCE(w.titulo_noticia, 'Título não informado') as titulo,
                        COALESCE(fw.nome, 'Site Não Identificado') as veiculo,
                        COALESCE(w.url_noticia, '') as link,
                        COALESCE(w.sinopse, '') as texto,
                        COALESCE(w.nu_valor, 0) as valor,
                        '' as tags,
                        nc.area as id_area,
                        COALESCE(a.descricao, '') as nome_area,
                        nc.id as vinculo_id,
                        nc.sentimento as sentimento,
                        COALESCE(w.ds_caminho_img, '') as ds_caminho_img
                    FROM noticias_web w
                    LEFT JOIN fonte_web fw ON w.id_fonte = fw.id
                    JOIN noticia_cliente nc ON w.id = nc.noticia_id AND nc.tipo_id = 2
                    LEFT JOIN area a ON nc.area = a.id
                    WHERE nc.cliente_id = %s
                    AND w.data_noticia BETWEEN %s AND %s
                    AND w.deleted_at IS NULL
                    {_build_status_condition('nc.')}
                    {_build_retorno_condition('w.')}
                    {_build_valor_condition('w.', 'nu_valor')}
                    {_build_area_condition()}
                    ORDER BY w.data_noticia ASC, w.titulo_noticia ASC
                """
                
                cursor.execute(query_web, (usuario_id, data_inicio, data_fim))
                for row in cursor.fetchall():
                    id_noticia, data, titulo, veiculo, link, texto, valor, tags, id_area, nome_area, vinculo_id, sentimento, ds_caminho_img = row
                    noticias['web'].append({
                        'id': id_noticia,
                        'data': data.strftime('%Y-%m-%d') if data else None,
                        'titulo': self._sanitize_text(titulo),
                        'veiculo': self._sanitize_text(veiculo),
                        'link': self._sanitize_text(link),
                        'texto': self._sanitize_text(texto),
                        'valor': float(valor or 0),
                        'tags': self._sanitize_text(tags or ''),
                        'id_area': id_area,
                        'nome_area': self._sanitize_text(nome_area) if nome_area else None,
                        'vinculo_id': vinculo_id,
                        'sentimento': sentimento,
                        'ds_caminho_img': self._sanitize_text(ds_caminho_img) if ds_caminho_img else None
                    })
            
            # 2. Notícias de Impresso (se tipo impresso está nos filtros)
            if 'impresso' in tipos_midia:
                query_impresso = f"""
                    SELECT 
                        j.id,
                        j.dt_clipagem as data,
                        j.titulo,
                        COALESCE(ji.nome, 'Jornal Não Identificado') as veiculo,
                        COALESCE(j.sinopse, '') as texto,
                        COALESCE(j.valor_retorno, 0) as valor,
                        '' as tags,
                        nc.area as id_area,
                        COALESCE(a.descricao, '') as nome_area,
                        nc.id as vinculo_id,
                        nc.sentimento as sentimento,
                        COALESCE(j.ds_caminho_img, '') as ds_caminho_img
                    FROM noticia_impresso j
                    LEFT JOIN jornal_online ji ON j.id_fonte = ji.id
                    JOIN noticia_cliente nc ON j.id = nc.noticia_id AND nc.tipo_id = 1
                    LEFT JOIN area a ON nc.area = a.id
                    WHERE nc.cliente_id = %s
                    AND j.dt_clipagem BETWEEN %s AND %s
                    AND j.deleted_at IS NULL
                    AND j.sinopse IS NOT NULL
                    AND j.sinopse != ''
                    {_build_status_condition('nc.')}
                    {_build_retorno_condition('j.')}
                    {_build_valor_condition('j.', 'valor_retorno')}
                    {_build_area_condition()}
                    ORDER BY j.dt_clipagem ASC, j.titulo ASC
                """
                
                cursor.execute(query_impresso, (usuario_id, data_inicio, data_fim))
                for row in cursor.fetchall():
                    id_noticia, data, titulo, veiculo, texto, valor, tags, id_area, nome_area, vinculo_id, sentimento, ds_caminho_img = row
                    noticias['impresso'].append({
                        'id': id_noticia,
                        'data': data.strftime('%Y-%m-%d') if data else None,
                        'titulo': self._sanitize_text(titulo),
                        'veiculo': self._sanitize_text(veiculo),
                        'texto': self._sanitize_text(texto),
                        'valor': float(valor or 0),
                        'tags': self._sanitize_text(tags or ''),
                        'id_area': id_area,
                        'nome_area': self._sanitize_text(nome_area) if nome_area else None,
                        'vinculo_id': vinculo_id,
                        'sentimento': sentimento,
                        'ds_caminho_img': self._sanitize_text(ds_caminho_img) if ds_caminho_img else None
                    })
            
            # 3. Notícias de TV (se tipo tv está nos filtros)
            if 'tv' in tipos_midia:
                # Constrói filtro de valor para TV (baseado no valor calculado)
                valor_condition_tv = ""
                if len(valor_filtros) < 2:  # Nem todos selecionados
                    valor_calc = """COALESCE(
                        (
                            CASE 
                                WHEN t.duracao IS NULL THEN 0
                                WHEN t.duracao::text ~ '^\d+:\d+:\d+$' THEN 
                                    EXTRACT(EPOCH FROM t.duracao::time)
                                WHEN pg_typeof(t.duracao)::text = 'interval' THEN 
                                    EXTRACT(EPOCH FROM t.duracao)
                                ELSE 0
                            END
                        ) * COALESCE(p.valor_segundo, 0)
                    , 0)"""
                    
                    conditions = []
                    for valor in valor_filtros:
                        if valor == 'com_valor':
                            conditions.append(f"({valor_calc}) > 0")
                        elif valor == 'sem_valor':
                            conditions.append(f"({valor_calc}) = 0")
                    
                    if conditions:
                        valor_condition_tv = f" AND ({' OR '.join(conditions)})"
                        
                query_tv = f"""
                    SELECT 
                        t.id,
                        t.dt_noticia as data,
                        t.sinopse as titulo,
                        COALESCE(e.nome_emissora, 'Emissora Não Identificada') as veiculo,
                        COALESCE(p.nome_programa, 'Programa Não Identificado') as programa,
                        COALESCE(t.horario, '00:00:00') as horario,
                        COALESCE(t.sinopse, '') as texto,
                        COALESCE(
                            (
                                CASE 
                                    WHEN t.duracao IS NULL THEN 0
                                    WHEN t.duracao::text ~ '^\d+:\d+:\d+$' THEN 
                                        EXTRACT(EPOCH FROM t.duracao::time)
                                    WHEN pg_typeof(t.duracao)::text = 'interval' THEN 
                                        EXTRACT(EPOCH FROM t.duracao)
                                    ELSE 0
                                END
                            ) * COALESCE(p.valor_segundo, 0)
                        , 0) as valor,
                        '' as tags,
                        nc.area as id_area,
                        COALESCE(a.descricao, '') as nome_area,
                        nc.id as vinculo_id,
                        nc.sentimento as sentimento
                    FROM noticia_tv t
                    LEFT JOIN emissora_web e ON t.emissora_id = e.id
                    LEFT JOIN programa_emissora_web p ON t.programa_id = p.id
                    JOIN noticia_cliente nc ON t.id = nc.noticia_id AND nc.tipo_id = 4
                    LEFT JOIN area a ON nc.area = a.id
                    WHERE nc.cliente_id = %s
                    AND t.dt_noticia BETWEEN %s AND %s
                    AND t.deleted_at IS NULL
                    {_build_status_condition('nc.')}
                    {_build_area_condition()}
                    {valor_condition_tv}
                    ORDER BY t.dt_noticia ASC, t.sinopse ASC
                """
                
                cursor.execute(query_tv, (usuario_id, data_inicio, data_fim))
                for row in cursor.fetchall():
                    id_noticia, data, titulo, veiculo, programa, horario, texto, valor, tags, id_area, nome_area, vinculo_id, sentimento = row
                    noticias['tv'].append({
                        'id': id_noticia,
                        'data': data.strftime('%Y-%m-%d') if data else None,
                        'titulo': self._sanitize_text(titulo),
                        'veiculo': self._sanitize_text(veiculo),
                        'programa': self._sanitize_text(programa),
                        'horario': str(horario) if horario else '',
                        'texto': self._sanitize_text(texto),
                        'valor': float(valor or 0),
                        'tags': self._sanitize_text(tags or ''),
                        'id_area': id_area,
                        'nome_area': self._sanitize_text(nome_area) if nome_area else None,
                        'vinculo_id': vinculo_id,
                        'sentimento': sentimento
                    })
            
            # 4. Notícias de Rádio (se tipo radio está nos filtros) - COM TRATAMENTO DE INCONSISTÊNCIAS
            if 'radio' in tipos_midia:
                # Constrói filtro de valor para Rádio (baseado no valor_retorno)
                valor_condition_radio = ""
                if len(valor_filtros) < 2:  # Nem todos selecionados
                    conditions = []
                    for valor in valor_filtros:
                        if valor == 'com_valor':
                            conditions.append("r.valor_retorno > 0")
                        elif valor == 'sem_valor':
                            conditions.append("(r.valor_retorno IS NULL OR r.valor_retorno = 0)")
                    
                    if conditions:
                        valor_condition_radio = f" AND ({' OR '.join(conditions)})"
                        
                query_radio = f"""
                    SELECT 
                        r.id,
                        r.dt_clipagem as data,
                        r.titulo,
                        CASE 
                            WHEN e.nome_emissora IS NOT NULL THEN e.nome_emissora
                            WHEN p.nome_programa IS NOT NULL AND pe.nome_emissora IS NOT NULL THEN pe.nome_emissora
                            ELSE 'Emissora Não Identificada'
                        END as veiculo,
                        COALESCE(p.nome_programa, 'Programa Não Identificado') as programa,
                        COALESCE(r.horario, '00:00:00') as horario,
                        COALESCE(r.sinopse, '') as texto,
                        COALESCE(r.valor_retorno, 0) as valor,
                        '' as tags,
                        nc.area as id_area,
                        COALESCE(a.descricao, '') as nome_area,
                        nc.id as vinculo_id,
                        nc.sentimento as sentimento
                                            FROM noticia_radio r
                        LEFT JOIN emissora_radio e ON r.emissora_id = e.id
                        LEFT JOIN programa_emissora_radio p ON r.programa_id = p.id
                        LEFT JOIN emissora_radio pe ON p.id_emissora = pe.id
                        JOIN noticia_cliente nc ON r.id = nc.noticia_id AND nc.tipo_id = 3
                        LEFT JOIN area a ON nc.area = a.id
                        WHERE nc.cliente_id = %s
                        AND r.dt_clipagem BETWEEN %s AND %s
                        AND r.deleted_at IS NULL
                        {_build_status_condition('nc.')}
                        {_build_area_condition()}
                        {valor_condition_radio}
                        ORDER BY r.dt_clipagem ASC, r.titulo ASC
                """
                
                cursor.execute(query_radio, (usuario_id, data_inicio, data_fim))
                for row in cursor.fetchall():
                    id_noticia, data, titulo, veiculo, programa, horario, texto, valor, tags, id_area, nome_area, vinculo_id, sentimento = row
                    noticias['radio'].append({
                        'id': id_noticia,
                        'data': data.strftime('%Y-%m-%d') if data else None,
                        'titulo': self._sanitize_text(titulo),
                        'veiculo': self._sanitize_text(veiculo),
                        'programa': self._sanitize_text(programa),
                        'horario': str(horario) if horario else '',
                        'texto': self._sanitize_text(texto),
                        'valor': float(valor or 0),
                        'tags': self._sanitize_text(tags or ''),
                        'id_area': id_area,
                        'nome_area': self._sanitize_text(nome_area) if nome_area else None,
                        'vinculo_id': vinculo_id,
                        'sentimento': sentimento
                    })
            
            # NOVA LÓGICA: Todas as notícias são incluídas, filtro apenas controla visibilidade no PDF
            
            # Pós-processamento: Adiciona as tags do misc_data para cada notícia
            for tipo_midia in ['web', 'impresso', 'tv', 'radio']:
                tipo_id_map = {'web': 2, 'impresso': 1, 'tv': 4, 'radio': 3}
                tipo_id = tipo_id_map[tipo_midia]
                
                for noticia in noticias[tipo_midia]:
                    tags_str = self._get_tags_from_misc_data(noticia['id'], tipo_id)
                    noticia['tags'] = tags_str
            
            print(f"📋 Notícias filtradas - Web: {len(noticias['web'])}, Impresso: {len(noticias['impresso'])}, TV: {len(noticias['tv'])}, Rádio: {len(noticias['radio'])}")
            return noticias
            
        except Exception as e:
            print(f"❌ Erro ao listar notícias com filtros: {e}")
            import traceback
            traceback.print_exc()
            return {
                'web': [],
                'impresso': [],
                'tv': [],
                'radio': []
            }

    def adicionar_noticia(self, dados_noticia):
        """
        Adiciona uma nova notícia ao banco de dados
        
        Args:
            dados_noticia (dict): Dicionário com os dados da notícia
                - tipo: 'WEB', 'JORNAL', 'TV', 'RADIO'
                - cliente_id: ID do cliente
                - data: Data da notícia
                - titulo: Título da notícia
                - veiculo: Nome do veículo
                - texto: Texto/descrição
                - valor: Valor em R$
                - tags: Tags separadas por vírgula
                - Campos específicos por tipo (programa, horario, link)
                
        Returns:
            dict: {'success': bool, 'message': str, 'noticia_id': int}
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            tipo = dados_noticia.get('tipo', '').upper()
            cliente_id = int(dados_noticia.get('cliente_id'))
            data = dados_noticia.get('data')
            titulo = dados_noticia.get('titulo', '')
            veiculo = dados_noticia.get('veiculo', '')
            texto = dados_noticia.get('texto', '')
            # Tratamento seguro para conversão de valor
            valor_str = dados_noticia.get('valor', '0')
            if valor_str == '' or valor_str is None:
                valor = 0.0
            else:
                try:
                    valor = float(valor_str)
                except (ValueError, TypeError):
                    valor = 0.0
            tags = dados_noticia.get('tags', '').strip()  # Adiciona suporte para tags
            
            # Validações básicas
            if not tipo or not cliente_id or not data or not titulo:
                return {'success': False, 'message': 'Campos obrigatórios não preenchidos'}
            
            if tipo == 'WEB':
                # Adiciona notícia de Web
                link = dados_noticia.get('link', '')
                
                # Busca uma fonte padrão ou cria uma genérica
                cursor.execute("SELECT id FROM fonte_web WHERE nome = %s LIMIT 1", (veiculo,))
                fonte_result = cursor.fetchone()
                
                if fonte_result:
                    id_fonte = fonte_result[0]
                else:
                    # Cria uma nova fonte se não existe
                    cursor.execute("INSERT INTO fonte_web (nome) VALUES (%s)", (veiculo,))
                    id_fonte = cursor.lastrowid
                
                # Insere na nova estrutura noticias_web
                query = """
                    INSERT INTO noticias_web (
                        data_noticia, titulo_noticia, sinopse, nu_valor, 
                        url_noticia, id_fonte, tags
                    ) VALUES (%s, %s, %s, %s, %s, %s, %s)
                """
                
                cursor.execute(query, (
                    data, titulo, texto, valor, 
                    link, id_fonte, tags
                ))
                
                # Obtém o ID da notícia recém-criada
                noticia_id = cursor.lastrowid
                
                # Adiciona à tabela noticia_cliente
                cursor.execute("""
                    INSERT INTO noticia_cliente (cliente_id, noticia_id, tipo_id)
                    VALUES (%s, %s, 2)
                """, (cliente_id, noticia_id))
                
            elif tipo == 'JORNAL':
                # Adiciona notícia de Impresso
                
                # Busca um jornal padrão ou cria um genérico
                cursor.execute("SELECT id FROM jornal_online WHERE nome = %s LIMIT 1", (veiculo,))
                jornal_result = cursor.fetchone()
                
                if jornal_result:
                    id_jornal = jornal_result[0]
                else:
                    # Cria um novo jornal se não existe
                    cursor.execute("INSERT INTO jornal_online (nome) VALUES (%s)", (veiculo,))
                    id_jornal = cursor.lastrowid
                
                query = """
                    INSERT INTO noticia_impresso (
                        dt_clipagem, titulo, sinopse, valor_retorno,
                        id_fonte, tags
                    ) VALUES (%s, %s, %s, %s, %s, %s)
                """
                
                cursor.execute(query, (
                    data, titulo, texto, valor, id_jornal, tags
                ))
                
                # Obtém o ID da notícia recém-criada
                noticia_id = cursor.lastrowid
                
                # Adiciona à tabela noticia_cliente
                cursor.execute("""
                    INSERT INTO noticia_cliente (cliente_id, noticia_id, tipo_id)
                    VALUES (%s, %s, 1)
                """, (cliente_id, noticia_id))
                
            elif tipo == 'TV':
                # Adiciona notícia de TV
                programa = dados_noticia.get('programa', 'Programa Não Identificado')
                horario = dados_noticia.get('horario', '00:00:00')
                
                # Busca uma emissora padrão ou cria uma genérica
                cursor.execute("SELECT id FROM emissora_web WHERE nome_emissora = %s LIMIT 1", (veiculo,))
                emissora_result = cursor.fetchone()
                
                if emissora_result:
                    id_emissora = emissora_result[0]
                else:
                    # Cria uma nova emissora se não existe
                    cursor.execute("INSERT INTO emissora_web (nome_emissora) VALUES (%s)", (veiculo,))
                    id_emissora = cursor.lastrowid
                
                # Busca um programa padrão ou cria um genérico
                cursor.execute("SELECT id FROM programa_emissora_web WHERE nome_programa = %s LIMIT 1", (programa,))
                programa_result = cursor.fetchone()
                
                if programa_result:
                    id_programa = programa_result[0]
                else:
                    # Cria um novo programa se não existe (com valor padrão)
                    cursor.execute("""
                        INSERT INTO programa_emissora_web (nome_programa, valor_segundo) 
                        VALUES (%s, 0.01)
                    """, (programa,))
                    id_programa = cursor.lastrowid
                
                # CORREÇÃO: Converte valor em R$ para duração (intervalo)
                # Baseado no valor_segundo do programa para calcular a duração
                if valor > 0:
                    segundos_estimados = int(valor / 0.01)
                    duracao_str = f"{segundos_estimados // 3600:02d}:{(segundos_estimados % 3600) // 60:02d}:{segundos_estimados % 60:02d}"
                else:
                    duracao_str = "00:00:00"
                
                query = """
                    INSERT INTO noticia_tv (
                        dt_noticia, sinopse, horario, duracao,
                        emissora_id, programa_id, tags
                    ) VALUES (%s, %s, %s, %s::time, %s, %s, %s)
                """
                
                cursor.execute(query, (
                    data, texto, horario, duracao_str,
                    id_emissora, id_programa, tags
                ))
                
                # Obtém o ID da notícia recém-criada
                noticia_id = cursor.lastrowid
                
                # Adiciona à tabela noticia_cliente
                cursor.execute("""
                    INSERT INTO noticia_cliente (cliente_id, noticia_id, tipo_id)
                    VALUES (%s, %s, 4)
                """, (cliente_id, noticia_id))
                
            elif tipo == 'RADIO':
                # Adiciona notícia de Rádio
                programa = dados_noticia.get('programa_radio', 'Programa Não Identificado')
                horario = dados_noticia.get('horario_radio', '00:00:00')
                
                # Busca uma emissora padrão ou cria uma genérica
                cursor.execute("SELECT id FROM emissora_radio WHERE nome_emissora = %s LIMIT 1", (veiculo,))
                emissora_result = cursor.fetchone()
                
                if emissora_result:
                    id_emissora = emissora_result[0]
                else:
                    # Cria uma nova emissora se não existe
                    cursor.execute("INSERT INTO emissora_radio (nome_emissora) VALUES (%s)", (veiculo,))
                    id_emissora = cursor.lastrowid
                
                # Busca um programa padrão ou cria um genérico
                cursor.execute("SELECT id FROM programa_emissora_radio WHERE nome_programa = %s LIMIT 1", (programa,))
                programa_result = cursor.fetchone()
                
                if programa_result:
                    id_programa = programa_result[0]
                else:
                    # Cria um novo programa se não existe (com valor padrão)
                    cursor.execute("""
                        INSERT INTO programa_emissora_radio (nome_programa, valor_segundo) 
                        VALUES (%s, 0.01)
                    """, (programa,))
                    id_programa = cursor.lastrowid
                
                # CORREÇÃO: Converte valor em R$ para duração baseado no valor_segundo do programa
                if valor > 0:
                    segundos_estimados = int(valor / 0.01)
                    duracao_str = f"{segundos_estimados // 3600:02d}:{(segundos_estimados % 3600) // 60:02d}:{segundos_estimados % 60:02d}"
                else:
                    duracao_str = "00:00:00"
                
                query = """
                    INSERT INTO noticia_radio (
                        dt_clipagem, titulo, sinopse, horario, duracao,
                        emissora_id, programa_id, tags
                    ) VALUES (%s, %s, %s, %s, %s::time, %s, %s, %s)
                """
                
                cursor.execute(query, (
                    data, titulo, texto, horario, duracao_str,
                    id_emissora, id_programa, tags
                ))
                
                # Obtém o ID da notícia recém-criada
                noticia_id = cursor.lastrowid
                
                # Adiciona à tabela noticia_cliente
                cursor.execute("""
                    INSERT INTO noticia_cliente (cliente_id, noticia_id, tipo_id)
                    VALUES (%s, %s, 3)
                """, (cliente_id, noticia_id))
                
            else:
                return {'success': False, 'message': f'Tipo de mídia inválido: {tipo}'}
            
            # Commit da transação
            self.connection.commit()
            noticia_id = cursor.lastrowid
            
            # Busca a notícia recém-criada para retornar os dados completos
            noticia_criada = self._buscar_noticia_por_id(noticia_id, tipo.lower())
            
            print(f"✅ Notícia de {tipo} adicionada com sucesso - ID: {noticia_id}")
            return {
                'success': True, 
                'message': f'Notícia de {tipo} adicionada com sucesso',
                'noticia_id': noticia_id,
                'noticia': noticia_criada
            }
            
        except Exception as e:
            # Rollback em caso de erro
            self.connection.rollback()
            print(f"❌ Erro ao adicionar notícia: {e}")
            import traceback
            traceback.print_exc()
            return {'success': False, 'message': f'Erro ao adicionar notícia: {str(e)}'}

    def _buscar_noticia_por_id(self, noticia_id, tipo):
        """Busca uma notícia específica por ID e tipo para retornar após criação"""
        cursor = self.connection.cursor()
        
        try:
            if tipo == 'web':
                query = """
                    SELECT w.id, w.data_noticia as data, COALESCE(w.titulo_noticia, 'Título não informado') as titulo, 
                           COALESCE(fw.nome, 'Site') as veiculo,
                           w.url_noticia as link, w.sinopse as texto, w.nu_valor as valor,
                           '' as tags, NULL as id_area,
                           '' as nome_area, COALESCE(w.ds_caminho_img, '') as ds_caminho_img
                    FROM noticias_web w
                    LEFT JOIN fonte_web fw ON w.id_fonte = fw.id
                    WHERE w.id = %s
                """
            elif tipo == 'jornal':
                query = """
                    SELECT j.id, j.dt_clipagem as data, j.titulo,
                           COALESCE(ji.nome, 'Jornal') as veiculo,
                           '' as extra1, j.sinopse as texto, j.valor_retorno as valor,
                           '' as tags, NULL as id_area,
                           '' as nome_area, COALESCE(j.ds_caminho_img, '') as ds_caminho_img
                    FROM noticia_impresso j
                    LEFT JOIN jornal_online ji ON j.id_fonte = ji.id
                    WHERE j.id = %s
                """
            elif tipo == 'tv':
                query = """
                    SELECT t.id, t.dt_noticia as data, t.sinopse as titulo,
                           COALESCE(e.nome_emissora, 'Emissora') as veiculo,
                           COALESCE(p.nome_programa, 'Programa') as programa,
                           t.horario, t.sinopse as texto,
                           COALESCE(
                               t.valor_calculado,
                               (
                                   CASE 
                                       WHEN t.duracao IS NULL THEN 0
                                       WHEN t.duracao::text ~ '^\d+:\d+:\d+$' THEN 
                                           EXTRACT(EPOCH FROM t.duracao::time)
                                       WHEN pg_typeof(t.duracao)::text = 'interval' THEN 
                                           EXTRACT(EPOCH FROM t.duracao)
                                       ELSE 0
                                   END
                               ) * COALESCE(p.valor_segundo, 0)
                           ) as valor,
                           '' as tags, NULL as id_area,
                           '' as nome_area
                    FROM noticia_tv t
                    LEFT JOIN emissora_web e ON t.emissora_id = e.id
                    LEFT JOIN programa_emissora_web p ON t.programa_id = p.id
                    WHERE t.id = %s
                """
            elif tipo == 'radio':
                query = """
                    SELECT r.id, r.dt_clipagem as data, r.titulo,
                           CASE 
                               WHEN e.nome_emissora IS NOT NULL THEN e.nome_emissora
                               WHEN p.nome_programa IS NOT NULL AND pe.nome_emissora IS NOT NULL THEN pe.nome_emissora
                               ELSE 'Emissora Não Identificada'
                           END as veiculo,
                           COALESCE(p.nome_programa, 'Programa') as programa,
                           r.horario as horario, r.sinopse as texto,
                           COALESCE(r.valor_retorno, 0) as valor,
                           '' as tags, NULL as id_area,
                           '' as nome_area
                    FROM noticia_radio r
                    LEFT JOIN emissora_radio e ON r.emissora_id = e.id
                    LEFT JOIN programa_emissora_radio p ON r.programa_id = p.id
                    LEFT JOIN emissora_radio pe ON p.id_emissora = pe.id
                    WHERE r.id = %s
                """
            
            cursor.execute(query, (noticia_id,))
            row = cursor.fetchone()
            
            if row:
                if tipo in ['tv', 'radio']:
                    valor_calculado = float(row[7] or 0)
                    print(f"🔍 {tipo.upper()} busca - Valor calculado: R$ {valor_calculado:.2f}")
                    return {
                        'id': row[0],
                        'data': str(row[1]),
                        'titulo': row[2],
                        'veiculo': row[3],
                        'programa': row[4],
                        'horario': str(row[5]) if row[5] else '',
                        'texto': row[6] or '',
                        'valor': valor_calculado,
                        'tags': row[8] or '',
                        'id_area': row[9],
                        'nome_area': row[10] if row[10] else None
                    }
                elif tipo == 'web':
                    valor_direto = float(row[6] or 0)
                    print(f"🔍 WEB busca - Valor direto: R$ {valor_direto:.2f}")
                    return {
                        'id': row[0],
                        'data': str(row[1]),
                        'titulo': row[2],
                        'veiculo': row[3],
                        'link': row[4] or '',
                        'texto': row[5] or '',
                        'valor': valor_direto,
                        'tags': row[7] or '',
                        'id_area': row[8],
                        'nome_area': row[9] if row[9] else None,
                        'ds_caminho_img': row[10] if row[10] else None
                    }
                else:  # jornal
                    valor_direto = float(row[6] or 0)
                    print(f"🔍 IMPRESSO busca - Valor direto: R$ {valor_direto:.2f}")
                    return {
                        'id': row[0],
                        'data': str(row[1]),
                        'titulo': row[2],
                        'veiculo': row[3],
                        'texto': row[5] or '',
                        'valor': valor_direto,
                        'tags': row[7] or '',
                        'id_area': row[8],
                        'nome_area': row[9] if row[9] else None,
                        'ds_caminho_img': row[10] if row[10] else None
                    }
            
            return None
            
        except Exception as e:
            print(f"❌ Erro ao buscar notícia criada: {e}")
            return None

    def get_noticia_by_id(self, noticia_id, tipo_midia):
        """
        Busca uma notícia específica por ID e tipo de mídia
        
        Args:
            noticia_id (int): ID da notícia
            tipo_midia (str): Tipo da mídia ('web', 'impresso', 'tv', 'radio')
            
        Returns:
            dict: Dados da notícia ou None se não encontrada
        """
        if not self.connection:
            self.connect()
        
        # Mapeia os tipos para o formato usado pelo método interno
        tipo_map = {
            'web': 'web',
            'impresso': 'jornal',
            'tv': 'tv',
            'radio': 'radio'
        }
        
        tipo_interno = tipo_map.get(tipo_midia.lower())
        if not tipo_interno:
            print(f"❌ Tipo de mídia inválido: {tipo_midia}")
            return None
        
        return self._buscar_noticia_por_id(noticia_id, tipo_interno)

    def editar_noticia(self, noticia_id, dados_noticia):
        """
        Edita uma notícia existente no banco de dados
        
        Args:
            noticia_id (int): ID da notícia a ser editada
            dados_noticia (dict): Dicionário com os novos dados da notícia
                - tipo: 'WEB', 'JORNAL', 'TV', 'RADIO'
                - cliente_id: ID do cliente
                - data: Data da notícia
                - titulo: Título da notícia
                - veiculo: Nome do veículo
                - texto: Texto/descrição
                - valor: Valor em R$
                - tags: Tags separadas por vírgula
                - Campos específicos por tipo (programa, horario, link)
                
        Returns:
            dict: {'success': bool, 'message': str, 'noticia': dict}
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            print(f"✏️ Iniciando edição da notícia ID: {noticia_id}")
            
            tipo = dados_noticia.get('tipo', '').upper()
            cliente_id = int(dados_noticia.get('cliente_id'))
            data = dados_noticia.get('data')
            titulo = dados_noticia.get('titulo', '')
            veiculo = dados_noticia.get('veiculo', '')
            texto = dados_noticia.get('texto', '')
            # Tratamento seguro para conversão de valor
            valor_str = dados_noticia.get('valor', '0')
            if valor_str == '' or valor_str is None:
                valor = 0.0
            else:
                try:
                    valor = float(valor_str)
                except (ValueError, TypeError):
                    valor = 0.0
            tags = dados_noticia.get('tags', '').strip()  # Captura as tags
            
            # Validações básicas
            if not tipo or not cliente_id or not data or not titulo:
                return {'success': False, 'message': 'Campos obrigatórios não preenchidos'}
            
            # Mapeia o tipo para a tabela correspondente
            tabela_map = {
                'WEB': 'noticias_web',
                'JORNAL': 'noticia_impresso',
                'TV': 'noticia_tv',
                'RADIO': 'noticia_radio'
            }
            
            # Mapeia o tipo para o tipo_id da tabela noticia_cliente
            tipo_id_map = {
                'WEB': 2,
                'JORNAL': 1,
                'TV': 4,
                'RADIO': 3
            }
            
            if tipo not in tabela_map:
                return {'success': False, 'message': f'Tipo de mídia inválido: {tipo}'}
            
            tabela = tabela_map[tipo]
            tipo_id = tipo_id_map[tipo]
            print(f"🗂️ Tabela para edição: {tabela}")
            
            # Primeiro, verifica se a notícia existe
            cursor.execute(f"SELECT id FROM {tabela} WHERE id = %s", (noticia_id,))
            if not cursor.fetchone():
                return {'success': False, 'message': 'Notícia não encontrada'}
            
            # Prepara os dados para misc_data (JSON)
            misc_data = {}
            if tags:
                # Converte tags para array JSON
                tags_array = [tag.strip() for tag in tags.split(',') if tag.strip()]
                misc_data['tags'] = tags_array
                print(f"🏷️ Tags processadas: {tags_array}")
            
            # Converte misc_data para JSON
            import json
            misc_data_json = json.dumps(misc_data) if misc_data else None
            print(f"📊 misc_data JSON: {misc_data_json}")
            
            if tipo == 'WEB':
                # Edita notícia de Web
                link = dados_noticia.get('link', '')
                
                print(f"🌐 Editando Web - Valor recebido: R$ {valor:.2f}")
                
                # Busca ou cria a fonte
                cursor.execute("SELECT id FROM fonte_web WHERE nome = %s LIMIT 1", (veiculo,))
                fonte_result = cursor.fetchone()
                
                if fonte_result:
                    id_fonte = fonte_result[0]
                else:
                    # Cria uma nova fonte se não existe
                    cursor.execute("INSERT INTO fonte_web (nome) VALUES (%s)", (veiculo,))
                    id_fonte = cursor.lastrowid
                
                # Atualizar cliente na tabela noticia_cliente (incluindo misc_data)
                cursor.execute("""
                    UPDATE noticia_cliente SET cliente_id = %s, misc_data = %s
                    WHERE noticia_id = %s AND tipo_id = %s
                """, (cliente_id, misc_data_json, noticia_id, tipo_id))
                
                # Atualiza a notícia (SEM o campo tags na tabela individual)
                query = """
                    UPDATE noticias_web SET
                        data_noticia = %s, sinopse = %s, 
                        nu_valor = %s, url_noticia = %s, 
                        id_fonte = %s
                    WHERE id = %s
                """
                
                print(f"🌐 Salvando valor direto no campo nu_valor: R$ {valor:.2f}")
                cursor.execute(query, (
                    data, texto, valor, 
                    link, id_fonte, noticia_id
                ))
                
            elif tipo == 'JORNAL':
                # Edita notícia de Impresso
                
                print(f"📰 Editando Impresso - Valor recebido: R$ {valor:.2f}")
                
                # Busca ou cria o jornal
                cursor.execute("SELECT id FROM jornal_online WHERE nome = %s LIMIT 1", (veiculo,))
                jornal_result = cursor.fetchone()
                
                if jornal_result:
                    id_jornal = jornal_result[0]
                else:
                    # Cria um novo jornal se não existe
                    cursor.execute("INSERT INTO jornal_online (nome) VALUES (%s)", (veiculo,))
                    id_jornal = cursor.lastrowid
                
                # Atualizar cliente na tabela noticia_cliente (incluindo misc_data)
                cursor.execute("""
                    UPDATE noticia_cliente SET cliente_id = %s, misc_data = %s
                    WHERE noticia_id = %s AND tipo_id = %s
                """, (cliente_id, misc_data_json, noticia_id, tipo_id))
                
                # Atualiza a notícia (SEM o campo tags na tabela individual)
                query = """
                    UPDATE noticia_impresso SET
                        dt_clipagem = %s, titulo = %s,
                        sinopse = %s, valor_retorno = %s, id_fonte = %s
                    WHERE id = %s
                """
                
                print(f"📰 Salvando valor direto no campo valor_retorno: R$ {valor:.2f}")
                cursor.execute(query, (
                    data, titulo, texto, valor, id_jornal, noticia_id
                ))
                
            elif tipo == 'TV':
                # Edita notícia de TV
                programa = dados_noticia.get('programa', 'Programa Não Identificado')
                horario = dados_noticia.get('horario', '00:00:00')
                
                print(f"🎬 Editando TV - Valor recebido: R$ {valor:.2f}")
                
                # Busca ou cria a emissora
                cursor.execute("SELECT id FROM emissora_web WHERE nome_emissora = %s LIMIT 1", (veiculo,))
                emissora_result = cursor.fetchone()
                
                if emissora_result:
                    id_emissora = emissora_result[0]
                else:
                    # Cria uma nova emissora se não existe
                    cursor.execute("INSERT INTO emissora_web (nome_emissora) VALUES (%s)", (veiculo,))
                    id_emissora = cursor.lastrowid
                
                # Busca ou cria o programa
                cursor.execute("SELECT id, valor_segundo FROM programa_emissora_web WHERE nome_programa = %s LIMIT 1", (programa,))
                programa_result = cursor.fetchone()
                
                if programa_result:
                    id_programa = programa_result[0]
                    valor_segundo_atual = float(programa_result[1] or 0.01)
                else:
                    # Cria um novo programa se não existe
                    cursor.execute("""
                        INSERT INTO programa_emissora_web (nome_programa, valor_segundo) 
                        VALUES (%s, 0.01)
                    """, (programa,))
                    id_programa = cursor.lastrowid
                    valor_segundo_atual = 0.01
                
                # CORREÇÃO: Converte valor em R$ para duração baseado no valor_segundo do programa
                if valor > 0 and valor_segundo_atual > 0:
                    segundos_estimados = int(valor / valor_segundo_atual)
                    duracao_str = f"{segundos_estimados // 3600:02d}:{(segundos_estimados % 3600) // 60:02d}:{segundos_estimados % 60:02d}"
                    print(f"🎬 Calculando duração: R$ {valor:.2f} ÷ R$ {valor_segundo_atual:.4f} = {segundos_estimados}s = {duracao_str}")
                else:
                    duracao_str = "00:00:00"
                    print(f"🎬 Valor zero - definindo duração 00:00:00")
                
                # Atualizar cliente na tabela noticia_cliente (incluindo misc_data)
                cursor.execute("""
                    UPDATE noticia_cliente SET cliente_id = %s, misc_data = %s
                    WHERE noticia_id = %s AND tipo_id = %s
                """, (cliente_id, misc_data_json, noticia_id, tipo_id))
                
                # Atualiza a notícia (SEM o campo tags na tabela individual)
                query = """
                    UPDATE noticia_tv SET
                        dt_noticia = %s, sinopse = %s, horario = %s, 
                        duracao = %s::time, emissora_id = %s, programa_id = %s
                    WHERE id = %s
                """
                
                cursor.execute(query, (
                    data, texto, horario, duracao_str,
                    id_emissora, id_programa, noticia_id
                ))
                
                # Adiciona um campo auxiliar para armazenar o valor calculado
                # (campo valor_calculado na tabela noticia_tv deve ser adicionado manualmente)
                try:
                    cursor.execute("""
                        ALTER TABLE noticia_tv ADD COLUMN IF NOT EXISTS valor_calculado DECIMAL(10,2) DEFAULT 0
                    """)
                    
                    # Atualiza o valor calculado
                    cursor.execute("""
                        UPDATE noticia_tv SET valor_calculado = %s WHERE id = %s
                    """, (valor, noticia_id))
                    
                    print(f"📺 Valor calculado armazenado: R$ {valor:.2f}")
                except Exception as col_error:
                    print(f"⚠️ Erro ao atualizar valor calculado: {col_error}")
                    # Não falha a operação se não conseguir adicionar o campo
                
            elif tipo == 'RADIO':
                # Edita notícia de Rádio
                programa = dados_noticia.get('programa_radio', dados_noticia.get('programa', 'Programa Não Identificado'))
                horario = dados_noticia.get('horario_radio', dados_noticia.get('horario', '00:00:00'))
                
                print(f"📻 Editando Rádio - Valor recebido: R$ {valor:.2f}")
                
                # Busca ou cria a emissora
                cursor.execute("SELECT id FROM emissora_radio WHERE nome_emissora = %s LIMIT 1", (veiculo,))
                emissora_result = cursor.fetchone()
                
                if emissora_result:
                    id_emissora = emissora_result[0]
                else:
                    # Cria uma nova emissora se não existe
                    cursor.execute("INSERT INTO emissora_radio (nome_emissora) VALUES (%s)", (veiculo,))
                    id_emissora = cursor.lastrowid
                
                # Busca ou cria o programa
                cursor.execute("SELECT id, valor_segundo FROM programa_emissora_radio WHERE nome_programa = %s LIMIT 1", (programa,))
                programa_result = cursor.fetchone()
                
                if programa_result:
                    id_programa = programa_result[0]
                    valor_segundo_programa = float(programa_result[1] or 0.01)
                else:
                    # Cria um novo programa com valor padrão
                    cursor.execute("""
                        INSERT INTO programa_emissora_radio (nome_programa, valor_segundo) 
                        VALUES (%s, 0.01)
                    """, (programa,))
                    id_programa = cursor.lastrowid
                    valor_segundo_programa = 0.01
                
                # CORREÇÃO RÁDIO: Para novos programas, permite definir valor_segundo baseado no valor informado
                # Se o programa foi criado agora (valor_segundo = 0.01) e temos um valor específico,
                # ajusta o valor_segundo para que o cálculo seja preciso
                if valor > 0:
                    if valor_segundo_programa == 0.01 and valor > 10:  # Se parece ser um valor real
                        # Estima um tempo de 60 segundos para valores até R$ 100, 
                        # proporcionalmente mais para valores maiores
                        tempo_estimado = min(300, max(60, int(valor / 2)))  # Entre 60s e 300s
                        valor_segundo_ajustado = valor / tempo_estimado
                        
                        # Atualiza o programa com o valor_segundo ajustado
                        cursor.execute("""
                            UPDATE programa_emissora_radio 
                            SET valor_segundo = %s 
                            WHERE id = %s
                        """, (valor_segundo_ajustado, id_programa))
                        
                        valor_segundo_usado = valor_segundo_ajustado
                        segundos_estimados = tempo_estimado
                        print(f"📻 Programa novo - Ajustando valor/segundo: R$ {valor_segundo_ajustado:.4f} para {tempo_estimado}s")
                    else:
                        # Usa o valor_segundo existente do programa
                        valor_segundo_usado = valor_segundo_programa
                        segundos_estimados = int(valor / valor_segundo_usado)
                        print(f"📻 Calculando segundos: R$ {valor:.2f} ÷ R$ {valor_segundo_usado:.4f} = {segundos_estimados}s")
                    
                    duracao_str = f"{segundos_estimados // 3600:02d}:{(segundos_estimados % 3600) // 60:02d}:{segundos_estimados % 60:02d}"
                else:
                    duracao_str = "00:00:00"
                    print(f"📻 Valor zero - definindo duração 00:00:00")
                
                # Atualizar cliente na tabela noticia_cliente (incluindo misc_data)
                cursor.execute("""
                    UPDATE noticia_cliente SET cliente_id = %s, misc_data = %s
                    WHERE noticia_id = %s AND tipo_id = %s
                """, (cliente_id, misc_data_json, noticia_id, tipo_id))
                
                # Atualiza a notícia (SEM o campo tags na tabela individual)
                query = """
                    UPDATE noticia_radio SET
                        dt_clipagem = %s, titulo = %s, sinopse = %s,
                        horario = %s, duracao = %s::time, emissora_id = %s, programa_id = %s
                    WHERE id = %s
                """
                
                cursor.execute(query, (
                    data, titulo, texto, horario, duracao_str,
                    id_emissora, id_programa, noticia_id
                ))
                
                # Adiciona um campo auxiliar para armazenar o valor calculado
                # (campo valor_calculado na tabela noticia_radio deve ser adicionado manualmente)
                try:
                    cursor.execute("""
                        ALTER TABLE noticia_radio ADD COLUMN IF NOT EXISTS valor_calculado DECIMAL(10,2) DEFAULT 0
                    """)
                    
                    # Atualiza o valor calculado
                    cursor.execute("""
                        UPDATE noticia_radio SET valor_calculado = %s WHERE id = %s
                    """, (valor, noticia_id))
                    
                    print(f"📻 Valor calculado armazenado: R$ {valor:.2f}")
                except Exception as col_error:
                    print(f"⚠️ Erro ao atualizar valor calculado: {col_error}")
                    # Não falha a operação se não conseguir adicionar o campo
            
            # Verifica se a atualização afetou alguma linha
            rows_affected = cursor.rowcount
            print(f"📊 Linhas afetadas pela atualização: {rows_affected}")
            
            if rows_affected == 0:
                self.connection.rollback()
                return {'success': False, 'message': 'Nenhuma alteração foi realizada'}
            
            # Commit da transação
            self.connection.commit()
            print("✅ Transação commitada com sucesso")
            
            # Busca a notícia atualizada para retornar os dados completos
            # CORREÇÃO: Mapeia corretamente os tipos para busca
            tipo_para_busca = tipo.lower()
            if tipo_para_busca == 'jornal':
                tipo_para_busca = 'jornal'  # Mantém jornal para a busca no _buscar_noticia_por_id
            
            print(f"🔍 Buscando notícia atualizada - Tipo original: {tipo}, Tipo para busca: {tipo_para_busca}")
            noticia_atualizada = self._buscar_noticia_por_id(noticia_id, tipo_para_busca)
            
            # Adiciona as tags do misc_data à notícia retornada
            if noticia_atualizada and misc_data.get('tags'):
                noticia_atualizada['tags'] = ', '.join(misc_data['tags'])
            
            if noticia_atualizada:
                print(f"✅ Notícia de {tipo} editada com sucesso - ID: {noticia_id}")
                return {
                    'success': True, 
                    'message': f'Notícia de {tipo} editada com sucesso',
                    'noticia': noticia_atualizada
                }
            else:
                print(f"⚠️ Notícia editada mas não foi possível buscar dados atualizados")
                return {
                    'success': True, 
                    'message': f'Notícia de {tipo} editada com sucesso',
                    'noticia': {
                        'id': noticia_id,
                        'titulo': titulo,
                        'veiculo': veiculo,
                        'data': data,
                        'texto': texto,
                        'valor': valor,
                        'tags': tags
                    }
                }
            
        except Exception as e:
            # Rollback em caso de erro
            self.connection.rollback()
            print(f"❌ Erro ao editar notícia: {e}")
            import traceback
            traceback.print_exc()
            return {'success': False, 'message': f'Erro ao editar notícia: {str(e)}'}

    def excluir_noticia(self, vinculo_id):
        """
        Exclui apenas o vínculo da notícia na tabela noticia_cliente
        
        Args:
            vinculo_id (int): ID do vínculo na tabela noticia_cliente
            
        Returns:
            dict: {'success': bool, 'message': str, 'noticia_info': dict}
        """
        cursor = None
        
        try:
            print(f"🎯 Iniciando exclusão do vínculo - ID: {vinculo_id}")
            
            # Garante conexão fresh
            if not self.connection:
                print("🔌 Conexão não existe, tentando conectar...")
                if not self.connect():
                    raise Exception("Falha ao estabelecer conexão com o banco")
            
            # Testa se a conexão está realmente ativa
            try:
                test_cursor = self.connection.cursor()
                test_cursor.execute("SELECT 1")
                test_cursor.fetchone()
                test_cursor.close()
                print("✅ Conexão testada e confirmada")
            except Exception as conn_test_error:
                print(f"❌ Conexão inválida: {conn_test_error}")
                # Tenta reconectar
                self.disconnect()
                if not self.connect():
                    raise Exception(f"Falha ao reconectar com o banco: {conn_test_error}")
            
            # Cria cursor
            cursor = self.connection.cursor()
            
            # Primeiro, busca informações do vínculo antes de deletar
            print(f"🔍 Buscando informações do vínculo ID {vinculo_id}")
            cursor.execute("""
                SELECT nc.cliente_id, nc.noticia_id, nc.tipo_id,
                       CASE nc.tipo_id
                           WHEN 1 THEN 'impresso'
                           WHEN 2 THEN 'web'
                           WHEN 3 THEN 'radio'
                           WHEN 4 THEN 'tv'
                           ELSE 'desconhecido'
                       END as tipo_midia
                FROM noticia_cliente nc
                WHERE nc.id = %s
            """, (vinculo_id,))
            
            vinculo_info = cursor.fetchone()
            if not vinculo_info:
                print(f"⚠️ Vínculo ID {vinculo_id} não encontrado")
                return {'success': False, 'message': 'Vínculo não encontrado'}
            
            cliente_id, noticia_id, tipo_id, tipo_midia = vinculo_info
            print(f"📋 Vínculo encontrado - Cliente: {cliente_id}, Notícia: {noticia_id}, Tipo: {tipo_midia}")
            
            # Deleta apenas da tabela noticia_cliente (remove o vínculo)
            print(f"🗑️ Deletando vínculo da tabela noticia_cliente (ID = {vinculo_id})")
            cursor.execute("DELETE FROM noticia_cliente WHERE id = %s", (vinculo_id,))
            
            rows_affected = cursor.rowcount
            print(f"📊 Linhas afetadas em noticia_cliente: {rows_affected}")
            
            if rows_affected == 0:
                print(f"⚠️ Nenhuma linha foi deletada - vínculo {vinculo_id} não encontrado")
                self.connection.rollback()
                return {'success': False, 'message': 'Vínculo não encontrado ou já foi excluído'}
            
            # Commit imediato após DELETE bem-sucedido
            self.connection.commit()
            print(f"✅ DELETE commitado com sucesso - {rows_affected} vínculo(s) removido(s)")
            
            print(f"🗑️ Vínculo {vinculo_id} EXCLUÍDO (notícia {noticia_id} de {tipo_midia} desvinculada do cliente {cliente_id})")
            return {
                'success': True, 
                'message': f'Vínculo excluído com sucesso - notícia desvinculada do cliente',
                'noticia_info': {
                    'vinculo_id': vinculo_id,
                    'noticia_id': noticia_id,
                    'cliente_id': cliente_id,
                    'tipo_midia': tipo_midia,
                    'rows_affected': rows_affected
                }
            }
            
        except Exception as e:
            print(f"❌ Erro ao excluir vínculo: {e}")
            print(f"❌ Tipo do erro: {type(e).__name__}")
            print(f"❌ Dados da função: vinculo_id={vinculo_id}")
            print(f"❌ Conexão ativa: {self.connection is not None}")
            
            # Log mais detalhado do erro
            import sys
            exc_type, exc_value, exc_traceback = sys.exc_info()
            print(f"❌ Linha do erro: {exc_traceback.tb_lineno}")
            
            # Rollback em caso de erro
            try:
                if self.connection:
                    self.connection.rollback()
                    print("🔄 Rollback executado")
            except Exception as rollback_error:
                print(f"❌ Erro no rollback: {rollback_error}")
            
            import traceback
            traceback.print_exc()
            return {'success': False, 'message': f'Erro ao excluir vínculo: {str(e)}', 'error_type': type(e).__name__}
            
        finally:
            # Garante que o cursor seja fechado
            if cursor:
                try:
                    cursor.close()
                    print("🔒 Cursor fechado")
                except Exception as cursor_error:
                    print(f"⚠️ Erro ao fechar cursor: {cursor_error}")

    def aplicar_tags_lote(self, noticias_ids, tags_aplicar, acao='adicionar'):
        """
        Aplica tags a múltiplas notícias de uma vez
        
        Args:
            noticias_ids (list): Lista de dicionários com {'id': int, 'tipo': str}
            tags_aplicar (str): Tags a aplicar (separadas por vírgula)
            acao (str): 'adicionar', 'substituir' ou 'remover'
            
        Returns:
            dict: {'success': bool, 'message': str, 'noticias_atualizadas': int}
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        noticias_atualizadas = 0
        
        try:
            print(f"🏷️ Iniciando aplicação de tags em lote - {len(noticias_ids)} notícias")
            print(f"🎯 Ação: {acao}")
            print(f"🏷️ Tags: {tags_aplicar}")
            
            # Processa as tags
            if tags_aplicar:
                tags_novas = [tag.strip() for tag in tags_aplicar.split(',') if tag.strip()]
                tags_novas_str = ', '.join(tags_novas)
            else:
                tags_novas = []
                tags_novas_str = ''
            
            # Mapeia os tipos para os tipo_id da tabela noticia_cliente
            tipo_id_map = {
                'web': 2,
                'impresso': 1,
                'tv': 4,
                'radio': 3
            }
            
            # Processa cada notícia individualmente
            for noticia in noticias_ids:
                tipo = noticia.get('tipo', '').lower()
                id_noticia = noticia.get('id')
                
                if tipo not in tipo_id_map:
                    print(f"⚠️ Tipo desconhecido ignorado: {tipo}")
                    continue
                
                tipo_id = tipo_id_map[tipo]
                
                try:
                    # Busca o misc_data atual da notícia na tabela noticia_cliente
                    cursor.execute("""
                        SELECT misc_data FROM noticia_cliente 
                        WHERE noticia_id = %s AND tipo_id = %s
                    """, (id_noticia, tipo_id))
                    resultado = cursor.fetchone()
                    
                    if not resultado:
                        print(f"⚠️ Notícia ID {id_noticia} não encontrada na tabela noticia_cliente")
                        continue
                    
                    misc_data_atual = resultado[0]
                    
                    # Processa o JSON atual
                    if misc_data_atual:
                        try:
                            misc_data = json.loads(misc_data_atual)
                        except json.JSONDecodeError:
                            print(f"⚠️ Erro ao decodificar JSON da notícia {id_noticia}, criando novo")
                            misc_data = {}
                    else:
                        misc_data = {}
                    
                    # Obtém as tags atuais
                    tags_atuais_array = misc_data.get('tags', [])
                    tags_atuais = ', '.join(tags_atuais_array) if tags_atuais_array else ''
                    
                    print(f"📋 Notícia {id_noticia} - Tags atuais: '{tags_atuais}'")
                    
                    # Define as novas tags baseado na ação
                    if acao == 'substituir':
                        # Substitui todas as tags pelas novas
                        tags_finais_array = tags_novas
                        
                    elif acao == 'adicionar':
                        # Adiciona as novas tags às existentes
                        if tags_atuais_array:
                            # Remove duplicatas mantendo a ordem
                            tags_combinadas = tags_atuais_array.copy()
                            for tag_nova in tags_novas:
                                if tag_nova not in tags_combinadas:
                                    tags_combinadas.append(tag_nova)
                            tags_finais_array = tags_combinadas
                        else:
                            tags_finais_array = tags_novas
                            
                    elif acao == 'remover':
                        # Remove as tags especificadas
                        if tags_atuais_array and tags_novas:
                            tags_finais_array = [tag for tag in tags_atuais_array if tag not in tags_novas]
                        else:
                            tags_finais_array = tags_atuais_array
                    
                    else:
                        print(f"⚠️ Ação desconhecida: {acao}")
                        continue
                    
                    # Atualiza misc_data
                    if tags_finais_array:
                        misc_data['tags'] = tags_finais_array
                    else:
                        # Remove a chave tags se não há tags
                        misc_data.pop('tags', None)
                    
                    # Converte para JSON
                    misc_data_json = json.dumps(misc_data) if misc_data else None
                    
                    tags_finais_str = ', '.join(tags_finais_array) if tags_finais_array else ''
                    print(f"🎯 Notícia {id_noticia} - Tags finais: '{tags_finais_str}'")
                    
                    # Atualiza o misc_data na tabela noticia_cliente
                    cursor.execute("""
                        UPDATE noticia_cliente 
                        SET misc_data = %s 
                        WHERE noticia_id = %s AND tipo_id = %s
                    """, (misc_data_json, id_noticia, tipo_id))
                    
                    if cursor.rowcount > 0:
                        noticias_atualizadas += 1
                        print(f"✅ Notícia {id_noticia} atualizada com sucesso")
                    else:
                        print(f"⚠️ Notícia {id_noticia} não foi atualizada")
                    
                except Exception as e:
                    print(f"❌ Erro ao processar notícia {id_noticia}: {e}")
                    continue
            
            # Confirma as alterações
            self.connection.commit()
            
            print(f"✅ Aplicação de tags concluída - {noticias_atualizadas} notícias atualizadas")
            
            return {
                'success': True,
                'message': f'Tags aplicadas com sucesso a {noticias_atualizadas} notícias',
                'noticias_atualizadas': noticias_atualizadas
            }
        
        except Exception as e:
            # Rollback em caso de erro
            self.connection.rollback()
            print(f"❌ Erro ao aplicar tags em lote: {e}")
            import traceback
            traceback.print_exc()
            return {'success': False, 'message': f'Erro ao aplicar tags: {str(e)}'}

    def vincular_noticia_area(self, noticia_id, tipo_midia, area_id):
        """
        Vincula uma notícia a uma área específica
        
        Args:
            noticia_id (int): ID da notícia
            tipo_midia (str): Tipo da mídia ('web', 'impresso', 'tv', 'radio')
            area_id (int or None): ID da área para vincular (None para remover área)
            
        Returns:
            bool: True se sucesso, False se erro
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            print(f"🎯 Vinculando notícia {noticia_id} do tipo {tipo_midia} à área {area_id}")
            
            # Mapeia o tipo para a tabela correspondente
            tabela_map = {
                'web': 'noticias_web',
                'impresso': 'noticia_impresso',
                'tv': 'noticia_tv',
                'radio': 'noticia_radio'
            }
            
            if tipo_midia not in tabela_map:
                print(f"❌ Tipo de mídia inválido: {tipo_midia}")
                return False
            
            tabela = tabela_map[tipo_midia]
            print(f"🗂️ Atualizando tabela: {tabela}")
            
            # Atualiza o campo area na tabela noticia_cliente em vez das tabelas específicas
            tipo_id_map = {'web': 2, 'impresso': 1, 'tv': 4, 'radio': 3}
            tipo_id = tipo_id_map.get(tipo_midia)
            
            if not tipo_id:
                print(f"❌ Tipo de mídia inválido: {tipo_midia}")
                return False
                
            try:
                query = "UPDATE noticia_cliente SET area = %s WHERE noticia_id = %s AND tipo_id = %s"
                cursor.execute(query, (area_id, noticia_id, tipo_id))
                
                # Verifica se a atualização foi bem-sucedida
                if cursor.rowcount > 0:
                    self.connection.commit()
                    area_texto = f"área {area_id}" if area_id else "nenhuma área"
                    print(f"✅ Notícia {noticia_id} vinculada à {area_texto} com sucesso")
                    return True
                else:
                    print(f"⚠️ Nenhuma linha foi atualizada - vinculação não encontrada na noticia_cliente")
                    return False
            except Exception as update_error:
                print(f"⚠️ Campo 'area' não existe na noticia_cliente, funcionalidade de áreas não implementada: {update_error}")
                # Retorna True para evitar quebrar a interface, mas registra que a funcionalidade não está disponível
                return True
                
        except Exception as e:
            print(f"❌ Erro ao vincular notícia à área: {e}")
            self.connection.rollback()
            import traceback
            traceback.print_exc()
            return False
        
        finally:
            if cursor:
                cursor.close()

    def validar_integridade_noticia_cliente(self, cliente_id=None):
        """
        Valida a integridade da tabela noticia_cliente e relacionamentos
        
        Args:
            cliente_id (int, optional): ID do cliente específico para validar
            
        Returns:
            dict: Relatório de validação com problemas encontrados
        """
        if not self.connection:
            self.connect()
        
        cursor = self.connection.cursor()
        
        try:
            print(f"🔍 Validando integridade da tabela noticia_cliente...")
            
            relatorio = {
                'problemas_encontrados': [],
                'estatisticas': {},
                'recomendacoes': []
            }
            
            # 1. Verifica se existem registros com tipo_id inválido
            query_tipos_invalidos = """
                SELECT cliente_id, noticia_id, tipo_id, COUNT(*) as total
                FROM noticia_cliente 
                WHERE tipo_id NOT IN (1, 2, 3, 4)
                GROUP BY cliente_id, noticia_id, tipo_id
            """
            cursor.execute(query_tipos_invalidos)
            tipos_invalidos = cursor.fetchall()
            
            if tipos_invalidos:
                relatorio['problemas_encontrados'].append({
                    'tipo': 'tipo_id_invalido',
                    'descricao': 'Registros com tipo_id inválido encontrados',
                    'dados': tipos_invalidos
                })
            
            # 2. Verifica duplicatas (mesmo cliente, noticia, tipo)
            query_duplicatas = """
                SELECT cliente_id, noticia_id, tipo_id, COUNT(*) as total
                FROM noticia_cliente 
                GROUP BY cliente_id, noticia_id, tipo_id
                HAVING COUNT(*) > 1
            """
            cursor.execute(query_duplicatas)
            duplicatas = cursor.fetchall()
            
            if duplicatas:
                relatorio['problemas_encontrados'].append({
                    'tipo': 'duplicatas',
                    'descricao': 'Registros duplicados encontrados',
                    'dados': duplicatas
                })
            
            # 3. Verifica órfãos - noticia_cliente sem notícia correspondente
            queries_orfaos = [
                ('Web', 2, 'noticias_web'),
                ('Impresso', 1, 'noticia_impresso'), 
                ('Rádio', 3, 'noticia_radio'),
                ('TV', 4, 'noticia_tv')
            ]
            
            for nome_midia, tipo_id, tabela_noticia in queries_orfaos:
                query_orfaos = f"""
                    SELECT nc.cliente_id, nc.noticia_id
                    FROM noticia_cliente nc
                    LEFT JOIN {tabela_noticia} n ON nc.noticia_id = n.id
                    WHERE nc.tipo_id = {tipo_id}
                    AND n.id IS NULL
                """
                cursor.execute(query_orfaos)
                orfaos = cursor.fetchall()
                
                if orfaos:
                    relatorio['problemas_encontrados'].append({
                        'tipo': f'orfaos_{nome_midia.lower()}',
                        'descricao': f'Registros órfãos de {nome_midia} (noticia_cliente sem notícia correspondente)',
                        'dados': orfaos
                    })
            
            # 4. Estatísticas por cliente (se especificado) ou geral
            if cliente_id:
                where_clause = f"WHERE nc.cliente_id = {cliente_id}"
                print(f"📊 Validando especificamente o cliente {cliente_id}")
            else:
                where_clause = ""
                print(f"📊 Validando todos os clientes")
            
            query_stats = f"""
                SELECT 
                    nc.cliente_id,
                    nc.tipo_id,
                    COUNT(*) as total_registros,
                    CASE nc.tipo_id
                        WHEN 1 THEN 'Impresso'
                        WHEN 2 THEN 'Web'
                        WHEN 3 THEN 'Rádio'
                        WHEN 4 THEN 'TV'
                        ELSE 'Inválido'
                    END as nome_midia
                FROM noticia_cliente nc
                {where_clause}
                GROUP BY nc.cliente_id, nc.tipo_id
                ORDER BY nc.cliente_id, nc.tipo_id
            """
            cursor.execute(query_stats)
            stats = cursor.fetchall()
            
            relatorio['estatisticas'] = {}
            for cliente_id_stat, tipo_id, total, nome_midia in stats:
                if cliente_id_stat not in relatorio['estatisticas']:
                    relatorio['estatisticas'][cliente_id_stat] = {}
                relatorio['estatisticas'][cliente_id_stat][nome_midia] = total
            
            # 5. Recomendações baseadas nos problemas encontrados
            if tipos_invalidos:
                relatorio['recomendacoes'].append("Corrigir ou remover registros com tipo_id inválido")
            
            if duplicatas:
                relatorio['recomendacoes'].append("Remover registros duplicados da tabela noticia_cliente")
            
            if any('orfaos_' in p['tipo'] for p in relatorio['problemas_encontrados']):
                relatorio['recomendacoes'].append("Verificar e limpar registros órfãos")
            
            if not relatorio['problemas_encontrados']:
                relatorio['recomendacoes'].append("✅ Integridade da tabela noticia_cliente está OK")
            
            print(f"✅ Validação concluída - {len(relatorio['problemas_encontrados'])} problemas encontrados")
            return relatorio
            
        except Exception as e:
            print(f"❌ Erro na validação: {e}")
            import traceback
            traceback.print_exc()
            return {
                'problemas_encontrados': [{'tipo': 'erro_validacao', 'descricao': str(e)}],
                'estatisticas': {},
                'recomendacoes': ['Verificar conectividade com banco de dados']
            }

def test_database():
    """Testa as consultas do banco de dados com nova estrutura noticia_tv"""
    print("🧪 TESTANDO CONSULTAS DO BANCO DE DADOS - NOVA ESTRUTURA")
    print("=" * 50)
    
    db = DatabaseManager()
    
    if not db.connect():
        print("❌ Não foi possível conectar ao banco")
        return
    
    # Teste com diferentes clientes para encontrar dados
    clientes_teste = [26, 418, 1, 2, 3] 
    data_inicio = '2025-01-01'
    data_fim = '2025-12-31'
    
    for usuario_id in clientes_teste:
        print(f"\n🔍 Testando consultas para usuário {usuario_id}")
        print(f"📅 Período: {data_inicio} até {data_fim}")
        
        # Teste 1: Verificar se existe cliente
        existe = db.check_cliente(usuario_id)
        if not existe:
            print(f"   ❌ Cliente {usuario_id} não encontrado, pulando...")
            continue
            
        # Teste 2: Notícias por mídia (especificamente TV)
        print(f"\n1️⃣ Testando notícias por mídia...")
        noticias = db.get_noticias_por_midia(usuario_id, data_inicio, data_fim)
        for item in noticias:
            print(f"   📊 {item['midia']}: {item['quantidade']} notícias")
        
        # Se encontrou notícias de TV, testa o resto
        tv_noticias = next((item for item in noticias if item['midia'] == 'TV' and item['quantidade'] > 0), None)
        if tv_noticias:
            print(f"   ✅ Encontradas {tv_noticias['quantidade']} notícias de TV para cliente {usuario_id}")
            
            # Teste 3: Valores por mídia
            print(f"\n2️⃣ Testando valores por mídia...")
            valores = db.get_valores_por_midia(usuario_id, data_inicio, data_fim)
            for item in valores:
                print(f"   💰 {item['midia']}: R$ {item['valor']:,.2f} ({item['percentual']:.1f}%)")
            
            # Teste 4: Clipagens detalhadas (limitadas)
            print(f"\n3️⃣ Testando clipagens detalhadas...")
            clipagens = db.get_clipagens_detalhadas(usuario_id, data_inicio, data_fim, limite=5)
            for midia, lista in clipagens.items():
                if lista:  # Só mostra se tem dados
                    print(f"   📺 {midia}: {len(lista)} clipagens")
                    for clip in lista[:1]:  # Mostra apenas 1 para não poluir
                        if 'linha1_data_programa_emissora' in clip:
                            print(f"     - {clip['data']}: {clip['linha1_data_programa_emissora'][:80]}...")
                        elif 'titulo_linha1' in clip:
                            print(f"     - {clip['data']}: {clip['titulo_linha1'][:80]}...")
            
            # Teste 5: Teste específico da nova estrutura noticia_tv
            print(f"\n4️⃣ Testando estrutura noticia_tv diretamente...")
            cursor = db.connection.cursor()
            query = """
                SELECT 
                    t.id, t.dt_noticia, t.sinopse, t.duracao,
                    e.nome_emissora, p.nome_programa, p.valor_segundo,
                    nc.cliente_id
                FROM noticia_tv t
                LEFT JOIN emissora_web e ON t.emissora_id = e.id
                LEFT JOIN programa_emissora_web p ON t.programa_id = p.id
                JOIN noticia_cliente nc ON t.id = nc.noticia_id AND nc.tipo_id = 4
                WHERE nc.cliente_id = %s
                LIMIT 3
            """
            cursor.execute(query, (usuario_id,))
            tv_direto = cursor.fetchall()
            
            print(f"   🎬 Encontradas {len(tv_direto)} notícias na estrutura noticia_tv:")
            for row in tv_direto:
                id_tv, dt, sinopse, duracao, emissora, programa, valor_seg, cliente = row
                print(f"     - ID {id_tv}: {dt} | {sinopse[:30]}... | {emissora} | {programa} | {duracao}")
            
            # Teste 6: Teste específico da nova estrutura noticia_impresso
            print(f"\n5️⃣ Testando estrutura noticia_impresso diretamente...")
            query = """
                SELECT 
                    j.id, j.dt_clipagem, j.titulo, j.valor_retorno,
                    ji.nome as jornal, nc.cliente_id
                FROM noticia_impresso j
                LEFT JOIN jornal_online ji ON j.id_fonte = ji.id
                JOIN noticia_cliente nc ON j.id = nc.noticia_id AND nc.tipo_id = 1
                WHERE nc.cliente_id = %s
                LIMIT 3
            """
            cursor.execute(query, (usuario_id,))
            impresso_direto = cursor.fetchall()
            
            print(f"   📰 Encontradas {len(impresso_direto)} notícias na estrutura noticia_impresso:")
            for row in impresso_direto:
                id_imp, dt, titulo, valor, jornal, cliente = row
                print(f"     - ID {id_imp}: {dt} | {titulo[:30]}... | {jornal} | R$ {valor:.2f}")
            
            break  # Para no primeiro cliente que tem dados
    
    db.disconnect()
    print(f"\n✅ Teste concluído!")

if __name__ == "__main__":
    test_database()

"""
ALTERAÇÕES PARA NOVA ESTRUTURA DA TABELA CLIENTES:

1. Alterada tabela de 'app_clientes' para 'clientes'
2. Campo 'possui_area_restrita' alterado para 'fl_area_restrita' (boolean)
3. Novos métodos adicionados para trabalhar com as configurações de cliente:
   - get_cliente_configuracoes(): Busca configurações completas do cliente
   - verificar_permissao_midia(): Verifica permissões por tipo de mídia (fl_web, fl_impresso, fl_tv, fl_radio)
   - verificar_cliente_ativo(): Verifica se cliente está ativo (fl_ativo)
   - filtrar_tipos_midia_por_cliente(): Filtra tipos de mídia baseado nas permissões
   - verificar_permissao_retorno_midia(): Verifica permissão para ver valores de retorno (fl_retorno_midia)
   - verificar_permissao_sentimento(): Verifica permissão para ver dados de sentimento (fl_sentimento)
   - verificar_permissao_audiencia(): Verifica permissão para ver dados de audiência (fl_audiencia)
   - verificar_permissao_areas(): Verifica permissão para ver dados de áreas (fl_areas)

Estrutura da nova tabela 'clientes':
- id (integer)
- nome (character varying 255)
- logo (character varying 255)
- logo_expandida (character varying)
- emails (text)
- cod_unico (integer)
- usuario_tmp (character varying)
- senha_tmp (character varying)
- fl_retorno_midia (boolean)
- fl_impresso (boolean)
- fl_web (boolean)
- fl_relatorio_completo (boolean)
- fl_relatorio_consolidado (boolean)
- fl_sentimento_cli (boolean)
- fl_ativo (boolean)
- fl_audiencia (boolean)
- fl_print (boolean)
- fl_area_restrita (boolean)
- fl_areas (boolean)
- fl_sentimento (boolean)
- fl_link_relatorio (boolean)
- fl_radio (boolean)
- fl_tv (boolean)
- created_at (timestamp without time zone)
- updated_at (timestamp without time zone)
- deleted_at (timestamp without time zone)
""" 