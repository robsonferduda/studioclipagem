# -*- coding: utf-8 -*-
import os
import time
from typing import List, Dict, Optional
from datetime import datetime
import requests
import psycopg2
from psycopg2.extras import RealDictCursor, execute_values
from config import DB_CONFIG as APP_DB

GRAPH_VER = 'v19.0'
HTTP_TIMEOUT = 20
PAGE_LIMIT = 20
PAGE_SLEEP_SECONDS = 1
TS_CONFIG = 'portuguese'
MAX_HASHTAGS_RUN = 25  # limite de hashtags por execução para evitar quota

IG_TOKEN = 'EAAICnmS4fO0BO5RV3GJqmbJ32WaNGdOmjUzZCtStd4BwakTx8jjcZA1RAHNO0sp6PikoyqZBSMYIA3L5aoc4KMz6RJFfqwYZA4n7FoEx5IsR3a7xmnYvpkGrATu34nmcSGoWdzydZAyRlaYADX3RLQBE2uRGGvWUacZA0j1gYw4jLfAZBJLtyogZBsvYDd6COTIZD'
IG_USER_ID = '17841408416359361'

DB_CONFIG = {
    'dbname': APP_DB['database'],
    'user': APP_DB['username'],
    'password': APP_DB['password'],
    'host': APP_DB['host'],
    'port': APP_DB['port'],
}

def log(m): print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] {m}")

def get_conn():
    return psycopg2.connect(**DB_CONFIG)

def criar_indices(conn):
    sqls = [
        "ALTER TABLE IF EXISTS post_instagram ADD COLUMN IF NOT EXISTS tsv_caption tsvector;",
        "CREATE INDEX IF NOT EXISTS idx_ig_tsv_caption ON public.post_instagram USING gin (tsv_caption);",
        "CREATE INDEX IF NOT EXISTS post_ig_data_idx ON public.post_instagram (timestamp);",
        "ALTER TABLE IF EXISTS post_instagram ADD CONSTRAINT IF NOT EXISTS post_instagram_media_id_key UNIQUE (media_id);",
    ]
    with conn.cursor() as cur:
        for s in sqls: cur.execute(s)
    conn.commit()

def request_json(url: str, params: dict) -> dict:
    r = requests.get(url, params=params, timeout=HTTP_TIMEOUT)
    r.raise_for_status()
    return r.json()

def carregar_clientes_hashtag(conn) -> List[Dict]:
    with conn.cursor(cursor_factory=RealDictCursor) as cur:
        cur.execute("""
            SELECT id, nome, hashtags
              FROM clientes
             WHERE COALESCE(fl_hashtag, FALSE) = TRUE
               AND TRIM(COALESCE(hashtags, '')) <> ''
        """)
        return cur.fetchall()
    
def parse_hashtags(raw: str) -> List[str]:
    import re
    tokens = re.split(r'[,\s;]+', raw.strip())
    tags = []
    seen = set()
    for t in tokens:
        if not t:
            continue
        t = t.strip().lstrip('#')
        if not t:
            continue
        key = t.lower()
        if key in seen:
            continue
        seen.add(key)
        tags.append(t)
    return tags

def coletar_por_hashtag(conn, tag: str, pages: int = 5) -> int:
    hid = buscar_hashtag_id(tag)
    if not hid:
        log(f"Hashtag '{tag}' não encontrada.")
        return 0
    after = None
    total = 0
    for _ in range(pages):
        data = listar_midias_por_hashtag(hid, after=after, top=False)  # ou top=True
        items = data.get('data', [])
        inseridos = inserir_midias(conn, items)  # username/likes/comments podem vir None
        total += inseridos
        after = data.get('paging', {}).get('cursors', {}).get('after')
        if not after or not items:
            break
        time.sleep(PAGE_SLEEP_SECONDS)
    log(f"Hashtag #{tag}: inseridos {total} registros.")
    return total

def buscar_hashtag_id(termo: str) -> Optional[str]:
    url = f"https://graph.facebook.com/{GRAPH_VER}/ig_hashtag_search"
    params = {
        'user_id': IG_USER_ID,
        'q': termo.lstrip('#'),
        'access_token': IG_TOKEN
    }
    data = request_json(url, params)
    arr = data.get('data', [])
    return arr[0]['id'] if arr else None

def listar_midias_mencionadas(ig_user_id: str, after: Optional[str] = None) -> dict:
    url = f"https://graph.facebook.com/{GRAPH_VER}/{ig_user_id}/mentioned_media"
    params = {
        'fields': 'id,caption,media_type,media_url,permalink,timestamp,username,comments_count,like_count',
        'access_token': IG_TOKEN,
        'limit': PAGE_LIMIT
    }
    if after:
        params['after'] = after
    return request_json(url, params)

