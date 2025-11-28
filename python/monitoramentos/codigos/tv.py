#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from datetime import datetime
from typing import List, Dict

from psycopg2.extras import RealDictCursor

from .base import BaseMonitoramento
from .config import TIPOS_MIDIA, TS_CONFIG, INTERVALO_HORAS


class MonitoramentoTV(BaseMonitoramento):
    """Monitoramento de notícias de TV"""
    
    def __init__(self):
        super().__init__(TIPOS_MIDIA['tv'], 'TV')
    
    def pos_processar_noticia(self, noticia: Dict, monitoramento: Dict):
        """Pós-processamento específico para notícias de TV"""
        with self.conn.cursor() as cur:
            # Busca dados do programa para calcular valor
            cur.execute("""
                SELECT p.nu_valor, EXTRACT(EPOCH FROM nt.duracao)
                FROM programa_emissora_web p
                JOIN noticia_tv nt ON nt.programa_id = p.id
                WHERE nt.id = %s
            """, (noticia['id'],))
            
            programa_data = cur.fetchone()
            
            if programa_data and programa_data[0] and programa_data[1]:
                valor_segundo = programa_data[0]
                duracao_segundos = programa_data[1]
                valor_retorno = valor_segundo * duracao_segundos
                
                # Atualiza valor de retorno
                cur.execute("""
                    UPDATE noticia_tv
                    SET valor_retorno = %s
                    WHERE id = %s
                """, (valor_retorno, noticia['id']))
    
    def buscar_noticias(self, monitoramento: Dict, dt_inicial: datetime, dt_final: datetime) -> List[Dict]:
        """Busca notícias de TV usando full-text search"""
        sql = f"""
            SELECT DISTINCT ON (nt.sinopse, nt.emissora_id, nt.programa_id)
                   nt.id, nt.emissora_id, nt.programa_id, nt.dt_noticia, 
                   nt.sinopse, nt.duracao, nt.link,
                   e.nome_emissora, p.nome_programa
            FROM noticia_tv nt
            LEFT JOIN emissora_web e ON e.id = nt.emissora_id
            LEFT JOIN programa_emissora_web p ON p.id = nt.programa_id
            WHERE nt.created_at >= NOW() - INTERVAL '{INTERVALO_HORAS} hours'
              AND nt.dt_noticia BETWEEN %s AND %s
              AND nt.deleted_at IS NULL
              AND nt.sinopse_tsv @@ websearch_to_tsquery(%s, %s)
        """
        
        params = [dt_inicial, dt_final, TS_CONFIG, monitoramento['expressao']]
        
        # Aplica filtro_tv (lista de IDs) de forma segura
        ids = self.parse_lista_ids(monitoramento.get('filtro_tv'))
        if ids:
            sql += " AND nt.emissora_id = ANY(%s)"
            params.append(ids)
        
        # Ordena pela data mais recente
        sql += " ORDER BY nt.sinopse, nt.emissora_id, nt.programa_id, nt.dt_noticia DESC"
        
        with self.conn.cursor(cursor_factory=RealDictCursor) as cur:
            cur.execute(sql, params)
            return cur.fetchall()
    
    def executar(self, grupo: int = None):
        """Executa monitoramentos de TV"""
        if not self.conectar_db():
            return
        
        try:
            dt_inicial_padrao, dt_final_padrao = self.gerar_datas_padrao()
            data_inicio_exec = datetime.now()
            
            # Busca monitoramentos ativos de TV
            monitoramentos = self.buscar_monitoramentos_ativos(grupo, 'fl_tv')
            
            if not monitoramentos:
                self.log("Nenhum monitoramento de TV ativo encontrado")
                return
            
            self.log(f"Processando {len(monitoramentos)} monitoramento(s) de TV")
            
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


def executar_monitoramento_tv(grupo: int = None):
    """Função principal para execução de monitoramentos de TV"""
    monitor = MonitoramentoTV()
    monitor.executar(grupo)


if __name__ == "__main__":
    import argparse
    
    parser = argparse.ArgumentParser(description="Monitoramento de notícias de TV")
    parser.add_argument("-g", "--grupo", type=int, help="Grupo de execução")
    args = parser.parse_args()
    
    executar_monitoramento_tv(args.grupo)

