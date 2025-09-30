import os
import re
import sys
import time
from typing import List, Dict, Any, Optional, Set
from datetime import datetime, timezone
from dotenv import load_dotenv

import psycopg2
from psycopg2.extras import RealDictCursor, execute_values
from apify_client import ApifyClient

load_dotenv(dotenv_path=os.path.join(os.path.dirname(__file__), '..', '.env'))

# =========================
# CONFIG (via env quando possível)
# =========================
TS_CONFIG = os.getenv("TS_CONFIG", "portuguese")  # 'portuguese' | 'simple'
APIFY_TOKEN = os.getenv('APIFY_TOKEN')            # export APIFY_TOKEN="seu_token_apify"
APIFY_ACTOR = os.getenv("APIFY_ACTOR", "danek/facebook-search-ppr")
APIFY_MAX_POSTS = int(os.getenv("APIFY_MAX_POSTS", "100"))
APIFY_RECENT = os.getenv("APIFY_RECENT", "true").lower() in ("1", "true", "yes")

DB = {
    'dbname': os.getenv('DB_DATABASE'),
    'user': os.getenv('DB_USERNAME'),
    'password': os.getenv('DB_PASSWORD'),
    'host': os.getenv('DB_HOST'),
    'port': int(os.getenv('DB_PORT')),
}

PAGE_SIZE_INSERT = int(os.getenv("BATCH_SIZE", "500"))
SLEEP_BETWEEN_QUERIES = float(os.getenv("SLEEP_BETWEEN_QUERIES", "1.0"))  # respiro leve entre buscas


# =========================
# LOG
# =========================
def log(msg: str) -> None:
    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] {msg}")


# =========================
# DB helpers
# =========================
def get_conn():
    return psycopg2.connect(**DB)

def carregar_clientes(conn) -> List[Dict[str, Any]]:
    """
    Lê clientes ativos com busca_midias_sociais não vazia.
    Ajuste o filtro conforme sua regra (ex.: fl_ativo = true).
    """
    sql = """
        SELECT id, nome, busca_midias_sociais
          FROM public.clientes
         WHERE COALESCE(fl_ativo, FALSE) = TRUE
           AND busca_midias_sociais IS NOT NULL
           AND length(trim(busca_midias_sociais)) > 0
    """
    with conn.cursor(cursor_factory=RealDictCursor) as cur:
        cur.execute(sql)
        return cur.fetchall()


# =========================
# Normalização / Mapping
# =========================
def to_utc_from_seconds(ts: Optional[int]) -> Optional[datetime]:
    if ts is None:
        return None
    # Armazena como timestamp "naive" UTC (coerente com seu schema)
    return datetime.fromtimestamp(int(ts), tz=timezone.utc).replace(tzinfo=None)

def pick_image(item: Dict[str, Any]) -> Optional[str]:
    img = item.get("image")
    if isinstance(img, dict):
        return img.get("uri") or img.get("url")
    return None

def map_row(item: Dict[str, Any]) -> Optional[tuple]:
    """
    Mapeia item do dataset da Apify (Actor facebook-search-ppr) -> linha da public.post_facebook.
    """
    post_id = item.get("post_id")
    if not post_id:
        return None

    mensagem = item.get("message") or ""
    data_postagem = to_utc_from_seconds(item.get("timestamp"))
    link = item.get("url")
    story = None  # payload não traz story
    imagem = pick_image(item)

    shares = item.get("reshare_count")
    reactions_total = item.get("reactions_count")
    comments = item.get("comments_count")
    status_type = item.get("status_type") or item.get("type")

    reactions_obj = item.get("reactions") or {}
    angry = reactions_obj.get("angry")
    care  = reactions_obj.get("care")
    haha  = reactions_obj.get("haha")
    like  = reactions_obj.get("like")
    love  = reactions_obj.get("love")
    sad   = reactions_obj.get("sad")
    wow   = reactions_obj.get("wow")

    return (
        post_id, mensagem, data_postagem, link, story, imagem,
        mensagem, shares, reactions_total, comments, status_type,
        angry, care, haha, like, love, sad, wow
    )


