#!/usr/bin/env python3
"""
Sistema de Geração de Relatórios de Mídia

Este sistema conecta ao banco de dados MySQL, extrai dados de notícias e valores por mídia,
gera gráficos e produz um relatório PDF completo com clipagens detalhadas.

Autor: Sistema de Relatórios
Data: 2025
"""

from datetime import datetime
import os
import argparse
import pandas as pd
from database import DatabaseManager
from pdf_generator import PDFGenerator
from chart_generator import ChartGenerator

def generate_report(usuario_id: int, data_inicio: str, data_fim: str, output_dir: str = "./output", filtros: dict = None):
    """
    Gera relatório PDF com dados de mídia filtrados por usuário e período
    
    Args:
        usuario_id (int): ID do usuário para filtrar os dados
        data_inicio (str): Data de início no formato 'YYYY-MM-DD'
        data_fim (str): Data de fim no formato 'YYYY-MM-DD'
        output_dir (str): Diretório onde salvar o relatório
        filtros (dict): Filtros opcionais para aplicar aos dados
        
    Returns:
        str: Caminho do arquivo PDF gerado
        
    Exemplo:
        generate_report(
            usuario_id=123,
            data_inicio='2024-01-01', 
            data_fim='2024-01-31',
            filtros={'tipos_midia': ['web', 'tv'], 'status': ['positivo']}
        )
    """
    
    print("🚀 Iniciando geração do relatório completo...")
    print(f"📊 Usuário: {usuario_id}")
    print(f"📅 Período: {data_inicio} a {data_fim}")
    
    try:
        # 1. Conecta ao banco e busca dados
        print("\n1️⃣ Conectando ao banco de dados...")
        db = DatabaseManager()
        
        print("2️⃣ Buscando dados de notícias...")
        noticias_data = db.get_noticias_por_midia(usuario_id, data_inicio, data_fim, filtros)
        print(f"   ✅ {len(noticias_data)} registros de notícias encontrados")
        
        print("3️⃣ Buscando dados de valores...")
        valores_data = db.get_valores_por_midia(usuario_id, data_inicio, data_fim, filtros)
        print(f"   ✅ {len(valores_data)} registros de valores encontrados")
        
        print("4️⃣ Buscando clipagens detalhadas...")
        clipagens_data = db.get_clipagens_detalhadas(usuario_id, data_inicio, data_fim, filtros=filtros)
        total_clipagens = sum(len(lista) for lista in clipagens_data.values())
        print(f"   ✅ {total_clipagens} clipagens detalhadas encontradas")
        
        # 2. Gera gráficos individuais para cada seção
        print("\n5️⃣ Gerando gráficos individuais...")
        chart_generator = ChartGenerator()
        
        # Converte dados para DataFrames (corrigido)
        noticias_df = pd.DataFrame(noticias_data)
        if not noticias_df.empty:
            noticias_df = noticias_df.rename(columns={'midia': 'Mídia', 'quantidade': 'Qtd.'})
        
        valores_df = pd.DataFrame(valores_data)
        if not valores_df.empty:
            valores_df = valores_df.rename(columns={'midia': 'Mídia', 'valor': 'Valor (R$)'})
        
        # Gráfico para seção de notícias
        noticias_chart_buffer = chart_generator.create_pie_chart(
            noticias_df, "Distribuição por Mídia", "Qtd."
        )
        
        # Gráfico para seção de valores
        valores_chart_buffer = chart_generator.create_pie_chart(
            valores_df, "Valores por Mídia", "Valor (R$)"
        )
        
        print("   ✅ Gráficos individuais gerados com sucesso")
        
        # 3. Cria diretório de output se não existir
        os.makedirs(output_dir, exist_ok=True)
        
        # 4. Define nome do arquivo
        data_inicio_clean = data_inicio.replace('-', '')
        data_fim_clean = data_fim.replace('-', '')
        filename = f"relatorio_{usuario_id}_{data_inicio_clean}_{data_fim_clean}.pdf"
        output_path = os.path.join(output_dir, filename)
        
        # Converte clipagens para DataFrame com novos campos
        all_clipagens = []
        for midia, lista_clipagens in clipagens_data.items():
            for clipagem in lista_clipagens:
                if midia in ['TV', 'Rádio']:
                    # Novo formato unificado para TV e Rádio: linha1, linha2_arquivo, linha3_sinopse
                    all_clipagens.append({
                        'Mídia': midia,
                        'Data': clipagem['data'],
                        'Linha1 Data Programa Emissora': clipagem['linha1_data_programa_emissora'],
                        'Linha2 Arquivo': clipagem['linha2_arquivo'],
                        'Linha3 Sinopse': clipagem['linha3_sinopse'],
                        'Tempo': clipagem['tempo'],
                        'Segundos': clipagem.get('segundos', 0),
                        'Valor': clipagem['valor']
                    })
                else:
                    # Formato existente para outras mídias (Impresso e Web)
                    all_clipagens.append({
                        'Mídia': midia,
                        'Data': clipagem['data'],
                        'Linha1 Data Programa Emissora': '',
                        'Linha2 Arquivo': '',  # Sempre vazio para Web e Impresso
                        'Linha3 Sinopse': clipagem.get('sinopse', ''),
                        'Tempo': '',
                        'Segundos': 0,
                        'Título Linha 1': clipagem.get('titulo_linha1', ''),
                        'Título Linha 2': clipagem.get('titulo_linha2', ''),
                        'Valor': clipagem['valor']
                    })
        
        clipagens_df = pd.DataFrame(all_clipagens) if all_clipagens else pd.DataFrame()

        # 5. Gera PDF com layout moderno e clipagens detalhadas
        print("6️⃣ Gerando relatório PDF completo...")
        pdf_generator = PDFGenerator()
        pdf_generator.generate_report(
            noticias_data=noticias_df,
            valores_data=valores_df,
            charts_buffer=None,  # Não usado no novo layout
            usuario_id=usuario_id,
            data_inicio=data_inicio,
            data_fim=data_fim,
            output_path=output_path,
            noticias_chart_buffer=noticias_chart_buffer,
            valores_chart_buffer=valores_chart_buffer,
            clipagens_data=clipagens_df,  # Novo parâmetro convertido para DataFrame
            database_manager=db,  # Adicionado para as seções de retorno
            filtros=filtros  # Adicionado para aplicar filtros em todas as seções
        )
        
        # 6. Limpa recursos
        db.disconnect()
        
        print(f"\n🎉 Relatório completo gerado com sucesso!")
        print(f"📁 Arquivo: {output_path}")
        
        # Estatísticas do relatório
        print(f"\n📊 Estatísticas do relatório:")
        total_noticias = sum(item['quantidade'] for item in noticias_data)
        total_valor = sum(item['valor'] for item in valores_data)
        print(f"   📈 Total de notícias: {total_noticias:,}")
        print(f"   💰 Valor total: R$ {total_valor:,.2f}".replace(',', 'X').replace('.', ',').replace('X', '.'))
        print(f"   📰 Clipagens detalhadas: {total_clipagens}")
        print(f"   📄 Seções incluídas: Resumo + 4 listagens de mídia")
        
        return output_path
        
    except Exception as e:
        print(f"\n❌ Erro ao gerar relatório: {e}")
        raise

