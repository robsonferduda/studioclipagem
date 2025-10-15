import os
import re
import time
from typing import List, Dict, Any, Optional, Set
from datetime import datetime, timezone

import psycopg2
from psycopg2.extras import RealDictCursor, execute_values
from apify_client import ApifyClient
from dotenv import load_dotenv


# =========================
# ENV / CONFIG
# =========================
# carrega .env da RAIZ do projeto (.. da pasta python)
load_dotenv(dotenv_path=os.path.join(os.path.dirname(__file__), '..', '.env'))

APIFY_TOKEN = os.getenv("APIFY_TOKEN")
APIFY_ACTOR = os.getenv("APIFY_ACTOR", "apify/instagram-hashtag-scraper")
APIFY_RESULTS_LIMIT = int(os.getenv("APIFY_RESULTS_LIMIT", "15"))
APIFY_RESULTS_TYPE = os.getenv("APIFY_RESULTS_TYPE", "posts")  # posts | reels (depende do actor)
APIFY_SLEEP_BETWEEN_BATCHES = float(os.getenv("APIFY_SLEEP_BETWEEN_BATCHES", "1.0"))

TS_CONFIG = os.getenv("TS_CONFIG", "portuguese")

DB = {
    'dbname': os.getenv('DB_DATABASE'),
    'user': os.getenv('DB_USERNAME'),
    'password': os.getenv('DB_PASSWORD'),
    'host': os.getenv('DB_HOST'),
    'port': int(os.getenv('DB_PORT')),
}

BATCH_INSERT_SIZE = int(os.getenv("BATCH_INSERT_SIZE", "500"))
HASHTAGS_PER_CALL = int(os.getenv("HASHTAGS_PER_CALL", "15"))  # quantas hashtags mandar por run do actor


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
    Clientes ativos com busca_midias_sociais não vazia.
    Ajuste o filtro conforme sua regra.
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
# Utils / parsing
# =========================
_SPLIT_RE = re.compile(r"[,\n;\|]+")

def split_terms(raw: str) -> List[str]:
    """Divide busca_midias_sociais em termos (vírgula, ;, |, quebra de linha)."""
    parts = [p.strip() for p in _SPLIT_RE.split(raw or "") if p.strip()]
    # dedup case-insensitive preservando ordem
    seen: Set[str] = set()
    out: List[str] = []
    for p in parts:
        key = p.lower()
        if key not in seen:
            seen.add(key)
            out.append(p)
    return out

def to_hashtag(term: str) -> Optional[str]:
    """
    Converte um termo livre em hashtag:
    - remove '#', espaços extras e símbolos nas pontas
    - converte espaços internos em nada (ou sublinhado, se preferir)
    - retorna em minúsculas
    """
    if not term:
        return None
    t = term.strip()
    t = t.lstrip('#').strip()
    if not t:
        return None
    # remove espaços -> junta (ex.: "Santa Catarina" => "SantaCatarina")
    t = re.sub(r"\s+", "", t)
    # remove caracteres não alfanuméricos nas pontas
    t = re.sub(r"^[^0-9A-Za-z_]+|[^0-9A-Za-z_]+$", "", t)
    if not t:
        return None
    return t.lower()

def chunk_list(lst: List[str], size: int) -> List[List[str]]:
    return [lst[i:i+size] for i in range(0, len(lst), size)]

def parse_timestamp(value) -> Optional[datetime]:
    """
    Aceita:
    - segundos Unix (int/str)
    - ISO 8601 (ex.: '2025-09-23T07:15:48Z')
    Retorna datetime timezone-aware (UTC) para gravar em timestamptz.
    """
    if value is None:
        return None
    try:
        # segundos (int-like)
        sec = int(value)
        return datetime.fromtimestamp(sec, tz=timezone.utc)
    except (ValueError, TypeError):
        pass
    try:
        # ISO
        s = str(value).replace('Z', '+00:00')
        dt = datetime.fromisoformat(s)
        if dt.tzinfo is None:
            dt = dt.replace(tzinfo=timezone.utc)
        return dt.astimezone(timezone.utc)
    except Exception:
        return None

