import csv
import re
import sys
import json
import psycopg2
import psycopg2.extras
from wordcloud import WordCloud, STOPWORDS, ImageColorGenerator
import nltk
import unicodedata
nltk.download('stopwords')

tipo = 'imagem'
cliente = sys.argv[1]
        
stopwords = ''

f = open ("/home/robson/repositorios/studiosocial/storage/app/hashtag/files/cliente-"+cliente+"-hashtag.json", "r")
wordcloud_words = json.loads(f.read())

if tipo == 'imagem' :
    if len(wordcloud_words) > 0:
        wordcloud = WordCloud(width = 3000, height = 2000, random_state=1, background_color='white', colormap='Set2', stopwords = stopwords).generate_from_frequencies(wordcloud_words)
        wordcloud.to_file("/home/robson/repositorios/studiosocial/storage/app/hashtag/files/images/cliente_"+cliente+"_hashtag.png")
print('END')