#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from datetime import datetime
from typing import List, Dict

from psycopg2.extras import RealDictCursor

from .base import BaseMonitoramento
from .config import TIPOS_MIDIA, TS_CONFIG, INTERVALO_HORAS


class MonitoramentoRadio(BaseMonitoramento):
    """Monitoramento de notícias de Rádio"""
    
    def __init__(self):
        super().__init__(TIPOS_MIDIA['radio'], 'Rádio')
    
    def pos_processar_noticia(self, noticia: Dict, monitoramento: Dict):
        """Pós-processamento específico para notícias de rádio"""
        with self.conn.cursor() as cur:
            # Busca dados do programa para calcular valor
            cur.execute("""
                SELECT p.valor_segundo, EXTRACT(EPOCH FROM nr.duracao)
                FROM programa_emissora_radio p
                JOIN noticia_radio nr ON nr.programa_id = p.id
                WHERE nr.id = %s
            """, (noticia['id'],))
            
            programa_data = cur.fetchone()
            
            if programa_data and programa_data[0] and programa_data[1]:
                valor_segundo = programa_data[0]
                duracao_segundos = programa_data[1]
                valor_retorno = valor_segundo * duracao_segundos
                
                # Atualiza valor de retorno
                cur.execute("""
                    UPDATE noticia_radio
                    SET valor_retorno = %s
                    WHERE id = %s
                """, (valor_retorno, noticia['id']))
    
    def buscar_noticias(self, monitoramento: Dict, dt_inicial: datetime, dt_final: datetime) -> List[Dict]:
        """Busca notícias de rádio usando full-text search"""
        sql = f"""
            SELECT DISTINCT ON (nr.sinopse, nr.emissora_id, nr.programa_id)
                   nr.id, nr.emissora_id, nr.programa_id, nr.dt_clipagem, 
                   nr.sinopse, nr.duracao, nr.link,
                   e.nome_emissora, p.nome_programa
            FROM noticia_radio nr
            LEFT JOIN emissora_radio e ON e.id = nr.emissora_id
            LEFT JOIN programa_emissora_radio p ON p.id = nr.programa_id
            WHERE nr.created_at >= NOW() - INTERVAL '{INTERVALO_HORAS} hours'
              AND nr.dt_clipagem BETWEEN %s AND %s
              AND nr.deleted_at IS NULL
              AND nr.sinopse_tsv @@ websearch_to_tsquery(%s, %s)
        """
        
        params = [dt_inicial, dt_final, TS_CONFIG, monitoramento['expressao']]
        
        # Aplica filtro_radio (lista de IDs) de forma segura
        ids = self.parse_lista_ids(monitoramento.get('filtro_radio'))
        if ids:
            sql += " AND nr.emissora_id = ANY(%s)"
            params.append(ids)
        
        # Ordena pela data mais recente
        sql += " ORDER BY nr.sinopse, nr.emissora_id, nr.programa_id, nr.dt_clipagem DESC"
        
        with self.conn.cursor(cursor_factory=RealDictCursor) as cur:
            cur.execute(sql, params)
            return cur.fetchall()
    
    def executar(self, grupo: int = None):
        """Executa monitoramentos de rádio"""
        if not self.conectar_db():
            return
        
        try:
            dt_inicial_padrao, dt_final_padrao = self.gerar_datas_padrao()
            data_inicio_exec = datetime.now()
            
            # Busca monitoramentos ativos de rádio
            monitoramentos = self.buscar_monitoramentos_ativos(grupo, 'fl_radio')
            
            if not monitoramentos:
                self.log("Nenhum monitoramento de rádio ativo encontrado")
                return
            
            self.log(f"Processando {len(monitoramentos)} monitoramento(s) de rádio")
            
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


def executar_monitoramento_radio(grupo: int = None):
    """Função principal para execução de monitoramentos de rádio"""
    monitor = MonitoramentoRadio()
    monitor.executar(grupo)


if __name__ == "__main__":
    import argparse
    
    parser = argparse.ArgumentParser(description="Monitoramento de notícias de rádio")
    parser.add_argument("-g", "--grupo", type=int, help="Grupo de execução")
    args = parser.parse_args()
    
    executar_monitoramento_radio(args.grupo)

