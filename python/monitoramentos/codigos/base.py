#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import smtplib
from datetime import datetime, date, time
from email.mime.text import MIMEText
from typing import List, Dict, Any, Optional

import psycopg2
from psycopg2.extras import RealDictCursor

from .config import DB_CONFIG, SMTP_HOST, SMTP_FROM, SMTP_TO_FALLBACK, TS_CONFIG


class BaseMonitoramento:
    """Classe base para todos os tipos de monitoramento"""
    
    def __init__(self, tipo_midia: int, nome_midia: str):
        self.tipo_midia = tipo_midia
        self.nome_midia = nome_midia
        self.conn = None
    
    def conectar_db(self):
        """Conecta ao banco de dados"""
        try:
            self.conn = psycopg2.connect(**DB_CONFIG)
            return True
        except Exception as e:
            print(f"[ERRO] Falha na conexão: {e}")
            return False
    
    def desconectar_db(self):
        """Desconecta do banco de dados"""
        if self.conn:
            try:
                self.conn.close()
            except Exception:
                pass
    
    def log(self, mensagem: str):
        """Log com timestamp"""
        timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        print(f"[{timestamp}] [{self.nome_midia}] {mensagem}")
    
    def enviar_email(self, titulo: str, corpo_html: str, destinatario: str = None):
        """Envia email de notificação"""
        destinatario = destinatario or SMTP_TO_FALLBACK
        msg = MIMEText(corpo_html, 'html', 'utf-8')
        msg['Subject'] = titulo
        msg['From'] = SMTP_FROM
        msg['To'] = destinatario
        
        try:
            with smtplib.SMTP(SMTP_HOST) as server:
                server.send_message(msg)
        except Exception as e:
            self.log(f'[WARN] Falha ao enviar e-mail: {e}')
    
    def parse_lista_ids(self, texto: str) -> List[int]:
        """Parse de string de IDs separados por vírgula"""
        if not texto:
            return []
        ids = []
        for x in str(texto).split(','):
            x = x.strip()
            if x.isdigit():
                ids.append(int(x))
        return ids
    
    def buscar_monitoramentos_ativos(self, grupo: int = None, campo_flag: str = None) -> List[Dict]:
        """Busca monitoramentos ativos"""
        if not self.conn:
            return []
        
        with self.conn.cursor(cursor_factory=RealDictCursor) as cur:
            sql = """
                SELECT *
                FROM monitoramento
                WHERE fl_ativo = TRUE
            """
            params = []
            
            if campo_flag:
                sql += f" AND {campo_flag} = TRUE"
            
            if grupo is not None:
                sql += " AND grupo_execucao = %s"
                params.append(grupo)
            
            cur.execute(sql, params)
            return cur.fetchall()
    
    def verificar_duplicata_generica(self, cliente_id: int, noticia_id: int) -> bool:
        """Verifica se já existe vínculo da notícia com o cliente"""
        with self.conn.cursor() as cur:
            cur.execute("""
                SELECT 1
                FROM noticia_cliente
                WHERE cliente_id = %s 
                AND noticia_id = %s
                AND tipo_id = %s
                AND deleted_at IS NULL
                LIMIT 1
            """, (cliente_id, noticia_id, self.tipo_midia))
            
            return cur.fetchone() is not None
    
    def associar_noticia_cliente(self, dados: List[Dict], monitoramento: Dict) -> int:
        """Associa notícias encontradas ao cliente"""
        total_vinculado = 0
        
        with self.conn.cursor() as cur:
            for noticia in dados:
                # Verifica duplicata
                if self.verificar_duplicata_generica(monitoramento['id_cliente'], noticia['id']):
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
                    
                    # Chama método específico de pós-processamento
                    self.pos_processar_noticia(noticia, monitoramento)
        
        self.conn.commit()
        return total_vinculado
    
    def pos_processar_noticia(self, noticia: Dict, monitoramento: Dict):
        """Método para pós-processamento específico de cada tipo de mídia"""
        pass  # Implementado nas classes filhas
    
    def registrar_execucao(self, monitoramento_id: int, total_vinculado: int, data_inicio: datetime, data_termino: datetime):
        """Registra a execução do monitoramento"""
        with self.conn.cursor() as cur:
            cur.execute("""
                INSERT INTO monitoramento_execucao (monitoramento_id, total_vinculado, created_at, updated_at)
                VALUES (%s, %s, %s, %s)
            """, (monitoramento_id, total_vinculado, data_inicio, data_termino))
            
            cur.execute("""
                UPDATE monitoramento
                SET updated_at = %s
                WHERE id = %s
            """, (data_termino, monitoramento_id))
        
        self.conn.commit()
    
    def executar(self, grupo: int = None):
        """Método principal de execução - implementado nas classes filhas"""
        raise NotImplementedError("Método deve ser implementado nas classes filhas")
    
    def gerar_datas_padrao(self):
        """Gera datas padrão para busca"""
        hoje = date.today()
        dt_inicial_padrao = datetime.combine(hoje, time(0, 0, 0))
        dt_final_padrao = datetime.combine(hoje, time(23, 59, 59))
        return dt_inicial_padrao, dt_final_padrao
    
    def notificar_erro(self, monitoramento: Dict, erro: Exception):
        """Notifica erro por email"""
        titulo = f"Notificação de Monitoramento {self.nome_midia} - Erro de Consulta - {datetime.now():%d/%m/%Y %H:%M:%S}"
        corpo = f"""
            <p><strong>Erro ao executar monitoramento {self.nome_midia}</strong></p>
            <p>Cliente: {monitoramento.get('cliente', 'Cliente não informado')}</p>
            <p>Expressão: {monitoramento.get('expressao')}</p>
            <p>ID Monitoramento: {monitoramento.get('id')}</p>
            <p>Detalhes: {str(erro)}</p>
        """
        self.enviar_email(titulo, corpo)

