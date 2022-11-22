import pytesseract as ocr
import os

from pdf2image import convert_from_path
from PIL import Image

def create_path(img, txt):
    os.makedirs(img)
    os.makedirs(txt)

pasta_pendentes = 'public/jornal-impresso/pendentes'

for diretorio, subpastas, arquivos in os.walk(pasta_pendentes):
    for arquivo in arquivos:
        imgs = convert_from_path(os.path.join(diretorio, arquivo), dpi=200)
        dados_arquivo = arquivo.split('_')
        pasta_data = dados_arquivo[0]
        pasta_id = dados_arquivo[1]

        path_img = "public/jornal-impresso/"+pasta_id+"/"+pasta_data+"/img/"
        path_txt = "public/jornal-impresso/"+pasta_id+"/"+pasta_data+"/txt/"
        
        if not os.path.exists(path_img):
            os.makedirs(path_img)

        if not os.path.exists(path_txt):
            os.makedirs(path_txt)
                
        for i, img in enumerate(imgs):
            file_name_img = path_img+"pagina_{0}.png".format(i)
            file_name_txt = path_txt+"pagina_{0}.txt".format(i)
            img.save(file_name_img, "PNG")
            texto = ocr.image_to_string(Image.open(file_name_img), lang='por')
            file_object = open(file_name_txt, 'w')
            file_object.write(texto)
            file_object.close()
print("Processamento concluido")