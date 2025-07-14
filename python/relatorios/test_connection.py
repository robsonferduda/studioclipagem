#!/usr/bin/env python3
"""
Script de teste para verificar a conectividade e dependências do sistema
"""

import sys
import os

def test_imports():
    """Testa se todas as dependências necessárias estão disponíveis"""
    print("🔍 Testando importações...")
    
    try:
        import psycopg2
        print("✅ psycopg2 - OK")
    except ImportError as e:
        print(f"❌ psycopg2 - Erro: {e}")
        return False
    
    try:
        import pandas
        print("✅ pandas - OK")
    except ImportError as e:
        print(f"❌ pandas - Erro: {e}")
        return False
    
    try:
        import reportlab
        print("✅ reportlab - OK")
    except ImportError as e:
        print(f"❌ reportlab - Erro: {e}")
        return False
    
    try:
        import matplotlib
        print("✅ matplotlib - OK")
    except ImportError as e:
        print(f"❌ matplotlib - Erro: {e}")
        return False
    
    try:
        import plotly
        print("✅ plotly - OK")
    except ImportError as e:
        print(f"❌ plotly - Erro: {e}")
        return False
    
    return True

def test_database_connection():
    """Testa a conexão com o banco de dados"""
    print("\n🔍 Testando conexão com banco de dados...")
    
    try:
        from config import DB_CONFIG
        print(f"✅ Configuração carregada: {DB_CONFIG['host']}:{DB_CONFIG['port']}")
    except ImportError as e:
        print(f"❌ Erro ao carregar configuração: {e}")
        return False
    
    try:
        from database import DatabaseManager
        db = DatabaseManager()
        
        if db.connect():
            print("✅ Conexão com banco de dados - OK")
            db.disconnect()
            return True
        else:
            print("❌ Falha na conexão com banco de dados")
            return False
    except Exception as e:
        print(f"❌ Erro na conexão: {e}")
        return False

def test_output_directory():
    """Testa se o diretório de saída pode ser criado"""
    print("\n🔍 Testando diretório de saída...")
    
    try:
        output_dir = "./output"
        os.makedirs(output_dir, exist_ok=True)
        
        # Testa escrita de arquivo
        test_file = os.path.join(output_dir, "test.txt")
        with open(test_file, 'w') as f:
            f.write("Teste de escrita")
        
        # Remove arquivo de teste
        os.remove(test_file)
        
        print("✅ Diretório de saída - OK")
        return True
    except Exception as e:
        print(f"❌ Erro no diretório de saída: {e}")
        return False

def main():
    """Função principal de teste"""
    print("🧪 TESTE DE SISTEMA DE RELATÓRIOS")
    print("=" * 50)
    
    tests_passed = 0
    total_tests = 3
    
    if test_imports():
        tests_passed += 1
    
    if test_database_connection():
        tests_passed += 1
    
    if test_output_directory():
        tests_passed += 1
    
    print("\n" + "=" * 50)
    print(f"📊 Resultados: {tests_passed}/{total_tests} testes passaram")
    
    if tests_passed == total_tests:
        print("🎉 Todos os testes passaram! Sistema pronto para usar.")
        return 0
    else:
        print("❌ Alguns testes falharam. Verifique as dependências.")
        return 1

if __name__ == "__main__":
    sys.exit(main()) 