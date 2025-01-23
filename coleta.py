import boto3
import botocore.exceptions  # Para capturar erros específicos do boto3
import mysql.connector
from mysql.connector import Error
import os
from PIL import Image
import io

#FILE_URL=http://clipagens.com.br/fmanager/clipagem/
#pasta_servidor = /public_html/fmanager/clipagem/web/
#arquivo(noticia_id)_1.jpg

aws_access_key_id = 'AKIAXH7FCUIUMZ7NFM5Q'
aws_secret_access_key = '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'
region_name = 'us-east-1'

BUCKET_NAME = 'docmidia-files'

s3 = boto3.client('s3',
                aws_access_key_id=aws_access_key_id,
                aws_secret_access_key=aws_secret_access_key,
                region_name=region_name)

try:
   
    connection = mysql.connector.connect(
        host='131.196.172.2',         
        database='studiocl_studioclipagem', 
        user='studiocl_stdclip',      
        password='mRI1IeT=Kqr@'   
    )

    if connection.is_connected():
    
        cursor = connection.cursor()
        query = "SELECT id, printurl FROM app_web WHERE id_knewin < 1000000 AND link_arquivo IS NULL"
        cursor.execute(query)

        for row in cursor.fetchall():
            try:

                #variaveis_banco
                noticia_id = str(row[0])
                print_url = str(row[1])

                #objeto_download
                parts = print_url.split('/')
                OBJECT_KEY = '/'.join(parts[3:])

                print(OBJECT_KEY) 

                response = s3.get_object(Bucket=BUCKET_NAME, Key=OBJECT_KEY)
                image_data = response['Body'].read()
                
                image = Image.open(io.BytesIO(image_data))
                if image.mode in ('RGBA', 'LA'):
                    background = Image.new('RGB', image.size, (255, 255, 255))
                    background.paste(image, mask=image.split()[-1])
                    image = background
                                
                DOWNLOAD_PATH = '/home/studioclipagemco/public_html/fmanager/clipagem/web/arquivo'+noticia_id+'_1.jpg'
                
                s3.download_file(BUCKET_NAME, OBJECT_KEY, DOWNLOAD_PATH)

                #se der certo, atualiza a tabela de notícias 
                campo_atualizacao = 'extraido'
                update_query = "UPDATE app_web SET link_arquivo = %s WHERE id = %s"
                valores = (campo_atualizacao, noticia_id)

                cursor.execute(update_query, valores)

            except Exception as e:
                print(f"Erro inesperado: {type(e).__name__} - {e}")

except Error as e:
    print(f"Erro ao conectar ao MySQL: {e}")

finally:
    if connection.is_connected():
        cursor.close()
        connection.commit()
        connection.close()
        print("Conexão com o MySQL foi encerrada.")