def extract_hashtags_from_caption(caption: Optional[str]) -> List[str]:
    if not caption:
        return []
    tags = re.findall(r"#(\w+)", caption, flags=re.UNICODE)
    # normaliza para minúsculas e dedup
    seen: Set[str] = set()
    out: List[str] = []
    for t in tags:
        key = t.lower()
        if key not in seen:
            seen.add(key)
            out.append(key)
    return out


# =========================
# Mapping para post_instagram
# =========================
def map_item_to_row(item: Dict[str, Any], fallback_hashtag: Optional[str]) -> Optional[tuple]:
    media_id = item.get("id") or item.get("media_id") or item.get("post_id")
    if not media_id:
        return None

    caption = item.get("caption") or item.get("message") or ""
    ts = (
        item.get("timestamp")
        or item.get("takenAtTimestamp")
        or item.get("taken_at_timestamp")
        or item.get("created_time")
    )
    dt_ts = parse_timestamp(ts)

    # permalink continua usando url como fallback
    permalink = item.get("permalink") or item.get("url") or item.get("link")

    location_id = item.get("locationId")
    location_name = item.get("locationName")    

    # media_type
    media_type = (
        item.get("type")
        or item.get("mediaType")
        or item.get("media_type")
    )

    # ✅ media_url deve ser o campo "url" do JSON que você enviou
    media_url = item.get("displayUrl") or \
                item.get("mediaUrl") or item.get("media_url") or \
                item.get("display_url") or item.get("displayUrl") or \
                item.get("imageHighResolutionUrl") or \
                (item.get("image", {}).get("url") if isinstance(item.get("image"), dict) else item.get("image")) or \
                item.get("video")

    # ✅ username e username_full vindos de ownerUsername / ownerFullName
    username = item.get("ownerUsername") or \
               ((item.get("owner") or {}).get("username") if isinstance(item.get("owner"), dict) else item.get("username"))

    username_full = item.get("ownerFullName") or \
                    ((item.get("owner") or {}).get("fullName") if isinstance(item.get("owner"), dict) else item.get("username_full"))

    comments_count = item.get("commentsCount") or item.get("comments_count") or item.get("comment_count")
    like_count = item.get("likesCount") or item.get("like_count") or item.get("reactions_count")

    # hashtags do payload ou extraídas da caption; adiciona fallback do lote (se houver)
    tags_from_item = item.get("hashtags")
    if isinstance(tags_from_item, list):
        tags_list = [str(x).lstrip('#').lower() for x in tags_from_item if str(x).strip()]
    elif isinstance(tags_from_item, str):
        tags_list = [h.strip().lstrip('#').lower() for h in _SPLIT_RE.split(tags_from_item) if h.strip()]
    else:
        tags_list = extract_hashtags_from_caption(caption)

    if fallback_hashtag:
        fh = fallback_hashtag.lstrip('#').lower()
        if fh and fh not in tags_list:
            tags_list.append(fh)

    hashtags_csv = ",".join(sorted(set(tags_list)))

    return (
        media_id,               # media_id
        caption,                # caption
        dt_ts,                  # "timestamp" (timestamptz)
        permalink,              # permalink
        media_type,             # media_type
        media_url,              # media_url (forçado para url)
        username,               # username (ownerUsername)
        comments_count,         # comments_count
        like_count,             # like_count
        caption,                # tsv_caption (texto base p/ to_tsvector)
        hashtags_csv,           # hashtags
        username_full,          # username_full (ownerFullName)
        location_id,
        location_name,
    )


