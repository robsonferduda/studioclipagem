import requests
import psycopg2
import re
from datetime import datetime

# Configurações
ACCESS_TOKEN = '565829271321837|xGQc9z5vEJP4qSxsJ7iWJrrgnY8'
PAGE_ID = '396998350479791'
GRAPH_API_URL = f'https://graph.facebook.com/v19.0/{PAGE_ID}/posts'
DB_CONFIG = {
    'dbname': 'postgres',
    'user': 'postgres',
    'password': 'cipplp10',
    'host': 'localhost',
    'port': 5432,
}

def log(msg):
    print(f"[{datetime.now().isoformat(sep=' ', timespec='seconds')}] {msg}")

def testar_conexao_db():
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        conn.close()
        log("✅ Conexão com o banco de dados estabelecida.")
        return True
    except Exception as e:
        log(f"❌ ERRO ao conectar com o banco de dados: {e}")
        return False

def salvar_postagem(post_id, mensagem, data_postagem, link, story=None, imagem=None,
                    shares=None, reactions=None, comments=None, status_type=None):
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        cur = conn.cursor()
        cur.execute("""
            INSERT INTO post_facebook (
                post_id, mensagem, data_postagem, link, story, imagem,
                tsv_mensagem, shares, reactions, comments, status_type
            )
            VALUES (%s, %s, %s, %s, %s, %s, to_tsvector('portuguese', %s),
                    %s, %s, %s, %s)
            ON CONFLICT (post_id) DO NOTHING
        """, (
            post_id, mensagem, data_postagem, link, story, imagem, mensagem,
            shares, reactions, comments, status_type
        ))
        if cur.rowcount > 0:
            log(f"✔ Inserido: {post_id}")
        else:
            log(f"⚠ Ignorado (duplicado): {post_id}")
        conn.commit()
        cur.close()
        conn.close()
    except Exception as e:
        log(f"❌ ERRO ao salvar {post_id}: {e}")

def buscar_postagens(limit=100):
    all_posts = []
    url = GRAPH_API_URL
    params = {
        'fields': 'message,story,created_time,permalink_url,full_picture,status_type,shares,reactions.summary(true),comments.summary(true),id',
        'access_token': ACCESS_TOKEN
    }

    while url and len(all_posts) < limit:
        response = requests.get(url, params=params)
        response.raise_for_status()
        data = response.json()

        posts = data.get('data', [])
        all_posts.extend(posts)
        url = data.get('paging', {}).get('next', None)
        params = None

    log(f"🔎 Total de postagens coletadas: {len(all_posts)}")
    return all_posts

def executar_coleta():
    if not testar_conexao_db():
        return

    postagens = buscar_postagens()
    for post in postagens:
        post_id = post.get('id')
        mensagem = post.get('message', '')
        data_postagem = post.get('created_time')
        link = post.get('permalink_url')
        story = post.get('story')
        imagem = post.get('full_picture')
        status_type = post.get('status_type')
        shares = post.get('shares', {}).get('count')
        reactions = post.get('reactions', {}).get('summary', {}).get('total_count')
        comments = post.get('comments', {}).get('summary', {}).get('total_count')

        salvar_postagem(post_id, mensagem, data_postagem, link, story, imagem,
                        shares, reactions, comments, status_type)

    log("🏁 Coleta finalizada.")

if __name__ == '__main__':
    executar_coleta()