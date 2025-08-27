#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import argparse
from datetime import datetime, date, time
import smtplib
from email.mime.text import MIMEText

import psycopg2
from psycopg2.extras import RealDictCursor
from config import DB_CONFIG

# =========================
# CONFIGURAÇÕES
# =========================

DB_CONFIG = {
    'dbname': DB_CONFIG['database'],
    'user': DB_CONFIG['username'],
    'password': DB_CONFIG['password'],
    'host': DB_CONFIG['host'],
    'port': DB_CONFIG['port']
}

SMTP_HOST = 'localhost'
SMTP_FROM = 'boletins@clipagens.com.br'
SMTP_TO_FALLBACK = 'robsonferduda@gmail.com'

# Troque para 'portuguese' se seu tsvector/índice usa esse dicionário
TS_CONFIG = 'simple'  # ou 'portuguese'


# =========================
# UTILITÁRIOS
# =========================

def enviar_email(titulo: str, corpo_html: str, destinatario: str):
    msg = MIMEText(corpo_html, 'html', 'utf-8')
    msg['Subject'] = titulo
    msg['From'] = SMTP_FROM
    msg['To'] = destinatario
    try:
        with smtplib.SMTP(SMTP_HOST) as server:
            server.send_message(msg)
    except Exception as e:
        # Não derruba o processo por falha no e-mail; apenas loga
        print(f'[WARN] Falha ao enviar e-mail: {e}')


def parse_lista_ids(texto):
    if not texto:
        return []
    ids = []
    for x in str(texto).split(','):
        x = x.strip()
        if x.isdigit():
            ids.append(int(x))
    return ids


# =========================
# DOMÍNIO
# =========================

def _cols_em_comum(conn, src_table, dst_table):
    sql = """
        SELECT a.column_name
          FROM information_schema.columns a
          JOIN information_schema.columns b
            ON b.table_schema = a.table_schema
           AND b.column_name  = a.column_name
         WHERE a.table_name = %s
           AND b.table_name = %s
           AND a.table_schema = 'public'
           AND b.table_schema = 'public'
         ORDER BY a.ordinal_position
    """
    with conn.cursor() as c:
        c.execute(sql, (src_table, dst_table))
        return [r[0] for r in c.fetchall()]

def copy_row(conn, src_table, dst_table, pk_field, pk_value, exclude=None, extra_set=None):
    """
    Copia 1 linha src->dst para colunas em comum, podendo excluir algumas colunas.
    extra_set = dict de {col_destino: valor_constante} para preencher colunas destino.
    """
    exclude = set(exclude or [])
    cols = _cols_em_comum(conn, src_table, dst_table)
    cols = [c for c in cols if c not in exclude]

    # Monta colunas do destino: comuns + extras
    dst_cols = cols[:]
    values_sql_parts = [f'"{c}"' for c in cols]  # SELECT "col" da src

    extra_set = extra_set or {}
    for k in extra_set.keys():
        dst_cols.append(k)
        values_sql_parts.append('%s')  # valor constante via param

    if pk_field not in dst_cols:
        # Não precisa estar em dst_cols, mas é bom checar que a PK existe em ambas as tabelas
        pass

    dst_cols_csv = ','.join(f'"{c}"' for c in dst_cols)
    values_sql = ','.join(values_sql_parts)

    sql = f"""
        INSERT INTO "{dst_table}" ({dst_cols_csv})
        SELECT {values_sql}
          FROM "{src_table}"
         WHERE "{pk_field}" = %s
        ON CONFLICT ("{pk_field}") DO NOTHING
    """
    params = list(extra_set.values()) + [pk_value]
    with conn.cursor() as c:
        c.execute(sql, params)

