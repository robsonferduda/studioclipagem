import matplotlib.pyplot as plt
import pandas as pd
import io
from matplotlib.backends.backend_agg import FigureCanvasAgg
import matplotlib
matplotlib.use('Agg')  # Para evitar problemas com display

class ChartGenerator:
    def __init__(self):
        """Inicializa o gerador de gráficos"""
        # Configurações gerais do matplotlib
        plt.style.use('default')
        
    def _create_empty_chart(self, title: str) -> io.BytesIO:
        """
        Cria um gráfico vazio quando não há dados para exibir
        
        Args:
            title: Título do gráfico
            
        Returns:
            Buffer com a imagem do gráfico vazio
        """
        # Cria a figura
        fig, ax = plt.subplots(figsize=(5, 4))
        fig.patch.set_facecolor('white')
        
        # Remove os eixos
        ax.set_xlim(0, 1)
        ax.set_ylim(0, 1)
        ax.axis('off')
        
        # Adiciona texto informativo
        ax.text(0.5, 0.5, 'Nenhum dado\ndisponível', 
                ha='center', va='center', 
                fontsize=12, color='#7f8c8d',
                fontweight='bold')
        
        # Adiciona título
        ax.set_title(title, fontsize=11, fontweight='bold', pad=15, 
                    color='#2c3e50')
        
        # Ajusta layout
        plt.tight_layout()
        
        # Salva em buffer
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight',
                   facecolor='white', edgecolor='none')
        plt.close()
        
        buffer.seek(0)
        return buffer
    
    def create_pie_chart(self, data: pd.DataFrame, title: str, value_column: str, color_scheme='qualitative', label_column='Mídia') -> io.BytesIO:
        """
        Cria um gráfico de pizza estilizado com cores personalizadas por mídia
        
        Args:
            data: DataFrame com dados
            title: Título do gráfico
            value_column: Nome da coluna com valores
            color_scheme: Esquema de cores ('qualitative' ou outro)
            label_column: Nome da coluna com labels (padrão: 'Mídia')
            
        Returns:
            Buffer com a imagem do gráfico
        """
        # Filtra dados válidos - remove valores zero ou NaN
        valid_data = data[
            (data[value_column].notna()) & 
            (data[value_column] > 0)
        ].copy()
        
        # Se não há dados válidos, cria gráfico vazio
        if valid_data.empty:
            return self._create_empty_chart(title)
        
        # REMOVIDO: Limitação de itens para jornais - agora mostra todos como solicitado
        # Os jornais serão exibidos todos no gráfico, assim como programas de rádio
        
        # Ordena dados por valor decrescente para melhor visualização
        if label_column in ['Jornal', 'Emissora', 'Programa']:
            valid_data = valid_data.sort_values(value_column, ascending=False)
        
        # Cria a figura com tamanho ajustado para acomodar legenda ao lado
        if label_column in ['Emissora', 'Programa', 'Jornal', 'Site']:
            # Para jornais, emissoras, programas e sites: figura mais larga para acomodar legenda ao lado
            fig, (ax, ax_legend) = plt.subplots(1, 2, figsize=(12, 6), gridspec_kw={'width_ratios': [2, 1]})
        else:
            fig, ax = plt.subplots(figsize=(6, 6))  # Figura quadrada para mídias
        fig.patch.set_facecolor('white')
        
        # Mapeia cores específicas para cada mídia - CORES MAIS DESTACADAS
        midia_colors = {
            'TV': '#E53E3E',        # Vermelho mais vibrante
            'Rádio': '#38B2AC',     # Verde-azulado mais forte
            'Impresso': '#3182CE',  # Azul mais forte
            'Web': '#38A169'        # Verde mais forte
        }
        
        # Cores específicas para emissoras de TV - PALETA VIBRANTE E DIVERSIFICADA
        emissora_colors = {
            'TV Globo': '#FF4757',      # Vermelho vibrante
            'SBT': '#2ED573',           # Verde brilhante
            'Record TV': '#3742FA',     # Azul royal
            'Band': '#FF6348',          # Coral vibrante
            'RedeTV!': '#FFA502',       # Laranja forte
            'TV Brasil': '#7F8C8D',     # Cinza azulado
            'TV Cultura': '#A55EEA',    # Roxo vibrante
            'Globo': '#FF4757',         # Mesmo que TV Globo
            'Record': '#3742FA',        # Mesmo que Record TV
            'Rede Globo': '#FF4757',    # Variação do nome
            'TV Record': '#3742FA',     # Variação do nome
            'SBT - Sistema Brasileiro de Televisão': '#2ED573',  # Nome completo
            'Rede Record': '#3742FA',   # Outra variação
            'Bandeirantes': '#FF6348',  # Nome completo da Band
            'TV Bandeirantes': '#FF6348',  # Outra variação
            'Não identificada': '#95A5A6',  # Cor neutra para emissoras não identificadas
        }
        
        # Cores específicas para programas de TV - PALETA DIFERENCIADA
        programa_colors = {
            'Jornal Nacional': '#8E44AD',       # Roxo escuro
            'Fantástico': '#E67E22',           # Laranja
            'Bom Dia Brasil': '#F39C12',       # Amarelo dourado
            'Jornal Hoje': '#27AE60',          # Verde
            'Jornal da Globo': '#3498DB',      # Azul
            'SPTV': '#E74C3C',                 # Vermelho
            'Cidade Alerta': '#9B59B6',        # Roxo claro
            'SBT Brasil': '#1ABC9C',           # Turquesa
            'Primeiro Impacto': '#F1C40F',     # Amarelo
            'Balanço Geral': '#34495E',        # Cinza escuro
            'Jornal da Record': '#16A085',     # Verde azulado
            'Fala Brasil': '#2980B9',          # Azul escuro
            'Brasil Urgente': '#C0392B',       # Vermelho escuro
            'RedeTV! News': '#D35400',         # Laranja escuro
            'Manhã Total': '#8E44AD',          # Roxo
            'JN': '#8E44AD',                   # Mesmo que Jornal Nacional
            'TJ': '#E74C3C',                   # Telejornal genérico
            'Jornal': '#34495E',               # Jornal genérico
            'Programa': '#7F8C8D',             # Programa genérico
            'Não identificado': '#95A5A6',     # Cor neutra para programas não identificados
        }
        
        # Cores específicas para emissoras de rádio - PALETA TURQUESA/VERDE
        emissora_radio_colors = {
            'CBN': '#1ABC9C',                  # Turquesa principal
            'Jovem Pan': '#16A085',            # Verde azulado
            'Rádio Globo': '#48C9B0',          # Turquesa claro
            'Bandeirantes': '#52C3A3',         # Verde menta
            'Record FM': '#5DADE2',            # Azul claro
            'Kiss FM': '#85E3E3',              # Ciano claro
            'Mix FM': '#76D7C4',               # Verde água
            'Capital FM': '#58D68D',           # Verde claro
            'Eldorado': '#2ECC71',             # Verde
            'Antena 1': '#28B463',             # Verde escuro
            'BandNews': '#148F77',             # Verde petróleo
            'Rádio Tupi': '#17A2B8',           # Azul turquesa
            'Radio Alpha': '#1BA1A1',          # Turquesa médio
            'Beat 98': '#1E8E8E',              # Verde azulado escuro
            'Rede Aleluia': '#20B2AA',         # Verde azulado médio
            'Não identificada': '#95A5A6',     # Cor neutra para emissoras não identificadas
        }
        
        # NOVAS CORES ESPECÍFICAS PARA JORNAIS - PALETA AZUL/CINZA ELEGANTE
        jornal_colors = {
            'Notícias do Dia': '#2C3E50',          # Azul escuro elegante
            'Diário do Litoral': '#34495E',        # Cinza azulado
            'Município Dia a Dia': '#3498DB',      # Azul médio
            'O Correio do Povo': '#5D6D7E',       # Cinza médio
            'Tribuna de Notícias': '#85929E',     # Cinza claro
            'Folha Regional': '#AEB6BF',          # Cinza azulado claro
            'Jornal do Vale do Itapocu': '#2E4057', # Azul acinzentado
            'Jornal do Médio Vale': '#1B4F72',    # Azul escuro
            'Expressão': '#154360',               # Azul petróleo
            'Jornal Metas': '#1A5276',            # Azul médio escuro
            'Diário do Sul': '#21618C',           # Azul aço
            'Folha Desbravador': '#2874A6',       # Azul
            'Diário de Riomafra': '#2E86AB',      # Azul claro
            'A Gazeta': '#3498DB',                # Azul padrão
            'Novoeste': '#5DADE2',                # Azul claro
            'Outros': '#95A5A6',                  # Cinza neutro para categoria "Outros"
        }
        
        # CORES ESPECÍFICAS PARA SITES WEB - PALETA VERDE/ESMERALDA
        site_colors = {
            'Portal ND Mais': '#27AE60',           # Verde principal
            'Site Jornal dos Bairros Itajaí': '#2ECC71',  # Verde claro
            'Site SCC 10': '#16A085',             # Verde azulado
            'Site Visor Notícias': '#1ABC9C',     # Turquesa
            'Portal Menina': '#48C9B0',           # Turquesa claro
            'Site Rede Peperi': '#52C3A3',        # Verde menta
            'Site Jornal do Vale do Itapocu': '#58D68D', # Verde água
            'Site BC Notícias': '#76D7C4',        # Verde claro
            'Site Página 3': '#85E3E3',           # Ciano claro
            'Site Eder Luiz': '#5DADE2',          # Azul claro
            'Site Jornal Razão': '#3498DB',       # Azul
            'Rede Catarinense de Notícia': '#2980B9', # Azul escuro
            'Site O Blumenauense': '#1B4F72',     # Azul petróleo
            'Site O Município': '#154360',        # Azul escuro
            'Site Click Camboriú': '#28B463',     # Verde escuro
            'Site Notícia Já': '#239B56',         # Verde médio
            'Site Vânio Bosle Notícias': '#1E8449', # Verde escuro
            'Site SC Hoje News': '#148F77',       # Verde petróleo
            'Site Timbó Net': '#117A65',          # Verde escuro
            'Site Testo Notícias': '#0E6B5C',     # Verde muito escuro
            'Portal Tri': '#0A5D4E',              # Verde floresta
            'Site Jornalismo Digital': '#065A60', # Verde azulado escuro
            'Site Correio de Santa Catarina': '#145A32', # Verde escuro
            'Site Folha do Estado SC': '#196F3D', # Verde médio escuro
            'Site Rio Mafra Mix': '#1E8449',      # Verde
            'Site Michel Teixeira': '#239B56',    # Verde claro
            'Site Sul em Destaque': '#28B463',    # Verde médio
            'Site Alesc': '#2ECC71',              # Verde brilhante
            'Site Radio Mirador': '#58D68D',      # Verde água
            'Site Notícia No Ato': '#76D7C4',     # Verde claro
            'Site Içara News': '#85E3E3',         # Ciano
            'Site Jornal Extra SC': '#A2D9CE',    # Verde muito claro
            'Site Tropical FM 99': '#ABEBC6',     # Verde pastel
            'Site CREA-SC': '#D5F4E6',            # Verde muito claro
            'Outros': '#95A5A6',                  # Cinza neutro para categoria "Outros"
        }
        
        # Define cores baseada no tipo de dados
        if label_column == 'Jornal':
            # Para relatório por jornais - usa cores específicas azul/cinza
            colors = []
            fallback_colors_jornais = [
                '#2C3E50', '#34495E', '#3498DB', '#5D6D7E', '#85929E',
                '#2E4057', '#1B4F72', '#154360', '#1A5276', '#21618C',
                '#2874A6', '#2E86AB', '#5DADE2', '#7FB3D3', '#A9CCE3'
            ]
            
            for i, jornal in enumerate(valid_data[label_column]):
                # Busca cor específica primeiro
                jornal_name = jornal.split('/')[0].strip()  # Remove localidade
                cor = jornal_colors.get(jornal_name)
                if cor is None:
                    # Tenta com nome completo
                    cor = jornal_colors.get(jornal)
                
                if cor is None:
                    # Usa paleta de fallback baseada no índice
                    if i < len(fallback_colors_jornais):
                        cor = fallback_colors_jornais[i]
                    else:
                        # Para muitos jornais, gera cores baseadas em hash na tonalidade azul/cinza
                        import hashlib
                        hash_obj = hashlib.md5(jornal.encode())
                        hex_dig = hash_obj.hexdigest()[:6]
                        # Força cores na tonalidade azul/cinza para jornais
                        r = min(120, max(40, int(hex_dig[0:2], 16)))   # Limita vermelho
                        g = min(140, max(60, int(hex_dig[2:4], 16)))   # Limita verde
                        b = min(200, max(120, int(hex_dig[4:6], 16)))  # Favorece azul
                        
                        cor = f"#{r:02x}{g:02x}{b:02x}"
                
                colors.append(cor)
        elif label_column == 'Emissora':
            # Para relatório por emissoras - distingue entre TV e Rádio pelo título
            colors = []
            
            # Determina se é TV ou Rádio baseado no título
            is_radio = 'Rádio' in title or 'rádio' in title.lower()
            
            if is_radio:
                # Para emissoras de rádio - usa cores específicas turquesa/verde
                fallback_colors = [
                    '#1ABC9C', '#16A085', '#48C9B0', '#52C3A3', '#5DADE2',
                    '#85E3E3', '#76D7C4', '#58D68D', '#2ECC71', '#28B463',
                    '#148F77', '#17A2B8', '#1BA1A1', '#1E8E8E', '#20B2AA'
                ]
                
                for i, emissora in enumerate(valid_data[label_column]):
                    # Busca cor específica primeiro
                    cor = emissora_radio_colors.get(emissora)
                    if cor is None:
                        # Tenta com o nome sem espaços extras
                        cor = emissora_radio_colors.get(emissora.strip())
                    
                    if cor is None:
                        # Se ainda não encontrou, usa paleta de fallback baseada no índice
                        if i < len(fallback_colors):
                            cor = fallback_colors[i]
                        else:
                            # Para muitas emissoras, gera cores baseadas em hash na tonalidade turquesa
                            import hashlib
                            hash_obj = hashlib.md5(emissora.encode())
                            hex_dig = hash_obj.hexdigest()[:6]
                            # Força cores na tonalidade turquesa/verde para rádio
                            r = min(150, max(40, int(hex_dig[0:2], 16)))  # Limita vermelho
                            g = min(255, max(150, int(hex_dig[2:4], 16)))  # Aumenta verde
                            b = min(255, max(150, int(hex_dig[4:6], 16)))  # Aumenta azul
                            
                            cor = f"#{r:02x}{g:02x}{b:02x}"
                    
                    colors.append(cor)
            else:
                # Para emissoras de TV - usa cores específicas vibrantes
                fallback_colors = [
                    '#FF4757', '#2ED573', '#3742FA', '#FF6348', '#FFA502', 
                    '#A55EEA', '#FF3838', '#00D2D3', '#5352ED', '#FF9FF3',
                    '#54A0FF', '#5F27CD', '#00D8FF', '#FF9F43', '#10AC84'
                ]
                
                for i, emissora in enumerate(valid_data[label_column]):
                    # Busca cor específica primeiro
                    cor = emissora_colors.get(emissora)
                    if cor is None:
                        # Tenta com o nome sem espaços extras
                        cor = emissora_colors.get(emissora.strip())
                    
                    if cor is None:
                        # Se ainda não encontrou, usa paleta de fallback baseada no índice
                        if i < len(fallback_colors):
                            cor = fallback_colors[i]
                        else:
                            # Para muitas emissoras, gera cores baseadas em hash mais inteligentes
                            import hashlib
                            hash_obj = hashlib.md5(emissora.encode())
                            # Garante cores mais vibrantes usando apenas os primeiros 6 caracteres do hash
                            # e ajustando a luminosidade
                            hex_dig = hash_obj.hexdigest()[:6]
                            # Força cores mais vibrantes ajustando valores RGB
                            r = int(hex_dig[0:2], 16)
                            g = int(hex_dig[2:4], 16) 
                            b = int(hex_dig[4:6], 16)
                            
                            # Aumenta saturação das cores
                            r = min(255, max(100, r + 50))
                            g = min(255, max(100, g + 50))
                            b = min(255, max(100, b + 50))
                            
                            cor = f"#{r:02x}{g:02x}{b:02x}"
                    
                    colors.append(cor)
        elif label_column == 'Programa':
            # Para relatório de TV por programas - usa cores específicas
            colors = []
            # Paleta de cores diferenciada para programas não mapeados
            fallback_colors_programas = [
                '#8E44AD', '#E67E22', '#F39C12', '#27AE60', '#3498DB',
                '#E74C3C', '#9B59B6', '#1ABC9C', '#F1C40F', '#34495E',
                '#16A085', '#2980B9', '#C0392B', '#D35400', '#7F8C8D'
            ]
            
            for i, programa in enumerate(valid_data[label_column]):
                # Busca cor específica primeiro
                cor = programa_colors.get(programa)
                if cor is None:
                    # Tenta com o nome sem espaços extras
                    cor = programa_colors.get(programa.strip())
                
                if cor is None:
                    # Se ainda não encontrou, usa paleta de fallback baseada no índice
                    if i < len(fallback_colors_programas):
                        cor = fallback_colors_programas[i]
                    else:
                        # Para muitos programas, gera cores baseadas em hash
                        import hashlib
                        hash_obj = hashlib.md5(programa.encode())
                        hex_dig = hash_obj.hexdigest()[:6]
                        # Força cores mais diferenciadas para programas
                        r = int(hex_dig[0:2], 16)
                        g = int(hex_dig[2:4], 16) 
                        b = int(hex_dig[4:6], 16)
                        
                        # Ajusta cores para serem mais distintivas para programas
                        r = min(255, max(80, r + 30))
                        g = min(255, max(80, g + 30))
                        b = min(255, max(80, b + 30))
                        
                        cor = f"#{r:02x}{g:02x}{b:02x}"
                
                colors.append(cor)
        elif label_column == 'Site':
            # Para relatório por sites Web - usa cores específicas verde/esmeralda
            colors = []
            fallback_colors_sites = [
                '#27AE60', '#2ECC71', '#16A085', '#1ABC9C', '#48C9B0',
                '#52C3A3', '#58D68D', '#76D7C4', '#85E3E3', '#5DADE2',
                '#3498DB', '#2980B9', '#1B4F72', '#154360', '#28B463',
                '#239B56', '#1E8449', '#148F77', '#117A65', '#0E6B5C'
            ]
            
            for i, site in enumerate(valid_data[label_column]):
                # Busca cor específica primeiro
                cor = site_colors.get(site)
                if cor is None:
                    # Tenta com nome sem espaços extras
                    cor = site_colors.get(site.strip())
                
                if cor is None:
                    # Usa paleta de fallback baseada no índice
                    if i < len(fallback_colors_sites):
                        cor = fallback_colors_sites[i]
                    else:
                        # Para muitos sites, gera cores baseadas em hash na tonalidade verde
                        import hashlib
                        hash_obj = hashlib.md5(site.encode())
                        hex_dig = hash_obj.hexdigest()[:6]
                        # Força cores na tonalidade verde/esmeralda para sites
                        r = min(150, max(20, int(hex_dig[0:2], 16)))   # Limita vermelho
                        g = min(255, max(150, int(hex_dig[2:4], 16)))  # Favorece verde
                        b = min(200, max(100, int(hex_dig[4:6], 16)))  # Limita azul
                        
                        cor = f"#{r:02x}{g:02x}{b:02x}"
                
                colors.append(cor)
        else:
            # Para relatório por mídia - usa cores tradicionais
            colors = [midia_colors.get(midia, '#A0AEC0') for midia in valid_data[label_column]]
        
        # Configuração específica para emissoras/programas/jornais/sites vs mídias
        if label_column in ['Emissora', 'Programa', 'Jornal', 'Site']:
            # Para emissoras, programas, jornais e sites: gráfico com legenda abaixo, sem labels sobrepostas
            wedges, texts, autotexts = ax.pie(
                valid_data[value_column],
                labels=None,  # Remove labels do gráfico para usar legenda
                autopct='%1.1f%%',
                startangle=90,
                colors=colors,
                explode=[0.02] * len(valid_data),  # Separação menor para acomodar legenda abaixo
                textprops={'fontsize': 10, 'fontweight': 'bold'},
                pctdistance=0.85,
                wedgeprops={'edgecolor': 'white', 'linewidth': 2}
            )
            
            # Adiciona legenda AO LADO do gráfico usando o subplot dedicado
            if label_column == 'Jornal':
                legend_title = "Jornais"
                if len(valid_data) > 30:
                    fontsize = 7  # Fonte menor para muitos jornais
                elif len(valid_data) > 15:
                    fontsize = 8  # Fonte média
                else:
                    fontsize = 9  # Fonte maior para poucos jornais
            elif label_column == 'Emissora':
                legend_title = "Emissoras"
                fontsize = 9
            elif label_column == 'Site':
                legend_title = "Sites"
                if len(valid_data) > 40:
                    fontsize = 6  # Fonte menor para muitos sites
                elif len(valid_data) > 25:
                    fontsize = 7  # Fonte média alta
                elif len(valid_data) > 15:
                    fontsize = 8  # Fonte média
                else:
                    fontsize = 9  # Fonte maior para poucos sites
            else:  # Programa
                legend_title = "Programas"
                fontsize = 9
            
            # Verifica se temos dois subplots (para legenda ao lado)
            if 'ax_legend' in locals():
                # Remove eixos do subplot da legenda
                ax_legend.axis('off')
                
                # Cria legenda no subplot ao lado
                legend_elements = []
                for i, (wedge, label) in enumerate(zip(wedges, valid_data[label_column])):
                    legend_elements.append(plt.Line2D([0], [0], marker='o', color='w', 
                                                    markerfacecolor=wedge.get_facecolor(), 
                                                    markersize=10, label=label))
                
                ax_legend.legend(handles=legend_elements, 
                               title=legend_title,
                               loc='center left',
                               fontsize=fontsize,
                               title_fontsize=fontsize + 1,
                               frameon=True,
                               fancybox=True,
                               shadow=True)
            else:
                # Fallback: adiciona legenda abaixo se não temos subplot dedicado
                ax.legend(wedges, valid_data[label_column], 
                         title=legend_title,
                         loc="upper center", 
                         bbox_to_anchor=(0.5, -0.05),
                         fontsize=fontsize,
                         title_fontsize=fontsize + 1,
                         ncol=2,
                         columnspacing=1.0,
                         handletextpad=0.5)
        else:
            # Para mídias: formato original com labels
            wedges, texts, autotexts = ax.pie(
                valid_data[value_column],
                labels=valid_data[label_column],  # Usa a coluna de labels especificada
                autopct='%1.1f%%',
                startangle=90,
                colors=colors,
                explode=[0.02] * len(valid_data),  # Separação mínima para manter formato redondo
                textprops={'fontsize': 9, 'fontweight': 'bold'},
                pctdistance=0.82,
                labeldistance=1.1,  # Distância das labels
                wedgeprops={'edgecolor': 'white', 'linewidth': 2}  # Bordas brancas para destaque
            )
        
        # Estiliza os textos de porcentagem
        for autotext in autotexts:
            autotext.set_color('white')
            autotext.set_fontweight('bold')
            autotext.set_fontsize(9)
        
        # Estiliza as labels das mídias (se existirem)
        if texts:
            for text in texts:
                text.set_fontsize(8)
                text.set_fontweight('bold')
                text.set_color('#2c3e50')
        
        # Adiciona título
        ax.set_title(title, fontsize=12, fontweight='bold', pad=20, 
                    color='#2c3e50')
        
        # GARANTIR QUE O GRÁFICO SEJA PERFEITAMENTE REDONDO
        ax.set_aspect('equal')  # Força aspecto igual
        ax.axis('equal')  # Garante eixos iguais
        
        # Ajusta layout otimizado para legenda ao lado
        plt.tight_layout()
        if label_column in ['Emissora', 'Programa', 'Jornal', 'Site']:
            # Para gráficos com legenda ao lado, ajusta espaçamento entre subplots
            plt.subplots_adjust(wspace=0.3)  # Espaço horizontal entre gráfico e legenda
        
        # Salva em buffer com tamanho otimizado
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight',
                   facecolor='white', edgecolor='none', pad_inches=0.2)
        plt.close()
        
        buffer.seek(0)
        return buffer
    
    def create_combined_charts(self, noticias_data: pd.DataFrame, valores_data: pd.DataFrame):
        """
        Cria gráficos lado a lado para notícias e valores
        
        Args:
            noticias_data: DataFrame com dados de notícias
            valores_data: DataFrame com dados de valores
            
        Returns:
            BytesIO: Imagem com os dois gráficos
        """
        # Cores personalizadas
        colors = {
            'TV': '#FF6B6B',
            'Rádio': '#4ECDC4', 
            'Impresso': '#45B7D1',
            'Web': '#96CEB4'
        }
        
        # Validação e limpeza dos dados para evitar valores NaN
        # Limpa dados de notícias
        noticias_clean = noticias_data.copy()
        if 'quantidade' in noticias_clean.columns:
            noticias_clean['quantidade'] = pd.to_numeric(noticias_clean['quantidade'], errors='coerce').fillna(0)
            # Remove linhas com quantidade 0 ou NaN
            noticias_clean = noticias_clean[noticias_clean['quantidade'] > 0]
        
        # Limpa dados de valores  
        valores_clean = valores_data.copy()
        if 'valor' in valores_clean.columns:
            valores_clean['valor'] = pd.to_numeric(valores_clean['valor'], errors='coerce').fillna(0)
            # Remove linhas com valor 0 ou NaN
            valores_clean = valores_clean[valores_clean['valor'] > 0]
        
        # Verifica se há dados válidos
        if noticias_clean.empty and valores_clean.empty:
            return self._create_empty_chart("Gráficos Combinados - Sem dados válidos")
        
        # Cria figura com dois subplots
        fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(16, 8))
        fig.patch.set_facecolor('white')
        
        # Gráfico 1 - Notícias
        if not noticias_clean.empty and 'quantidade' in noticias_clean.columns and 'midia' in noticias_clean.columns:
            chart_colors1 = [colors.get(midia, '#95A5A6') for midia in noticias_clean['midia']]
            wedges1, texts1, autotexts1 = ax1.pie(
                noticias_clean['quantidade'], 
                labels=noticias_clean['midia'],
                colors=chart_colors1,
                autopct='%1.1f%%',
                startangle=90,
                textprops={'fontsize': 10}
            )
            
            for autotext in autotexts1:
                autotext.set_color('white')
                autotext.set_fontweight('bold')
        else:
            # Gráfico vazio para notícias
            ax1.text(0.5, 0.5, 'Sem dados de notícias válidos', 
                    ha='center', va='center', transform=ax1.transAxes,
                    fontsize=12, color='gray')
            ax1.set_xlim(0, 1)
            ax1.set_ylim(0, 1)
        
        ax1.set_title('Notícias Encontradas', fontsize=14, fontweight='bold', pad=20)
        
        # Gráfico 2 - Valores
        if not valores_clean.empty and 'valor' in valores_clean.columns and 'midia' in valores_clean.columns:
            chart_colors2 = [colors.get(midia, '#95A5A6') for midia in valores_clean['midia']]
            wedges2, texts2, autotexts2 = ax2.pie(
                valores_clean['valor'], 
                labels=valores_clean['midia'],
                colors=chart_colors2,
                autopct='%1.1f%%',
                startangle=90,
                textprops={'fontsize': 10}
            )
            
            for autotext in autotexts2:
                autotext.set_color('white')
                autotext.set_fontweight('bold')
        else:
            # Gráfico vazio para valores
            ax2.text(0.5, 0.5, 'Sem dados de valores válidos', 
                    ha='center', va='center', transform=ax2.transAxes,
                    fontsize=12, color='gray')
            ax2.set_xlim(0, 1)
            ax2.set_ylim(0, 1)
        
        ax2.set_title('Valores por Mídia (R$)', fontsize=14, fontweight='bold', pad=20)
        
        # Salva em bytes
        img_buffer = io.BytesIO()
        plt.tight_layout()
        plt.savefig(img_buffer, format='png', dpi=300, bbox_inches='tight',
                   facecolor='white', edgecolor='none')
        img_buffer.seek(0)
        
        plt.close(fig)  # Libera memória
        
        return img_buffer 
    
    def create_status_stacked_bar_chart(self, status_data: pd.DataFrame) -> io.BytesIO:
        """
        Cria gráfico de barras horizontais empilhadas para status por mídia (como no PHP antigo)
        
        Args:
            status_data: DataFrame com colunas ['midia', 'positivo', 'negativo', 'neutro', 'total']
            
        Returns:
            Buffer com a imagem do gráfico
        """
        if status_data.empty:
            return self._create_empty_chart("Status por Mídia")
        
        # Calcula percentuais - como no PHP antigo, cada barra soma 100%
        status_data = status_data.copy()
        
        # Converte todos os valores numéricos para float para evitar erros de tipo
        status_data['positivo'] = pd.to_numeric(status_data['positivo'], errors='coerce').fillna(0).astype(float)
        status_data['negativo'] = pd.to_numeric(status_data['negativo'], errors='coerce').fillna(0).astype(float)
        status_data['neutro'] = pd.to_numeric(status_data['neutro'], errors='coerce').fillna(0).astype(float)
        status_data['total'] = pd.to_numeric(status_data['total'], errors='coerce').fillna(0).astype(float)
        
        # Evita divisão por zero
        status_data['total'] = status_data['total'].replace(0, 1)
        
        status_data['perc_positivo'] = (status_data['positivo'] / status_data['total']) * 100
        status_data['perc_negativo'] = (status_data['negativo'] / status_data['total']) * 100
        status_data['perc_neutro'] = (status_data['neutro'] / status_data['total']) * 100
        
        # Garante que os percentuais sejam float
        status_data['perc_positivo'] = status_data['perc_positivo'].astype(float)
        status_data['perc_negativo'] = status_data['perc_negativo'].astype(float)
        status_data['perc_neutro'] = status_data['perc_neutro'].astype(float)
        
        # Cria figura para barras horizontais empilhadas
        fig, ax = plt.subplots(figsize=(10, 6))  # Figura maior para barras horizontais
        fig.patch.set_facecolor('white')
        
        # Configura dados para gráfico de barras horizontais empilhadas
        midias = status_data['midia'].tolist()
        y_pos = range(len(midias))
        
        # Cores para cada status (como usadas nas seções de análise)
        colors = {
            'Positivo': '#27ae60',   # Verde
            'Negativo': '#e74c3c',   # Vermelho  
            'Neutro': '#f39c12'      # Laranja
        }
        
        # Converte arrays para numpy arrays com dtype float para garantir compatibilidade
        perc_positivo = status_data['perc_positivo'].values.astype(float)
        perc_negativo = status_data['perc_negativo'].values.astype(float)
        perc_neutro = status_data['perc_neutro'].values.astype(float)
        
        # Cria barras horizontais empilhadas
        # Barra base - Positivo (começa do 0)
        bars_positivo = ax.barh(y_pos, perc_positivo, 
                               color=colors['Positivo'], label='Positivo',
                               height=0.6, alpha=0.9)
        
        # Barra intermediária - Negativo (empilhada sobre Positivo)
        bars_negativo = ax.barh(y_pos, perc_negativo, 
                               left=perc_positivo,
                               color=colors['Negativo'], label='Negativo',
                               height=0.6, alpha=0.9)
        
        # Barra final - Neutro (empilhada sobre Positivo + Negativo)
        bars_neutro = ax.barh(y_pos, perc_neutro, 
                             left=perc_positivo + perc_negativo,
                             color=colors['Neutro'], label='Neutro',
                             height=0.6, alpha=0.9)
        
        # Adiciona labels de porcentagem nas barras (somente se significativo)
        for i, (idx, row) in enumerate(status_data.iterrows()):
            y = i
            
            # Label para Positivo
            if row['perc_positivo'] > 5:  # Só mostra se maior que 5%
                ax.text(row['perc_positivo']/2, y, f"{row['perc_positivo']:.1f}%", 
                       ha='center', va='center', fontweight='bold', 
                       color='white', fontsize=9)
            
            # Label para Negativo  
            if row['perc_negativo'] > 5:  # Só mostra se maior que 5%
                ax.text(row['perc_positivo'] + row['perc_negativo']/2, y, f"{row['perc_negativo']:.1f}%",
                       ha='center', va='center', fontweight='bold', 
                       color='white', fontsize=9)
                       
            # Label para Neutro
            if row['perc_neutro'] > 5:  # Só mostra se maior que 5%
                ax.text(row['perc_positivo'] + row['perc_negativo'] + row['perc_neutro']/2, y, 
                       f"{row['perc_neutro']:.1f}%", ha='center', va='center', 
                       fontweight='bold', color='white', fontsize=9)
        
        # Configurações do gráfico
        ax.set_yticks(y_pos)
        ax.set_yticklabels(midias, fontsize=11, fontweight='bold')
        ax.set_xlabel('Percentual (%)', fontsize=12, fontweight='bold')
        ax.set_ylabel('Mídia', fontsize=12, fontweight='bold')
        ax.set_title('Distribuição de Status por Mídia (%)', 
                    fontsize=14, fontweight='bold', pad=20, color='#2c3e50')
        
        # Configurações específicas para barras empilhadas
        ax.set_xlim(0, 100)  # Cada barra sempre totaliza 100%
        ax.grid(True, alpha=0.3, axis='x', linestyle='--')
        
        # Legenda posicionada no melhor local
        ax.legend(loc='lower right', frameon=True, fancybox=True, shadow=True,
                 fontsize=10, bbox_to_anchor=(0.98, 0.02))
        
        # Inverte eixo Y para ter ordem similar ao PHP (TV no topo)
        ax.invert_yaxis()
        
        # Ajusta layout
        plt.tight_layout()
        
        # Salva em buffer
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight',
                   facecolor='white', edgecolor='none', pad_inches=0.2)
        plt.close()
        
        buffer.seek(0)
        return buffer