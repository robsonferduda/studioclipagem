import os
import speech_recognition as sr
import datetime

from pydub import AudioSegment
from pydub.utils import make_chunks

pasta_pendentes = 'public/radio/pendentes'
pasta_temporaria = 'public/radio/temporaria'
pasta_processados = 'public/radio/processados'

def transcreve_audio(nome_audio):
    r = sr.Recognizer()
    with sr.AudioFile(nome_audio) as source:
        audio = r.record(source)

    try:
        texto = r.recognize_google(audio,language='pt-BR')
    except sr.UnknownValueError:
        texto = ''
    except sr.RequestError as e:
        texto = ''
    return texto

for diretorio, subpastas, arquivos in os.walk(pasta_pendentes):

    for arquivo in arquivos:
        dados_arquivo = arquivo.split('_')
        dt_arquivo = dados_arquivo[1]
        hr_arquivo = dados_arquivo[2]

        print(os.path.join(os.path.realpath(diretorio), arquivo))

        audio = AudioSegment.from_file(os.path.join(os.path.realpath(diretorio), arquivo), 'mp3')  
        tamanho = 60000
        partes_audio = []
        texto = ''  
        partes = make_chunks (audio, tamanho) 

        for i, parte in enumerate(partes):
            parte_name = 'parte{0}.wav'.format(i)
            partes_audio.append(parte_name)
            parte.export(pasta_temporaria+'/'+parte_name, format='wav')  

        texto = ''
        for parte in partes_audio:
            parte_texto = transcreve_audio(pasta_temporaria+'/'+parte)
            texto = texto + ' ' + parte_texto
            print(parte_texto)

        
print("Processamento concluido")