def copy_rows_by_fk(conn, src_table, dst_table, fk_field, fk_value, exclude=None, extra_set=None):
    """
    Copia N linhas src->dst filtrando por fk_field, com exclusões e valores extras.
    """
    exclude = set(exclude or [])
    cols = _cols_em_comum(conn, src_table, dst_table)
    cols = [c for c in cols if c not in exclude]

    dst_cols = cols[:]
    select_cols = [f'"{c}"' for c in cols]

    extra_set = extra_set or {}
    for k in extra_set.keys():
        dst_cols.append(k)
        select_cols.append('%s')

    dst_cols_csv = ','.join(f'"{c}"' for c in dst_cols)
    select_sql = ','.join(select_cols)

    sql = f"""
        INSERT INTO "{dst_table}" ({dst_cols_csv})
        SELECT {select_sql}
          FROM "{src_table}"
         WHERE "{fk_field}" = %s
        ON CONFLICT DO NOTHING
    """
    params = list(extra_set.values()) + [fk_value]
    with conn.cursor() as c:
        c.execute(sql, params)

def verificar_duplicata_web_cliente(conn, cliente_id, url_noticia, titulo_noticia, id_fonte):
    """
    Verifica se já existe notícia web vinculada ao cliente por URL ou título+fonte
    """
    with conn.cursor() as cur:
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

def associar(conn, dados, tipo, monitoramento):
    total_vinculado = 0
    with conn.cursor(cursor_factory=RealDictCursor) as cur:
        for noticia in dados:
            # Para Web, verifica duplicatas por URL ou título+fonte
            if tipo == 2:  # Web
                if verificar_duplicata_web_cliente(conn, monitoramento['id_cliente'], 
                                                 noticia.get('url_noticia'), 
                                                 noticia.get('titulo_noticia'), 
                                                 noticia.get('id_fonte')):
                    continue  # Pula esta notícia pois já existe uma similar para o cliente
            
            # Já existe vínculo?
            cur.execute("""
                SELECT 1
                  FROM noticia_cliente
                 WHERE noticia_id = %s AND tipo_id = %s
                   AND cliente_id = %s AND monitoramento_id = %s
                 LIMIT 1
            """, (noticia['id'], tipo, monitoramento['id_cliente'], monitoramento['id']))
            noticia_cliente = cur.fetchone()

            if not noticia_cliente:
                # Cria vínculo
                cur.execute("""
                    INSERT INTO noticia_cliente (cliente_id, tipo_id, noticia_id, sentimento, monitoramento_id)
                    VALUES (%s, %s, %s, %s, %s)
                """, (monitoramento['id_cliente'], tipo, noticia['id'], 1, monitoramento['id']))
                total_vinculado += 1

                # >>> NOVO: copiar registros para as tabelas de desempenho <<<
                # Copia a notícia principal
                copy_row(
                    conn,
                    src_table='noticias_web',
                    dst_table='noticia_web',
                    pk_field='id',
                    pk_value=noticia['id'],
                    exclude=['documento']  # <— ignora a coluna
                    # extra_set={'documento': None}  # use isto se a coluna for NOT NULL sem default (ajuste o valor)
                )

                # Copia os conteúdos
                copy_rows_by_fk(
                    conn,
                    src_table='conteudo_noticia_web',
                    dst_table='conteudo_noticia_web_clientes',
                    fk_field='id_noticia_web',
                    fk_value=noticia['id'],
                    exclude=['documento']  # <— ignora a coluna
                    # extra_set={'documento': None}
                )


                # Ajustes específicos para Web
                if tipo == 2:
                    cur.execute("SELECT nu_valor FROM fonte_web WHERE id = %s", (noticia['id_fonte'],))
                    fonte = cur.fetchone()
                    valor_retorno = fonte['nu_valor'] if fonte and fonte.get('nu_valor') is not None else 0
                    cur.execute("""
                        UPDATE noticias_web
                           SET screenshot = TRUE, nu_valor = %s, fl_boletim = TRUE
                         WHERE id = %s
                    """, (valor_retorno, noticia['id']))
    conn.commit()
    return total_vinculado