# =========================
# Insert em lote
# =========================
def insert_batch(conn, rows: List[tuple]) -> int:
    if not rows:
        return 0

    sql = f"""
        INSERT INTO public.post_instagram (
            media_id, caption, "timestamp", permalink, media_type, media_url,
            username, comments_count, like_count, tsv_caption, hashtags, username_full, location_id, location_name
        ) VALUES %s
        ON CONFLICT (media_id) DO NOTHING
    """
    template = f"""
        (%s, %s, %s, %s, %s, %s,
         %s, %s, %s, to_tsvector('{TS_CONFIG}', %s), %s, %s, %s, %s)
    """

    with conn.cursor() as cur:
        cur.execute("SELECT COUNT(*) FROM public.post_instagram")
        before = cur.fetchone()[0]

        execute_values(cur, sql, rows, template=template, page_size=BATCH_INSERT_SIZE)

        cur.execute("SELECT COUNT(*) FROM public.post_instagram")
        after = cur.fetchone()[0]
    conn.commit()
    return max(0, after - before)


# =========================
# Apify
# =========================
def run_hashtag_batch(client: ApifyClient, hashtags: List[str]) -> List[Dict[str, Any]]:
    """
    Chama o actor com um lote de hashtags.
    """
    run_input = {
        "hashtags": hashtags,
        "resultsType": APIFY_RESULTS_TYPE,  # posts (default)
        "resultsLimit": APIFY_RESULTS_LIMIT,
    }
    run = client.actor(APIFY_ACTOR).call(run_input=run_input)
    dataset_id = run["defaultDatasetId"]

    items: List[Dict[str, Any]] = []
    for it in client.dataset(dataset_id).iterate_items():
        items.append(it)
    return items


# =========================
# Runner
# =========================
def main():
    if not APIFY_TOKEN:
        raise RuntimeError("APIFY_TOKEN não definido. Configure no .env (APIFY_TOKEN=...)")

    conn = get_conn()
    client = ApifyClient(APIFY_TOKEN)

    try:
        clientes = carregar_clientes(conn)
        if not clientes:
            log("ℹ Nenhum cliente com busca_midias_sociais encontrado.")
            return

        total_inserted_global = 0
        seen_media_ids: Set[str] = set()

        for c in clientes:
            cid = c["id"]
            nome = c.get("nome") or f"cliente_{cid}"
            raw = c.get("busca_midias_sociais") or ""
            terms = split_terms(raw)

            # converte termos em hashtags normalizadas
            hashtags = []
            seen_ht: Set[str] = set()
            for t in terms:
                h = to_hashtag(t)
                if h and h not in seen_ht:
                    seen_ht.add(h)
                    hashtags.append(h)

            if not hashtags:
                log(f"➡ Cliente {cid} ({nome}): nenhum termo válido para hashtags.")
                continue

            log(f"➡ Cliente {cid} ({nome}): {len(hashtags)} hashtag(s) → {hashtags}")

            rows_to_insert: List[tuple] = []
            for batch in chunk_list(hashtags, HASHTAGS_PER_CALL):
                try:
                    items = run_hashtag_batch(client, batch)
                    log(f"   • Lote {batch}: {len(items)} item(ns)")

                    # fallback hashtag por item: se o actor não trouxer, marcamos a primeira do lote
                    fallback = batch[0] if len(batch) > 0 else None

                    for it in items:
                        media_id = it.get("id") or it.get("media_id") or it.get("post_id")
                        if not media_id or media_id in seen_media_ids:
                            continue
                        row = map_item_to_row(it, fallback_hashtag=fallback)
                        if row:
                            rows_to_insert.append(row)
                            seen_media_ids.add(media_id)

                    if APIFY_SLEEP_BETWEEN_BATCHES > 0:
                        time.sleep(APIFY_SLEEP_BETWEEN_BATCHES)

                except Exception as e:
                    log(f"   ❌ Erro no lote {batch}: {e}")

            inserted = insert_batch(conn, rows_to_insert)
            total_inserted_global += inserted
            log(f"✔ Cliente {cid} ({nome}): {inserted} mídia(s) nova(s) inserida(s)")

        log(f"🏁 Concluído. Total inserido na execução: {total_inserted_global}")

    finally:
        conn.close()


if __name__ == "__main__":
    main()