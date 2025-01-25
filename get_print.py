import requests

# URL que você quer acessar
url = "http://studioclipagem.com.br/arrumar_print_web.php?id=1633576"

import psycopg2
import psycopg2.extras

from decouple import config
from datetime import datetime

host = config('DB_HOST')
database = config('DB_DATABASE')
user = config('DB_USERNAME')
password = config('DB_PASSWORD')

con = psycopg2.connect(host=host, database=database,user=user, password=password)
cur = con.cursor(cursor_factory = psycopg2.extras.RealDictCursor)

sql = "SELECT exported, noticia_id FROM noticia_cliente WHERE tipo_id = 2 AND exported = true AND send_print = false AND noticia_id = 5361694"
cur.execute(sql)
fontes = cur.fetchall()

print("Montou laço")
for fonte in fontes:
    print("Entrou laço")
    try:
        
        print_url = "http://studioclipagem.com.br/arrumar_print_web.php?id="+str(fonte['noticia_id'])

        try:
            response = requests.get(print_url)
            
            if response.status_code == 200:
                resultado = response.text
                print("Retorno da URL:")
                print(resultado)
            else:
                print(f"Erro ao acessar a URL. Código de status: {response.status_code}")
        except Exception as e:
            print(f"Ocorreu um erro: {e}")

        dt_final = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        cur.execute("UPDATE noticia_cliente SET send_print = true WHERE noticia_id = "+str(fonte['noticia_id']))
        con.commit() 
                
    except:
        print("Falha ao recuperar dados da fonte "+fonte['url'])