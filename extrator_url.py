import trafilatura
import json
import psycopg2
import psycopg2.extras

from trafilatura import spider
from decouple import config

host = config('DB_HOST')
database = config('DB_DATABASE')
user = config('DB_USERNAME')
password = config('DB_PASSWORD')

con = psycopg2.connect(host=host, database=database,user=user, password=password)
cur = con.cursor(cursor_factory = psycopg2.extras.RealDictCursor)

sql = 'SELECT id, ds_url FROM fonte WHERE tipo_fonte_id = 2 AND fl_coleta = true'
cur.execute(sql)
fontes = cur.fetchall()

for fonte in fontes:

    try:
        to_visit, known_urls = spider.focused_crawler(fonte['ds_url'], max_seen_urls=1)
        to_visit, known_urls = spider.focused_crawler(fonte['ds_url'], max_seen_urls=1, todo=to_visit, known_links=known_urls)

        for visit in to_visit:

            visit = visit.replace("'", '')
            sql = "SELECT id FROM links_pendentes WHERE url = '"+visit+"'"
            
            cur.execute(sql)
            id_url = cur.fetchone()

            if id_url is None:
                cur.execute("INSERT INTO links_pendentes (fonte_id, url) VALUES(%s, %s)", (fonte['id'], visit))
                con.commit() 
    except:
        print("Falha ao recuperar dados da fonte "+fonte['ds_url'])