#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import os
import time
from typing import List, Dict, Optional
from datetime import datetime, timezone

import requests
import psycopg2
from psycopg2.extras import RealDictCursor, execute_values
from config import DB_CONFIG

# =========================
# CONFIG
# =========================
ACCESS_TOKEN = '565829271321837|xGQc9z5vEJP4qSxsJ7iWJrrgnY8'        # coloque seu token aqui ou via env
GRAPH_VER = 'v19.0'
HTTP_TIMEOUT = 20
MAX_RETRIES = 4
BACKOFF_BASE = 2
RATE_LIMIT_SLEEP = 15 * 60   # 15 minutos quando bater (#4)
PAGE_SLEEP_SECONDS = 2       # respiro entre páginas
PAGE_FETCH_LIMIT = 10       # limite lógico por página (pode paginar além)
TS_CONFIG = 'portuguese'     # 'portuguese' ou 'simple'

DB_CONFIG = {
    'dbname': DB_CONFIG['database'],
    'user': DB_CONFIG['username'],
    'password': DB_CONFIG['password'],
    'host': DB_CONFIG['host'],
    'port': DB_CONFIG['port']
}

# =========================
# LOG
# =========================
def log(msg: str):
    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] {msg}")

# =========================
# DB helpers
# =========================
def get_conn():
    return psycopg2.connect(**DB_CONFIG)

def criar_indices_recomendados(conn):
    sqls = [
        "ALTER TABLE IF EXISTS post_facebook ADD CONSTRAINT IF NOT EXISTS post_facebook_post_id_key UNIQUE (post_id);",
        "CREATE INDEX IF NOT EXISTS idx_facebook_tsv_mensagem ON public.post_facebook USING gin (tsv_mensagem);",
        "CREATE INDEX IF NOT EXISTS post_fb_data_idx ON public.post_facebook (data_postagem);",
    ]
    with conn.cursor() as cur:
        for s in sqls:
            cur.execute(s)
    conn.commit()

# =========================
# Rate-limit exception
# =========================
class RateLimitError(Exception):
    pass

# =========================
# HTTP / Graph
# =========================
def request_with_retry(url: str, params: dict) -> dict:
    last_err = None
    for attempt in range(1, MAX_RETRIES + 1):
        try:
            r = requests.get(url, params=params, timeout=HTTP_TIMEOUT)
            if r.status_code == 200:
                return r.json()

            # Erro de limite (#4)
            try:
                j = r.json()
            except Exception:
                j = {}
            err_code = j.get('error', {}).get('code')
            if r.status_code == 403 and err_code == 4:
                # Limite atingido — sinalizamos para o caller aplicar cooldown
                raise RateLimitError(f"Graph limit (#4): {j}")

            # Erros não-transientes
            if r.status_code in (400,401,403,404):
                log(f"❌ Graph {r.status_code}: {r.text[:200]}")
                r.raise_for_status()

            last_err = Exception(f"HTTP {r.status_code}: {r.text[:200]}")
        except RateLimitError:
            # Propaga pro caller
            raise
        except requests.RequestException as e:
            last_err = e

        sleep_s = BACKOFF_BASE ** (attempt - 1)
        log(f"⚠ Falha (tentativa {attempt}/{MAX_RETRIES}). Retentando em {sleep_s}s...")
        time.sleep(sleep_s)
    raise last_err

# =========================
# Normalização
# =========================
def parse_dt_iso_to_utc(dt_str: Optional[str]) -> Optional[datetime]:
    if not dt_str:
        return None
    try:
        dt = datetime.fromisoformat(dt_str.replace('Z', '+00:00'))
        return dt.astimezone(timezone.utc)
    except Exception:
        return None
    
def parse_dt_iso_to_utc_str(dt_str: Optional[str]) -> Optional[str]:
    if not dt_str:
        return None
    try:
        # Converte "+0000" para "+00:00"
        if dt_str.endswith('+0000'):
            dt_str = dt_str[:-5] + '+00:00'
        dt = datetime.fromisoformat(dt_str)
        dt_utc = dt.astimezone(timezone.utc)
        return dt_utc.strftime('%Y-%m-%d %H:%M:%S')
    except Exception as e:
        print("Erro ao converter data:", dt_str, e)
        return None

def safe_get(d: dict, path: str, default=None):
    cur = d
    for key in path.split('.'):
        if isinstance(cur, dict) and key in cur:
            cur = cur[key]
        else:
            return default
    return cur

# =========================
# Páginas
# =========================
def carregar_paginas(conn) -> List[Dict]:
    with conn.cursor(cursor_factory=RealDictCursor) as cur:
        cur.execute("""
            SELECT id, name, url, page_id
              FROM fb_pages_monitor
             WHERE COALESCE(post, FALSE) = TRUE
               AND deleted_at IS NULL
        """)
        return cur.fetchall()

def max_data_postagem_da_pagina(conn, page_id: str) -> Optional[datetime]:
    """
    Usa o prefixo do post_id (PAGEID_) para descobrir a maior data_postagem já salva.
    """
    pattern = f"{page_id}_%"
    with conn.cursor() as cur:
        cur.execute("""
            SELECT MAX(data_postagem) 
              FROM post_facebook
             WHERE post_id LIKE %s
        """, (pattern,))
        row = cur.fetchone()
        return row[0] if row and row[0] else None

