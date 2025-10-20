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
APIFY_MAX_POSTS = int(os.getenv("APIFY_MAX_POSTS", "15"))
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

# ======= Config de idioma =======
ALLOW_LANGS = {x.strip() for x in (os.getenv("ALLOW_LANGS", "pt,pt-BR,pt_PT").split(","))}
MIN_LANG_CHARS = int(os.getenv("MIN_LANG_CHARS", "20"))
LANG_PROB_MIN = float(os.getenv("LANG_PROB_MIN", "0.70"))
FASTTEXT_MODEL = os.getenv("FASTTEXT_MODEL")  # caminho opcional para lid.176.ftz

def _norm_lang_code(code: str) -> str:
    return (code or "").split("-")[0].split("_")[0].lower()

_ALLOWED_NORM = {_norm_lang_code(x) for x in ALLOW_LANGS}

# tenta importar detectores em cascata
_CLD3 = None
_FT = None
_LD = None
try:
    import pycld3  # pip install pycld3
    _CLD3 = pycld3
except Exception:
    _CLD3 = None

if FASTTEXT_MODEL:
    try:
        import fasttext  # pip install fasttext
        _FT = fasttext.load_model(FASTTEXT_MODEL)
    except Exception:
        _FT = None

try:
    from langdetect import detect_langs, DetectorFactory  # pip install langdetect
    DetectorFactory.seed = 0
    _LD = True
except Exception:
    _LD = None

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
# Utilidades de idioma
# =========================
_SPLIT_RE = re.compile(r"[,\n;\|]+")

def _strip_hashtags_urls(text: str) -> str:
    t = re.sub(r"#\w+", " ", text or "")
    t = re.sub(r"https?://\S+|www\.\S+", " ", t)
    return re.sub(r"\s+", " ", t).strip()

def detect_lang(text: str) -> (Optional[str], float):
    """
    Retorna (lang, prob). lang ISO curto (ex.: 'pt'), prob em [0,1] quando disponível.
    """
    if not text:
        return None, 0.0
    t = _strip_hashtags_urls(text)
    if len(t) < MIN_LANG_CHARS:
        return None, 0.0

    if _CLD3:
        try:
            r = _CLD3.get_language(t)
            if r and r.is_reliable:
                return _norm_lang_code(r.language), float(r.probability or 0.0)
        except Exception:
            pass

    if _FT:
        try:
            labels, probs = _FT.predict(t.replace("\n", " "))
            if labels and probs:
                lang = labels[0].replace("__label__", "")
                return _norm_lang_code(lang), float(probs[0])
        except Exception:
            pass

    if _LD:
        try:
            cands = detect_langs(t)  # ex.: [pt:0.63, en:0.32]
            if cands:
                best = max(cands, key=lambda x: x.prob)
                return _norm_lang_code(best.lang), float(best.prob or 0.0)
        except Exception:
            pass

    return None, 0.0

def is_lang_allowed(text: str) -> bool:
    lang, prob = detect_lang(text)
    if lang is None:
        return False
    if lang not in _ALLOWED_NORM:
        return False
    return prob >= LANG_PROB_MIN if prob > 0 else True

# =========================
# Normalização / Mapping
# =========================
def to_utc_from_seconds(ts: Optional[int]) -> Optional[datetime]:
    if ts is None:
        return None
    # Armazena como UTC “aware” (ajuste se sua coluna for timestamptz/naive)
    return datetime.fromtimestamp(int(ts), tz=timezone.utc)

def pick_image(item: Dict[str, Any]) -> Optional[str]:
    img = item.get("image")
    if isinstance(img, dict):
        return img.get("uri") or img.get("url")
    return None

def _to_int(x):
    try:
        return int(x)
    except (TypeError, ValueError):
        return None

def map_row(item: Dict[str, Any]) -> Optional[tuple]:
    """
    Mapeia item do dataset da Apify (Actor facebook-search-ppr) -> linha da public.post_facebook_api.
    Aplica filtro de idioma na mensagem.
    """
    post_id = item.get("post_id")
    if not post_id:
        return None

    mensagem = item.get("message") or ""
    # ===== filtro de idioma =====
    if not is_lang_allowed(mensagem):
        return None

    data_postagem = to_utc_from_seconds(item.get("timestamp"))
    link = item.get("url")
    story = None  # payload não traz story
    imagem = pick_image(item)

    shares = _to_int(item.get("reshare_count"))
    reactions_total = _to_int(item.get("reactions_count"))
    comments = _to_int(item.get("comments_count"))
    status_type = item.get("status_type") or item.get("type")

    reactions_obj = item.get("reactions") or {}
    angry = _to_int(reactions_obj.get("angry"))
    care  = _to_int(reactions_obj.get("care"))
    haha  = _to_int(reactions_obj.get("haha"))
    like  = _to_int(reactions_obj.get("like"))
    love  = _to_int(reactions_obj.get("love"))
    sad   = _to_int(reactions_obj.get("sad"))
    wow   = _to_int(reactions_obj.get("wow"))

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
    Insere em lote na public.post_facebook_api e retorna quantos entraram (before/after).
    """
    if not rows:
        return 0

    sql = f"""
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
# Auxiliares
# =========================
def split_queries(raw: str) -> List[str]:
    parts = [p.strip() for p in _SPLIT_RE.split(raw or "") if p.strip()]
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

    conn = get_conn()
    client = ApifyClient(APIFY_TOKEN)

    try:
        clientes = carregar_clientes(conn)
        if not clientes:
            log("ℹ Nenhum cliente com busca_midias_sociais encontrado.")
            return

        log(f"🔎 {len(clientes)} cliente(s) com buscas configuradas.")

        seen_posts: Set[str] = set()
        total_inserted_global = 0
        total_discarded_lang_global = 0

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
            discarded_lang_client = 0

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
                        else:
                            # provavelmente descartado por idioma ou payload inválido
                            discarded_lang_client += 1

                    if SLEEP_BETWEEN_QUERIES > 0:
                        time.sleep(SLEEP_BETWEEN_QUERIES)

                except Exception as e:
                    log(f"   ❌ Erro na query '{q}' (cliente {cid}): {e}")

            inserted = insert_batch(conn, rows_to_insert)
            total_inserted_global += inserted
            total_discarded_lang_global += discarded_lang_client

            log(f"✔ Cliente {cid} ({nome}): {inserted} post(s) novo(s) inserido(s) | descartados por idioma: {discarded_lang_client}")

        log(f"🏁 Concluído. Total inserido: {total_inserted_global} | Descartados por idioma: {total_discarded_lang_global}")

    finally:
        conn.close()

if __name__ == "__main__":
    main()
