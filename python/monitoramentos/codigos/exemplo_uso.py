#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Exemplo de como usar os monitoramentos individualmente
"""

from web import executar_monitoramento_web
from tv import executar_monitoramento_tv
from radio import executar_monitoramento_radio
from impresso import executar_monitoramento_impresso
from executor import ExecutorMonitoramentos


def exemplo_execucao_individual():
    """Exemplo de execução individual por tipo"""
    print("=== EXECUÇÃO INDIVIDUAL ===")
    
    # Executa monitoramento de web
    print("Executando monitoramento Web...")
    executar_monitoramento_web()
    
    # Executa monitoramento de web de um grupo específico
    print("Executando monitoramento Web do grupo 1...")
    executar_monitoramento_web(grupo=1)
    
    # Executa monitoramento de TV
    print("Executando monitoramento TV...")
    executar_monitoramento_tv()
    
    # Executa monitoramento de rádio
    print("Executando monitoramento Rádio...")
    executar_monitoramento_radio()
    
    # Executa monitoramento de impressos
    print("Executando monitoramento Impressos...")
    executar_monitoramento_impresso()


def exemplo_executor_completo():
    """Exemplo usando o executor principal"""
    print("=== EXECUÇÃO COM EXECUTOR ===")
    
    executor = ExecutorMonitoramentos()
    
    # Executa todos os tipos
    print("Executando todos os tipos...")
    executor.executar_todos()
    
    # Executa tipos específicos
    print("Executando apenas Web e TV...")
    executor.executar_todos(['web', 'tv'])
    
    # Executa com grupo específico
    print("Executando grupo 1...")
    executor.executar_todos(grupo=1)
    
    # Executa tipo específico com grupo
    print("Executando Web do grupo 2...")
    executor.executar_tipo('web', grupo=2)


def exemplo_programacao_cron():
    """Exemplos de como programar no cron"""
    print("""
    === EXEMPLOS PARA CRON ===
    
    # Executa todos os monitoramentos a cada 2 horas
    0 */2 * * * cd /path/to/python && python -m monitoramentos.codigos.executor
    
    # Executa monitoramentos web a cada hora
    0 * * * * cd /path/to/python && python -m monitoramentos.codigos.web
    
    # Executa grupo 1 de web a cada 30 minutos
    */30 * * * * cd /path/to/python && python -m monitoramentos.codigos.web -g 1
    
    # Executa todos os grupos sequencialmente
    0 */2 * * * cd /path/to/python && python -m monitoramentos.codigos.executor --all-grupos
    
    # Executa apenas TV e rádio a cada 3 horas
    0 */3 * * * cd /path/to/python && python -m monitoramentos.codigos.executor -t tv radio
    """)


if __name__ == "__main__":
    print("Escolha o exemplo a executar:")
    print("1 - Execução individual")
    print("2 - Executor completo")
    print("3 - Exemplos cron")
    
    escolha = input("Digite sua escolha (1-3): ")
    
    if escolha == "1":
        exemplo_execucao_individual()
    elif escolha == "2":
        exemplo_executor_completo()
    elif escolha == "3":
        exemplo_programacao_cron()
    else:
        print("Escolha inválida!")