def executar_web(grupo: int):
    """
    Executa monitoramentos web ativos do grupo informado:
    - Busca notícias das últimas 24h
    - Aplica expressão full-text
    - Filtra por fonte se houver filtro_web
    - Vincula e registra execução
    """
    conn = psycopg2.connect(**DB_CONFIG)
    cur = conn.cursor(cursor_factory=RealDictCursor)

    hoje = date.today()
    dt_inicial_padrao = datetime.combine(hoje, time(0, 0, 0))
    dt_final_padrao   = datetime.combine(hoje, time(23, 59, 59))
    data_inicio_exec  = datetime.now()
    tipo_midia = 2  # Web

    # Monitoramentos ativos do grupo
    cur.execute("""
        SELECT *
          FROM monitoramento
         WHERE fl_ativo = TRUE
           AND fl_web   = TRUE
           AND grupo_execucao = %s
    """, (grupo,))
    monitoramentos = cur.fetchall()

    for mon in monitoramentos:
        try:
            dt_inicial = mon['dt_inicio'] or dt_inicial_padrao
            dt_final   = dt_final_padrao

            # Base da consulta
            sql = f"""
                SELECT DISTINCT ON (n.titulo_noticia, n.url_noticia, n.id_fonte)
                       n.id,
                       n.id_fonte,
                       n.url_noticia,
                       n.data_insert,
                       n.data_noticia,
                       n.titulo_noticia,
                       fw.nome
                  FROM noticias_web n
            INNER JOIN conteudo_noticia_web cnw
                    ON cnw.id_noticia_web = n.id
            INNER JOIN fonte_web fw
                    ON fw.id = n.id_fonte
                 WHERE n.created_at >= NOW() - INTERVAL '24 hours'
                   AND n.data_noticia BETWEEN %s AND %s
                   AND cnw.conteudo_tsv @@ websearch_to_tsquery(%s, %s)
            """

            params = [dt_inicial, dt_final, TS_CONFIG, mon['expressao']]

            # Aplica filtro_web (lista de IDs) de forma segura
            ids = parse_lista_ids(mon.get('filtro_web'))
            if ids:
                sql += " AND fw.id = ANY(%s)"
                params.append(ids)

            # Linha vencedora para o DISTINCT ON
            sql += " ORDER BY n.titulo_noticia, n.url_noticia, n.id_fonte, n.data_noticia DESC"

            cur.execute(sql, params)
            dados = cur.fetchall()

            total_vinculado = associar(conn, dados, tipo_midia, mon)
            data_termino = datetime.now()

            # Registro de execução e atualização do monitoramento
            with conn.cursor() as cur2:
                cur2.execute("""
                    INSERT INTO monitoramento_execucao (monitoramento_id, total_vinculado, created_at, updated_at)
                    VALUES (%s, %s, %s, %s)
                """, (mon['id'], total_vinculado, data_inicio_exec, data_termino))

                cur2.execute("""
                    UPDATE monitoramento
                       SET updated_at = %s
                     WHERE id = %s
                """, (data_termino, mon['id']))

            conn.commit()
            print(f"[OK] Monitoramento {mon['id']} | Total vinculado: {total_vinculado}")

        except Exception as e:
            titulo = f"Notificação de Monitoramento - Erro de Consulta - {datetime.now():%d/%m/%Y %H:%M:%S}"
            corpo = f"""
                <p><strong>Erro ao executar monitoramento</strong></p>
                <p>Cliente: {mon.get('cliente', 'Cliente não informado')}</p>
                <p>Expressão: {mon.get('expressao')}</p>
                <p>ID Monitoramento: {mon.get('id')}</p>
                <p>Detalhes: {str(e)}</p>
            """
            enviar_email(titulo, corpo, SMTP_TO_FALLBACK)
            print(f"[ERRO] Monitoramento {mon.get('id')} - {e}")

    cur.close()
    conn.close()


# =========================
# CLI
# =========================

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Executor de monitoramento web")
    parser.add_argument("-g", "--grupo", type=int, required=True, help="ID do grupo de execução")
    args = parser.parse_args()
    executar_web(grupo=args.grupo)
