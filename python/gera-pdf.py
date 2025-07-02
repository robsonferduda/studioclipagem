import os
import pandas as pd
from pdf_generator import PDFGenerator
from chart_generator import ChartGenerator
from database import DatabaseManager

def exemplo_gerar_pdf():
    # 1. Dados básicos
    usuario_id = 102
    data_inicio = "2025-05-01"
    data_fim = "2025-05-31"
    output_path = "./relatorios/relatorio_102_20250101_20250731.pdf"
    
    # 2. Conecta ao banco e busca dados
    db = DatabaseManager()
    db.connect()
    
    # Busca dados (você implementa estes métodos)
    noticias_raw = db.get_noticias_por_midia(usuario_id, data_inicio, data_fim)
    valores_raw = db.get_valores_por_midia(usuario_id, data_inicio, data_fim)  
    clipagens_raw = db.get_clipagens_detalhadas(usuario_id, data_inicio, data_fim)

    # 3. Converte para DataFrames (formato esperado)
    noticias_df = pd.DataFrame(noticias_raw)
    noticias_df_g = noticias_df.rename(columns={'midia': 'Mídia', 'quantidade': 'Qtd.'})
    
    valores_df = pd.DataFrame(valores_raw)
    valores_df_g = valores_df.rename(columns={'midia': 'Mídia', 'valor': 'Valor (R$)'})
    
    # 4. Gera gráficos
    chart_gen = ChartGenerator()
    noticias_chart = chart_gen.create_pie_chart(noticias_df_g, "Distribuição por Mídia", "Qtd.")
    valores_chart = chart_gen.create_pie_chart(valores_df_g, "Valores por Mídia", "Valor (R$)")
    
    # 5. Converte clipagens para DataFrame
    all_clipagens = []
    for midia, lista in clipagens_raw.items():
        for item in lista:
            if midia in ['TV', 'Rádio']:
                all_clipagens.append({
                    'Mídia': midia,
                    'Data': item['data'],
                    'Linha1 Data Programa Emissora': item['linha1_data_programa_emissora'],
                    'Linha2 Arquivo': item['linha2_arquivo'], 
                    'Linha3 Sinopse': item['linha3_sinopse'],
                    'Tempo': item['tempo'],
                    'Segundos': item.get('segundos', 0),
                    'Valor': item['valor']
                })
            else:  # Impresso e Web
                all_clipagens.append({
                    'Mídia': midia,
                    'Data': item['data'],
                    'Linha1 Data Programa Emissora': '',
                    'Linha2 Arquivo': item.get('arquivo', ''),
                    'Linha3 Sinopse': item.get('sinopse', ''),
                    'Tempo': '',
                    'Segundos': 0,
                    'Título Linha 1': item.get('titulo_linha1', ''),
                    'Título Linha 2': item.get('titulo_linha2', ''),
                    'Valor': item['valor']
                })
    
    clipagens_df = pd.DataFrame(all_clipagens)
    
    # 6. Filtros opcionais
    filtros = {
        'tipos_midia': ['radio'],
        'status': ['1', '0', '-1'],
        'retorno': ['com_retorno']
    }
    
    # 7. Cria diretório se não existir
    os.makedirs(os.path.dirname(output_path), exist_ok=True)
    
    # 8. 🎯 GERA O PDF COM TODOS OS PARÂMETROS
    pdf_generator = PDFGenerator()
    pdf_generator.generate_report(
        noticias_data=noticias_df,                    # DataFrame com colunas: Mídia, Qtd.
        valores_data=valores_df,                      # DataFrame com colunas: Mídia, Valor (R$)
        charts_buffer=None,                           # Sempre None (não usado)
        usuario_id=usuario_id,                        # int: 123
        data_inicio=data_inicio,                      # str: "2024-01-01"
        data_fim=data_fim,                           # str: "2024-01-31"
        output_path=output_path,                      # str: caminho completo do PDF
        noticias_chart_buffer=noticias_chart,         # io.BytesIO: gráfico pizza notícias
        valores_chart_buffer=valores_chart,           # io.BytesIO: gráfico pizza valores
        clipagens_data=clipagens_df,                  # DataFrame: dados detalhados
        database_manager=db,                          # Seu DatabaseManager (opcional)
        filtros=filtros                              # dict: filtros aplicados (opcional)
    )
    
    # 9. Limpa recursos
    db.disconnect()
    
    print(f"✅ PDF gerado: {output_path}")
    return output_path

# Exemplo de uso
if __name__ == "__main__":
    exemplo_gerar_pdf()