def listar_midias_por_hashtag(hashtag_id: str, after: Optional[str] = None, top: bool = False) -> dict:
    tipo = 'top_media' if top else 'recent_media'
    url = f"https://graph.facebook.com/{GRAPH_VER}/{hashtag_id}/{tipo}"
    params = {
        'user_id': IG_USER_ID,
        'fields': 'id,caption,media_type,media_url,permalink,timestamp',  # 'like_count,comments_count' se tiver permissão
        'access_token': IG_TOKEN,
        'limit': PAGE_LIMIT
    }
    if after:
        params['after'] = after
    return request_json(url, params)

def listar_midias(ig_user_id: str, after: Optional[str] = None) -> dict:
    url = f"https://graph.facebook.com/{GRAPH_VER}/{ig_user_id}/tags"
    params = {
        'fields': 'id,caption,media_type,media_url,permalink,timestamp,username,comments_count,like_count',
        'access_token': IG_TOKEN,
        'limit': PAGE_LIMIT
    }
    if after: params['after'] = after
    return request_json(url, params)

def inserir_midias(conn, items: List[Dict]) -> int:
    if not items: return 0
    rows = []
    for m in items:
        rows.append((
            m.get('id'),
            m.get('caption') or '',
            m.get('timestamp'),
            m.get('permalink'),
            m.get('media_type'),
            m.get('media_url'),
            m.get('username'),
            m.get('comments_count'),
            m.get('like_count')
        ))
    sql = """
        INSERT INTO post_instagram (
          media_id, caption, timestamp, permalink, media_type, media_url,
          username, comments_count, like_count, tsv_caption
        ) VALUES %s
        ON CONFLICT (media_id) DO NOTHING
    """
    template = f"(%s,%s,%s,%s,%s,%s,%s,%s,%s,to_tsvector('{TS_CONFIG}', %s))"
    rows_tsv = [r + (r[1],) for r in rows]
    with conn.cursor() as cur:
        cur.execute("SELECT COUNT(*) FROM post_instagram")
        before = cur.fetchone()[0]
        execute_values(cur, sql, rows_tsv, template=template, page_size=500)
        cur.execute("SELECT COUNT(*) FROM post_instagram")
        after = cur.fetchone()[0]
    conn.commit()
    return max(0, after - before)

def executar():
    if not IG_TOKEN or not IG_USER_ID:
        log("IG_TOKEN/IG_USER_ID ausentes.")
        return
    conn = get_conn()
    try:
        # criar_indices(conn)  # habilite se ainda não criou os índices
        total_global = 0

        # 1) Coleta por hashtags definidas em clientes
        clientes = carregar_clientes_hashtag(conn)
        if clientes:
            processadas = set()
            for cli in clientes:
                tags = parse_hashtags(cli.get('hashtags') or '')
                for tag in tags:
                    if tag.lower() in processadas:
                        continue
                    if len(processadas) >= MAX_HASHTAGS_RUN:
                        log("Limite de hashtags por execução atingido.")
                        break
                    total_global += coletar_por_hashtag(conn, tag)
                    processadas.add(tag.lower())
                if len(processadas) >= MAX_HASHTAGS_RUN:
                    break
            log(f"Total inserido via hashtags: {total_global}")

        # 2) (Opcional) Fallback: coleta de mídias da conta (se não houver clientes/hashtags)
        after = None
        for _ in range(15):
            data = listar_midias(IG_USER_ID, after=after)
            items = data.get('data', [])
            inseridos = inserir_midias(conn, items)
            total_global += inseridos
            after = data.get('paging', {}).get('cursors', {}).get('after')
            if not after or not items:
                break
            time.sleep(PAGE_SLEEP_SECONDS)
        log(f"Inseridos {total_global} registros do Instagram.")

        # ...dentro do executar()...
        # 3) Coleta de mídias em que a conta foi mencionada
        after = None
        for _ in range(10):  # ajuste o limite conforme necessário
            data = listar_midias_mencionadas(IG_USER_ID, after=after)
            items = data.get('data', [])
            inseridos = inserir_midias(conn, items)
            total_global += inseridos
            after = data.get('paging', {}).get('cursors', {}).get('after')
            if not after or not items:
                break
            time.sleep(PAGE_SLEEP_SECONDS)
        log(f"Inseridos {total_global} registros de menções no Instagram.")

    finally:
        conn.close()

if __name__ == '__main__':
    executar()