import os
import tempfile
import paramiko
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import inch
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Image, KeepTogether, PageBreak
from reportlab.lib.enums import TA_CENTER, TA_LEFT, TA_RIGHT
from datetime import datetime
import locale
from PIL import Image as PILImage
import io

# Configuração de locale para formatação brasileira
try:
    locale.setlocale(locale.LC_ALL, 'pt_BR.UTF-8')
except:
    try:
        locale.setlocale(locale.LC_ALL, 'Portuguese_Brazil.1252')
    except:
        pass

class PDFGeneratorImpresso:
    def __init__(self):
        """Inicializa o gerador de PDF para impressos"""
        self.styles = getSampleStyleSheet()
        self._setup_custom_styles()
        self._setup_ssh_config()
        
    def _setup_custom_styles(self):
        """Configura estilos personalizados para o PDF"""
        # Estilo do título principal
        self.title_style = ParagraphStyle(
            'CustomTitle',
            parent=self.styles['Title'],
            fontSize=22,
            spaceAfter=30,
            textColor=colors.HexColor('#2c3e50'),
            alignment=TA_CENTER
        )
        
        # Estilo do subtítulo
        self.subtitle_style = ParagraphStyle(
            'CustomSubtitle',
            parent=self.styles['Heading1'],
            fontSize=16,
            textColor=colors.HexColor('#34495e'),
            spaceAfter=20,
            alignment=TA_CENTER
        )
        
        # Estilo do título da notícia
        self.noticia_title_style = ParagraphStyle(
            'NoticiaTitle',
            parent=self.styles['Heading2'],
            fontSize=14,
            textColor=colors.HexColor('#2c3e50'),
            spaceAfter=8,
            spaceBefore=20,
            alignment=TA_LEFT,
            fontName='Helvetica-Bold'
        )
        
        # Estilo do veículo e valor
        self.veiculo_valor_style = ParagraphStyle(
            'VeiculoValor',
            parent=self.styles['Normal'],
            fontSize=12,
            textColor=colors.HexColor('#7f8c8d'),
            spaceAfter=8,
            alignment=TA_LEFT,
            fontName='Helvetica-Bold'
        )
        
        # Estilo da descrição
        self.descricao_style = ParagraphStyle(
            'Descricao',
            parent=self.styles['Normal'],
            fontSize=10,
            textColor=colors.HexColor('#2c3e50'),
            spaceAfter=8,
            alignment=TA_LEFT,
            fontName='Helvetica'
        )
        
        # Estilo da data
        self.data_style = ParagraphStyle(
            'Data',
            parent=self.styles['Normal'],
            fontSize=10,
            textColor=colors.HexColor('#95a5a6'),
            spaceAfter=15,
            alignment=TA_LEFT,
            fontName='Helvetica-Oblique'
        )
    
    def _setup_ssh_config(self):
        """Configura as informações de conexão SSH"""
        self.ssh_host = 'ubuntu@ec2-54-91-187-59.compute-1.amazonaws.com'
        self.ssh_key_path = 'cadu.pem'
        self.remote_image_path = '/var/www/studioclipagem/public/img/noticia-impressa/'
    
    def _download_image_from_scp(self, ds_caminho_img):
        """
        Baixa a imagem do servidor via SSH/SCP usando paramiko para uma notícia específica (impressa)
        
        Args:
            ds_caminho_img: Nome do arquivo de imagem (ex: "35442173.jpg")
            
        Returns:
            tuple: (caminho_arquivo_temporario, sucesso)
        """
        ssh_client = None
        sftp_client = None
        
        try:
            if not ds_caminho_img or ds_caminho_img.strip() == '':
                print(f"❌ Nome do arquivo de imagem não informado")
                return None, False
            
            print(f"🔍 Tentando baixar imagem impressa via SSH/SCP: {ds_caminho_img}")
            
            # Constrói o caminho completo no servidor remoto
            remote_file_path = f"{self.remote_image_path}{ds_caminho_img}"
            
            # Cria arquivo temporário com a extensão correta
            file_extension = os.path.splitext(ds_caminho_img)[1]
            temp_file = tempfile.NamedTemporaryFile(delete=False, suffix=file_extension)
            temp_file.close()  # Fecha o arquivo para permitir escrita
            
            print(f"🔄 Conectando via SSH: {self.ssh_host}")
            
            # Configura cliente SSH
            ssh_client = paramiko.SSHClient()
            ssh_client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
            
            # Conecta usando a chave privada
            private_key = paramiko.RSAKey.from_private_key_file(self.ssh_key_path)
            ssh_client.connect(
                hostname='ec2-54-91-187-59.compute-1.amazonaws.com',
                username='ubuntu',
                pkey=private_key,
                timeout=60,
                banner_timeout=60
            )
            
            print(f"✅ Conectado via SSH")
            
            # Cria cliente SFTP
            sftp_client = ssh_client.open_sftp()
            
            print(f"🔄 Baixando arquivo: {remote_file_path} -> {temp_file.name}")
            
            # Baixa o arquivo
            sftp_client.get(remote_file_path, temp_file.name)
            
            # Verifica se o arquivo foi baixado com sucesso
            if os.path.exists(temp_file.name) and os.path.getsize(temp_file.name) > 0:
                print(f"✅ Imagem impressa baixada com sucesso via SSH/SCP: {temp_file.name} ({os.path.getsize(temp_file.name)} bytes)")
                return temp_file.name, True
            else:
                print(f"❌ Arquivo baixado está vazio ou não existe: {temp_file.name}")
                # Remove o arquivo temporário vazio
                try:
                    os.unlink(temp_file.name)
                except:
                    pass
                return None, False
            
        except paramiko.AuthenticationException:
            print(f"❌ Erro de autenticação SSH")
            return None, False
        except paramiko.SSHException as e:
            print(f"❌ Erro SSH: {str(e)}")
            return None, False
        except FileNotFoundError as e:
            print(f"❌ Arquivo não encontrado no servidor: {remote_file_path}")
            return None, False
        except Exception as e:
            print(f"❌ Erro ao baixar imagem impressa via SSH/SCP para arquivo {ds_caminho_img}: {str(e)}")
            # Remove o arquivo temporário em caso de erro
            try:
                if 'temp_file' in locals() and hasattr(temp_file, 'name'):
                    os.unlink(temp_file.name)
            except:
                pass
            return None, False
        finally:
            # Fecha conexões
            try:
                if sftp_client:
                    sftp_client.close()
                if ssh_client:
                    ssh_client.close()
                print(f"🔒 Conexões SSH fechadas")
            except:
                pass
    
    def _resize_image_for_pdf(self, image_path, max_width=4*inch, max_height=3*inch):
        """
        Redimensiona a imagem para caber no PDF
        
        Args:
            image_path: Caminho da imagem
            max_width: Largura máxima
            max_height: Altura máxima
            
        Returns:
            tuple: (largura, altura) ou None se erro
        """
        try:
            with PILImage.open(image_path) as img:
                width, height = img.size
                
                # Calcula proporção
                ratio = min(max_width / width, max_height / height)
                
                new_width = width * ratio
                new_height = height * ratio
                
                return new_width, new_height
                
        except Exception as e:
            print(f"❌ Erro ao redimensionar imagem: {str(e)}")
            return max_width, max_height
    
    def _format_currency(self, value):
        """Formata valor monetário em reais"""
        if value is None:
            return "R$ 0,00"
        try:
            return f"R$ {float(value):,.2f}".replace(',', 'X').replace('.', ',').replace('X', '.')
        except:
            return "R$ 0,00"
    
    def _format_date(self, date_value):
        """Formata data para exibição"""
        if date_value is None:
            return ""
        try:
            if isinstance(date_value, str):
                # Tenta diferentes formatos
                for fmt in ['%Y-%m-%d', '%d/%m/%Y', '%Y-%m-%d %H:%M:%S']:
                    try:
                        date_obj = datetime.strptime(date_value, fmt)
                        return date_obj.strftime('%d/%m/%Y')
                    except:
                        continue
                return date_value
            else:
                return date_value.strftime('%d/%m/%Y')
        except:
            return str(date_value)
    
    def _clean_text(self, text):
        """Remove HTML e caracteres especiais do texto"""
        if not text:
            return ""
        
        # Remove HTML básico
        import re
        text = re.sub(r'<[^>]*>', '', str(text))
        
        # Remove caracteres especiais
        text = text.replace('&nbsp;', ' ')
        text = text.replace('&amp;', '&')
        text = text.replace('&lt;', '<')
        text = text.replace('&gt;', '>')
        text = text.replace('&quot;', '"')
        
        return text.strip()
    
    def generate_impresso_report(self, noticias_data, cliente_nome, data_inicio, data_fim, output_path):
        """
        Gera relatório PDF específico para notícias impressas com imagens baixadas via SCP
        
        Args:
            noticias_data: Lista de notícias impressas
            cliente_nome: Nome do cliente
            data_inicio: Data de início do período
            data_fim: Data de fim do período
            output_path: Caminho para salvar o PDF
        """
        try:
            print(f"🎯 Iniciando geração do relatório de impressos...")
            print(f"📊 Total de notícias: {len(noticias_data)}")
            print(f"📁 Caminho de saída: {output_path}")
            print(f"👤 Cliente: {cliente_nome}")
            print(f"📅 Período: {data_inicio} a {data_fim}")
            
            # Cria o documento PDF
            print(f"📄 Criando documento PDF...")
            doc = SimpleDocTemplate(
                output_path,
                pagesize=A4,
                rightMargin=inch,
                leftMargin=inch,
                topMargin=inch,
                bottomMargin=inch
            )
            
            # Lista de elementos do PDF
            elements = []
            
            # Arquivos temporários para limpeza posterior
            temp_files = []
            
            print(f"✅ Estrutura do PDF inicializada")
            
            # Processa cada notícia
            for i, noticia in enumerate(noticias_data, 1):
                try:
                    print(f"📰 Processando notícia {i}/{len(noticias_data)}: ID {noticia.get('id', 'N/A')}")
                    
                    # Título da notícia
                    titulo = self._clean_text(noticia.get('titulo', 'Sem título'))
                    elements.append(Paragraph(f"{titulo}", self.noticia_title_style))
                    
                    # Veículo e valor
                    veiculo = noticia.get('veiculo', 'Veículo não informado')
                    veiculo_valor_text = f"{veiculo}"
                    elements.append(Paragraph(veiculo_valor_text, self.veiculo_valor_style))
                    
                    # Descrição
                    descricao = self._clean_text(noticia.get('texto', 'Sem descrição'))
                    if descricao:
                        # Limita a descrição a 500 caracteres
                        if len(descricao) > 500:
                            descricao = descricao[:500] + "..."
                        elements.append(Paragraph(descricao, self.descricao_style))
                    
                    # Data
                    data_noticia = self._format_date(noticia.get('data', ''))
                    if data_noticia:
                        elements.append(Paragraph(f"Data: {data_noticia}", self.data_style))
                    
                    # Tenta baixar e adicionar imagem via SCP
                    ds_caminho_img = noticia.get('ds_caminho_img')
                    if ds_caminho_img:
                        try:
                            print(f"🔄 Tentando baixar imagem para notícia {noticia.get('id')}: {ds_caminho_img}...")
                            #image_path, success = self._download_image_from_scp(ds_caminho_img) - Alteração para usar caminho remoto
                            image_path, success = self._download_image_from_scp(ds_caminho_img)
                            if success and image_path:
                                try:
                                    print(f"✅ Imagem baixada, processando dimensões...")
                                    # Calcula largura máxima da página (descontando margens)
                                    page_width = A4[0] - 2 * inch  # Largura da página menos margens esquerda e direita
                                    
                                    # Obtém dimensões originais da imagem
                                    with PILImage.open(image_path) as pil_img:
                                        original_width, original_height = pil_img.size
                                        print(f"📏 Dimensões originais: {original_width}x{original_height}")
                                    
                                    # Validação para evitar divisão por zero e valores inválidos
                                    if original_width <= 0 or original_height <= 0:
                                        print(f"❌ Dimensões inválidas da imagem: {original_width}x{original_height}")
                                        elements.append(Paragraph("❌ Imagem com dimensões inválidas", self.data_style))
                                    else:
                                        # Define altura máxima fixa para garantir que a imagem caiba na mesma página que o texto
                                        # Esta altura foi calculada para deixar espaço para o texto e garantir que não quebre a página
                                        MAX_IMAGE_HEIGHT = 6.5 * inch  # Altura máxima conservadora
                                        MAX_IMAGE_WIDTH = page_width    # Largura máxima é a largura da página
                                        
                                        print(f"📏 Limites máximos: {MAX_IMAGE_WIDTH:.2f}x{MAX_IMAGE_HEIGHT:.2f}")
                                        
                                        # Calcula proporção da imagem
                                        aspect_ratio = original_width / original_height
                                        
                                        # Começar com a altura máxima e calcular a largura proporcional
                                        new_height = min(MAX_IMAGE_HEIGHT, original_height)  # Não aumenta imagens pequenas
                                        new_width = new_height * aspect_ratio
                                        
                                        # Se a largura calculada excede o máximo, ajusta pela largura
                                        if new_width > MAX_IMAGE_WIDTH:
                                            print(f"⚠️  Largura calculada ({new_width:.2f}) excede máximo, ajustando...")
                                            new_width = MAX_IMAGE_WIDTH
                                            new_height = new_width / aspect_ratio
                                            
                                            # Garante que mesmo após ajuste pela largura, a altura não exceda o máximo
                                            if new_height > MAX_IMAGE_HEIGHT:
                                                print(f"⚠️  Altura ainda excede máximo após ajuste, forçando altura máxima...")
                                                new_height = MAX_IMAGE_HEIGHT
                                                new_width = new_height * aspect_ratio
                                        
                                        print(f"📐 Dimensões finais: {new_width:.2f}x{new_height:.2f}")
                                        print(f"✅ Imagem será exibida com altura máxima de {MAX_IMAGE_HEIGHT/inch:.1f} inches")
                                        
                                        # Adiciona imagem ao PDF com dimensões controladas
                                        img = Image(image_path, width=new_width, height=new_height)
                                        elements.append(img)
                                        print(f"✅ Imagem adicionada ao PDF com sucesso")
                                    
                                    # Adiciona arquivo à lista de limpeza
                                    temp_files.append(image_path)
                                    
                                except Exception as e:
                                    print(f"❌ Erro ao processar imagem {ds_caminho_img}: {str(e)}")
                                    import traceback
                                    traceback.print_exc()
                                    # Adiciona mensagem de erro no lugar da imagem
                                    elements.append(Paragraph("❌ Imagem não disponível", self.data_style))
                            else:
                                # Adiciona mensagem quando não há imagem
                                elements.append(Paragraph("📷 Sem imagem", self.data_style))
                        except Exception as e:
                            print(f"❌ Erro geral ao processar imagem {ds_caminho_img}: {str(e)}")
                            import traceback
                            traceback.print_exc()
                            elements.append(Paragraph("❌ Erro ao processar imagem", self.data_style))
                    
                    elements.append(Spacer(1, 20))
                    
                    # Força uma nova página para cada notícia (exceto a última)
                    if i < len(noticias_data):
                        print(f"📄 Adicionando quebra de página após notícia {i}")
                        elements.append(PageBreak())
                
                except Exception as e:
                    print(f"❌ Erro ao processar notícia {i} (ID: {noticia.get('id', 'N/A')}): {str(e)}")
                    import traceback
                    traceback.print_exc()
                    # Adiciona uma notícia de erro para não interromper o processamento
                    elements.append(Paragraph(f"❌ Erro ao processar notícia {i}", self.data_style))
                    elements.append(Spacer(1, 20))
                    # Também adiciona quebra de página após erro (exceto na última)
                    if i < len(noticias_data):
                        elements.append(PageBreak())
                    continue
            
            # Gera o PDF
            print(f"📄 Gerando arquivo PDF com {len(elements)} elementos...")
            try:
                doc.build(elements)
                print("✅ PDF construído com sucesso")
            except Exception as e:
                print(f"❌ Erro ao construir o PDF: {str(e)}")
                import traceback
                traceback.print_exc()
                raise e
            
            # Verifica se o arquivo foi criado
            if os.path.exists(output_path):
                file_size = os.path.getsize(output_path)
                print(f"✅ Arquivo PDF criado: {output_path} ({file_size} bytes)")
            else:
                print(f"❌ Arquivo PDF não foi criado: {output_path}")
                raise Exception("Arquivo PDF não foi gerado")
                        
            print(f"✅ Relatório de impressos gerado com sucesso: {output_path}")
            return True
            
        except Exception as e:
            print(f"❌ Erro ao gerar relatório de impressos: {str(e)}")
            import traceback
            print("🔍 Traceback completo:")
            traceback.print_exc()
            return False 