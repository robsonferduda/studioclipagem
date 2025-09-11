from reportlab.lib.pagesizes import A4, letter
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import inch
from reportlab.platypus import SimpleDocTemplate, Table, TableStyle, Paragraph, Spacer, Image, KeepTogether, PageBreak
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.platypus.tableofcontents import TableOfContents
import pandas as pd
from datetime import datetime
import locale
import io
import re
import html

# Configuração de locale para formatação brasileira
try:
    locale.setlocale(locale.LC_ALL, 'pt_BR.UTF-8')
except:
    try:
        locale.setlocale(locale.LC_ALL, 'Portuguese_Brazil.1252')
    except:
        pass  # Mantém locale padrão se não conseguir configurar

class PDFGenerator:
    def __init__(self):
        """Inicializa o gerador de PDF"""
        self.styles = getSampleStyleSheet()
        self._setup_custom_styles()
        self._setup_color_palette()
        
    def _setup_custom_styles(self):
        """Configura estilos personalizados para o PDF"""
        styles = getSampleStyleSheet()
        
        # Estilo do título principal
        self.title_style = ParagraphStyle(
            'CustomTitle',
            parent=styles['Title'],
            fontSize=24,
            spaceAfter=30,
            textColor=colors.HexColor('#2c3e50'),
            alignment=TA_CENTER
        )
        
        # Estilo do subtítulo
        self.subtitle_style = ParagraphStyle(
            'CustomSubtitle',
            parent=styles['Heading1'],
            fontSize=16,
            textColor=colors.HexColor('#34495e'),
            spaceAfter=20,
            alignment=TA_CENTER
        )
        
        # Estilo do título de seção
        self.section_title_style = ParagraphStyle(
            'SectionTitle',
            parent=styles['Heading2'],
            fontSize=14,
            textColor=colors.HexColor('#2c3e50'),
            spaceAfter=10,
            alignment=TA_LEFT
        )
        
        # Estilo do subtítulo de clipagem
        self.clipagem_subtitle_style = ParagraphStyle(
            'ClipAgemSubtitle',
            parent=styles['Heading2'],
            fontSize=14,
            spaceAfter=10,
            spaceBefore=15,
            textColor=colors.HexColor('#2980b9'),
            alignment=TA_LEFT
        )
        
        # Estilo normal para texto
        self.normal_style = ParagraphStyle(
            'CustomNormal',
            parent=styles['Normal'],
            fontSize=10,
            textColor=colors.HexColor('#2c3e50')
        )
    
    def _setup_color_palette(self):
        """Configura paleta de cores dinâmica para uso genérico"""
        # Paleta de cores diversificada e profissional
        self.color_palette = [
            '#3498db',  # Azul
            '#e74c3c',  # Vermelho
            '#2ecc71',  # Verde
            '#f39c12',  # Laranja
            '#9b59b6',  # Roxo
            '#1abc9c',  # Turquesa
            '#34495e',  # Azul escuro
            '#e67e22',  # Laranja escuro
            '#8e44ad',  # Roxo escuro
            '#16a085',  # Verde-azulado
            '#27ae60',  # Verde escuro
            '#d35400',  # Laranja queimado
            '#c0392b',  # Vermelho escuro
            '#2980b9',  # Azul médio
            '#7f8c8d',  # Cinza
            '#95a5a6',  # Cinza claro
            '#f1c40f',  # Amarelo
            '#e91e63',  # Rosa
            '#673ab7',  # Roxo profundo
            '#ff5722',  # Laranja avermelhado
            '#607d8b',  # Azul acinzentado
            '#795548',  # Marrom
            '#4caf50',  # Verde claro
            '#ff9800',  # Laranja vibrante
            '#9c27b0',  # Magenta
            '#00bcd4',  # Ciano
            '#ff6f00',  # Âmbar escuro
            '#388e3c',  # Verde floresta
            '#5d4037',  # Marrom escuro
            '#455a64'   # Cinza azulado
        ]
    
    def _convert_sentiment_to_text(self, sentiment_value):
        """
        Converte valor de sentimento de formato numérico para texto legível
        
        Args:
            sentiment_value: Valor do sentimento (-1, 0, 1, '-1', '0', '1')
            
        Returns:
            str: Texto do sentimento ('Negativo', 'Neutro', 'Positivo')
        """
        # Converte para string para normalizar
        sentiment_str = str(sentiment_value).strip()
        
        # Mapeamento de sentimentos
        sentiment_map = {
            '-1': 'Negativo',
            '0': 'Neutro', 
            '1': 'Positivo'
        }
        
        return sentiment_map.get(sentiment_str, 'Neutro')  # Default para Neutro se não encontrar
    
    def _get_dynamic_color(self, index: int):
        """Retorna cor da paleta baseada no índice, ciclando se necessário"""
        return colors.HexColor(self.color_palette[index % len(self.color_palette)])
    
    def _generate_color_scheme(self, data_count: int):
        """Gera esquema de cores para uma quantidade específica de dados"""
        if data_count <= len(self.color_palette):
            # Usa cores diretas da paleta
            return [self.color_palette[i] for i in range(data_count)]
        else:
            # Se precisar de mais cores, gera variações
            base_colors = []
            for i in range(data_count):
                base_color = self.color_palette[i % len(self.color_palette)]
                # Para cores repetidas, varia a saturação/luminosidade
                if i >= len(self.color_palette):
                    # Aplica variação na cor
                    variation = (i // len(self.color_palette)) * 20
                    # Simples variação adicionando/subtraindo valores hex
                    hex_color = base_color.lstrip('#')
                    r, g, b = tuple(int(hex_color[i:i+2], 16) for i in (0, 2, 4))
                    
                    # Varia a luminosidade
                    factor = 1 + (variation / 100)
                    r = min(255, max(0, int(r * factor)))
                    g = min(255, max(0, int(g * factor)))
                    b = min(255, max(0, int(b * factor)))
                    
                    base_color = f"#{r:02x}{g:02x}{b:02x}"
                
                base_colors.append(base_color)
            return base_colors
    
    def _create_generic_data_table(self, data: pd.DataFrame, table_title: str, columns_config: list, 
                                 table_type: str = "standard"):
        """
        Cria tabela genérica com cores dinâmicas - tabela contínua sem limitação de linhas
        
        Args:
            data: DataFrame com os dados
            table_title: Título da tabela (para logs)
            columns_config: Lista de dicts com configuração das colunas
                           [{'column': 'nome_coluna', 'header': 'Cabeçalho', 'width': largura_em_inch, 'format': 'currency'/'percentage'/'text'}]
            table_type: Tipo da tabela para determinação de cor base
        """
        if data.empty:
            # Retorna tabela vazia se não houver dados
            no_data_style = ParagraphStyle(
                'NoData',
                parent=self.normal_style,
                fontSize=10,
                textColor=colors.HexColor('#7f8c8d'),
                alignment=TA_CENTER
            )
            return Table([[Paragraph(f"Nenhum dado encontrado para {table_title}", no_data_style)]], 
                        colWidths=[6*inch])
        
        # Calcula totais dinâmicos baseado nas colunas numéricas
        totals = {}
        for col_config in columns_config:
            col_name = col_config['column']
            if col_name in data.columns:
                if col_config.get('format') == 'time':
                    # Soma especial para tempo
                    totals[col_name] = self._sum_time_column(data[col_name])
                elif col_config.get('format') in ['currency', 'number']:
                    totals[col_name] = data[col_name].sum()
        
        # Determina cor do cabeçalho baseada no tipo e posição
        header_color_index = hash(table_type) % len(self.color_palette)
        header_color = self._get_dynamic_color(header_color_index)
        
        # Cabeçalho da tabela
        headers = [col_config['header'] for col_config in columns_config]
        table_data = [headers]
        
        # Dados da tabela - TODOS os dados sem limitação
        for _, row in data.iterrows():
            row_data = []
            for col_config in columns_config:
                col_name = col_config['column']
                value = row.get(col_name, '')
                
                # Formatação baseada no tipo
                if col_config.get('format') == 'currency':
                    formatted_value = self._format_currency(value)
                elif col_config.get('format') == 'percentage':
                    total_col = col_config.get('percentage_total_column')
                    total_value = totals.get(total_col, 1)
                    formatted_value = self._format_percentage(value, total_value)
                elif col_config.get('format') == 'date':
                    formatted_value = self._format_date(value)
                elif col_config.get('format') == 'number':
                    # Formatação especial para números decimais
                    try:
                        if isinstance(value, (int, float)):
                            formatted_value = f"{value:.2f}" if value != int(value) else str(int(value))
                        else:
                            formatted_value = str(value)
                    except:
                        formatted_value = str(value)
                elif col_config.get('format') == 'time':
                    formatted_value = str(value)
                elif col_config.get('format') == 'sentiment':
                    # Formatação para valores de sentimento
                    formatted_value = self._convert_sentiment_to_text(value)
                else:
                    formatted_value = str(value)
                
                row_data.append(formatted_value)
            
            table_data.append(row_data)
        
        # Adiciona linha de total somente para tabelas que não são de retorno
        if not table_type.startswith('retorno'):
            total_row = []
            for i, col_config in enumerate(columns_config):
                if i == 0:  # Primeira coluna
                    total_row.append('Total Geral')
                else:
                    col_name = col_config['column']
                    if col_name in totals:
                        if col_config.get('format') == 'currency':
                            total_row.append(self._format_currency(totals[col_name]))
                        elif col_config.get('format') == 'percentage':
                            total_row.append('100.00%')
                        else:
                            total_row.append(str(totals[col_name]))
                    else:
                        total_row.append('')
            
            table_data.append(total_row)
        
        # Larguras das colunas
        col_widths = [col_config['width']*inch for col_config in columns_config]
        table = Table(table_data, colWidths=col_widths)
        
        # Determina se há linha de total
        has_total_row = not table_type.startswith('retorno')
        
        # Estilo da tabela com cor dinâmica
        table_styles = [
            # Cabeçalho com cor dinâmica
            ('BACKGROUND', (0, 0), (-1, 0), header_color),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, 0), 8),
            ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#bdc3c7')),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
        ]
        
        if has_total_row:
            # Estilos específicos para quando há linha de total
            table_styles.extend([
                # Corpo da tabela (exclui linha de total)
                ('FONTNAME', (0, 1), (-1, -2), 'Helvetica'),
                ('FONTSIZE', (0, 1), (-1, -2), 6),
                
                # Linha de total
                ('BACKGROUND', (0, -1), (-1, -1), colors.HexColor('#ecf0f1')),
                ('FONTNAME', (0, -1), (-1, -1), 'Helvetica-Bold'),
                ('FONTSIZE', (0, -1), (-1, -1), 6),
            ])
        else:
            # Estilos para quando NÃO há linha de total (tabelas de retorno)
            table_styles.extend([
                # Corpo da tabela (todas as linhas após cabeçalho)
                ('FONTNAME', (0, 1), (-1, -1), 'Helvetica'),
                ('FONTSIZE', (0, 1), (-1, -1), 6),
            ])
        
        table.setStyle(TableStyle(table_styles))
        
        return table
    
    def _sum_time_column(self, time_series):
        """Soma uma coluna de tempo no formato HH:MM:SS"""
        total_seconds = 0
        for time_str in time_series:
            try:
                h, m, s = map(int, str(time_str).split(':'))
                total_seconds += h * 3600 + m * 60 + s
            except:
                pass
        
        hours = total_seconds // 3600
        minutes = (total_seconds % 3600) // 60
        seconds = total_seconds % 60
        return f"{hours:02d}:{minutes:02d}:{seconds:02d}"
    
    def _format_currency(self, value):
        """Formata valor monetário"""
        try:
            return f"R$ {value:,.2f}".replace(',', 'X').replace('.', ',').replace('X', '.')
        except:
            return f"R$ {value}"
    
    def _format_percentage(self, value, total):
        """Formata porcentagem com 2 casas decimais"""
        if total <= 0:
            return "0.00%"
        percentage = (value / total) * 100
        return f"{percentage:.2f}%"
    
    def _format_date(self, date_value):
        """Formata data para padrão brasileiro dd/mm/aaaa"""
        try:
            # Se já é um objeto datetime
            if hasattr(date_value, 'strftime'):
                return date_value.strftime('%d/%m/%Y')
            # Se é string no formato ISO (YYYY-MM-DD)
            elif isinstance(date_value, str):
                if len(date_value) >= 10:  # Pelo menos YYYY-MM-DD
                    date_obj = datetime.strptime(date_value[:10], '%Y-%m-%d')
                    return date_obj.strftime('%d/%m/%Y')
            return str(date_value)
        except:
            return str(date_value)
    
    def _clean_html(self, text):
        """Remove tags HTML e converte entidades HTML"""
        if not text or pd.isna(text):
            return ""
        
        # Converte para string se não for
        text = str(text)
        
        # Remove tags HTML
        text = re.sub(r'<[^>]+>', '', text)
        
        # Converte entidades HTML
        text = html.unescape(text)
        
        # Remove quebras de linha desnecessárias
        text = re.sub(r'\s+', ' ', text)
        
        # Remove espaços extras
        text = text.strip()
        
        return text
    
    def _generate_public_media_link(self, ds_caminho_img, midia_tipo):
        """
        Gera link público da mídia baseado no tipo de mídia
        
        Args:
            ds_caminho_img: Nome do arquivo de mídia (ex: "35442173.jpg", "audio.mp3", "video.mp4")
            midia_tipo: Tipo da mídia ('TV', 'Rádio', 'Impresso', 'Web')
            
        Returns:
            str: URL pública da mídia ou string vazia se não aplicável
        """
        if not ds_caminho_img or ds_caminho_img.strip() == '' or midia_tipo == 'Web':
            return ""
        
        # URL base do sistema
        base_url = "https://studioclipagem.com/"
        
        # Mapeia tipo de mídia para pasta correspondente
        # TV e Rádio usam pastas de vídeo/áudio, não imagens
        midia_folders = {
            'TV': 'video/noticia-tv/',
            'Rádio': 'audio/noticia-radio/', 
            'Impresso': 'img/noticia-impressa/'
        }
        
        # Obtém pasta baseada no tipo de mídia
        folder = midia_folders.get(midia_tipo, '')
        
        if not folder:
            return ""
        
        # Constrói URL pública
        public_url = f"{base_url}{folder}{ds_caminho_img}"
        
        return public_url
    
    def _create_noticias_table(self, data: pd.DataFrame):
        """Cria tabela para seção de notícias"""
        # Calcula totais e percentuais
        total_noticias = data['quantidade'].sum()
        
        # Dados da tabela
        table_data = [['Mídia', 'Qtd.', '%']]
        
        for _, row in data.iterrows():
            percentage = self._format_percentage(row['quantidade'], total_noticias)
            table_data.append([
                row['midia'],
                str(row['quantidade']),
                percentage
            ])
        
        # Linha de total
        table_data.append([
            'Total Geral',
            str(total_noticias),
            '100.00%'
        ])
        
        table = Table(table_data, colWidths=[1.2*inch, 0.5*inch, 0.5*inch])  # Tabela mais compacta
        
        # Estilo da tabela moderno
        table.setStyle(TableStyle([
            # Cabeçalho
            ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#3498db')),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, 0), 9), 
            
            # Corpo da tabela
            ('FONTNAME', (0, 1), (-1, -2), 'Helvetica'),
            ('FONTSIZE', (0, 1), (-1, -2), 8),  
            ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#bdc3c7')),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
            
            # Linha de total
            ('BACKGROUND', (0, -1), (-1, -1), colors.HexColor('#ecf0f1')),
            ('FONTNAME', (0, -1), (-1, -1), 'Helvetica-Bold'),
            ('FONTSIZE', (0, -1), (-1, -1), 8),  # Reduzido
        ]))
        
        return table
    
    def _create_valores_table(self, data: pd.DataFrame):
        """Cria tabela para seção de valores - TAMANHO REDUZIDO"""
        # Calcula totais e percentuais
        total_valores = data['valor'].sum()
        
        # Dados da tabela com cabeçalho completo
        table_data = [['Mídia', 'Valor (R$)', 'Valor (%)']]
        
        for _, row in data.iterrows():
            percentage = self._format_percentage(row['valor'], total_valores)
            table_data.append([
                row['midia'],
                self._format_currency(row['valor']),
                percentage
            ])
        
        # Linha de total
        table_data.append([
            'Total Geral',
            self._format_currency(total_valores),
            '100.00%'
        ])
        
        # Cria tabela
        table = Table(table_data, colWidths=[1.0*inch, 1.0*inch, 0.5*inch])  # Tabela mais compacta
        
        # Estilo da tabela moderno
        table.setStyle(TableStyle([
            # Cabeçalho
            ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#3498db')),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, 0), 9), 
            
            # Corpo da tabela
            ('FONTNAME', (0, 1), (-1, -2), 'Helvetica'),
            ('FONTSIZE', (0, 1), (-1, -2), 8), 
            ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#bdc3c7')),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
            
            # Linha de total
            ('BACKGROUND', (0, -1), (-1, -1), colors.HexColor('#ecf0f1')),
            ('FONTNAME', (0, -1), (-1, -1), 'Helvetica-Bold'),
            ('FONTSIZE', (0, -1), (-1, -1), 8), 
        ]))
        
        return table
    
    def _process_tv_data_by_emissora(self, clipagens_data: pd.DataFrame):
        """Processa dados de TV agrupando por emissora"""
        if clipagens_data is None or clipagens_data.empty:
            return pd.DataFrame()
        
        # Dicionário para armazenar dados por emissora
        emissoras_data = {}
        
        for _, row in clipagens_data.iterrows():
            # Extrai nome da emissora - usa campo 'veiculo' diretamente dos dados
            emissora = str(row.get('veiculo', row.get('Veículo', 'Não identificada')))
            
            # Extrai duração diretamente dos dados (já formatada como HH:MM:SS)
            tempo_str = str(row.get('duracao', row.get('Duração', '00:00:00')))
            
            # Validação básica do tempo
            if not re.match(r'\d{2}:\d{2}:\d{2}', tempo_str):
                tempo_str = '00:00:00'
            
            # Converte tempo para segundos para soma
            def time_to_seconds(time_str):
                try:
                    h, m, s = map(int, time_str.split(':'))
                    return h * 3600 + m * 60 + s
                except:
                    return 0
            
            tempo_segundos = time_to_seconds(tempo_str)
            
            # Extrai valor
            valor = float(row.get('valor', row.get('Valor', 0))) if pd.notnull(row.get('valor', row.get('Valor', 0))) else 0
            
            # Agrega dados por emissora
            if emissora not in emissoras_data:
                emissoras_data[emissora] = {
                    'quantidade': 0,
                    'tempo_segundos': 0,
                    'valor': 0
                }
            
            emissoras_data[emissora]['quantidade'] += 1
            emissoras_data[emissora]['tempo_segundos'] += tempo_segundos
            emissoras_data[emissora]['valor'] += valor
        
        # Converte para DataFrame
        result_data = []
        for emissora, dados in emissoras_data.items():
            # Converte segundos de volta para formato HH:MM:SS
            total_segundos = dados['tempo_segundos']
            horas = total_segundos // 3600
            minutos = (total_segundos % 3600) // 60
            segundos = total_segundos % 60
            tempo_formatado = f"{horas:02d}:{minutos:02d}:{segundos:02d}"
            
            result_data.append({
                'Emissora': emissora,
                'Qtd.': dados['quantidade'],
                'Tempo': tempo_formatado,
                'Valor': dados['valor']
            })
        
        # Ordena por quantidade (decrescente)
        result_df = pd.DataFrame(result_data)
        if not result_df.empty:
            result_df = result_df.sort_values('Qtd.', ascending=False)
        
        return result_df
    
    def _process_tv_data_by_programa(self, clipagens_data: pd.DataFrame):
        """Processa dados de TV agrupando por programa"""
        if clipagens_data is None or clipagens_data.empty:
            return pd.DataFrame()
        
        # Dicionário para armazenar dados por programa
        programas_data = {}
        
        for _, row in clipagens_data.iterrows():
            # Extrai nome do programa - usa campo 'programa' diretamente dos dados
            programa = str(row.get('programa', row.get('Programa', 'Não identificado')))
            
            # Extrai duração diretamente dos dados (já formatada como HH:MM:SS)
            tempo_str = str(row.get('duracao', row.get('Duração', '00:00:00')))
            
            # Validação básica do tempo
            if not re.match(r'\d{2}:\d{2}:\d{2}', tempo_str):
                tempo_str = '00:00:00'
            
            # Converte tempo para segundos para soma
            def time_to_seconds(time_str):
                try:
                    h, m, s = map(int, time_str.split(':'))
                    return h * 3600 + m * 60 + s
                except:
                    return 0
            
            tempo_segundos = time_to_seconds(tempo_str)
            
            # Extrai valor
            valor = float(row.get('valor', row.get('Valor', 0))) if pd.notnull(row.get('valor', row.get('Valor', 0))) else 0
            
            # Agrega dados por programa
            if programa not in programas_data:
                programas_data[programa] = {
                    'quantidade': 0,
                    'tempo_segundos': 0,
                    'valor': 0
                }
            
            programas_data[programa]['quantidade'] += 1
            programas_data[programa]['tempo_segundos'] += tempo_segundos
            programas_data[programa]['valor'] += valor
        
        # Converte para DataFrame
        result_data = []
        for programa, dados in programas_data.items():
            # Converte segundos de volta para formato HH:MM:SS
            total_segundos = dados['tempo_segundos']
            horas = total_segundos // 3600
            minutos = (total_segundos % 3600) // 60
            segundos = total_segundos % 60
            tempo_formatado = f"{horas:02d}:{minutos:02d}:{segundos:02d}"
            
            result_data.append({
                'Programa': programa,
                'Qtd.': dados['quantidade'],
                'Tempo': tempo_formatado,
                'Valor': dados['valor']
            })
        
        # Ordena por quantidade (decrescente)
        result_df = pd.DataFrame(result_data)
        if not result_df.empty:
            result_df = result_df.sort_values('Qtd.', ascending=False)
        
        return result_df
    
    def _process_radio_data_by_emissora(self, clipagens_data: pd.DataFrame):
        """Processa dados de Rádio agrupando por emissora"""
        if clipagens_data is None or clipagens_data.empty:
            return pd.DataFrame()
        
        # Dicionário para armazenar dados por emissora
        emissoras_data = {}
        
        for _, row in clipagens_data.iterrows():
            # Extrai nome da emissora - usa campo 'veiculo' diretamente dos dados
            emissora = str(row.get('veiculo', row.get('Veículo', 'Não identificada')))
            
            # Extrai duração diretamente dos dados (já formatada como HH:MM:SS)
            tempo_str = str(row.get('duracao', row.get('Duração', '00:00:00')))
            
            # Validação básica do tempo
            if not re.match(r'\d{2}:\d{2}:\d{2}', tempo_str):
                tempo_str = '00:00:00'
            
            # Converte tempo para segundos para soma
            def time_to_seconds(time_str):
                try:
                    h, m, s = map(int, time_str.split(':'))
                    return h * 3600 + m * 60 + s
                except:
                    return 0
            
            tempo_segundos = time_to_seconds(tempo_str)
            
            # Extrai valor
            valor = float(row.get('valor', row.get('Valor', 0))) if pd.notnull(row.get('valor', row.get('Valor', 0))) else 0
            
            # Agrega dados por emissora
            if emissora not in emissoras_data:
                emissoras_data[emissora] = {
                    'quantidade': 0,
                    'tempo_segundos': 0,
                    'valor': 0
                }
            
            emissoras_data[emissora]['quantidade'] += 1
            emissoras_data[emissora]['tempo_segundos'] += tempo_segundos
            emissoras_data[emissora]['valor'] += valor
        
        # Converte para DataFrame
        result_data = []
        for emissora, dados in emissoras_data.items():
            # Converte segundos de volta para formato HH:MM:SS
            total_segundos = dados['tempo_segundos']
            horas = total_segundos // 3600
            minutos = (total_segundos % 3600) // 60
            segundos = total_segundos % 60
            tempo_formatado = f"{horas:02d}:{minutos:02d}:{segundos:02d}"
            
            result_data.append({
                'Emissora': emissora,
                'Qtd.': dados['quantidade'],
                'Tempo': tempo_formatado,
                'Valor': dados['valor']
            })
        
        # Ordena por quantidade (decrescente)
        result_df = pd.DataFrame(result_data)
        if not result_df.empty:
            result_df = result_df.sort_values('Qtd.', ascending=False)
        
        return result_df
    
    def _process_radio_data_by_programa(self, clipagens_data: pd.DataFrame):
        """Processa dados de Rádio agrupando por programa"""
        if clipagens_data is None or clipagens_data.empty:
            return pd.DataFrame()
        
        # Dicionário para armazenar dados por programa
        programas_data = {}
        
        for _, row in clipagens_data.iterrows():
            # Extrai nome do programa - usa campo 'programa' diretamente dos dados
            programa = str(row.get('programa', row.get('Programa', 'Não identificado')))
            
            # Extrai duração diretamente dos dados (já formatada como HH:MM:SS)
            tempo_str = str(row.get('duracao', row.get('Duração', '00:00:00')))
            
            # Validação básica do tempo
            if not re.match(r'\d{2}:\d{2}:\d{2}', tempo_str):
                tempo_str = '00:00:00'
            
            # Converte tempo para segundos para soma
            def time_to_seconds(time_str):
                try:
                    h, m, s = map(int, time_str.split(':'))
                    return h * 3600 + m * 60 + s
                except:
                    return 0
            
            tempo_segundos = time_to_seconds(tempo_str)
            
            # Extrai valor
            valor = float(row.get('valor', row.get('Valor', 0))) if pd.notnull(row.get('valor', row.get('Valor', 0))) else 0
            
            # Agrega dados por programa
            if programa not in programas_data:
                programas_data[programa] = {
                    'quantidade': 0,
                    'tempo_segundos': 0,
                    'valor': 0
                }
            
            programas_data[programa]['quantidade'] += 1
            programas_data[programa]['tempo_segundos'] += tempo_segundos
            programas_data[programa]['valor'] += valor
        
        # Converte para DataFrame
        result_data = []
        for programa, dados in programas_data.items():
            # Converte segundos de volta para formato HH:MM:SS
            total_segundos = dados['tempo_segundos']
            horas = total_segundos // 3600
            minutos = (total_segundos % 3600) // 60
            segundos = total_segundos % 60
            tempo_formatado = f"{horas:02d}:{minutos:02d}:{segundos:02d}"
            
            result_data.append({
                'Programa': programa,
                'Qtd.': dados['quantidade'],
                'Tempo': tempo_formatado,
                'Valor': dados['valor']
            })
        
        # Ordena por quantidade (decrescente)
        result_df = pd.DataFrame(result_data)
        if not result_df.empty:
            result_df = result_df.sort_values('Qtd.', ascending=False)
        
        return result_df
    
    def _process_impresso_data_by_jornal(self, clipagens_data: pd.DataFrame):
        """Processa dados de impresso agrupando por jornal"""
        if clipagens_data is None or clipagens_data.empty:
            return pd.DataFrame()
        
        # Dicionário para armazenar dados por jornal
        jornais_data = {}
        
        for _, row in clipagens_data.iterrows():
            # Extrai nome do jornal da linha1 (formato: "Data da clipagem: DD/MM/YYYY | Título | Jornal/UF | Seção")
            linha1 = str(row.get('titulo_linha1', ''))
            jornal = 'Não identificado'
            
            # Extrai jornal usando regex (após o segundo "|")
            jornal_match = re.search(r'\|[^|]*\|([^|]+)\|', linha1)
            if jornal_match:
                jornal = jornal_match.group(1).strip()
            
            # Extrai valor
            valor = float(row.get('valor', 0)) if pd.notnull(row.get('valor')) else 0
            
            # Calcula cm/coluna como no PHP antigo: coluna × altura
            coluna = float(row.get('coluna', 0)) if pd.notnull(row.get('coluna')) else 0
            altura = float(row.get('altura', 0)) if pd.notnull(row.get('altura')) else 0
            cm_coluna_calculado = coluna * altura
            
            # Se não tem campos separados, usa o campo cm_coluna direto (fallback)
            if cm_coluna_calculado == 0:
                cm_coluna_calculado = float(row.get('cm_coluna', 0)) if pd.notnull(row.get('cm_coluna')) else 0
            
            # Agrega dados por jornal
            if jornal not in jornais_data:
                jornais_data[jornal] = {
                    'quantidade': 0,
                    'cm_coluna': 0,
                    'valor': 0
                }
            
            jornais_data[jornal]['quantidade'] += 1
            jornais_data[jornal]['cm_coluna'] += cm_coluna_calculado
            jornais_data[jornal]['valor'] += valor
        
        # Converte para DataFrame
        result_data = []
        for jornal, dados in jornais_data.items():
            result_data.append({
                'Jornal': jornal,
                'Qtd.': dados['quantidade'],
                'Cm/Coluna': round(dados['cm_coluna'], 2),
                'Valor': dados['valor']
            })
        
        # Ordena por quantidade (decrescente)
        result_df = pd.DataFrame(result_data)
        if not result_df.empty:
            result_df = result_df.sort_values('Qtd.', ascending=False)
        
        return result_df
    
    def _process_web_data_by_site(self, clipagens_data: pd.DataFrame):
        """Processa dados de Web agrupando por site"""
        if clipagens_data is None or clipagens_data.empty:
            return pd.DataFrame()
        
        # Dicionário para armazenar dados por site
        sites_data = {}
        
        for _, row in clipagens_data.iterrows():
            # Extrai nome do site da estrutura: "Data da clipagem: DD/MM/YYYY | Título - SiteNome"
            linha1 = str(row.get('titulo_linha1', ''))
            site = 'Não identificado'
            
            # Formato específico Web: "Data da clipagem: 12/02/2025 | Título - SiteNome"
            # Extrai o site após o último " - "
            if linha1 and ' - ' in linha1:
                site_match = linha1.split(' - ')[-1].strip()
                if site_match:
                    site = site_match
            
            # Se não conseguir extrair do título, tenta outros campos
            if site == 'Não identificado':
                if row.get('site'):
                    # Se existe campo 'site' específico
                    site = str(row.get('site', 'Não identificado')).strip()
                elif row.get('veiculo'):
                    # Se existe campo 'veiculo'
                    site = str(row.get('veiculo', 'Não identificado')).strip()
                elif row.get('domain'):
                    # Se existe campo 'domain'
                    site = str(row.get('domain', 'Não identificado')).strip()
                else:
                    # Tenta extrair de URL se disponível
                    url = str(row.get('url', ''))
                    if url and url != 'nan':
                        # Extrai domínio da URL
                        url_match = re.search(r'https?://(?:www\.)?([^/]+)', url)
                        if url_match:
                            site = url_match.group(1).strip()
            
            # Extrai valor
            valor = float(row.get('valor', 0)) if pd.notnull(row.get('valor')) else 0
            
            # Agrega dados por site
            if site not in sites_data:
                sites_data[site] = {
                    'quantidade': 0,
                    'valor': 0
                }
            
            sites_data[site]['quantidade'] += 1
            sites_data[site]['valor'] += valor
        
        # Converte para DataFrame
        result_data = []
        for site, dados in sites_data.items():
            result_data.append({
                'Site': site,
                'Qtd.': dados['quantidade'],
                'Valor': dados['valor']
            })
        
        # Ordena por quantidade (decrescente)
        result_df = pd.DataFrame(result_data)
        if not result_df.empty:
            result_df = result_df.sort_values('Qtd.', ascending=False)
        
        return result_df
    
    def _create_tv_veiculos_table(self, tv_data: pd.DataFrame, mostrar_valores: bool = True):
        """Cria tabela para relatório por veículos - TV usando sistema genérico"""
        columns_config = [
            {'column': 'Emissora', 'header': 'Emissora', 'width': 2.5, 'format': 'text'},
            {'column': 'Qtd.', 'header': 'Qtd.', 'width': 1.0, 'format': 'number'},
            {'column': 'Tempo', 'header': 'Tempo', 'width': 1.2, 'format': 'time'},
        ]
        if mostrar_valores:
            columns_config.append({'column': 'Valor', 'header': 'Valor', 'width': 1.8, 'format': 'currency'})
        return self._create_generic_data_table(tv_data, "TV Veículos", columns_config, "tv_veiculos")
    
    def _create_tv_programas_table(self, tv_data: pd.DataFrame, mostrar_valores: bool = True):
        """Cria tabela para relatório por programas - TV usando sistema genérico"""
        columns_config = [
            {'column': 'Programa', 'header': 'Programa', 'width': 2.5, 'format': 'text'},
            {'column': 'Qtd.', 'header': 'Qtd.', 'width': 1.0, 'format': 'number'},
            {'column': 'Tempo', 'header': 'Tempo', 'width': 1.2, 'format': 'time'},
        ]
        if mostrar_valores:
            columns_config.append({'column': 'Valor', 'header': 'Valor', 'width': 1.8, 'format': 'currency'})
        return self._create_generic_data_table(tv_data, "TV Programas", columns_config, "tv_programas")
    
    def _create_radio_veiculos_table(self, radio_data: pd.DataFrame, mostrar_valores: bool = True):
        """Cria tabela para relatório por veículos - Rádio usando sistema genérico"""
        columns_config = [
            {'column': 'Emissora', 'header': 'Emissora', 'width': 2.5, 'format': 'text'},
            {'column': 'Qtd.', 'header': 'Qtd.', 'width': 1.0, 'format': 'number'},
            {'column': 'Tempo', 'header': 'Tempo', 'width': 1.2, 'format': 'time'},
        ]
        if mostrar_valores:
            columns_config.append({'column': 'Valor', 'header': 'Valor', 'width': 1.8, 'format': 'currency'})
        return self._create_generic_data_table(radio_data, "Rádio Veículos", columns_config, "radio_veiculos")
    
    def _create_radio_programas_table(self, radio_data: pd.DataFrame, mostrar_valores: bool = True):
        """Cria tabela para relatório por programas - Rádio usando sistema genérico"""
        columns_config = [
            {'column': 'Programa', 'header': 'Programa', 'width': 2.5, 'format': 'text'},
            {'column': 'Qtd.', 'header': 'Qtd.', 'width': 1.0, 'format': 'number'},
            {'column': 'Tempo', 'header': 'Tempo', 'width': 1.2, 'format': 'time'},
        ]
        if mostrar_valores:
            columns_config.append({'column': 'Valor', 'header': 'Valor', 'width': 1.8, 'format': 'currency'})
        return self._create_generic_data_table(radio_data, "Rádio Programas", columns_config, "radio_programas")
    
    def _create_impresso_veiculos_table(self, impresso_data: pd.DataFrame, mostrar_valores: bool = True):
        """Cria tabela para relatório por veículos - Impresso usando sistema genérico"""
        columns_config = [
            {'column': 'Jornal', 'header': 'Jornal', 'width': 2.5, 'format': 'text'},
            {'column': 'Qtd.', 'header': 'Qtd.', 'width': 1.0, 'format': 'number'},
            {'column': 'Cm/Coluna', 'header': 'Cm/Coluna', 'width': 1.2, 'format': 'number'},
        ]
        if mostrar_valores:
            columns_config.append({'column': 'Valor', 'header': 'Valor', 'width': 1.8, 'format': 'currency'})
        return self._create_generic_data_table(impresso_data, "Impresso Veículos", columns_config, "impresso_veiculos")
    
    def _create_web_veiculos_table(self, web_data: pd.DataFrame, mostrar_valores: bool = True):
        """Cria tabela para relatório por veículos - Web usando sistema genérico"""
        columns_config = [
            {'column': 'Site', 'header': 'Site', 'width': 4.0, 'format': 'text'},
            {'column': 'Qtd.', 'header': 'Qtd.', 'width': 1.0, 'format': 'number'},
        ]
        if mostrar_valores:
            columns_config.append({'column': 'Valor', 'header': 'Valor', 'width': 1.5, 'format': 'currency'})
        return self._create_generic_data_table(web_data, "Web Veículos", columns_config, "web_veiculos")
    
    def _process_sentimento_data(self, sentimento_data: pd.DataFrame, has_tempo=False):
        """
        Processa dados de sentimento por cidade para criar a tabela final
        
        Args:
            sentimento_data: DataFrame com dados de sentimento por cidade e tipo
            has_tempo: Se True, inclui processamento de tempo (para TV e Rádio)
            
        Returns:
            pd.DataFrame: DataFrame processado com colunas finais para a tabela
        """
        if sentimento_data.empty:
            return pd.DataFrame()
        
        # Agrupa por cidade, somando valores por sentimento
        cidade_summary = {}
        
        for _, row in sentimento_data.iterrows():
            cidade = row['cidade']
            sentimento = row['sentimento']
            quantidade = row['quantidade']
            valor = row['valor']
            
            if cidade not in cidade_summary:
                cidade_summary[cidade] = {
                    '1': {'quantidade': 0, 'valor': 0, 'tempo_segundos': 0},
                    '-1': {'quantidade': 0, 'valor': 0, 'tempo_segundos': 0},
                    '0': {'quantidade': 0, 'valor': 0, 'tempo_segundos': 0}
                }
            
            cidade_summary[cidade][sentimento]['quantidade'] += quantidade
            cidade_summary[cidade][sentimento]['valor'] += valor
            
            if has_tempo and 'tempo_segundos' in row:
                cidade_summary[cidade][sentimento]['tempo_segundos'] += row['tempo_segundos']
        
        # CALCULA TOTAIS GERAIS COMO NO PHP ANTIGO - para porcentagens das colunas
        total_geral_quantidade = sum(row['quantidade'] for _, row in sentimento_data.iterrows())
        total_geral_valor = sum(row['valor'] for _, row in sentimento_data.iterrows())
        
        # Converte para formato final da tabela
        result_data = []
        
        for cidade, sentimentos in cidade_summary.items():
            # Calcula totais da cidade
            total_quantidade_cidade = sum(s['quantidade'] for s in sentimentos.values())
            total_valor_cidade = sum(s['valor'] for s in sentimentos.values())
            total_tempo_segundos = sum(s['tempo_segundos'] for s in sentimentos.values()) if has_tempo else 0
            
            # Determina sentimento predominante por quantidade
            sentimento_predominante = max(sentimentos.keys(), key=lambda k: sentimentos[k]['quantidade'])
            
            # Formata tempo se necessário
            tempo_formatado = self._seconds_to_time_format(total_tempo_segundos) if has_tempo else "N/A"
            
            # Calcula percentual do valor por sentimento (para o gráfico vertical)
            valor_percentuais = {}
            if total_valor_cidade > 0:
                for sent, data in sentimentos.items():
                    valor_percentuais[sent] = (data['valor'] / total_valor_cidade) * 100
            else:
                valor_percentuais = {s: 0 for s in sentimentos.keys()}
            
            # Calcula percentual da quantidade por sentimento (para o gráfico vertical)
            quantidade_percentuais = {}
            if total_quantidade_cidade > 0:
                for sent, data in sentimentos.items():
                    quantidade_percentuais[sent] = (data['quantidade'] / total_quantidade_cidade) * 100
            else:
                quantidade_percentuais = {s: 0 for s in sentimentos.keys()}
            
            # CÁLCULO CORRETO DAS PORCENTAGENS COMO NO PHP ANTIGO
            # As colunas "Valor (%)" e "Qtd. (%)" mostram o percentual da cidade em relação ao total geral
            valor_percentual_geral = (total_valor_cidade / total_geral_valor * 100) if total_geral_valor > 0 else 0
            quantidade_percentual_geral = (total_quantidade_cidade / total_geral_quantidade * 100) if total_geral_quantidade > 0 else 0
            
            row_data = {
                'Cidade': cidade,
                'Tempo': tempo_formatado,
                'Valor (R$)': total_valor_cidade,
                'Valor (%)': f"{valor_percentual_geral:.1f}%",  # Percentual em relação ao total geral
                'Qtd.': total_quantidade_cidade,
                'Qtd. (%)': f"{quantidade_percentual_geral:.1f}%",  # Percentual em relação ao total geral
                'Sentimento_Predominante': self._convert_sentiment_to_text(sentimento_predominante)
            }
            
            result_data.append(row_data)
        
        # Ordena por valor total decrescente
        result_data.sort(key=lambda x: x['Valor (R$)'], reverse=True)
        
        return pd.DataFrame(result_data)
    
    def _seconds_to_time_format(self, seconds_value):
        """Converte segundos para formato HH:MM:SS - método específico para sentimentos"""
        if not seconds_value:
            return "00:00:00"
        
        try:
            # Trata tanto valores numéricos quanto string
            if isinstance(seconds_value, str):
                if seconds_value.strip() == '' or seconds_value.strip() == '0':
                    return "00:00:00"
                seconds = int(seconds_value)
            else:
                seconds = int(seconds_value)
            
            if seconds <= 0:
                return "00:00:00"
                
            hours = seconds // 3600
            minutes = (seconds % 3600) // 60
            secs = seconds % 60
            return f"{hours:02d}:{minutes:02d}:{secs:02d}"
        except:
            return "00:00:00"
    
    def _create_sentimento_bar_indicator(self, percentual_valor, width=30, height=10):
        """
        Cria um indicador de barra horizontal de 100% com cores condicionais
        
        Args:
            percentual_valor: Valor percentual da cidade (float)
            width: Largura da barra em pontos
            height: Altura da barra em pontos
            
        Returns:
            Table: Tabela com a barra indicadora
        """
        from reportlab.platypus import Table, TableStyle
        from reportlab.lib import colors
        
        # Garante que o percentual esteja entre 0 e 100
        percentual_valor = max(0, min(100, percentual_valor))
        
        # Define cores baseadas na lógica solicitada
        if percentual_valor >= 50:
            # >= 50%: Verde + Laranja (resto)
            cor_principal = colors.HexColor('#27ae60')    # Verde
            cor_restante = colors.HexColor('#f39c12')     # Laranja
        else:
            # < 50%: Verde + Vermelho (resto)
            cor_principal = colors.HexColor('#27ae60')    # Verde
            cor_restante = colors.HexColor('#e74c3c')     # Vermelho
        
        # Calcula larguras
        largura_principal = (percentual_valor / 100) * width
        largura_restante = width - largura_principal
        
        # Cria dados da tabela
        table_data = [[]]
        table_styles = []
        col_widths = []
        
        # Primeira célula (valor da cidade)
        if largura_principal > 0:
            table_data[0].append("")
            col_widths.append(largura_principal)
            table_styles.append(('BACKGROUND', (0, 0), (0, 0), cor_principal))
        
        # Segunda célula (resto até 100%)
        if largura_restante > 0:
            table_data[0].append("")
            col_widths.append(largura_restante)
            col_index = 1 if largura_principal > 0 else 0
            table_styles.append(('BACKGROUND', (col_index, 0), (col_index, 0), cor_restante))
        
        # Se não há dados, cria barra cinza
        if not col_widths:
            col_widths = [width]
            table_data = [[""]]
            table_styles = [('BACKGROUND', (0, 0), (0, 0), colors.lightgrey)]
        
        # Cria tabela
        bar_table = Table(table_data, colWidths=col_widths, rowHeights=[height])
        bar_table.setStyle(TableStyle([
            ('GRID', (0, 0), (-1, -1), 0, colors.white),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
        ] + table_styles))
        
        return bar_table
    
    def _create_sentimento_table(self, sentimento_data: pd.DataFrame, table_title: str, has_tempo=False):
        """
        Cria tabela de sentimentos com indicadores visuais
        
        Args:
            sentimento_data: DataFrame com dados processados de sentimento
            table_title: Título da tabela
            has_tempo: Se True, inclui coluna de tempo
            
        Returns:
            Table: Tabela formatada com sentimentos
        """
        if sentimento_data.empty:
            # Retorna tabela vazia se não houver dados
            no_data_style = ParagraphStyle(
                'NoData',
                parent=self.normal_style,
                fontSize=10,
                textColor=colors.HexColor('#7f8c8d'),
                alignment=TA_CENTER
            )
            return Table([[Paragraph(f"Nenhum dado de sentimento encontrado para {table_title}", no_data_style)]], 
                        colWidths=[6*inch])
        
        # Cabeçalho da tabela
        if has_tempo:
            headers = ['Cidade', 'Tempo', 'Valor (R$)', 'Valor (%)', '', 'Qtd.', 'Qtd. (%)', '']
            col_widths = [1.0*inch, 0.7*inch, 0.8*inch, 0.6*inch, 0.8*inch, 0.5*inch, 0.6*inch, 0.8*inch]
        else:
            headers = ['Cidade', 'Valor (R$)', 'Valor (%)', '', 'Qtd.', 'Qtd. (%)', '']
            col_widths = [1.2*inch, 0.8*inch, 0.7*inch, 0.8*inch, 0.5*inch, 0.7*inch, 0.8*inch]
        
        table_data = [headers]
        
        # Dados da tabela
        for _, row in sentimento_data.iterrows():
            row_data = []
            
            # Cidade
            row_data.append(str(row['Cidade']))
            
            # Tempo (se aplicável)
            if has_tempo:
                row_data.append(str(row['Tempo']))
            
            # Valor (R$)
            row_data.append(self._format_currency(row['Valor (R$)']))
            
            # Valor (%)
            row_data.append(str(row['Valor (%)']))
            
            # Nível de Sentimento - Valor (indicador visual)
            # Extrai o valor percentual da coluna "Valor (%)" para a barra
            valor_percentual_str = str(row['Valor (%)'])
            try:
                valor_percentual = float(valor_percentual_str.replace('%', ''))
                bar_indicator_valor = self._create_sentimento_bar_indicator(valor_percentual)
                row_data.append(bar_indicator_valor)
            except:
                row_data.append("N/A")
            
            # Quantidade
            row_data.append(str(int(row['Qtd.'])))
            
            # Quantidade (%)
            row_data.append(str(row['Qtd. (%)']))
            
            # Sentimento baseado em quantidade (indicador visual)
            # Extrai o valor percentual da coluna "Qtd. (%)" para a barra
            qtd_percentual_str = str(row['Qtd. (%)'])
            try:
                qtd_percentual = float(qtd_percentual_str.replace('%', ''))
                bar_indicator_qtd = self._create_sentimento_bar_indicator(qtd_percentual)
                row_data.append(bar_indicator_qtd)
            except:
                row_data.append("N/A")
            
            table_data.append(row_data)
        
        # Cria tabela
        table = Table(table_data, colWidths=col_widths)
        
        # Estilo da tabela
        header_color = self._get_dynamic_color(hash(table_title) % len(self.color_palette))
        
        table.setStyle(TableStyle([
            # Cabeçalho
            ('BACKGROUND', (0, 0), (-1, 0), header_color),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, 0), 8),
            
            # Corpo da tabela
            ('FONTNAME', (0, 1), (-1, -1), 'Helvetica'),
            ('FONTSIZE', (0, 1), (-1, -1), 7),
            ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#bdc3c7')),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
            
            # Alinhamento específico das colunas
            ('ALIGN', (0, 1), (0, -1), 'LEFT'),    # Cidade à esquerda
        ]))
        
        return table
    
    def _split_table_if_needed(self, table_data, max_rows_per_page=50):
        """
        Divide uma tabela grande em várias tabelas menores para evitar problemas de layout
        
        Args:
            table_data: Dados da tabela (incluindo cabeçalho)
            max_rows_per_page: Número máximo de linhas por página (excluindo cabeçalho)
            
        Returns:
            Lista de conjuntos de dados da tabela divididos
        """
        if len(table_data) <= max_rows_per_page + 1:  # +1 para o cabeçalho
            return [table_data]
        
        # Extrai cabeçalho
        header = table_data[0]
        data_rows = table_data[1:]
        
        # Divide os dados em chunks
        chunks = []
        for i in range(0, len(data_rows), max_rows_per_page):
            chunk = [header] + data_rows[i:i + max_rows_per_page]
            chunks.append(chunk)
        
        return chunks

    def _create_clipagens_table(self, data: pd.DataFrame, midia_tipo: str, mostrar_valores: bool = True):
        """Cria tabela para seção de clipagens com formato otimizado"""
        # Dados da tabela - condicional baseado em mostrar_valores
        if mostrar_valores:
            table_data = [['Clipagem', 'Valor (R$)']]
        else:
            table_data = [['Clipagem']]
        
        # Ordena por data
        date_column = 'Data' if 'Data' in data.columns else 'data'
        if date_column in data.columns:
            data = data.sort_values(date_column, ascending=True)
        
        # Função para truncar texto longo
        def truncate_text(text, max_length=500):
            """Trunca texto para evitar células muito grandes"""
            if not text or len(str(text)) <= max_length:
                return str(text)
            return str(text)[:max_length] + "..."
        
        # Função para limpar e formatar texto
        def clean_and_format_text(text):
            """Limpa HTML e formata texto para evitar problemas de layout"""
            if not text:
                return ""
            
            # Remove HTML e limpa o texto
            cleaned = self._clean_html(str(text))
            
            # Remove quebras de linha excessivas
            cleaned = ' '.join(cleaned.split())
            
            # Trunca se muito longo
            cleaned = truncate_text(cleaned, 400)
            
            return cleaned
        
        for _, row in data.iterrows():
            # Formata valor apenas se necessário
            if mostrar_valores:
                valor = self._format_currency(row.get('Valor', 0) or row.get('valor', 0))
            
            if midia_tipo in ['TV', 'Rádio']:
                # Formato para TV e Rádio (3 linhas bem definidas)
                linha1 = clean_and_format_text(row.get('Linha1 Data Programa Emissora', '') or row.get('linha1_data_programa_emissora', ''))
                arquivo = str(row.get('Linha2 Arquivo', '') or row.get('linha2_arquivo', '') or row.get('arquivo_url', '') or row.get('url', ''))
                sinopse = clean_and_format_text(row.get('Linha3 Sinopse', '') or row.get('linha3_sinopse', '') or row.get('sinopse', '') or row.get('descricao', ''))
                sinopse_text = sinopse if sinopse else "não disponível"
                
                # Gera link público da mídia
                # Prioriza ds_caminho_img que agora contém o campo correto (ds_caminho_video/ds_caminho_audio)
                ds_caminho = row.get('ds_caminho_img', '') or row.get('Caminho Imagem', '')
                arquivo_linha2 = row.get('Linha2 Arquivo', '') or row.get('linha2_arquivo', '')
                
                # Ignora valores inválidos como "Arquivo não disponível", "nan", etc.
                valores_invalidos = ['', 'nan', 'Arquivo não disponível', None]
                
                midia_file = ''
                # Prioridade 1: ds_caminho_img (agora contém ds_caminho_video ou ds_caminho_audio)
                if ds_caminho and ds_caminho not in valores_invalidos:
                    midia_file = ds_caminho
                # Prioridade 2: Linha2 Arquivo (fallback)
                elif arquivo_linha2 and arquivo_linha2 not in valores_invalidos:
                    midia_file = arquivo_linha2
                else:
                    # Fallback para outros campos
                    for campo in ['midia', 'arquivo_midia']:
                        campo_valor = row.get(campo, '')
                        if campo_valor and campo_valor not in valores_invalidos:
                            midia_file = campo_valor
                            break
                
                public_link = self._generate_public_media_link(midia_file, midia_tipo) if midia_file else ''
                
                # Formatação com estilo específico para cada linha
                linha1_formatted = f"<b>{linha1}</b>" if linha1 else "<b>Informação não disponível</b>"
                sinopse_formatted = f"Sinopse: {sinopse_text}"
                
                # Combina as linhas - inclui arquivo apenas se disponível
                if arquivo and arquivo != 'nan' and arquivo != '' and arquivo.strip() and arquivo != 'Arquivo não disponível':
                    arquivo_truncated = truncate_text(arquivo, 100)
                    arquivo_formatted = f"<font color='#666666'><i>Arquivo: {arquivo_truncated}</i></font>"
                    clipagem_text = f"{linha1_formatted}<br/>{arquivo_formatted}<br/>{sinopse_formatted}"
                else:
                    clipagem_text = f"{linha1_formatted}<br/>{sinopse_formatted}"
                
                # Adiciona link público se disponível
                if public_link:
                    public_link_formatted = f"<font color='#3498db'><link href='{public_link}'>visualizar</link></font>"
                    clipagem_text += f"<br/>{public_link_formatted}"
                
            else:  # Impresso e Web
                # Formato para Impresso e Web (2 linhas bem definidas)
                linha1 = clean_and_format_text(row.get('Título Linha 1', '') or row.get('titulo_linha1', ''))
                descricao = clean_and_format_text(row.get('Título Linha 2', '') or row.get('titulo_linha2', '') or row.get('descricao', '') or row.get('sinopse', ''))
                
                # Formatação com estilo específico
                linha1_formatted = f"<b>{linha1}</b>" if linha1 else "<b>Informação não disponível</b>"
                descricao_formatted = descricao if descricao else "Descrição não disponível"
                
                # Combina as 2 linhas
                clipagem_text = f"{linha1_formatted}<br/>{descricao_formatted}"
                
                if midia_tipo == 'Web':
                    # Para Web, adiciona URL da notícia (completa, sem truncar)
                    url_noticia = row.get('url', '') or row.get('link', '') or row.get('url_noticia', '')
                    if url_noticia:
                        url_formatted = f"<font color='#3498db'><link href='{url_noticia}'>visualizar</link></font>"
                        clipagem_text += f"<br/>{url_formatted}"
                else:
                    # Para Impresso, adiciona link público da imagem (completo, sem truncar)
                    ds_caminho_img = row.get('ds_caminho_img', '') or row.get('Caminho Imagem', '')
                    public_link = self._generate_public_media_link(ds_caminho_img, midia_tipo)
                    if public_link:
                        public_link_formatted = f"<font color='#3498db'><link href='{public_link}'>visualizar</link></font>"
                        clipagem_text += f"<br/>{public_link_formatted}"
            
            # Estilo customizado para clipagens com melhor quebra de linha
            clipagem_style = ParagraphStyle(
                'ClipAgemText',
                parent=self.normal_style,
                fontSize=8,
                textColor=colors.HexColor('#2c3e50'),
                spaceBefore=2,
                spaceAfter=2,
                leftIndent=5,
                rightIndent=5,
                # Propriedades para garantir que URLs longas apareçam completamente
                wordWrap='CJK',  # Permite quebra de linha em qualquer caractere
                splitLongWords=True,  # Permite quebrar palavras longas (como URLs)
                allowWidows=1,  # Permite linhas órfãs
                allowOrphans=1,  # Permite linhas viúvas
                leading=10,  # Espaçamento entre linhas
                hyphenationLang=None,  # Desabilita hifenização automática
                uriWasteReduce=0.1,  # Reduz "desperdício" para melhor quebra de URLs
                embeddedHyphenation=1,  # Permite hifenização manual em URLs
            )
            
            # Não trunca aqui para evitar cortar tags HTML - já foi truncado nos componentes individuais
            
            # Cria parágrafo com HTML interno para formatação
            clipagem_paragraph = Paragraph(clipagem_text, clipagem_style)
            
            # Adiciona linha à tabela - condicional baseado em mostrar_valores
            if mostrar_valores:
                table_data.append([clipagem_paragraph, valor])
            else:
                table_data.append([clipagem_paragraph])
        
        # Não divide a tabela - mostra todas as clipagens em uma tabela contínua
        # O ReportLab gerencia automaticamente as quebras de página
        
        # Cria tabela com larguras otimizadas - condicional baseado em mostrar_valores
        # Larguras aumentadas para garantir que URLs longas sejam totalmente visíveis
        if mostrar_valores:
            table = Table(table_data, colWidths=[5.8*inch, 1.0*inch])
        else:
            table = Table(table_data, colWidths=[6.8*inch])
        
        # Estilo da tabela com melhor aparência - condicional baseado em mostrar_valores
        if mostrar_valores:
            table.setStyle(TableStyle([
                # Cabeçalho
                ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#3498db')),
                ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
                ('ALIGN', (0, 0), (-1, 0), 'CENTER'),
                ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
                ('FONTSIZE', (0, 0), (-1, 0), 10),
                
                # Corpo da tabela
                ('FONTNAME', (0, 1), (-1, -1), 'Helvetica'),
                ('FONTSIZE', (0, 1), (-1, -1), 8),
                ('ALIGN', (0, 1), (0, -1), 'LEFT'),    # Clipagem alinhada à esquerda
                ('ALIGN', (1, 1), (1, -1), 'CENTER'),  # Valor centralizado
                ('VALIGN', (0, 0), (-1, -1), 'TOP'),   # Alinhamento vertical no topo
                ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#bdc3c7')),
                ('LEFTPADDING', (0, 1), (0, -1), 8),   # Padding esquerdo para clipagens
                ('RIGHTPADDING', (0, 1), (0, -1), 8),  # Padding direito para clipagens
                
                # Linhas alternadas com cores suaves
                ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, colors.HexColor('#f8f9fa')]),
                
                # Altura automática para acomodar URLs longas completamente
                ('ROWHEIGHT', (0, 1), (-1, -1), None),  # Altura automática
            ]))
        else:
            table.setStyle(TableStyle([
                # Cabeçalho
                ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#3498db')),
                ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
                ('ALIGN', (0, 0), (-1, 0), 'CENTER'),
                ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
                ('FONTSIZE', (0, 0), (-1, 0), 10),
                
                # Corpo da tabela
                ('FONTNAME', (0, 1), (-1, -1), 'Helvetica'),
                ('FONTSIZE', (0, 1), (-1, -1), 8),
                ('ALIGN', (0, 1), (0, -1), 'LEFT'),    # Clipagem alinhada à esquerda
                ('VALIGN', (0, 0), (-1, -1), 'TOP'),   # Alinhamento vertical no topo
                ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#bdc3c7')),
                ('LEFTPADDING', (0, 1), (0, -1), 8),   # Padding esquerdo para clipagens
                ('RIGHTPADDING', (0, 1), (0, -1), 8),  # Padding direito para clipagens
                
                # Linhas alternadas com cores suaves
                ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, colors.HexColor('#f8f9fa')]),
                
                # Altura automática para acomodar URLs longas completamente
                ('ROWHEIGHT', (0, 1), (-1, -1), None),  # Altura automática
            ]))
        
        return table
    
    def _create_section_with_chart(self, title: str, table, chart_buffer: io.BytesIO):
        """Cria uma seção com título, gráfico acima e tabela abaixo ocupando a página inteira"""
        elements = []
        
        # Espaçamento maior antes do novo título de relatório
        elements.append(Spacer(1, 40))  # Espaçamento maior entre relatórios
        
        # Subtítulo da seção posicionado acima
        subtitle = Paragraph(title, self.subtitle_style)
        
        # Gráfico posicionado acima da tabela
        chart_elements = []
        if chart_buffer:
            chart_buffer.seek(0)
            # Gráfico padronizado para todos os relatórios - mesmo tamanho do Web
            chart_img = Image(chart_buffer, width=7.5*inch, height=4.2*inch)
            
            # Centraliza o gráfico
            chart_table = Table([[chart_img]], colWidths=[8.0*inch])
            chart_table.setStyle(TableStyle([
                ('ALIGN', (0, 0), (0, 0), 'CENTER'),
                ('VALIGN', (0, 0), (0, 0), 'MIDDLE'),
                ('LEFTPADDING', (0, 0), (-1, -1), 0),
                ('RIGHTPADDING', (0, 0), (-1, -1), 0),
                ('TOPPADDING', (0, 0), (-1, -1), 0),
                ('BOTTOMPADDING', (0, 0), (-1, -1), 0),
            ]))
            
            chart_elements.append(chart_table)
        
        # Mantém título e gráfico juntos usando KeepTogether
        if chart_elements:
            title_and_chart = KeepTogether([subtitle, Spacer(1, 15), chart_elements[0]])
            elements.append(title_and_chart)
        else:
            elements.append(subtitle)
        
        elements.append(Spacer(1, 20))  # Espaço entre gráfico e tabela
        
        # Tabela abaixo do gráfico ocupando a página inteira
        elements.append(table)
        
        return elements
    
    def _create_tv_section_with_chart(self, title: str, table, chart_buffer: io.BytesIO):
        """Cria uma seção com título, gráfico acima e tabela abaixo com tamanho específico baseado no tipo"""
        elements = []
        
        # Espaçamento maior antes do novo título de relatório
        elements.append(Spacer(1, 40))  # Espaçamento maior entre relatórios
        
        # Subtítulo da seção posicionado acima
        subtitle = Paragraph(title, self.subtitle_style)
        
        # Gráfico posicionado acima da tabela
        chart_elements = []
        if chart_buffer:
            chart_buffer.seek(0)
            
            # Define tamanho reduzido para relatórios de TV e Rádio
            chart_img = Image(chart_buffer, width=5.5*inch, height=3.0*inch)
            
            # Centraliza o gráfico
            chart_table = Table([[chart_img]], colWidths=[6.0*inch])
            chart_table.setStyle(TableStyle([
                ('ALIGN', (0, 0), (0, 0), 'CENTER'),
                ('VALIGN', (0, 0), (0, 0), 'MIDDLE'),
                ('LEFTPADDING', (0, 0), (-1, -1), 0),
                ('RIGHTPADDING', (0, 0), (-1, -1), 0),
                ('TOPPADDING', (0, 0), (-1, -1), 0),
                ('BOTTOMPADDING', (0, 0), (-1, -1), 0),
            ]))
            
            chart_elements.append(chart_table)
        
        # Mantém título e gráfico juntos usando KeepTogether
        if chart_elements:
            title_and_chart = KeepTogether([subtitle, Spacer(1, 15), chart_elements[0]])
            elements.append(title_and_chart)
        else:
            elements.append(subtitle)
        
        elements.append(Spacer(1, 20))  # Espaço entre gráfico e tabela
        
        # Tabela abaixo do gráfico ocupando a página inteira
        elements.append(table)
        
        return elements
    
    def _create_clipagem_section(self, title: str, table):
        """Cria uma seção de clipagem com título e tabela"""
        elements = []
        
        # Subtítulo da seção de clipagem
        subtitle = Paragraph(title, self.clipagem_subtitle_style)
        
        # Mantém título junto com a tabela usando KeepTogether
        section_content = KeepTogether([subtitle, table])
        elements.append(section_content)
        
        return elements
    
    def generate_report(self, noticias_data: pd.DataFrame, valores_data: pd.DataFrame, 
                       charts_buffer: io.BytesIO, usuario_id: int, 
                       data_inicio: str, data_fim: str, output_path: str,
                       noticias_chart_buffer: io.BytesIO = None,
                       valores_chart_buffer: io.BytesIO = None,
                       clipagens_data=None, database_manager=None, filtros=None):
        """Gera o relatório completo em PDF"""
        
        print(f"Gerando relatório para o usuário {usuario_id}")
        print(f"Período: {data_inicio} a {data_fim}")
        print(f"Caminho de saída: {output_path}")
        
        # Verifica se deve mostrar seções de retorno baseado na nova flag
        mostrar_retorno_relatorio = filtros.get('mostrar_retorno_relatorio', True) if filtros else True
        tem_permissao_retorno = filtros.get('tem_permissao_retorno', True) if filtros else True
        
        # Se o cliente não tem permissão, força ocultar seções de retorno
        if not tem_permissao_retorno:
            mostrar_retorno_relatorio = False
        
        # NOVA LÓGICA: Controla seções de retorno baseado na permissão do cliente e escolha do usuário
        mostrar_secoes_retorno = mostrar_retorno_relatorio and tem_permissao_retorno
        mostrar_valores = mostrar_retorno_relatorio and tem_permissao_retorno
        
        # NOVA LÓGICA: Controla seções de análise de sentimento baseado na permissão do cliente e escolha do usuário
        mostrar_sentimento_relatorio = filtros.get('mostrar_sentimento_relatorio', True) if filtros else True
        tem_permissao_sentimento = filtros.get('tem_permissao_sentimento', True) if filtros else True
        
        # Se o cliente não tem permissão, força ocultar seções de sentimento
        if not tem_permissao_sentimento:
            mostrar_sentimento_relatorio = False
        
        # Controla seções de análise de sentimento baseado na permissão do cliente e escolha do usuário
        mostrar_secoes_sentimento = mostrar_sentimento_relatorio and tem_permissao_sentimento
        
        print(f"🔐 Cliente tem permissão para retorno: {tem_permissao_retorno}")
        print(f"🎯 Usuário quer mostrar retorno no relatório: {mostrar_retorno_relatorio}")
        print(f"📊 Mostrar seções de retorno: {mostrar_secoes_retorno}")
        print(f"💰 Mostrar valores: {mostrar_valores}")
        print(f"🧠 Cliente tem permissão para sentimento: {tem_permissao_sentimento}")
        print(f"🎭 Usuário quer mostrar sentimento no relatório: {mostrar_sentimento_relatorio}")
        print(f"📈 Mostrar seções de sentimento: {mostrar_secoes_sentimento}")
        print(f"🔎 DEBUG FILTROS SENTIMENTO: {filtros.get('mostrar_sentimento_relatorio') if filtros else 'Filtros None'}")
        print(f"🔎 DEBUG TEM PERMISSAO SENTIMENTO: {filtros.get('tem_permissao_sentimento') if filtros else 'Filtros None'}")
        
        # Lista de elementos do PDF
        story = []
        
        # Primeira página com layout otimizado (tabela à esquerda, gráfico à direita)
        if noticias_chart_buffer and valores_chart_buffer:
            primeira_pagina_elements = self._create_primeira_pagina_layout(
                noticias_data, valores_data, noticias_chart_buffer, valores_chart_buffer, mostrar_valores
            )
            for element in primeira_pagina_elements:
                story.append(element)
        else:
            # Fallback para layout tradicional se não houver gráficos
            title = Paragraph("Relatório Completo", self.title_style)
            story.append(title)
            story.append(Spacer(1, 30))
            
            # Seção de notícias
            noticias_table = self._create_noticias_table(noticias_data)
            subtitle = Paragraph("Notícias Encontradas", self.subtitle_style)
            story.append(subtitle)
            story.append(noticias_table)
            story.append(Spacer(1, 40))
            
            # Seção de valores - CONDICIONAL
            if mostrar_valores:
                valores_table = self._create_valores_table(valores_data)
                subtitle = Paragraph("Geral", self.subtitle_style)
                story.append(subtitle)
                story.append(valores_table)
            else:
                # Mensagem explicativa quando valores estão ocultos
                if not tem_permissao_retorno:
                    no_valores_msg = Paragraph(
                        "Seção de valores não disponível para este cliente", 
                        self.normal_style
                    )
                else:
                    no_valores_msg = Paragraph(
                        "Seção de valores oculta conforme solicitado", 
                        self.normal_style
                    )
                story.append(no_valores_msg)
        
        story.append(Spacer(1, 30))
        
        # Quebra de página antes das clipagens
        story.append(PageBreak())
        
        # Processar clipagens por mídia
        if clipagens_data is not None:
            # Se for DataFrame, converter para dicionário agrupado por mídia
            if isinstance(clipagens_data, pd.DataFrame):
                clipagens_dict = {}
                for midia in clipagens_data['Mídia'].unique():
                    midia_data = clipagens_data[clipagens_data['Mídia'] == midia].to_dict('records')
                    clipagens_dict[midia] = midia_data
                clipagens_data = clipagens_dict
            
            # Seção TV
            if 'TV' in clipagens_data and clipagens_data['TV']:
                tv_table = self._create_clipagens_table(pd.DataFrame(clipagens_data['TV']), 'TV', mostrar_valores)
                tv_section = self._create_clipagem_section("Clipagens de Mídia TV", tv_table)
                for element in tv_section:
                    story.append(element)
                story.append(Spacer(1, 15))
            
            # Seção Rádio
            if 'Rádio' in clipagens_data and clipagens_data['Rádio']:
                radio_table = self._create_clipagens_table(pd.DataFrame(clipagens_data['Rádio']), 'Rádio', mostrar_valores)
                radio_section = self._create_clipagem_section("Clipagens de Mídia Rádio", radio_table)
                for element in radio_section:
                    story.append(element)
                story.append(Spacer(1, 15))
            
            # Seção Impresso
            if 'Impresso' in clipagens_data and clipagens_data['Impresso']:
                impresso_table = self._create_clipagens_table(pd.DataFrame(clipagens_data['Impresso']), 'Impresso', mostrar_valores)
                impresso_section = self._create_clipagem_section("Clipagens de Mídia Impressa", impresso_table)
                for element in impresso_section:
                    story.append(element)
                story.append(Spacer(1, 15))
            
            # Seção Web
            if 'Web' in clipagens_data and clipagens_data['Web']:
                web_table = self._create_clipagens_table(pd.DataFrame(clipagens_data['Web']), 'Web', mostrar_valores)
                web_section = self._create_clipagem_section("Clipagens de Mídia Web", web_table)
                for element in web_section:
                    story.append(element)
                story.append(Spacer(1, 15))
        
        # NOVA SEÇÃO: Relatório por veículos - TV (ao final das clipagens)
        # NOVO: Verificar filtros de mídia para relatórios
        tipos_midia_filtrados_relatorios = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso']) if filtros else ['web', 'tv', 'radio', 'impresso']
        
        # Processa dados de TV por emissora
        if 'tv' in tipos_midia_filtrados_relatorios and clipagens_data and 'TV' in clipagens_data and clipagens_data['TV']:
            tv_veiculos_data = self._process_tv_data_by_emissora(pd.DataFrame(clipagens_data['TV']))
            
            if not tv_veiculos_data.empty:
                # Adiciona quebra de página antes do relatório de veículos
                story.append(PageBreak())
                
                # Cria gráfico de pizza para TV
                from chart_generator import ChartGenerator
                chart_gen = ChartGenerator()
                tv_chart_buffer = chart_gen.create_pie_chart(
                    tv_veiculos_data, "Relatório por veículos - TV", "Qtd.", label_column="Emissora"
                )
                
                # Cria tabela de veículos TV (tabela única)
                tv_veiculos_table = self._create_tv_veiculos_table(tv_veiculos_data, mostrar_valores)
                
                # Seção com gráfico e tabela
                tv_veiculos_section = self._create_tv_section_with_chart(
                    "Relatório por veículos - TV", tv_veiculos_table, tv_chart_buffer
                )
                for element in tv_veiculos_section:
                    story.append(element)
                
                story.append(Spacer(1, 10))
                
                # NOVA SEÇÃO: Relatório por programas - TV (abaixo do relatório de veículos)
                # Processa dados de TV por programa
                tv_programas_data = self._process_tv_data_by_programa(pd.DataFrame(clipagens_data['TV']))
                
                if not tv_programas_data.empty:
                    # Cria gráfico de pizza para programas de TV
                    tv_programas_chart_buffer = chart_gen.create_pie_chart(
                        tv_programas_data, "Relatório por programas - TV", "Qtd.", label_column="Programa"
                    )
                    
                    # Cria tabela de programas TV (tabela única)
                    tv_programas_table = self._create_tv_programas_table(tv_programas_data, mostrar_valores)
                    
                    # Seção com gráfico e tabela
                    tv_programas_section = self._create_tv_section_with_chart(
                        "Relatório por programas - TV", tv_programas_table, tv_programas_chart_buffer
                    )
                    for element in tv_programas_section:
                        story.append(element)
                    
                    story.append(Spacer(1, 10))
        
        # NOVA SEÇÃO: Relatório por veículos - Rádio (abaixo do relatório de programas TV)
        # Processa dados de Rádio por emissora
        if 'radio' in tipos_midia_filtrados_relatorios and clipagens_data and 'Rádio' in clipagens_data and clipagens_data['Rádio']:
            radio_veiculos_data = self._process_radio_data_by_emissora(pd.DataFrame(clipagens_data['Rádio']))
            
            if not radio_veiculos_data.empty:
                # Cria quebra de página se necessário
                if 'tv' not in tipos_midia_filtrados_relatorios:  # Se TV não foi processada, adiciona quebra
                    story.append(PageBreak())
                
                # Cria gráfico de pizza para emissoras de Rádio
                from chart_generator import ChartGenerator
                chart_gen = ChartGenerator()
                radio_chart_buffer = chart_gen.create_pie_chart(
                    radio_veiculos_data, "Relatório por veículos - Rádio", "Qtd.", label_column="Emissora"
                )
                
                # Cria tabela de veículos Rádio (tabela única)
                radio_veiculos_table = self._create_radio_veiculos_table(radio_veiculos_data, mostrar_valores)
                
                # Seção com gráfico e tabela
                radio_veiculos_section = self._create_tv_section_with_chart(
                    "Relatório por veículos - Rádio", radio_veiculos_table, radio_chart_buffer
                )
                for element in radio_veiculos_section:
                    story.append(element)
                
                story.append(Spacer(1, 10))
                
                # NOVA SEÇÃO: Relatório por programas - Rádio (abaixo do relatório de veículos - Rádio)
                # Processa dados de Rádio por programa
                radio_programas_data = self._process_radio_data_by_programa(pd.DataFrame(clipagens_data['Rádio']))
                
                if not radio_programas_data.empty:
                    # Cria gráfico de pizza para programas de Rádio
                    radio_programas_chart_buffer = chart_gen.create_pie_chart(
                        radio_programas_data, "Relatório por programas - Rádio", "Qtd.", label_column="Programa"
                    )
                    
                    # Cria tabela de programas Rádio (tabela única)
                    radio_programas_table = self._create_radio_programas_table(radio_programas_data, mostrar_valores)
                    
                    # Seção com gráfico e tabela - MÉTODO ESPECÍFICO PARA PROGRAMA RÁDIO
                    radio_programas_section = self._create_tv_section_with_chart(
                        "Relatório por programas - Rádio", radio_programas_table, radio_programas_chart_buffer
                    )
                    for element in radio_programas_section:
                        story.append(element)
                    
                    story.append(Spacer(1, 10))
        
        # NOVA SEÇÃO: Relatório por veículos - Impresso (abaixo do relatório de programas - Rádio)
        # Processa dados de Impresso por jornal
        if 'impresso' in tipos_midia_filtrados_relatorios and clipagens_data and 'Impresso' in clipagens_data and clipagens_data['Impresso']:
            impresso_veiculos_data = self._process_impresso_data_by_jornal(pd.DataFrame(clipagens_data['Impresso']))
            
            if not impresso_veiculos_data.empty:
                # Cria quebra de página se necessário
                if 'tv' not in tipos_midia_filtrados_relatorios and 'radio' not in tipos_midia_filtrados_relatorios:
                    story.append(PageBreak())
                
                # Cria gráfico de pizza para jornais
                from chart_generator import ChartGenerator
                chart_gen = ChartGenerator()
                impresso_chart_buffer = chart_gen.create_pie_chart(
                    impresso_veiculos_data, "Relatório por veículos - Impresso", "Qtd.", label_column="Jornal"
                )
                
                # Cria tabela de veículos Impresso (tabela única)
                impresso_veiculos_table = self._create_impresso_veiculos_table(impresso_veiculos_data, mostrar_valores)
                
                # Seção com gráfico e tabela - MÉTODO ESPECÍFICO PARA IMPRESSO VEÍCULOS
                impresso_veiculos_section = self._create_section_with_chart(
                    "Relatório por veículos - Impresso", impresso_veiculos_table, impresso_chart_buffer
                )
                for element in impresso_veiculos_section:
                    story.append(element)
                
                story.append(Spacer(1, 10))
                
        # NOVA SEÇÃO: Relatório por veículos - Web (após impresso)
        # Processa dados de Web por site
        if 'web' in tipos_midia_filtrados_relatorios and clipagens_data and 'Web' in clipagens_data and clipagens_data['Web']:
            web_veiculos_data = self._process_web_data_by_site(pd.DataFrame(clipagens_data['Web']))
            
            if not web_veiculos_data.empty:
                # Cria quebra de página se necessário
                if not any(midia in tipos_midia_filtrados_relatorios for midia in ['tv', 'radio', 'impresso']):
                    story.append(PageBreak())
                
                # Cria gráfico de pizza para sites
                from chart_generator import ChartGenerator
                chart_gen = ChartGenerator()
                web_chart_buffer = chart_gen.create_pie_chart(
                    web_veiculos_data, "Relatório por veículos - Web", "Qtd.", label_column="Site"
                )
                
                # Cria tabela de veículos Web (tabela única)
                web_veiculos_table = self._create_web_veiculos_table(web_veiculos_data, mostrar_valores)
                
                # Seção com gráfico e tabela - MÉTODO ESPECÍFICO PARA WEB VEÍCULOS
                web_veiculos_section = self._create_section_with_chart(
                    "Relatório por veículos - Web", web_veiculos_table, web_chart_buffer
                )
                for element in web_veiculos_section:
                    story.append(element)
                
                story.append(Spacer(1, 10))
        
        # NOVA LÓGICA: SEÇÕES DE RETORNO - Usa a variável já definida no início da função
        print(f"🔍 Permissão de retorno: {tem_permissao_retorno}")
        print(f"📊 Mostrar seções de retorno: {mostrar_secoes_retorno}")
        
        if database_manager and mostrar_secoes_retorno:
            # Quebra de página antes das seções de retorno
            story.append(PageBreak())
            
            # CORRIGIDO: Verificar filtros de mídia para retornos - agora só inclui tipos de mídia que o cliente tem permissão
            tipos_midia_filtrados = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso']) if filtros else ['web', 'tv', 'radio', 'impresso']
            
            # 1. Retornos de Mídia TV - só mostra se o cliente tem permissão para TV
            if 'tv' in tipos_midia_filtrados:
                retornos_tv = database_manager.get_retornos_tv(usuario_id, data_inicio, data_fim, filtros)
                if not retornos_tv.empty:
                    tv_retorno_tables = self._create_retorno_tv_table(retornos_tv)
                    tv_retorno_section = self._create_retorno_section("Retornos de Mídia TV", tv_retorno_tables)
                    for element in tv_retorno_section:
                        story.append(element)
                
            # REMOVIDO: Não mostra seção se o cliente não tem permissão para TV
            
            # 2. Retornos de Mídia Rádio - só mostra se o cliente tem permissão para Rádio
            if 'radio' in tipos_midia_filtrados:
                retornos_radio = database_manager.get_retornos_radio(usuario_id, data_inicio, data_fim, filtros)
                if not retornos_radio.empty:
                    radio_retorno_tables = self._create_retorno_radio_table(retornos_radio)
                    radio_retorno_section = self._create_retorno_section("Retornos de Mídia Rádio", radio_retorno_tables)
                    for element in radio_retorno_section:
                        story.append(element)
                
            # REMOVIDO: Não mostra seção se o cliente não tem permissão para Rádio
            
            # 3. Retornos de Mídia Web - só mostra se o cliente tem permissão para Web
            if 'web' in tipos_midia_filtrados:
                retornos_web = database_manager.get_retornos_web(usuario_id, data_inicio, data_fim, filtros)
                if not retornos_web.empty:
                    web_retorno_tables = self._create_retorno_web_table(retornos_web)
                    web_retorno_section = self._create_retorno_section("Retornos de Mídia Web", web_retorno_tables)
                    for element in web_retorno_section:
                        story.append(element)
                
            # REMOVIDO: Não mostra seção se o cliente não tem permissão para Web
            
            # 4. Retornos de Mídia Impressa - só mostra se o cliente tem permissão para Impresso
            if 'impresso' in tipos_midia_filtrados:
                retornos_impresso = database_manager.get_retornos_impresso(usuario_id, data_inicio, data_fim, filtros)
                if not retornos_impresso.empty:
                    impresso_retorno_tables = self._create_retorno_impresso_table(retornos_impresso)
                    impresso_retorno_section = self._create_retorno_section("Retornos de Mídia Impressa", impresso_retorno_tables)
                    for element in impresso_retorno_section:
                        story.append(element)
                
            # REMOVIDO: Não mostra seção se o cliente não tem permissão para Impresso
        else:
            # Se não deve mostrar seções de retorno, adiciona uma seção explicativa
            if not mostrar_secoes_retorno:
                story.append(PageBreak())
                
                # Adiciona seção informativa sobre filtro aplicado
                info_style = ParagraphStyle(
                    'InfoRetorno',
                    parent=self.normal_style,
                    fontSize=12,
                    textColor=colors.HexColor('#6c757d'),
                    alignment=TA_CENTER,
                    spaceAfter=10,
                    spaceBefore=10
                )
                
                if mostrar_retorno_relatorio:
                    info_title = Paragraph("Informação sobre Retornos", self.section_title_style)
                    story.append(info_title)
                    story.append(Spacer(1, 10))
                
                
                    story.append(Spacer(1, 20))

        # SEÇÕES DE ANÁLISE POR CIDADE - Adicionadas após todas as seções de retorno
        if database_manager and mostrar_secoes_sentimento:
            # Quebra de página antes das seções de análise
            story.append(PageBreak())
            
            # CORRIGIDO: Verificar filtros de mídia aplicados - agora só inclui tipos de mídia que o cliente tem permissão
            tipos_midia_filtrados = filtros.get('tipos_midia', ['web', 'tv', 'radio', 'impresso']) if filtros else ['web', 'tv', 'radio', 'impresso']
            
            # 1. Análise de Mídia TV - só mostra se o cliente tem permissão para TV
            if 'tv' in tipos_midia_filtrados:
                sentimentos_tv_raw = database_manager.get_sentimentos_tv(usuario_id, data_inicio, data_fim, filtros)
                if not sentimentos_tv_raw.empty:
                    sentimentos_tv_processed = self._process_sentimento_data(sentimentos_tv_raw, has_tempo=True)
                    if not sentimentos_tv_processed.empty:
                        tv_sentimento_table = self._create_sentimento_table(sentimentos_tv_processed, "TV", has_tempo=True)
                        tv_sentimento_section = self._create_retorno_section("Análise - TV", [tv_sentimento_table])
                        for element in tv_sentimento_section:
                            story.append(element)
            # REMOVIDO: Não mostra seção se o cliente não tem permissão para TV
            
            # 2. Análise de Mídia Rádio - só mostra se o cliente tem permissão para Rádio
            if 'radio' in tipos_midia_filtrados:
                sentimentos_radio_raw = database_manager.get_sentimentos_radio(usuario_id, data_inicio, data_fim, filtros)
                if not sentimentos_radio_raw.empty:
                    sentimentos_radio_processed = self._process_sentimento_data(sentimentos_radio_raw, has_tempo=True)
                    if not sentimentos_radio_processed.empty:
                        radio_sentimento_table = self._create_sentimento_table(sentimentos_radio_processed, "Rádio", has_tempo=True)
                        radio_sentimento_section = self._create_retorno_section("Análise - Rádio", [radio_sentimento_table])
                        for element in radio_sentimento_section:
                            story.append(element)
            # REMOVIDO: Não mostra seção se o cliente não tem permissão para Rádio
            
            # 3. Análise de Mídia Impressa - só mostra se o cliente tem permissão para Impresso
            if 'impresso' in tipos_midia_filtrados:
                sentimentos_impresso_raw = database_manager.get_sentimentos_impresso(usuario_id, data_inicio, data_fim, filtros)
                if not sentimentos_impresso_raw.empty:
                    sentimentos_impresso_processed = self._process_sentimento_data(sentimentos_impresso_raw, has_tempo=False)
                    if not sentimentos_impresso_processed.empty:
                        impresso_sentimento_table = self._create_sentimento_table(sentimentos_impresso_processed, "Impresso", has_tempo=False)
                        impresso_sentimento_section = self._create_retorno_section("Análise - Impresso", [impresso_sentimento_table])
                        for element in impresso_sentimento_section:
                            story.append(element)
            # REMOVIDO: Não mostra seção se o cliente não tem permissão para Impresso
            
            # 4. Análise de Mídia Web - só mostra se o cliente tem permissão para Web
            if 'web' in tipos_midia_filtrados:
                sentimentos_web_raw = database_manager.get_sentimentos_web(usuario_id, data_inicio, data_fim, filtros)
                if not sentimentos_web_raw.empty:
                    sentimentos_web_processed = self._process_sentimento_data(sentimentos_web_raw, has_tempo=False)
                    if not sentimentos_web_processed.empty:
                        web_sentimento_table = self._create_sentimento_table(sentimentos_web_processed, "Web", has_tempo=False)
                        web_sentimento_section = self._create_retorno_section("Análise - Web", [web_sentimento_table])
                        for element in web_sentimento_section:
                            story.append(element)
            # REMOVIDO: Não mostra seção se o cliente não tem permissão para Web
        
        # SEÇÕES DE STATUS DE MÍDIA - Adicionadas após todas as seções de análise
        if database_manager and mostrar_secoes_sentimento:
            # Quebra de página antes das seções de status
            story.append(PageBreak())
            
            # 1. Resumo Geral dos Status por Mídia
            status_resumo_data = database_manager.get_status_resumo_por_midia(usuario_id, data_inicio, data_fim, filtros)
            if not status_resumo_data.empty:
                # Cria gráfico de barras empilhadas para status por mídia
                from chart_generator import ChartGenerator
                chart_gen = ChartGenerator()
                status_chart_buffer = chart_gen.create_status_stacked_bar_chart(status_resumo_data)
                
                # Cria tabela de resumo
                status_resumo_table = self._create_status_resumo_table(status_resumo_data)
                
                # Cria seção com tabela e gráfico abaixo
                status_resumo_section = self._create_section_with_chart_below(
                    "Resumo Geral - Status por Mídia", status_resumo_table, status_chart_buffer
                )
                for element in status_resumo_section:
                    story.append(element)
                story.append(Spacer(1, 20))
            
            # 2. Status detalhado por mídia - CORRIGIDO: agora só mostra tipos de mídia que o cliente tem permissão
            # TV - só mostra se o cliente tem permissão para TV
            if 'tv' in tipos_midia_filtrados:
                status_tv_data = database_manager.get_status_tv_detalhado(usuario_id, data_inicio, data_fim, filtros)
                if not status_tv_data.empty:
                    tv_status_tables = self._create_status_detalhado_table(status_tv_data, "TV")
                    tv_status_section = self._create_retorno_section("Status Detalhado - TV", tv_status_tables)
                    for element in tv_status_section:
                        story.append(element)
            # REMOVIDO: Não mostra seção se o cliente não tem permissão para TV
            
            # Rádio - só mostra se o cliente tem permissão para Rádio
            if 'radio' in tipos_midia_filtrados:
                status_radio_data = database_manager.get_status_radio_detalhado(usuario_id, data_inicio, data_fim, filtros)
                if not status_radio_data.empty:
                    radio_status_tables = self._create_status_detalhado_table(status_radio_data, "Rádio")
                    radio_status_section = self._create_retorno_section("Status Detalhado - Rádio", radio_status_tables)
                    for element in radio_status_section:
                        story.append(element)
            # REMOVIDO: Não mostra seção se o cliente não tem permissão para Rádio
            
            # Web - só mostra se o cliente tem permissão para Web
            if 'web' in tipos_midia_filtrados:
                status_web_data = database_manager.get_status_web_detalhado(usuario_id, data_inicio, data_fim, filtros)
                if not status_web_data.empty:
                    web_status_tables = self._create_status_detalhado_table(status_web_data, "Web")
                    web_status_section = self._create_retorno_section("Status Detalhado - Web", web_status_tables)
                    for element in web_status_section:
                        story.append(element)
            # REMOVIDO: Não mostra seção se o cliente não tem permissão para Web
            
            # Impresso - só mostra se o cliente tem permissão para Impresso
            if 'impresso' in tipos_midia_filtrados:
                status_impresso_data = database_manager.get_status_impresso_detalhado(usuario_id, data_inicio, data_fim, filtros)
                if not status_impresso_data.empty:
                    impresso_status_tables = self._create_status_detalhado_table(status_impresso_data, "Impresso")
                    impresso_status_section = self._create_retorno_section("Status Detalhado - Impresso", impresso_status_tables)
                    for element in impresso_status_section:
                        story.append(element)
            # REMOVIDO: Não mostra seção se o cliente não tem permissão para Impresso
        
        
        # Gera o PDF
        doc = SimpleDocTemplate(
            output_path,
            pagesize=letter,
            rightMargin=inch/2,
            leftMargin=inch/2,
            topMargin=inch/2,
            bottomMargin=inch/2
        )
        doc.build(story)
        print(f"Relatório PDF gerado: {output_path}")

    def _create_retorno_tv_table(self, retorno_data: pd.DataFrame):
        """Cria tabela de retorno para TV sem limitação de linhas"""
        if retorno_data.empty:
            # Retorna tabela vazia se não houver dados
            no_data_style = ParagraphStyle(
                'NoData',
                parent=self.normal_style,
                fontSize=10,
                textColor=colors.HexColor('#7f8c8d'),
                alignment=TA_CENTER
            )
            return [Table([[Paragraph("Nenhum retorno de TV encontrado", no_data_style)]], 
                        colWidths=[6*inch])]
        
        # Cabeçalho da tabela
        headers = ['Data Clipagem', 'Emissora', 'Programa', 'Valor']
        table_data = [headers]
        
        # Dados da tabela - TODAS as linhas sem limitação
        for _, row in retorno_data.iterrows():
            row_data = []
            
            # Data formatada
            data_value = row.get('data_clipagem', '')
            formatted_date = self._format_date(data_value)
            row_data.append(formatted_date)
            
            # Emissora
            emissora_value = str(row.get('emissora', ''))
            row_data.append(emissora_value)
            
            # Programa  
            programa_value = str(row.get('programa', ''))
            row_data.append(programa_value)
            
            # Valor formatado
            valor_value = row.get('valor', 0)
            formatted_valor = self._format_currency(valor_value)
            row_data.append(formatted_valor)
            
            table_data.append(row_data)
        
        # Larguras das colunas
        col_widths = [1.0*inch, 2.0*inch, 2.0*inch, 1.0*inch]
        table = Table(table_data, colWidths=col_widths)
        
        # Determina cor do cabeçalho
        header_color = self._get_dynamic_color(hash("retorno_tv") % len(self.color_palette))
        
        # Estilo da tabela
        table.setStyle(TableStyle([
            # Cabeçalho com cor dinâmica
            ('BACKGROUND', (0, 0), (-1, 0), header_color),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, 0), 8),
            
            # Corpo da tabela
            ('FONTNAME', (0, 1), (-1, -1), 'Helvetica'),
            ('FONTSIZE', (0, 1), (-1, -1), 6),
            ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#bdc3c7')),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
            
            # Alinhamento específico das colunas
            ('ALIGN', (0, 1), (0, -1), 'CENTER'),    # Data centralizada
            ('ALIGN', (1, 1), (1, -1), 'LEFT'),      # Emissora à esquerda
            ('ALIGN', (2, 1), (2, -1), 'LEFT'),      # Programa à esquerda
            ('ALIGN', (3, 1), (3, -1), 'CENTER'),    # Valor centralizado
        ]))
        
        # Retorna uma lista com apenas uma tabela (sem divisão)
        return [table]
    
    def _create_retorno_radio_table(self, retorno_data: pd.DataFrame):
        """Cria tabela de retorno para Rádio sem limitação de linhas"""
        if retorno_data.empty:
            # Retorna tabela vazia se não houver dados
            no_data_style = ParagraphStyle(
                'NoData',  
                parent=self.normal_style,
                fontSize=10,
                textColor=colors.HexColor('#7f8c8d'),
                alignment=TA_CENTER
            )
            return [Table([[Paragraph("Nenhum retorno de Rádio encontrado", no_data_style)]], 
                        colWidths=[6*inch])]
        
        # Cabeçalho da tabela
        headers = ['Data Clipagem', 'Emissora', 'Programa', 'Valor']
        table_data = [headers]
        
        # Dados da tabela - TODAS as linhas sem limitação
        for _, row in retorno_data.iterrows():
            row_data = []
            
            # Data formatada
            data_value = row.get('data_clipagem', '')
            formatted_date = self._format_date(data_value)
            row_data.append(formatted_date)
            
            # Emissora
            emissora_value = str(row.get('emissora', ''))
            row_data.append(emissora_value)
            
            # Programa
            programa_value = str(row.get('programa', ''))
            row_data.append(programa_value)
            
            # Valor formatado
            valor_value = row.get('valor', 0)
            formatted_valor = self._format_currency(valor_value)
            row_data.append(formatted_valor)
            
            table_data.append(row_data)
        
        # Larguras das colunas
        col_widths = [1.0*inch, 2.0*inch, 2.0*inch, 1.0*inch]
        table = Table(table_data, colWidths=col_widths)
        
        # Determina cor do cabeçalho
        header_color = self._get_dynamic_color(hash("retorno_radio") % len(self.color_palette))
        
        # Estilo da tabela
        table.setStyle(TableStyle([
            # Cabeçalho com cor dinâmica
            ('BACKGROUND', (0, 0), (-1, 0), header_color),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, 0), 8),
            
            # Corpo da tabela
            ('FONTNAME', (0, 1), (-1, -1), 'Helvetica'),
            ('FONTSIZE', (0, 1), (-1, -1), 6),
            ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#bdc3c7')),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
            
            # Alinhamento específico das colunas
            ('ALIGN', (0, 1), (0, -1), 'CENTER'),    # Data centralizada
            ('ALIGN', (1, 1), (1, -1), 'LEFT'),      # Emissora à esquerda
            ('ALIGN', (2, 1), (2, -1), 'LEFT'),      # Programa à esquerda
            ('ALIGN', (3, 1), (3, -1), 'CENTER'),    # Valor centralizado
        ]))
        
        # Retorna uma lista com apenas uma tabela (sem divisão)
        return [table]
    
    def _create_retorno_web_table(self, retorno_data: pd.DataFrame):
        """Cria tabela de retorno para Web sem limitação de linhas"""
        if retorno_data.empty:
            # Retorna tabela vazia se não houver dados
            no_data_style = ParagraphStyle(
                'NoData',
                parent=self.normal_style,
                fontSize=10,
                textColor=colors.HexColor('#7f8c8d'),
                alignment=TA_CENTER
            )
            return [Table([[Paragraph("Nenhum retorno de Web encontrado", no_data_style)]], 
                        colWidths=[6*inch])]
        
        # Cabeçalho da tabela
        headers = ['Data Clipagem', 'Site', 'Seção', 'Valor']
        table_data = [headers]
        
        # Dados da tabela - TODAS as linhas sem limitação
        for _, row in retorno_data.iterrows():
            row_data = []
            
            # Data formatada
            data_value = row.get('data_clipagem', '')
            formatted_date = self._format_date(data_value)
            row_data.append(formatted_date)
            
            # Site
            site_value = str(row.get('site', ''))
            row_data.append(site_value)
            
            # Seção
            secao_value = str(row.get('secao', ''))
            row_data.append(secao_value)
            
            # Valor formatado
            valor_value = row.get('valor', 0)
            formatted_valor = self._format_currency(valor_value)
            row_data.append(formatted_valor)
            
            table_data.append(row_data)
        
        # Larguras das colunas
        col_widths = [1.0*inch, 2.5*inch, 1.5*inch, 1.0*inch]
        table = Table(table_data, colWidths=col_widths)
        
        # Determina cor do cabeçalho
        header_color = self._get_dynamic_color(hash("retorno_web") % len(self.color_palette))
        
        # Estilo da tabela
        table.setStyle(TableStyle([
            # Cabeçalho com cor dinâmica
            ('BACKGROUND', (0, 0), (-1, 0), header_color),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, 0), 8),
            
            # Corpo da tabela
            ('FONTNAME', (0, 1), (-1, -1), 'Helvetica'),
            ('FONTSIZE', (0, 1), (-1, -1), 6),
            ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#bdc3c7')),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
            
            # Alinhamento específico das colunas
            ('ALIGN', (0, 1), (0, -1), 'CENTER'),    # Data centralizada
            ('ALIGN', (1, 1), (1, -1), 'LEFT'),      # Site à esquerda
            ('ALIGN', (2, 1), (2, -1), 'LEFT'),      # Seção à esquerda
            ('ALIGN', (3, 1), (3, -1), 'CENTER'),    # Valor centralizado
        ]))
        
        # Retorna uma lista com apenas uma tabela (sem divisão)
        return [table]
    
    def _create_retorno_impresso_table(self, retorno_data: pd.DataFrame):
        """Cria tabela de retorno para Mídia Impressa sem limitação de linhas"""
        if retorno_data.empty:
            # Retorna tabela vazia se não houver dados
            no_data_style = ParagraphStyle(
                'NoData',
                parent=self.normal_style,
                fontSize=10,
                textColor=colors.HexColor('#7f8c8d'),
                alignment=TA_CENTER
            )
            return [Table([[Paragraph("Nenhum retorno de Mídia Impressa encontrado", no_data_style)]], 
                        colWidths=[6*inch])]
        
        # Cabeçalho da tabela
        headers = ['Data Clipagem', 'Jornal', 'Seção', 'Valor']
        table_data = [headers]
        
        # Dados da tabela - TODAS as linhas sem limitação
        for _, row in retorno_data.iterrows():
            row_data = []
            
            # Data formatada
            data_value = row.get('data_clipagem', '')
            formatted_date = self._format_date(data_value)
            row_data.append(formatted_date)
            
            # Jornal
            jornal_value = str(row.get('jornal', ''))
            row_data.append(jornal_value)
            
            # Seção
            secao_value = str(row.get('secao', ''))
            row_data.append(secao_value)
            
            # Valor formatado
            valor_value = row.get('valor', 0)
            formatted_valor = self._format_currency(valor_value)
            row_data.append(formatted_valor)
            
            table_data.append(row_data)
        
        # Larguras das colunas
        col_widths = [1.0*inch, 2.5*inch, 1.5*inch, 1.0*inch]
        table = Table(table_data, colWidths=col_widths)
        
        # Determina cor do cabeçalho
        header_color = self._get_dynamic_color(hash("retorno_impresso") % len(self.color_palette))
        
        # Estilo da tabela
        table.setStyle(TableStyle([
            # Cabeçalho com cor dinâmica
            ('BACKGROUND', (0, 0), (-1, 0), header_color),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, 0), 8),
            
            # Corpo da tabela
            ('FONTNAME', (0, 1), (-1, -1), 'Helvetica'),
            ('FONTSIZE', (0, 1), (-1, -1), 6),
            ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#bdc3c7')),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
            
            # Alinhamento específico das colunas
            ('ALIGN', (0, 1), (0, -1), 'CENTER'),    # Data centralizada
            ('ALIGN', (1, 1), (1, -1), 'LEFT'),      # Jornal à esquerda
            ('ALIGN', (2, 1), (2, -1), 'LEFT'),      # Seção à esquerda
            ('ALIGN', (3, 1), (3, -1), 'CENTER'),    # Valor centralizado
        ]))
        
        # Retorna uma lista com apenas uma tabela (sem divisão)
        return [table]
    
    def _create_status_resumo_table(self, status_data: pd.DataFrame):
        """Cria tabela de resumo de status por mídia"""
        if status_data.empty:
            # Retorna tabela vazia se não houver dados
            no_data_style = ParagraphStyle(
                'NoData',
                parent=self.normal_style,
                fontSize=10,
                textColor=colors.HexColor('#7f8c8d'),
                alignment=TA_CENTER
            )
            return Table([[Paragraph("Nenhum dado de status encontrado", no_data_style)]], 
                        colWidths=[6*inch])
        
        # Cabeçalho da tabela
        headers = ['Mídia', 'Positivo', 'Negativo', 'Neutro', 'Total']
        table_data = [headers]
        
        # Dados da tabela
        for _, row in status_data.iterrows():
            table_data.append([
                str(row['midia']),
                str(int(row['positivo'])),
                str(int(row['negativo'])),
                str(int(row['neutro'])),
                str(int(row['total']))
            ])
        
        # Calcula totais gerais
        total_positivo = status_data['positivo'].sum()
        total_negativo = status_data['negativo'].sum()
        total_neutro = status_data['neutro'].sum()
        total_geral = status_data['total'].sum()
        
        # Linha de total
        table_data.append([
            'Total Geral',
            str(int(total_positivo)),
            str(int(total_negativo)),
            str(int(total_neutro)),
            str(int(total_geral))
        ])
        
        # Cria tabela
        table = Table(table_data, colWidths=[1.2*inch, 0.8*inch, 0.8*inch, 0.8*inch, 0.8*inch])
        
        # Estilo da tabela
        header_color = self._get_dynamic_color(10)  # Cor específica para status
        
        table.setStyle(TableStyle([
            # Cabeçalho
            ('BACKGROUND', (0, 0), (-1, 0), header_color),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, 0), 9),
            
            # Corpo da tabela
            ('FONTNAME', (0, 1), (-1, -2), 'Helvetica'),
            ('FONTSIZE', (0, 1), (-1, -2), 8),
            ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#bdc3c7')),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
            
            # Linha de total
            ('BACKGROUND', (0, -1), (-1, -1), colors.HexColor('#ecf0f1')),
            ('FONTNAME', (0, -1), (-1, -1), 'Helvetica-Bold'),
            ('FONTSIZE', (0, -1), (-1, -1), 8),
        ]))
        
        return table
    
    def _create_status_detalhado_table(self, status_data: pd.DataFrame, midia_tipo: str):
        """Cria tabela detalhada de status para uma mídia específica sem limitação de linhas"""
        if status_data.empty:
            # Retorna tabela vazia se não houver dados
            no_data_style = ParagraphStyle(
                'NoData',
                parent=self.normal_style,
                fontSize=10,
                textColor=colors.HexColor('#7f8c8d'),
                alignment=TA_CENTER
            )
            return [Table([[Paragraph(f"Nenhum dado de status encontrado para {midia_tipo}", no_data_style)]], 
                        colWidths=[6*inch])]
        
        # Adiciona numeração de linha
        status_data = status_data.reset_index(drop=True)
        status_data['numero'] = range(1, len(status_data) + 1)
        
        # Cabeçalho da tabela
        headers = ['Nº', 'Data', 'Status']
        table_data = [headers]
        
        # Dados da tabela - TODAS as linhas sem limitação
        for _, row in status_data.iterrows():
            row_data = []
            
            # Número
            row_data.append(str(int(row['numero'])))
            
            # Data formatada
            data_value = row.get('data', '')
            formatted_date = self._format_date(data_value)
            row_data.append(formatted_date)
            
            # Status - Converte sentimentos numéricos para texto legível
            status_value = str(row.get('status', ''))
            # Se o status é um valor de sentimento numérico, converte para texto
            if status_value in ['-1', '0', '1']:
                status_formatted = self._convert_sentiment_to_text(status_value)
            else:
                status_formatted = status_value
            row_data.append(status_formatted)
            
            table_data.append(row_data)
        
        # Larguras das colunas
        col_widths = [0.5*inch, 1.0*inch, 1.0*inch]
        table = Table(table_data, colWidths=col_widths)
        
        # Determina cor do cabeçalho baseada no tipo de mídia
        header_color_index = hash(f"status_{midia_tipo.lower()}") % len(self.color_palette)
        header_color = self._get_dynamic_color(header_color_index)
        
        # Estilo da tabela
        table.setStyle(TableStyle([
            # Cabeçalho com cor dinâmica
            ('BACKGROUND', (0, 0), (-1, 0), header_color),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, 0), 8),
            
            # Corpo da tabela
            ('FONTNAME', (0, 1), (-1, -1), 'Helvetica'),
            ('FONTSIZE', (0, 1), (-1, -1), 6),
            ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#bdc3c7')),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
            
            # Alinhamento específico das colunas
            ('ALIGN', (0, 1), (0, -1), 'CENTER'),    # Número centralizado
            ('ALIGN', (1, 1), (1, -1), 'CENTER'),    # Data centralizada
            ('ALIGN', (2, 1), (2, -1), 'LEFT'),      # Status à esquerda
        ]))
        
        # Retorna uma lista com apenas uma tabela (sem divisão)
        return [table]
    
    def _create_no_data_table(self, message: str):
        """Cria uma tabela simples para exibir mensagem de 'sem dados'"""
        no_data_style = ParagraphStyle(
            'NoData',
            parent=self.normal_style,
            fontSize=10,
            textColor=colors.HexColor('#7f8c8d'),
            alignment=TA_CENTER,
            spaceAfter=6,
            spaceBefore=6
        )
        
        table_data = [[Paragraph(message, no_data_style)]]
        table = Table(table_data, colWidths=[6*inch])
        
        table.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor('#f8f9fa')),
            ('BORDER', (0, 0), (-1, -1), 1, colors.HexColor('#dee2e6')),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
            ('FONTNAME', (0, 0), (-1, -1), 'Helvetica'),
            ('FONTSIZE', (0, 0), (-1, -1), 10),
            ('TEXTCOLOR', (0, 0), (-1, -1), colors.HexColor('#6c757d')),
            ('TOPPADDING', (0, 0), (-1, -1), 12),
            ('BOTTOMPADDING', (0, 0), (-1, -1), 12),
        ]))
        
        return table

    def _create_retorno_section(self, title: str, tables):
        """
        Cria seção de retorno com título e tabela(s)
        
        Args:
            title: Título da seção
            tables: Lista de tabelas (pode ser uma ou múltiplas)
            
        Returns:
            list: Lista de elementos para adicionar ao story
        """
        elements = []
        
        # Título da seção de retorno
        title_para = Paragraph(title, self.section_title_style)
        elements.append(title_para)
        elements.append(Spacer(1, 10))
        
        # Adiciona as tabelas
        for i, table in enumerate(tables):
            if i > 0:  # Quebra de página entre tabelas múltiplas
                elements.append(PageBreak())
                continuation_title = Paragraph(f"{title} (continuação)", self.section_title_style)
                elements.append(continuation_title)
                elements.append(Spacer(1, 10))
            
            elements.append(table)
            elements.append(Spacer(1, 15))
        
        return elements 
    
    def _create_section_with_chart_below(self, title: str, table, chart_buffer: io.BytesIO):
        """Cria uma seção com título, tabela e gráfico posicionado abaixo da tabela"""
        elements = []
        
        # Espaçamento maior antes do novo título de relatório
        elements.append(Spacer(1, 40))  # Espaçamento maior entre relatórios
        
        # Subtítulo da seção posicionado acima
        subtitle = Paragraph(title, self.subtitle_style)
        
        # Mantém título e tabela juntos usando KeepTogether
        title_and_table = KeepTogether([subtitle, Spacer(1, 10), table])
        elements.append(title_and_table)
        elements.append(Spacer(1, 20))  # Espaço entre tabela e gráfico
        
        # Depois adiciona o gráfico abaixo
        if chart_buffer:
            chart_buffer.seek(0)
            # Gráfico padronizado para todos os relatórios - mas esse é gráfico de barras, não precisa legenda ao lado
            chart_img = Image(chart_buffer, width=6.0*inch, height=4.0*inch)
            
            # Centraliza o gráfico
            chart_table = Table([[chart_img]], colWidths=[6.5*inch])
            chart_table.setStyle(TableStyle([
                ('ALIGN', (0, 0), (0, 0), 'CENTER'),
                ('VALIGN', (0, 0), (0, 0), 'MIDDLE'),
                ('LEFTPADDING', (0, 0), (-1, -1), 0),
                ('RIGHTPADDING', (0, 0), (-1, -1), 0),
                ('TOPPADDING', (0, 0), (-1, -1), 0),
                ('BOTTOMPADDING', (0, 0), (-1, -1), 0),
            ]))
            
            elements.append(chart_table)
        
        return elements
    
    def _create_radio_programa_section_with_chart(self, title: str, table, chart_buffer: io.BytesIO):
        """Cria seção específica para Relatório por programas - Rádio com tamanho padronizado"""
        elements = []
        
        # Espaçamento maior antes do novo título de relatório
        elements.append(Spacer(1, 40))  # Espaçamento maior entre relatórios
        
        # Subtítulo da seção posicionado acima
        subtitle = Paragraph(title, self.subtitle_style)
        
        # Gráfico posicionado acima da tabela
        chart_elements = []
        if chart_buffer:
            chart_buffer.seek(0)
            
            # TAMANHO REDUZIDO PARA PROGRAMA RÁDIO - conforme solicitado
            chart_img = Image(chart_buffer, width=5.5*inch, height=3.0*inch)
            
            # Centraliza o gráfico
            chart_table = Table([[chart_img]], colWidths=[6.0*inch])
            chart_table.setStyle(TableStyle([
                ('ALIGN', (0, 0), (0, 0), 'CENTER'),
                ('VALIGN', (0, 0), (0, 0), 'MIDDLE'),
                ('LEFTPADDING', (0, 0), (-1, -1), 0),
                ('RIGHTPADDING', (0, 0), (-1, -1), 0),
                ('TOPPADDING', (0, 0), (-1, -1), 0),
                ('BOTTOMPADDING', (0, 0), (-1, -1), 0),
            ]))
            
            chart_elements.append(chart_table)
        
        # Mantém título e gráfico juntos usando KeepTogether
        if chart_elements:
            title_and_chart = KeepTogether([subtitle, Spacer(1, 15), chart_elements[0]])
            elements.append(title_and_chart)
        else:
            elements.append(subtitle)
        
        elements.append(Spacer(1, 20))  # Espaço entre gráfico e tabela
        
        # Tabela abaixo do gráfico ocupando a página inteira
        elements.append(table)
        
        return elements
    
    def _create_impresso_veiculo_section_with_chart(self, title: str, table, chart_buffer: io.BytesIO):
        """Cria seção específica para Relatório por veículos - Impresso com tamanho padronizado"""
        elements = []
        
        # Espaçamento maior antes do novo título de relatório
        elements.append(Spacer(1, 40))  # Espaçamento maior entre relatórios
        
        # Subtítulo da seção posicionado acima
        subtitle = Paragraph(title, self.subtitle_style)
        
        # Gráfico posicionado acima da tabela
        chart_elements = []
        if chart_buffer:
            chart_buffer.seek(0)
            
            # TAMANHO ESPECÍFICO PARA IMPRESSO VEÍCULOS - garantindo mesmo tamanho visual que outros
            chart_img = Image(chart_buffer, width=7.5*inch, height=4.2*inch)
            
            # Centraliza o gráfico
            chart_table = Table([[chart_img]], colWidths=[8.0*inch])
            chart_table.setStyle(TableStyle([
                ('ALIGN', (0, 0), (0, 0), 'CENTER'),
                ('VALIGN', (0, 0), (0, 0), 'MIDDLE'),
                ('LEFTPADDING', (0, 0), (-1, -1), 0),
                ('RIGHTPADDING', (0, 0), (-1, -1), 0),
                ('TOPPADDING', (0, 0), (-1, -1), 0),
                ('BOTTOMPADDING', (0, 0), (-1, -1), 0),
            ]))
            
            chart_elements.append(chart_table)
        
        # Mantém título e gráfico juntos usando KeepTogether
        if chart_elements:
            title_and_chart = KeepTogether([subtitle, Spacer(1, 15), chart_elements[0]])
            elements.append(title_and_chart)
        else:
            elements.append(subtitle)
        
        elements.append(Spacer(1, 20))  # Espaço entre gráfico e tabela
        
        # Tabela abaixo do gráfico ocupando a página inteira
        elements.append(table)
        
        return elements
    
    def _create_web_veiculo_section_with_chart(self, title: str, table, chart_buffer: io.BytesIO):
        """Cria seção específica para Relatório por veículos - Web com tamanho padronizado"""
        elements = []
        
        # Espaçamento maior antes do novo título de relatório
        elements.append(Spacer(1, 40))  # Espaçamento maior entre relatórios
        
        # Subtítulo da seção posicionado acima
        subtitle = Paragraph(title, self.subtitle_style)
        
        # Gráfico posicionado acima da tabela
        chart_elements = []
        if chart_buffer:
            chart_buffer.seek(0)
            
            # TAMANHO ESPECÍFICO PARA WEB VEÍCULOS - garantindo mesmo tamanho visual que outros
            chart_img = Image(chart_buffer, width=7.5*inch, height=4.2*inch)
            
            # Centraliza o gráfico
            chart_table = Table([[chart_img]], colWidths=[8.0*inch])
            chart_table.setStyle(TableStyle([
                ('ALIGN', (0, 0), (0, 0), 'CENTER'),
                ('VALIGN', (0, 0), (0, 0), 'MIDDLE'),
                ('LEFTPADDING', (0, 0), (-1, -1), 0),
                ('RIGHTPADDING', (0, 0), (-1, -1), 0),
                ('TOPPADDING', (0, 0), (-1, -1), 0),
                ('BOTTOMPADDING', (0, 0), (-1, -1), 0),
            ]))
            
            chart_elements.append(chart_table)
        
        # Mantém título e gráfico juntos usando KeepTogether
        if chart_elements:
            title_and_chart = KeepTogether([subtitle, Spacer(1, 15), chart_elements[0]])
            elements.append(title_and_chart)
        else:
            elements.append(subtitle)
        
        elements.append(Spacer(1, 20))  # Espaço entre gráfico e tabela
        
        # Tabela abaixo do gráfico ocupando a página inteira
        elements.append(table)
        
        return elements
    
    def _create_section_with_side_by_side_chart(self, title: str, table, chart_buffer: io.BytesIO):
        """Cria uma seção com título, tabela à esquerda e gráfico à direita"""
        elements = []
        
        # Subtítulo da seção
        subtitle = Paragraph(title, self.subtitle_style)
        elements.append(subtitle)
        elements.append(Spacer(1, 15))
        
        # Cria tabela lado a lado com gráfico
        if chart_buffer:
            chart_buffer.seek(0)
            # Gráfico menor para layout lado a lado
            chart_img = Image(chart_buffer, width=3.5*inch, height=2.5*inch)
            
            # Tabela lado a lado - usa Table com duas colunas
            side_by_side_table = Table(
                [[table, chart_img]], 
                colWidths=[2.5*inch, 4.0*inch]  # Tabela mais estreita, gráfico maior
            )
            
            side_by_side_table.setStyle(TableStyle([
                ('ALIGN', (0, 0), (0, 0), 'LEFT'),    # Tabela à esquerda
                ('ALIGN', (1, 0), (1, 0), 'CENTER'),  # Gráfico ao centro da célula direita
                ('VALIGN', (0, 0), (1, 0), 'TOP'),    # Alinhamento vertical no topo
                ('LEFTPADDING', (0, 0), (-1, -1), 0),
                ('RIGHTPADDING', (0, 0), (-1, -1), 0),
                ('TOPPADDING', (0, 0), (-1, -1), 0),
                ('BOTTOMPADDING', (0, 0), (-1, -1), 0),
            ]))
            
            elements.append(side_by_side_table)
        else:
            elements.append(table)
        
        return elements
    
    def _create_primeira_pagina_layout(self, noticias_data: pd.DataFrame, valores_data: pd.DataFrame, 
                                      noticias_chart_buffer: io.BytesIO, valores_chart_buffer: io.BytesIO, mostrar_valores: bool = True):
        """Cria o layout otimizado da primeira página com ambas seções"""
        elements = []
        
        # Título principal
        title = Paragraph("Relatório Completo", self.title_style)
        elements.append(title)
        elements.append(Spacer(1, 30))
        
        # Primeira linha: Notícias (tabela à esquerda, gráfico à direita)
        noticias_table = self._create_noticias_table(noticias_data)
        noticias_section = self._create_section_with_side_by_side_chart(
            "Notícias Encontradas", noticias_table, noticias_chart_buffer
        )
        for element in noticias_section:
            elements.append(element)
        
        elements.append(Spacer(1, 30))  # Espaço entre as duas seções
        
        # Segunda linha: Valores (condicional - só mostra se mostrar_valores for True)
        if mostrar_valores:
            valores_table = self._create_valores_table(valores_data)
            valores_section = self._create_section_with_side_by_side_chart(
                "Geral", valores_table, valores_chart_buffer
            )
            for element in valores_section:
                elements.append(element)
        
        return elements