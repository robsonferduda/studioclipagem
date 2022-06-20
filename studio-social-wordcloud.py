import csv
import re
import json
import psycopg2
import psycopg2.extras
import numpy as np
import pandas as pd
from wordcloud import WordCloud, STOPWORDS, ImageColorGenerator
import nltk
import unicodedata
nltk.download('stopwords')

con = psycopg2.connect(host='162.241.40.125', database='studiosocial',user='postgres', password='DMK@rr19')

cur = con.cursor(cursor_factory = psycopg2.extras.RealDictCursor)

sql = 'select * from clients'

cur.execute(sql)
clients = cur.fetchall()

for client in clients:

    sql = 'select 	caption from medias t0  where t0.deleted_at is null and t0.client_id ='+str(client['id'])
    cur.execute(sql)
    medias = cur.fetchall()

    sql = 'select full_text from media_twitter t0  where t0.deleted_at is null and t0.client_id = '+str(client['id'])
    cur.execute(sql)
    medias_t = cur.fetchall()

    sql = 'select message from fb_posts t0 where t0.deleted_at is null and t0.client_id = '+str(client['id'])
    cur.execute(sql)
    medias_f = cur.fetchall()

    sql = 'select message from fb_page_posts t0 inner join page_post_term t1 on (t0.id = t1.page_post_id) inner join terms t2 on (t1.term_id = t2.id) where t0.deleted_at is null and t2.client_id = '+str(client['id'])
    cur.execute(sql)
    medias_fpp = cur.fetchall()

    sql = 'select text from fb_page_posts_comments t0 inner join page_post_comment_term t1 on (t0.id = t1.page_post_comment_id) inner join terms t2 on (t1.term_id = t2.id) where t0.deleted_at is null and t2.client_id = '+str(client['id'])
    cur.execute(sql)
    medias_fppc = cur.fetchall()

    sql = 'select t0.text from  ig_comments t0 inner join medias t2 on (t0.media_id = t2.id) where t0.deleted_at is null and t2.client_id = '+str(client['id'])
    cur.execute(sql)
    comments_i = cur.fetchall()

    sql = 'select * from fb_comments t0 inner join fb_posts t2 on (t0.post_id = t2.id) where t0.deleted_at is null and t2.client_id = '+str(client['id'])
    cur.execute(sql)
    comments_f = cur.fetchall()

    text_t = ''
    if len(medias_t) > 0:
        df_t = pd.DataFrame (medias_t)
        text_t = df_t.dropna(subset=['full_text'], axis=0)['full_text']

    text_f = ''
    if len(medias_f) > 0:
        df_f = pd.DataFrame (medias_f)
        text_f = df_f.dropna(subset=['message'], axis=0)['message']

    text_i = ''
    if len(medias) > 0:
        df = pd.DataFrame (medias)
        text_i = df.dropna(subset=['caption'], axis=0)['caption']

    text_ci = ''
    if len(comments_i) > 0:
        df = pd.DataFrame (comments_i)
        text_ci = df.dropna(subset=['text'], axis=0)['text']

    text_cf = ''
    if len(comments_f) > 0:
        df = pd.DataFrame (comments_f)
        text_cf = df.dropna(subset=['text'], axis=0)['text']

    text_fpp = ''
    if len(medias_fpp) > 0:
        df = pd.DataFrame (medias_fpp)
        text_fpp = df.dropna(subset=['message'], axis=0)['message']

    text_fppc = ''
    if len(medias_fppc) > 0:
        df = pd.DataFrame (medias_fppc)
        text_fppc = df.dropna(subset=['text'], axis=0)['text']

    text_i = " ".join(c.lstrip("#").lower() for c in text_i)
    text_t = " ".join(c.lstrip("#").lower() for c in text_t)
    text_f = " ".join(c.lstrip("#").lower() for c in text_f)
    text_ci = " ".join(c.lstrip("#").lower() for c in text_ci)
    text_cf = " ".join(c.lstrip("#").lower() for c in text_cf)
    text_fpp = " ".join(c.lstrip("#").lower() for c in text_fpp)
    text_fppc = " ".join(c.lstrip("#").lower() for c in text_fppc)

    text = text_i+' '+text_t+' '+text_f+' '+text_ci+' '+text_cf+' '+text_fpp+' '+text_fppc

    text = unicodedata.normalize('NFKD', text).encode('ASCII', 'ignore').decode()

    extraStopWords = ["m²","pra","a","acerca","adeus","agora","ainda","alem","algmas","algo","algumas","alguns","ali","além","ambas","ambos","ano","anos","antes","ao","aonde","aos","apenas","apoio","apontar","apos","após","aquela","aquelas","aquele","aqueles","aqui","aquilo","as","assim","através","atrás","até","aí","baixo","bastante","bem","boa","boas","bom","bons","breve","cada","caminho","catorze","cedo","cento","certamente","certeza","cima","cinco","coisa","com","como","comprido","conhecido","conselho","contra","contudo","corrente","cuja","cujas","cujo","cujos","custa","cá","da","daquela","daquelas","daquele","daqueles","dar","das","de","debaixo","dela","delas","dele","deles","demais","dentro","depois","desde","desligado","dessa","dessas","desse","desses","desta","destas","deste","destes","deve","devem","deverá","dez","dezanove","dezasseis","dezassete","dezoito","dia","diante","direita","dispoe","dispoem","diversa","diversas","diversos","diz","dizem","dizer","do","dois","dos","doze","duas","durante","dá","dão","dúvida","e","ela","elas","ele","eles","em","embora","enquanto","entao","entre","então","era","eram","essa","essas","esse","esses","esta","estado","estamos","estar","estará","estas","estava","estavam","este","esteja","estejam","estejamos","estes","esteve","estive","estivemos","estiver","estivera","estiveram","estiverem","estivermos","estivesse","estivessem","estiveste","estivestes","estivéramos","estivéssemos","estou","está","estás","estávamos","estão","eu","exemplo","falta","fará","favor","faz","fazeis","fazem","fazemos","fazer","fazes","fazia","faço","fez","fim","final","foi","fomos","for","fora","foram","forem","forma","formos","fosse","fossem","foste","fostes","fui","fôramos","fôssemos","geral","grande","grandes","grupo","ha","haja","hajam","hajamos","havemos","havia","hei","hoje","hora","horas","houve","houvemos","houver","houvera","houveram","houverei","houverem","houveremos","houveria","houveriam","houvermos","houverá","houverão","houveríamos","houvesse","houvessem","houvéramos","houvéssemos","há","hão","iniciar","inicio","ir","irá","isso","ista","iste","isto","já","lado","lhe","lhes","ligado","local","logo","longe","lugar","lá","maior","maioria","maiorias","mais","mal","mas","me","mediante","meio","menor","menos","meses","mesma","mesmas","mesmo","mesmos","meu","meus","mil","minha","minhas","momento","muito","muitos","máximo","mês","na","nada","nao","naquela","naquelas","naquele","naqueles","nas","nem","nenhuma","nessa","nessas","nesse","nesses","nesta","nestas","neste","nestes","no","noite","nome","nos","nossa","nossas","nosso","nossos","nova","novas","nove","novo","novos","num","numa","numas","nunca","nuns","não","nível","nós","número","o","obra","obrigada","obrigado","oitava","oitavo","oito","onde","ontem","onze","os","ou","outra","outras","outro","outros","para","parece","parte","partir","paucas","pegar","pela","pelas","pelo","pelos","perante","perto","pessoas","pode","podem","poder","poderá","podia","pois","ponto","pontos","por","porque","porquê","portanto","posição","possivelmente","posso","possível","pouca","pouco","poucos","povo","primeira","primeiras","primeiro","primeiros","promeiro","propios","proprio","própria","próprias","próprio","próprios","próxima","próximas","próximo","próximos","puderam","pôde","põe","põem","quais","qual","qualquer","quando","quanto","quarta","quarto","quatro","que","q","quem","quer","quereis","querem","queremas","queres","quero","questão","quieto","quinta","quinto","quinze","quáis","quê","relação","sabe","sabem","saber","se","segunda","segundo","sei","seis","seja","sejam","sejamos","sem","sempre","sendo","ser","serei","seremos","seria","seriam","será","serão","seríamos","sete","seu","seus","sexta","sexto","sim","sistema","sob","sobre","sois","somente","somos","sou","sua","suas","são","sétima","sétimo","só","tal","talvez","tambem","também","tanta","tantas","tanto","tarde","te","tem","temos","tempo","tendes","tenha","tenham","tenhamos","tenho","tens","tentar","tentaram","tente","tentei","ter","terceira","terceiro","terei","teremos","teria","teriam","terá","terão","teríamos","teu","teus","teve","tinha","tinham","tipo","tive","tivemos","tiver","tivera","tiveram","tiverem","tivermos","tivesse","tivessem","tiveste","tivestes","tivéramos","tivéssemos","toda","todas","todo","todos","trabalhar","trabalho","treze","três","tu","tua","tuas","tudo","tão","tém","têm","tínhamos","um","uma","umas","uns","usa","usar","vai","vais","valor","veja","vem","vens","ver","verdade","verdadeiro","vez","vezes","viagem","vindo","vinte","você","vc","vocês","vos","vossa","vossas","vosso","vossos","vários","vão","vêm","vós","zero","à","às","área","é","éramos","és","último"]
   
    stopwords = set(STOPWORDS)
    stopwords.update(extraStopWords)
    
    stopwords.union(nltk.corpus.stopwords.words('portuguese'))

    text = re.sub(r"www\S+", " ", text)
    text = re.sub(r"http\S+", " ", text)
    text = re.sub(r"#([a-zA-Z0-9_]{1,50})", " ", text)
     
    wordcloud_words = WordCloud(stopwords=stopwords, random_state=1).process_text(text)

    with open("storage/app/wordcloud/files/cliente-"+str(client['id'])+"-wordclould.json", "w") as outfile:
        json.dump(wordcloud_words, outfile)

    #with open("storage/app/wordcloud/files/cliente-"+str(client['id'])+"-wordclould.text", "w") as outfile:
        #json.dump(text, outfile)

    #if len(wordcloud_words) > 0:
        #wordcloud = WordCloud(width = 3000, height = 2000, random_state=1, background_color='white', colormap='Set2', stopwords = stopwords).generate(text)
        #wordcloud.to_file("storage/app/wordcloud/files/cliente-"+str(client['id'])+"-wordclould.png")
    

