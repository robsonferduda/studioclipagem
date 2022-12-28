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

sql = 'SELECT id, fonte_id, url FROM links_pendentes WHERE fl_coletado is false'
cur.execute(sql)
pendentes = cur.fetchall()

for pendente in pendentes:
    try:
        downloaded = trafilatura.fetch_url(pendente['url'])
        if downloaded:
            try:
                result = trafilatura.extract(downloaded, output_format="json")

                if result:
                    json_object = json.loads(result)
                    if json_object.get('date'):
        
                        id_fonte = pendente['fonte_id']
                        hash = json_object.get('fingerprint')

                        cur.execute("INSERT INTO noticia_web (id_fonte, hash, dt_clipagem, titulo, categoria, url, texto) VALUES(%s, %s, %s, %s, %s, %s, %s)",(id_fonte, hash, json_object.get('date'), json_object.get('title'),json_object.get('categories'),json_object.get('source'),json_object.get('text')))
                        con.commit()
            except:
                print("Erro na recuperação de dados da URL "+pendente['url'])

        sql = "UPDATE links_pendentes SET fl_coletado = true WHERE id = "+str(pendente['id'])
        cur.execute(sql)
        con.commit() 

    except:
        print("Erro na recuperação da URL "+pendente['url'])