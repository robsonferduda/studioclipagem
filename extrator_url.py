import trafilatura
import json
import psycopg2
import psycopg2.extras

from trafilatura import spider
from decouple import config
from datetime import datetime

host = config('DB_HOST')
database = config('DB_DATABASE')
user = config('DB_USERNAME')
password = config('DB_PASSWORD')

con = psycopg2.connect(host=host, database=database,user=user, password=password)
cur = con.cursor(cursor_factory = psycopg2.extras.RealDictCursor)

total_coleta = 10
dt_atual = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
sql = 'SELECT id, url FROM fonte_web'
cur.execute(sql)
fontes = cur.fetchall()

cur.execute("INSERT INTO public.coleta_web(total_coletas) VALUES(0) RETURNING id")
con.commit() 
id_coleta = cur.fetchone()['id']

for fonte in fontes:

    try:
        to_visit, known_urls = spider.focused_crawler(fonte['url'], max_seen_urls=1)
        to_visit, known_urls = spider.focused_crawler(fonte['url'], max_seen_urls=1, todo=to_visit, known_links=known_urls)

        for visit in to_visit:

            visit = visit.replace("'", '')
            sql = "SELECT id FROM links_pendentes WHERE url = '"+visit+"'"
            
            cur.execute(sql)
            id_url = cur.fetchone()

            if id_url is None:
                cur.execute("INSERT INTO links_pendentes (fonte_id, url) VALUES(%s, %s)", (fonte['id'], visit))
                con.commit() 

                total_coleta += 1
    except:
        print("Falha ao recuperar dados da fonte "+fonte['url'])

dt_final = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
cur.execute("UPDATE coleta_web SET total_coletas = "+str(total_coleta)+", updated_at = '"+str(dt_final)+"' WHERE id = "+str(id_coleta))
con.commit() 