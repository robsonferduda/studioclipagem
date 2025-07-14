#!/bin/bash

# Script para instalar Python e dependências no container Docker
# Útil para testar sem reconstruir a imagem

echo "🐍 Instalando Python e dependências no container..."

# Atualiza pacotes
echo "📦 Atualizando lista de pacotes..."
apt-get update

# Instala Python3 e pip
echo "📦 Instalando Python3 e pip..."
apt-get install -y python3 python3-pip python3-venv

# Cria link simbólico para python
echo "🔗 Criando link simbólico para python..."
ln -sf /usr/bin/python3 /usr/bin/python

# Instala dependências Python
echo "📦 Instalando dependências Python..."
if [ -f "/var/www/html/python/relatorios/requirements.txt" ]; then
    pip3 install -r /var/www/html/python/relatorios/requirements.txt
else
    echo "❌ Arquivo requirements.txt não encontrado!"
    exit 1
fi

# Cria diretório de saída
echo "📁 Criando diretório de saída..."
mkdir -p /var/www/html/python/relatorios/output
chown -R www-data:www-data /var/www/html/python/relatorios/output

# Testa instalação
echo "🧪 Testando instalação..."
cd /var/www/html/python/relatorios
python3 test_connection.py

echo "✅ Instalação concluída!"
echo "🎉 Agora você pode testar a geração de relatórios pela interface web." 