#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from datetime import datetime
from typing import List, Dict

from psycopg2.extras import RealDictCursor

from .base import BaseMonitoramento
from .config import TIPOS_MIDIA, TS_CONFIG, INTERVALO_HORAS


class MonitoramentoWeb(BaseMonitoramento):
    """Monitoramento de notícias web"""
    
    def __init__(self):
        super().__init__(TIPOS_MIDIA['web'], 'Web')
    
    def verificar_duplicata_web_cliente(self, cliente_id: int, url_noticia: str, titulo_noticia: str, id_fonte: int) -> bool:
        """Verifica se já existe notícia web vinculada ao cliente por URL ou título+fonte"""
        with self.conn.cursor() as cur:
            # Verifica por URL
            if url_noticia:
                cur.execute("""
                    SELECT 1 
                    FROM noticia_cliente nc
                    JOIN noticias_web nw ON nw.id = nc.noticia_id
                    WHERE nc.cliente_id = %s 
                    AND nc.tipo_id = 2
                    AND nw.url_noticia = %s
                    AND nc.deleted_at IS NULL
                    LIMIT 1
                """, (cliente_id, url_noticia))
                
                if cur.fetchone():
                    return True
                    
            # Verifica por título + fonte
            if titulo_noticia and id_fonte:
                cur.execute("""
                    SELECT 1 
                    FROM noticia_cliente nc
                    JOIN noticias_web nw ON nw.id = nc.noticia_id
                    WHERE nc.cliente_id = %s 
                    AND nc.tipo_id = 2
                    AND nw.titulo_noticia = %s
                    AND nw.id_fonte = %s
                    AND nc.deleted_at IS NULL
                    LIMIT 1
                """, (cliente_id, titulo_noticia, id_fonte))
                
                if cur.fetchone():
                    return True
                    
            return False
    
    def associar_noticia_cliente(self, dados: List[Dict], monitoramento: Dict) -> int:
        """Associa notícias encontradas ao cliente - sobrescreve para incluir verificação específica de web"""
        total_vinculado = 0
        
        with self.conn.cursor() as cur:
            for noticia in dados:
                # Para Web, verifica duplicatas por URL ou título+fonte
                if self.verificar_duplicata_web_cliente(
                    monitoramento['id_cliente'],
                    noticia.get('url_noticia'),
                    noticia.get('titulo_noticia'),
                    noticia.get('id_fonte')
                ):
                    continue
                
                # Verifica se já existe vínculo específico
                cur.execute("""
                    SELECT 1
                    FROM noticia_cliente
                    WHERE noticia_id = %s AND tipo_id = %s
                    AND cliente_id = %s AND monitoramento_id = %s
                    LIMIT 1
                """, (noticia['id'], self.tipo_midia, monitoramento['id_cliente'], monitoramento['id']))
                
                if not cur.fetchone():
                    # Cria vínculo
                    cur.execute("""
                        INSERT INTO noticia_cliente (cliente_id, tipo_id, noticia_id, sentimento, monitoramento_id)
                        VALUES (%s, %s, %s, %s, %s)
                    """, (monitoramento['id_cliente'], self.tipo_midia, noticia['id'], 1, monitoramento['id']))
                    
                    total_vinculado += 1
                    
                    # Pós-processamento específico para Web
                    self.pos_processar_noticia(noticia, monitoramento)
        
        self.conn.commit()
        return total_vinculado
    
    def pos_processar_noticia(self, noticia: Dict, monitoramento: Dict):
        """Pós-processamento específico para notícias web"""
        with self.conn.cursor() as cur:
            # Busca valor da fonte
            cur.execute("SELECT nu_valor FROM fonte_web WHERE id = %s", (noticia['id_fonte'],))
            fonte = cur.fetchone()
            valor_retorno = fonte[0] if fonte and fonte[0] is not None else 0
            
            # Atualiza notícia web
            cur.execute("""
                UPDATE noticias_web
                SET screenshot = TRUE, nu_valor = %s, fl_boletim = TRUE
                WHERE id = %s
            """, (valor_retorno, noticia['id']))
    
    def buscar_noticias(self, monitoramento: Dict, dt_inicial: datetime, dt_final: datetime) -> List[Dict]:
        """Busca notícias web usando full-text search com CTEs"""
        sql = f"""
            WITH noticias_fts AS (
                -- Busca FTS primeiro (mais seletivo)
                SELECT DISTINCT id_noticia_web
                FROM conteudo_noticia_web
                WHERE conteudo_tsv @@ websearch_to_tsquery(%s, %s)
                  AND created_at >= NOW() - INTERVAL '{INTERVALO_HORAS} hours'
            ),
            noticias_filtradas AS (
                -- Aplica filtros de data na tabela principal
                SELECT n.id, n.id_fonte, n.url_noticia, 
                       n.data_insert, n.data_noticia, n.titulo_noticia
                FROM noticias_web n
                INNER JOIN noticias_fts nf ON nf.id_noticia_web = n.id
                WHERE n.data_noticia BETWEEN %s AND %s
                  AND n.created_at >= NOW() - INTERVAL '{INTERVALO_HORAS} hours'
            )
            -- SELECT final com DISTINCT ON
            SELECT DISTINCT ON (nf.titulo_noticia, nf.url_noticia, nf.id_fonte)
                   nf.id, nf.id_fonte, nf.url_noticia,
                   nf.data_insert, nf.data_noticia, nf.titulo_noticia,
                   fw.nome
            FROM noticias_filtradas nf
            INNER JOIN fonte_web fw ON fw.id = nf.id_fonte
        """
        
        params = [TS_CONFIG, monitoramento['expressao'], dt_inicial, dt_final]
        
        # Aplica filtro_web (lista de IDs) de forma segura
        ids = self.parse_lista_ids(monitoramento.get('filtro_web'))
        if ids:
            sql += " AND fw.id = ANY(%s)"
            params.append(ids)
        
        # Linha vencedora para o DISTINCT ON
        sql += " ORDER BY nf.titulo_noticia, nf.url_noticia, nf.id_fonte, nf.data_noticia DESC"
        
        with self.conn.cursor(cursor_factory=RealDictCursor) as cur:
            cur.execute(sql, params)
            return cur.fetchall()
    
    def executar(self, grupo: int = None):
        """Executa monitoramentos web"""
        if not self.conectar_db():
            return
        
        try:
            dt_inicial_padrao, dt_final_padrao = self.gerar_datas_padrao()
            data_inicio_exec = datetime.now()
            
            # Busca monitoramentos ativos de web
            monitoramentos = self.buscar_monitoramentos_ativos(grupo, 'fl_web')
            
            if not monitoramentos:
                self.log("Nenhum monitoramento web ativo encontrado")
                return
            
            self.log(f"Processando {len(monitoramentos)} monitoramento(s) web")
            
            for mon in monitoramentos:
                try:
                    dt_inicial = mon['dt_inicio'] or dt_inicial_padrao
                    dt_final = dt_final_padrao
                    
                    # Busca notícias
                    dados = self.buscar_noticias(mon, dt_inicial, dt_final)
                    
                    # Associa notícias ao cliente
                    total_vinculado = self.associar_noticia_cliente(dados, mon)
                    
                    # Registra execução
                    data_termino = datetime.now()
                    self.registrar_execucao(mon['id'], total_vinculado, data_inicio_exec, data_termino)
                    
                    self.log(f"Monitoramento {mon['id']} | Total vinculado: {total_vinculado}")
                
                except Exception as e:
                    self.log(f"[ERRO] Monitoramento {mon.get('id')} - {e}")
                    self.notificar_erro(mon, e)
        
        finally:
            self.desconectar_db()


def executar_monitoramento_web(grupo: int = None):
    """Função principal para execução de monitoramentos web"""
    monitor = MonitoramentoWeb()
    monitor.executar(grupo)


if __name__ == "__main__":
    import argparse
    
    parser = argparse.ArgumentParser(description="Monitoramento de notícias web")
    parser.add_argument("-g", "--grupo", type=int, help="Grupo de execução")
    args = parser.parse_args()
    
    executar_monitoramento_web(args.grupo)

