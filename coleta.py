import boto3
import os
from PIL import Image
import io

def download_and_convert_image(bucket_name, s3_key, output_path):
    """
    Downloads image from S3 and converts it to JPEG format
    
    Args:
        bucket_name (str): S3 bucket name
        s3_key (str): Path to image in S3
        output_path (str): Local path to save the JPEG image
    """
    s3_client = boto3.client('s3')
    
    try:
        # Download image from S3
        response = s3_client.get_object(Bucket='', Key=s3_key)
        image_data = response['Body'].read()
        
        # Convert to JPEG
        image = Image.open(io.BytesIO(image_data))
        if image.mode in ('RGBA', 'LA'):
            background = Image.new('RGB', image.size, (255, 255, 255))
            background.paste(image, mask=image.split()[-1])
            image = background
        
        # Create output directory if it doesn't exist
        os.makedirs(os.path.dirname(output_path), exist_ok=True)
        
        # Save as JPEG
        image.convert('RGB').save(output_path, 'JPEG', quality=95)
        
        return True
        
    except Exception as e:
        print(f"Erro: {str(e)}")
        return False

# Exemplo de uso
bucket = 'docmidia-files'
s3_key = 'caminho/para/imagem'
output_path = 'var/www/html/studiclipagem/imagem.jpg'

download_and_convert_image(bucket, s3_key, output_path)

