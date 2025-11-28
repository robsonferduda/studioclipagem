#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Script de teste para validar o sistema de monitoramentos
"""

import sys
import traceback
from datetime import datetime

def test_imports():
    """Testa se todos os módulos podem ser importados"""
    print("=== TESTE DE IMPORTAÇÕES ===")
    
    try:
        from . import config
        print("✅ config importado com sucesso")
    except Exception as e:
        print(f"❌ Erro ao importar config: {e}")
        return False
    
    try:
        from .base import BaseMonitoramento
        print("✅ base importado com sucesso")
    except Exception as e:
        print(f"❌ Erro ao importar base: {e}")
        return False
    
    try:
        from .web import MonitoramentoWeb
        print("✅ web importado com sucesso")
    except Exception as e:
        print(f"❌ Erro ao importar web: {e}")
        return False
    
    try:
        from .tv import MonitoramentoTV
        print("✅ tv importado com sucesso")
    except Exception as e:
        print(f"❌ Erro ao importar tv: {e}")
        return False
    
    try:
        from .radio import MonitoramentoRadio
        print("✅ radio importado com sucesso")
    except Exception as e:
        print(f"❌ Erro ao importar radio: {e}")
        return False
    
    try:
        from .impresso import MonitoramentoImpresso
        print("✅ impresso importado com sucesso")
    except Exception as e:
        print(f"❌ Erro ao importar impresso: {e}")
        return False
    
    try:
        from .executor import ExecutorMonitoramentos
        print("✅ executor importado com sucesso")
    except Exception as e:
        print(f"❌ Erro ao importar executor: {e}")
        return False
    
    return True

def test_config():
    """Testa configurações"""
    print("\n=== TESTE DE CONFIGURAÇÕES ===")
    
    try:
        from .config import DB_CONFIG, TIPOS_MIDIA, TS_CONFIG
        
        print(f"✅ Configuração de banco: {DB_CONFIG['host']}:{DB_CONFIG['port']}")
        print(f"✅ Tipos de mídia: {list(TIPOS_MIDIA.keys())}")
        print(f"✅ TS Config: {TS_CONFIG}")
        
        return True
    except Exception as e:
        print(f"❌ Erro nas configurações: {e}")
        return False

def test_classes():
    """Testa instanciação das classes"""
    print("\n=== TESTE DE CLASSES ===")
    
    try:
        from .web import MonitoramentoWeb
        from .tv import MonitoramentoTV
        from .radio import MonitoramentoRadio
        from .impresso import MonitoramentoImpresso
        from .executor import ExecutorMonitoramentos
        
        # Testa instanciação
        web = MonitoramentoWeb()
        print(f"✅ MonitoramentoWeb: {web.nome_midia} (tipo {web.tipo_midia})")
        
        tv = MonitoramentoTV()
        print(f"✅ MonitoramentoTV: {tv.nome_midia} (tipo {tv.tipo_midia})")
        
        radio = MonitoramentoRadio()
        print(f"✅ MonitoramentoRadio: {radio.nome_midia} (tipo {radio.tipo_midia})")
        
        impresso = MonitoramentoImpresso()
        print(f"✅ MonitoramentoImpresso: {impresso.nome_midia} (tipo {impresso.tipo_midia})")
        
        executor = ExecutorMonitoramentos()
        print(f"✅ ExecutorMonitoramentos: {len(executor.executores)} executores")
        
        return True
    except Exception as e:
        print(f"❌ Erro nas classes: {e}")
        return False

def test_database_connection():
    """Testa conexão com banco de dados"""
    print("\n=== TESTE DE CONEXÃO COM BANCO ===")
    
    try:
        from .web import MonitoramentoWeb
        
        web = MonitoramentoWeb()
        if web.conectar_db():
            print("✅ Conexão com banco estabelecida")
            web.desconectar_db()
            print("✅ Desconexão realizada")
            return True
        else:
            print("❌ Falha na conexão com banco")
            return False
            
    except Exception as e:
        print(f"❌ Erro na conexão: {e}")
        return False

def test_utility_functions():
    """Testa funções utilitárias"""
    print("\n=== TESTE DE FUNÇÕES UTILITÁRIAS ===")
    
    try:
        from .base import BaseMonitoramento
        from .config import TIPOS_MIDIA
        
        base = BaseMonitoramento(TIPOS_MIDIA['web'], 'Test')
        
        # Testa parse de IDs
        ids = base.parse_lista_ids("1,2,3,4,5")
        assert ids == [1, 2, 3, 4, 5], f"Parse IDs falhou: {ids}"
        print("✅ Parse de IDs funcionando")
        
        # Testa geração de datas
        dt_inicial, dt_final = base.gerar_datas_padrao()
        assert dt_inicial < dt_final, "Datas padrão incorretas"
        print("✅ Geração de datas funcionando")
        
        # Testa log
        base.log("Teste de log")
        print("✅ Sistema de log funcionando")
        
        return True
    except Exception as e:
        print(f"❌ Erro nas funções utilitárias: {e}")
        return False

def test_executor():
    """Testa executor principal"""
    print("\n=== TESTE DE EXECUTOR ===")
    
    try:
        from .executor import ExecutorMonitoramentos
        
        executor = ExecutorMonitoramentos()
        
        # Testa listagem de tipos
        tipos = executor.listar_tipos_disponiveis()
        expected = ['web', 'tv', 'radio', 'impresso']
        assert set(tipos) == set(expected), f"Tipos incorretos: {tipos}"
        print("✅ Listagem de tipos funcionando")
        
        # Note: Não executamos os monitoramentos reais no teste para evitar efeitos colaterais
        print("✅ Executor instanciado corretamente")
        
        return True
    except Exception as e:
        print(f"❌ Erro no executor: {e}")
        return False

def run_all_tests():
    """Executa todos os testes"""
    print("🔍 INICIANDO TESTES DO SISTEMA DE MONITORAMENTOS")
    print("=" * 60)
    
    tests = [
        ("Importações", test_imports),
        ("Configurações", test_config),
        ("Classes", test_classes),
        ("Funções Utilitárias", test_utility_functions),
        ("Executor", test_executor),
        ("Conexão Banco", test_database_connection),  # Por último pois pode falhar
    ]
    
    results = []
    
    for test_name, test_func in tests:
        try:
            success = test_func()
            results.append((test_name, success))
        except Exception as e:
            print(f"\n❌ ERRO CRÍTICO no teste {test_name}:")
            traceback.print_exc()
            results.append((test_name, False))
    
    # Resumo
    print("\n" + "=" * 60)
    print("📋 RESUMO DOS TESTES")
    print("=" * 60)
    
    passed = 0
    failed = 0
    
    for test_name, success in results:
        status = "✅ PASSOU" if success else "❌ FALHOU"
        print(f"{test_name:20} | {status}")
        if success:
            passed += 1
        else:
            failed += 1
    
    print(f"\n🎯 RESULTADO FINAL: {passed} passaram, {failed} falharam")
    
    if failed == 0:
        print("🎉 TODOS OS TESTES PASSARAM! Sistema pronto para uso.")
        return True
    else:
        print("⚠️  ALGUNS TESTES FALHARAM. Verifique as configurações.")
        return False

if __name__ == "__main__":
    success = run_all_tests()
    sys.exit(0 if success else 1)