def main():
    """Função principal que gera o relatório PDF"""
    
    # Configuração do parser de argumentos
    parser = argparse.ArgumentParser(description='Gerador de Relatórios de Mídia')
    parser.add_argument('--cliente', type=int, required=True, help='ID do cliente')
    parser.add_argument('--data_inicio', type=str, required=True, help='Data início (YYYY-MM-DD)')
    parser.add_argument('--data_fim', type=str, required=True, help='Data fim (YYYY-MM-DD)')
    parser.add_argument('--output', type=str, required=True, help='Nome do arquivo PDF de saída')
    parser.add_argument('--filtros', type=str, default='{}', help='Filtros em formato JSON')
    
    args = parser.parse_args()
    
    # Processa filtros JSON
    import json
    try:
        filtros = json.loads(args.filtros) if args.filtros != '{}' else None
    except json.JSONDecodeError:
        print("❌ Erro ao decodificar filtros JSON")
        filtros = None
    
    print("=" * 70)
    print("🏢 SISTEMA DE RELATÓRIOS DE MÍDIA - VERSÃO COMPLETA")
    print("📋 Inclui: Resumo + Clipagens Detalhadas por Mídia")
    print("=" * 70)
    
    print("\n🚀 Iniciando geração do relatório completo...")
    print(f"📊 Usuário: {args.cliente}")
    print(f"📅 Período: {args.data_inicio} a {args.data_fim}")
    
    # Instancia o gerenciador de banco de dados
    db = DatabaseManager()
    
    print("\n1️⃣ Conectando ao banco de dados...")
    
    # Verifica se o cliente existe
    if not db.check_cliente(args.cliente):
        print("\n❌ Não é possível gerar o relatório: cliente não encontrado")
        return
    
    print("\n2️⃣ Buscando dados de notícias...")
    noticias_data = db.get_noticias_por_midia(args.cliente, args.data_inicio, args.data_fim, filtros)
    
    print("\n3️⃣ Buscando dados de valores...")
    valores_data = db.get_valores_por_midia(args.cliente, args.data_inicio, args.data_fim, filtros)
    
    print("\n4️⃣ Buscando clipagens detalhadas...")
    clipagens_data = db.get_clipagens_detalhadas(args.cliente, args.data_inicio, args.data_fim, filtros=filtros)
    
    # Gera gráficos individuais
    print("\n5️⃣ Gerando gráficos individuais...")
    chart_gen = ChartGenerator()
    
    # Gráfico de notícias
    noticias_df = pd.DataFrame(noticias_data)
    noticias_chart_buffer = chart_gen.create_pie_chart(noticias_df, "Notícias por Mídia", "quantidade", label_column="midia")
    
    # Gráfico de valores
    valores_df = pd.DataFrame(valores_data)
    valores_chart_buffer = chart_gen.create_pie_chart(valores_df, "Valores por Mídia", "valor", label_column="midia")
    
    # Gráfico geral (combinado)
    charts_buffer = chart_gen.create_combined_charts(noticias_df, valores_df)
    print("   ✅ Gráficos individuais gerados com sucesso")
    
    # Gera o PDF
    print("\n6️⃣ Gerando relatório PDF completo...")
    pdf_gen = PDFGenerator()
    
    # Cria diretório de saída se não existir
    output_dir = "./output"
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)
    
    # Define nome do arquivo de saída
    output_filename = f"relatorio_{args.cliente}_{args.data_inicio.replace('-','')}_{args.data_fim.replace('-','')}.pdf"
    output_path = os.path.join(output_dir, output_filename)
    
    # Gera o relatório
    pdf_gen.generate_report(
        noticias_data=pd.DataFrame(noticias_data),
        valores_data=pd.DataFrame(valores_data),
        charts_buffer=charts_buffer,
        usuario_id=args.cliente,
        data_inicio=args.data_inicio,
        data_fim=args.data_fim,
        output_path=output_path,
        noticias_chart_buffer=noticias_chart_buffer,
        valores_chart_buffer=valores_chart_buffer,
        clipagens_data=clipagens_data,
        database_manager=db,  # Adicionado para as seções de retorno
        filtros=filtros  # Adicionado para aplicar filtros em todas as seções
    )
    
    # Desconecta do banco
    db.disconnect()
    print("🔌 Desconectado do banco")
    
    # Imprime resumo
    print("\n🎉 Relatório completo gerado com sucesso!")
    print(f"📁 Arquivo: {output_path}")
    
    total_noticias = sum(item['quantidade'] for item in noticias_data)
    total_valor = sum(item['valor'] for item in valores_data)
    total_clipagens = sum(len(clipagens) for clipagens in clipagens_data.values())
    
    print("\n📊 Estatísticas do relatório:")
    print(f"   📈 Total de notícias: {total_noticias}")
    print(f"   💰 Valor total: R$ {total_valor:,.2f}")
    print(f"   📰 Clipagens detalhadas: {total_clipagens}")
    print(f"   📄 Seções incluídas: Resumo + 4 listagens de mídia")
    
    print("\n✅ Processo concluído!")
    print(f"📄 Relatório disponível em: {output_path}")
    
    print("\n🔍 O relatório inclui:")
    print("   • Página 1: Resumo com gráficos")
    print("   • Página 2+: Clipagens detalhadas por mídia")
    print("     - Clipagens de Mídia TV")
    print("     - Clipagens de Mídia Rádio")
    print("     - Clipagens de Mídia Impressa")
    print("     - Clipagens de Mídia Web")
    print("   • Páginas intermediárias: Relatórios de análise")
    print("     - Relatório por veículos - TV")
    print("     - Relatório por programas - TV")
    print("     - Relatório por veículos - Rádio")
    print("     - Relatório por programas - Rádio")
    print("   • Páginas finais: Retornos por mídia")
    print("     - Retornos de Mídia TV")
    print("     - Retornos de Mídia Rádio")
    print("     - Retornos de Mídia Web")
    print("     - Retornos de Mídia Impressa")

if __name__ == "__main__":
    main() 