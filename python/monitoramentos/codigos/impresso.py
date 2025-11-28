#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from datetime import datetime
from typing import List, Dict

from psycopg2.extras import RealDictCursor

from .base import BaseMonitoramento
from .config import TIPOS_MIDIA, TS_CONFIG, INTERVALO_HORAS


class MonitoramentoImpresso(BaseMonitoramento):
    """Monitoramento de notícias impressas"""
    
    def __init__(self):
        super().__init__(TIPOS_MIDIA['impresso'], 'Impresso')
    
    def pos_processar_noticia(self, noticia: Dict, monitoramento: Dict):
        """Pós-processamento específico para notícias impressas"""
        with self.conn.cursor() as cur:
            # Busca dados do jornal para calcular valor
            cur.execute("""
                SELECT j.nu_valor, ni.nu_colunas, ni.nu_largura, ni.nu_altura
                FROM jornal_online j
                JOIN noticia_impresso ni ON ni.id_fonte = j.id
                WHERE ni.id = %s
            """, (noticia['id'],))
            
            jornal_data = cur.fetchone()
            
            if jornal_data and jornal_data[0]:
                valor_coluna = jornal_data[0]
                colunas = jornal_data[1] or 1
                largura = jornal_data[2] or 1
                altura = jornal_data[3] or 1
                
                # Calcula valor baseado em colunas
                cm_coluna = altura * colunas
                valor_retorno = valor_coluna * cm_coluna
                
                # Atualiza valor de retorno
                cur.execute("""
                    UPDATE noticia_impresso
                    SET valor_retorno = %s
                    WHERE id = %s
                """, (valor_retorno, noticia['id']))
    
    def buscar_noticias(self, monitoramento: Dict, dt_inicial: datetime, dt_final: datetime) -> List[Dict]:
        """Busca notícias impressas usando full-text search"""
        sql = f"""
            SELECT DISTINCT ON (ni.titulo, ni.id_fonte, ni.id_sessao_impresso)
                   ni.id, ni.id_fonte, ni.dt_clipagem, ni.titulo, 
                   ni.sinopse, ni.nu_colunas, ni.nu_largura, ni.nu_altura,
                   j.nome AS jornal_nome, s.ds_sessao
            FROM noticia_impresso ni
            LEFT JOIN jornal_online j ON j.id = ni.id_fonte
            LEFT JOIN sessao_impresso s ON s.id_sessao_impresso = ni.id_sessao_impresso
            WHERE ni.created_at >= NOW() - INTERVAL '{INTERVALO_HORAS} hours'
              AND ni.dt_clipagem BETWEEN %s AND %s
              AND ni.deleted_at IS NULL
              AND ni.sinopse_tsv @@ websearch_to_tsquery(%s, %s)
        """
        
        params = [dt_inicial, dt_final, TS_CONFIG, monitoramento['expressao']]
        
        # Aplica filtro_impresso (lista de IDs) de forma segura
        ids = self.parse_lista_ids(monitoramento.get('filtro_impresso'))
        if ids:
            sql += " AND ni.id_fonte = ANY(%s)"
            params.append(ids)
        
        # Ordena pela data mais recente
        sql += " ORDER BY ni.titulo, ni.id_fonte, ni.id_sessao_impresso, ni.dt_clipagem DESC"
        
        with self.conn.cursor(cursor_factory=RealDictCursor) as cur:
            cur.execute(sql, params)
            return cur.fetchall()
    
    def executar(self, grupo: int = None):
        """Executa monitoramentos de impressos"""
        if not self.conectar_db():
            return
        
        try:
            dt_inicial_padrao, dt_final_padrao = self.gerar_datas_padrao()
            data_inicio_exec = datetime.now()
            
            # Busca monitoramentos ativos de impressos
            monitoramentos = self.buscar_monitoramentos_ativos(grupo, 'fl_impresso')
            
            if not monitoramentos:
                self.log("Nenhum monitoramento de impressos ativo encontrado")
                return
            
            self.log(f"Processando {len(monitoramentos)} monitoramento(s) de impressos")
            
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


def executar_monitoramento_impresso(grupo: int = None):
    """Função principal para execução de monitoramentos de impressos"""
    monitor = MonitoramentoImpresso()
    monitor.executar(grupo)


if __name__ == "__main__":
    import argparse
    
    parser = argparse.ArgumentParser(description="Monitoramento de notícias impressas")
    parser.add_argument("-g", "--grupo", type=int, help="Grupo de execução")
    args = parser.parse_args()
    
    executar_monitoramento_impresso(args.grupo)