# =========================
# Insert em lote
# =========================
def insert_batch(conn, rows: List[tuple]) -> int:
    """
    Insere em lote na public.post_facebook e retorna quantos entraram (before/after).
    """
    if not rows:
        return 0

    sql = """
        INSERT INTO public.post_facebook_api (
            post_id, mensagem, data_postagem, link, story, imagem,
            tsv_mensagem, shares, reactions, "comments", status_type,
            reactions_angry, reactions_care, reactions_haha, reactions_like,
            reactions_love, reactions_sad, reactions_wow
        ) VALUES %s
        ON CONFLICT (post_id) DO NOTHING
    """

    template = f"""
        (%s, %s, %s, %s, %s, %s,
         to_tsvector('{TS_CONFIG}', %s), %s, %s, %s, %s,
         %s, %s, %s, %s, %s, %s, %s)
    """

    with conn.cursor() as cur:
        cur.execute("SELECT COUNT(*) FROM public.post_facebook_api")
        before = cur.fetchone()[0]

        execute_values(cur, sql, rows, template=template, page_size=PAGE_SIZE_INSERT)

        cur.execute("SELECT COUNT(*) FROM public.post_facebook_api")
        after = cur.fetchone()[0]
    conn.commit()
    return max(0, after - before)


# =========================
# Apify
# =========================
def run_apify_search(query: str, client: ApifyClient) -> List[Dict[str, Any]]:
    run_input = {
        "query": query,
        "search_type": "posts",
        "max_posts": APIFY_MAX_POSTS,
        "recent_posts": APIFY_RECENT,
    }
    run = client.actor(APIFY_ACTOR).call(run_input=run_input)
    dataset_id = run["defaultDatasetId"]

    items: List[Dict[str, Any]] = []
    for it in client.dataset(dataset_id).iterate_items():
        items.append(it)
    return items


# =========================
# Utilidades
# =========================
_SPLIT_RE = re.compile(r"[,\n;\|]+")

def split_queries(raw: str) -> List[str]:
    """
    Divide o campo busca_midias_sociais em queries individuais.
    Suporta separadores: vírgula, ponto-e-vírgula, pipe, quebras de linha.
    Remove duplicadas e espaços.
    """
    parts = [p.strip() for p in _SPLIT_RE.split(raw or "") if p.strip()]
    # dedup preservando ordem
    seen: Set[str] = set()
    out: List[str] = []
    for p in parts:
        if p.lower() not in seen:
            seen.add(p.lower())
            out.append(p)
    return out


# =========================
# Runner
# =========================
def main():
    if not APIFY_TOKEN:
        raise RuntimeError("APIFY_TOKEN não definido. Ex: export APIFY_TOKEN=seu_token_apify")

    # Conexão DB única para toda execução
    conn = get_conn()
    client = ApifyClient(APIFY_TOKEN)

    try:
        clientes = carregar_clientes(conn)
        if not clientes:
            log("ℹ Nenhum cliente com busca_midias_sociais encontrado.")
            return

        log(f"🔎 {len(clientes)} cliente(s) com buscas configuradas.")

        # dedup global de post_id dentro da execução (evita reinsert entre queries/clientes)
        seen_posts: Set[str] = set()
        total_inserted_global = 0

        for c in clientes:
            cid = c["id"]
            nome = c.get("nome") or f"cliente_{cid}"
            raw = c.get("busca_midias_sociais") or ""
            queries = split_queries(raw)

            if not queries:
                log(f"➡ Cliente {cid} ({nome}): nenhum termo válido em busca_midias_sociais.")
                continue

            log(f"➡ Cliente {cid} ({nome}): {len(queries)} termo(s) → {queries}")

            rows_to_insert: List[tuple] = []

            for q in queries:
                try:
                    items = run_apify_search(q, client)
                    log(f"   • Query '{q}': {len(items)} item(ns)")

                    for it in items:
                        pid = it.get("post_id")
                        if not pid or pid in seen_posts:
                            continue
                        row = map_row(it)
                        if row:
                            rows_to_insert.append(row)
                            seen_posts.add(pid)

                    # respiro leve entre queries para não “espancar” a API
                    if SLEEP_BETWEEN_QUERIES > 0:
                        time.sleep(SLEEP_BETWEEN_QUERIES)

                except Exception as e:
                    log(f"   ❌ Erro na query '{q}' (cliente {cid}): {e}")

            inserted = insert_batch(conn, rows_to_insert)
            total_inserted_global += inserted
            log(f"✔ Cliente {cid} ({nome}): {inserted} post(s) novo(s) inserido(s)")

        log(f"🏁 Concluído. Total inserido na execução: {total_inserted_global}")

    finally:
        conn.close()


if __name__ == "__main__":
    # Uso opcional: pode passar nada; tudo vem do banco
    # python3 facebook_apify_from_client_queries.py
    main()