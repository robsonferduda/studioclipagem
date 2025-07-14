#!/usr/bin/env python3
"""
Configuração de conexão com o banco de dados
Este arquivo pode ser usado para configurar a conexão com o banco de dados
de forma flexível, lendo variáveis de ambiente ou usando configurações padrão
"""

import os
from dotenv import load_dotenv

# Carrega variáveis de ambiente do arquivo .env se existir
load_dotenv()

# Configurações do banco de dados
DB_CONFIG = {
    'host': os.getenv('DB_HOST', 'studioclipagemdb.cvxurxqqog54.us-east-1.rds.amazonaws.com'),
    'port': int(os.getenv('DB_PORT', 5432)),
    'user': os.getenv('DB_USERNAME', 'postgres'),
    'password': os.getenv('DB_PASSWORD', 'AASsdas213das21sd'),
    'database': os.getenv('DB_DATABASE', 'studio_clipagem')
}

# Configurações de paths
STORAGE_PATH = os.getenv('STORAGE_PATH', './output')
TEMP_PATH = os.getenv('TEMP_PATH', './temp')

# Configurações do sistema
DEBUG = os.getenv('DEBUG', 'false').lower() == 'true' 