def dt_to_since_iso(dt: datetime) -> str:
    # ISO UTC com 'Z'
    return dt.astimezone(timezone.utc).strftime('%Y-%m-%dT%H:%M:%SZ')

# =========================
# Coleta de posts
# =========================
def coletar_posts_pagina(page_id: str, limit_total=PAGE_FETCH_LIMIT, since_iso: Optional[str] = None) -> List[Dict]:
    url = f'https://graph.facebook.com/{GRAPH_VER}/{page_id}/posts'
    params = {
        'fields': (
            'message,story,created_time,permalink_url,full_picture,status_type,'
            'shares,reactions.summary(true),comments.summary(true),id'
        ),
        'access_token': ACCESS_TOKEN
    }
    if since_iso:
        params['since'] = since_iso

    posts_all: List[Dict] = []
    while url and len(posts_all) < limit_total:
        data = request_with_retry(url, params)
        batch = data.get('data', [])
        posts_all.extend(batch)
        url = data.get('paging', {}).get('next')
        params = None
        if not batch:
            break
    return posts_all[:limit_total]

# =========================
# Persistência
# =========================
def inserir_posts(conn, posts: List[Dict]) -> int:
    if not posts:
        return 0

    rows = []
    for p in posts:
        rows.append((
            p.get('id'),
            p.get('message') or '',
            parse_dt_iso_to_utc_str(p.get('created_time')),
            p.get('permalink_url'),
            p.get('story'),
            p.get('full_picture'),
            p.get('message') or '',  # texto para tsv_mensagem
            safe_get(p, 'shares.count'),
            safe_get(p, 'reactions.summary.total_count'),
            safe_get(p, 'comments.summary.total_count'),
            p.get('status_type'),
        ))

        print("created_time:", p.get('created_time'), "->", type(p.get('created_time')))
        print("parse_dt_iso_to_utc_str:", parse_dt_iso_to_utc_str(p.get('created_time')))
   
    sql = """
        INSERT INTO post_facebook (
            post_id, mensagem, data_postagem, link, story, imagem,
            tsv_mensagem, shares, reactions, comments, status_type
        )
        VALUES %s
        ON CONFLICT (post_id) DO NOTHING
    """
    template = f"""
        (%s, %s, %s, %s, %s, %s,
         to_tsvector('{TS_CONFIG}', %s), %s, %s, %s, %s)
    """

    # mede before/after para saber quantos realmente entraram
    with conn.cursor() as cur:
        cur.execute("SELECT COUNT(*) FROM post_facebook")
        before = cur.fetchone()[0]

        execute_values(cur, sql, rows, template=template, page_size=500)

        cur.execute("SELECT COUNT(*) FROM post_facebook")
        after = cur.fetchone()[0]

    conn.commit()
    return max(0, after - before)

def registrar_log_pagina(conn, id_pagina: int, total_inseridos: int):
    with conn.cursor() as cur:
        cur.execute("""
            INSERT INTO fb_page_monitor (id_pagina, total_coletas, created_at, updated_at)
            VALUES (%s, %s, NOW(), NOW())
        """, (id_pagina, total_inseridos))
    conn.commit()

# =========================
# Processamento por página
# =========================
def processar_pagina(conn, page_row: Dict):
    page_db_id = page_row['id']    # id em fb_pages_monitor
    page_id = page_row['page_id']  # Page ID/username
    page_name = page_row['name']

    # since incremental por página
    since_dt = max_data_postagem_da_pagina(conn, page_id)
    since_iso = dt_to_since_iso(since_dt) if since_dt else None

    log(f"➡ Coletando página: {page_name} ({page_id})"
        + (f" desde {since_iso}" if since_iso else ""))

    # 1 tentativa “normal”; se bater rate-limit, cooldown e tenta 1x novamente
    for tentativa in (1, 2):
        try:
            posts = coletar_posts_pagina(page_id, limit_total=PAGE_FETCH_LIMIT, since_iso=since_iso)
            inseridos = inserir_posts(conn, posts)
            registrar_log_pagina(conn, page_db_id, inseridos)
            log(f"✔ Página {page_name}: {inseridos} posts novos")
            break
        except RateLimitError as e:
            if tentativa == 1:
                log(f"⏳ Rate limit atingido. Aguardando {RATE_LIMIT_SLEEP//60} min e tentando novamente…")
                time.sleep(RATE_LIMIT_SLEEP)
                continue
            else:
                log(f"❌ Rate limit persistente para {page_name}. Pulando.")
                # registra log com 0 inseridos para ter histórico do “passou por aqui”
                registrar_log_pagina(conn, page_db_id, 0)
                break

# =========================
# Runner
# =========================
def executar():
    if not ACCESS_TOKEN or ACCESS_TOKEN == '---':
        log("❌ ACCESS_TOKEN inválido. Configure FB_TOKEN.")
        return

    conn = get_conn()
    # criar_indices_recomendados(conn)  # rode 1x se ainda não
    try:
        paginas = carregar_paginas(conn)
        if not paginas:
            log("ℹ Nenhuma página com post=true encontrada.")
            return

        for p in paginas:
            try:
                processar_pagina(conn, p)
                time.sleep(PAGE_SLEEP_SECONDS)
            except Exception as e:
                log(f"❌ Erro na página id={p.get('id')} ({p.get('name')}): {e}")

        log("🏁 Coleta finalizada.")
    finally:
        conn.close()

if __name__ == '__main__':
    executar()