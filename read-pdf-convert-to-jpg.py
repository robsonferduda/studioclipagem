#pip3 install pytesseract

import pytesseract as ocr
import os
import psycopg2
import psycopg2.extras
import shutil

from pdf2image import convert_from_path
from PIL import Image
from decouple import config
from datetime import datetime

Image.MAX_IMAGE_PIXELS = 1000000000

def create_path(img, txt):
    os.makedirs(img)
    os.makedirs(txt)

pasta_pendentes = '/var/www/html/studioclipagem/public/jornal-impresso/pendentes'
pasta_andamento = '/var/www/html/studioclipagem/public/jornal-impresso/andamento'
pasta_falhas = '/var/www/html/studioclipagem/public/jornal-impresso/falhas'
pasta_processados = '/var/www/html/studioclipagem/public/jornal-impresso/processados'

host = config('DB_HOST')
database = config('DB_DATABASE')
user = config('DB_USERNAME')
password = config('DB_PASSWORD')

con = psycopg2.connect(host=host, database=database,user=user, password=password)
cur = con.cursor(cursor_factory = psycopg2.extras.RealDictCursor)

for diretorio, subpastas, arquivos in os.walk(pasta_pendentes):
    for arquivo in arquivos:
        print(arquivo)
        imgs = convert_from_path(os.path.join(diretorio, arquivo), dpi=200)
        dados_arquivo = arquivo.split('_')
        pasta_data = dados_arquivo[0]
        pasta_id = dados_arquivo[1]
        dt_formatada = pasta_data[:4]+"-"+ pasta_data[4:6]+"-"+pasta_data[6:]
        dt_atual = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

        sql = "SELECT id FROM fonte_impressa WHERE codigo = '"+pasta_id+"'"
        cur.execute(sql)
        id_fonte = cur.fetchone()['id']

        sql = "SELECT id FROM fila_impresso WHERE ds_arquivo = '"+arquivo+"'"
        cur.execute(sql)
        id_fila = cur.fetchone()['id']

        path_img = "public/jornal-impresso/"+pasta_id+"/"+pasta_data+"/img/"
        path_txt = "public/jornal-impresso/"+pasta_id+"/"+pasta_data+"/txt/"
        
        if not os.path.exists(path_img):
            os.makedirs(path_img)

        if not os.path.exists(path_txt):
            os.makedirs(path_txt)

        #Atualiza o status do arquivo, indicando que o mesmo foi processado   
        sql_update = "UPDATE fila_impresso SET start_at = '"+dt_atual+"' WHERE id_fonte = "+str(id_fonte)+" AND dt_arquivo = '"+dt_formatada+"'" 
        cur.execute(sql_update)
        con.commit()  

        #Move arquivo para a pasta de arquivos em andamento
        shutil.move(pasta_pendentes+'/'+arquivo, pasta_andamento+'/'+arquivo)
        
        try:
            for i, img in enumerate(imgs):
                i = i + 1;
                file_name_img = path_img+"pagina_{0}.png".format(i)
                file_name_txt = path_txt+"pagina_{0}.txt".format(i)
                img.save(file_name_img, "PNG")
                texto = ocr.image_to_string(Image.open(file_name_img), lang='por')
                titulo = texto[0:40]
                file_object = open(file_name_txt, 'w')
                file_object.write(texto)
                file_object.close()

                #sql = "INSERT INTO noticia_impresso (id_fonte, dt_clipagem, nu_pagina_atual, texto) VALUES("+pasta_id+",'"+dt_formatada+"',"+str(i)+",'"+texto+"')"
                cur.execute("INSERT INTO noticia_impresso (id_fonte, id_fila, dt_clipagem, nu_pagina_atual, titulo, texto) VALUES(%s, %s, %s, %s, %s, %s)", (id_fonte, id_fila, dt_formatada, i, titulo, texto))
                con.commit() 

            sql = "UPDATE noticia_impresso SET nu_paginas_total = "+str(i)+" WHERE id_fonte = "+str(id_fonte)+" AND dt_clipagem = '"+dt_formatada+"'"
            cur.execute(sql)
            con.commit()     

            #Move arquivo para a pasta de arquivos processados
            shutil.move(pasta_andamento+'/'+arquivo, pasta_processados+'/'+arquivo)
            
            #Atualiza o status do arquivo, indicando que o mesmo foi processado   
            dt_fim_processamento = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            sql_update = "UPDATE fila_impresso SET fl_processado = true, updated_at = '"+dt_fim_processamento+"' WHERE id_fonte = "+str(id_fonte)+" AND dt_arquivo = '"+dt_formatada+"'" 
            cur.execute(sql_update)
            con.commit() 

            print("SUCESSO - Arquivo: "+arquivo)

        except Exception as e:
            #Move novamente o arquivo para a pasta de arquivos pendentes
            shutil.move(pasta_andamento+'/'+arquivo, pasta_pendentes+'/'+arquivo)

            #Atualiza o status do arquivo, indicando que o mesmo foi retornou para processamento   
            sql_update = "UPDATE fila_impresso SET start_at = null WHERE id_fonte = "+str(id_fonte)+" AND dt_arquivo = '"+dt_formatada+"'" 
            cur.execute(sql_update)
            con.commit() 

            print("ERRO - Arquivo: "+arquivo+" Erro:"+str(e))