#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import os
from dotenv import load_dotenv

# Carrega variáveis de ambiente
load_dotenv(dotenv_path=os.path.join(os.path.dirname(__file__), '..', '..', '.env'))

# =========================
# CONFIGURAÇÕES DE BANCO
# =========================
DB_CONFIG = {
    'dbname': os.getenv('DB_DATABASE', 'studio_clipagem'),
    'user': os.getenv('DB_USERNAME', 'postgres'),
    'password': os.getenv('DB_PASSWORD', ''),
    'host': os.getenv('DB_HOST', 'localhost'),
    'port': int(os.getenv('DB_PORT', 5432))
}

# =========================
# CONFIGURAÇÕES DE EMAIL
# =========================
SMTP_HOST = os.getenv('SMTP_HOST', 'localhost')
SMTP_FROM = os.getenv('SMTP_FROM', 'boletins@clipagens.com.br')
SMTP_TO_FALLBACK = os.getenv('SMTP_TO_FALLBACK', 'robsonferduda@gmail.com')

# =========================
# CONFIGURAÇÕES DE BUSCA
# =========================
TS_CONFIG = os.getenv('TS_CONFIG', 'simple')  # ou 'portuguese'

# =========================
# CONFIGURAÇÕES DE INTERVALO
# =========================
INTERVALO_HORAS = int(os.getenv('INTERVALO_HORAS', 4))  # 4 horas por padrão

# =========================
# MAPEAMENTO DE TIPOS DE MÍDIA
# =========================
TIPOS_MIDIA = {
    'impresso': 1,
    'web': 2, 
    'radio': 3,
    'tv': 4,
    'facebook': 5,
    'instagram': 6
}

