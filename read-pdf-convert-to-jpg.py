import pytesseract as ocr

from pdf2image import convert_from_path
from PIL import Image

imgs = convert_from_path("pdf/img.pdf", dpi=200)
for i, img in enumerate(imgs):
    file_name_img = "img/img_{0}.png".format(i)
    file_name_txt = "txt/txt_{0}.txt".format(i)
    img.save(file_name_img, "PNG")
    phrase = ocr.image_to_string(Image.open(file_name_img), lang='por')
    file_object = open(file_name_txt, 'w')
    file_object.write(phrase)
    file_object.close()
print("Processamento concluído")