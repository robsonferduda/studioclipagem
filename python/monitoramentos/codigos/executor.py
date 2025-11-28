#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Executor principal de monitoramentos
Permite executar todos os tipos de monitoramento ou tipos específicos
"""

import argparse
import sys
from datetime import datetime
from typing import List, Optional

from .web import executar_monitoramento_web
from .tv import executar_monitoramento_tv
from .radio import executar_monitoramento_radio
from .impresso import executar_monitoramento_impresso


class ExecutorMonitoramentos:
    """Executor principal para todos os tipos de monitoramento"""
    
    def __init__(self):
        self.executores = {
            'web': executar_monitoramento_web,
            'tv': executar_monitoramento_tv,
            'radio': executar_monitoramento_radio,
            'impresso': executar_monitoramento_impresso
        }
    
    def log(self, mensagem: str):
        """Log com timestamp"""
        timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        print(f"[{timestamp}] [EXECUTOR] {mensagem}")
    
    def executar_tipo(self, tipo_midia: str, grupo: Optional[int] = None):
        """Executa monitoramento de um tipo específico"""
        if tipo_midia not in self.executores:
            self.log(f"Tipo de mídia '{tipo_midia}' não encontrado")
            return False
        
        try:
            self.log(f"Iniciando monitoramento de {tipo_midia.upper()}" + 
                    (f" (grupo {grupo})" if grupo is not None else ""))
            
            self.executores[tipo_midia](grupo)
            
            self.log(f"Monitoramento de {tipo_midia.upper()} concluído")
            return True
            
        except Exception as e:
            self.log(f"Erro no monitoramento de {tipo_midia.upper()}: {e}")
            return False
    
    def executar_todos(self, tipos: List[str] = None, grupo: Optional[int] = None):
        """Executa todos os tipos de monitoramento ou uma lista específica"""
        tipos = tipos or list(self.executores.keys())
        
        self.log(f"Iniciando execução de monitoramentos: {', '.join(tipos).upper()}")
        
        sucessos = 0
        falhas = 0
        
        for tipo in tipos:
            if self.executar_tipo(tipo, grupo):
                sucessos += 1
            else:
                falhas += 1
        
        self.log(f"Execução concluída - Sucessos: {sucessos}, Falhas: {falhas}")
        return falhas == 0
    
    def listar_tipos_disponiveis(self):
        """Lista tipos de monitoramento disponíveis"""
        return list(self.executores.keys())


def main():
    """Função principal"""
    parser = argparse.ArgumentParser(
        description="Executor de monitoramentos de mídia",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Exemplos de uso:
  python executor.py                        # Executa todos os tipos
  python executor.py -t web                 # Executa apenas web
  python executor.py -t web tv              # Executa web e tv
  python executor.py -t web -g 1            # Executa web do grupo 1
  python executor.py --all-grupos           # Executa todos os grupos
  python executor.py --list                 # Lista tipos disponíveis
        """
    )
    
    parser.add_argument(
        '-t', '--tipos',
        nargs='*',
        choices=['web', 'tv', 'radio', 'impresso'],
        help='Tipos de mídia a executar (padrão: todos)'
    )
    
    parser.add_argument(
        '-g', '--grupo',
        type=int,
        help='Grupo específico a executar'
    )
    
    parser.add_argument(
        '--all-grupos',
        action='store_true',
        help='Executa todos os grupos (1-5) para cada tipo'
    )
    
    parser.add_argument(
        '--list',
        action='store_true',
        help='Lista tipos de monitoramento disponíveis'
    )
    
    args = parser.parse_args()
    
    executor = ExecutorMonitoramentos()
    
    # Lista tipos disponíveis
    if args.list:
        print("Tipos de monitoramento disponíveis:")
        for tipo in executor.listar_tipos_disponiveis():
            print(f"  - {tipo}")
        return
    
    # Determina tipos a executar
    tipos_executar = args.tipos if args.tipos else executor.listar_tipos_disponiveis()
    
    # Executa todos os grupos
    if args.all_grupos:
        sucesso_geral = True
        for grupo in range(1, 6):  # Grupos 1-5
            executor.log(f"=== EXECUTANDO GRUPO {grupo} ===")
            sucesso = executor.executar_todos(tipos_executar, grupo)
            if not sucesso:
                sucesso_geral = False
        
        sys.exit(0 if sucesso_geral else 1)
    
    # Execução normal
    sucesso = executor.executar_todos(tipos_executar, args.grupo)
    sys.exit(0 if sucesso else 1)


if __name__ == "__main__":
    main()

