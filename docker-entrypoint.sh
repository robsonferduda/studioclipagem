#!/bin/bash

# Script de inicialização do container
echo "🚀 Iniciando container Laravel..."

# Navegar para o diretório da aplicação
cd /var/www/html

# Verificar se o link simbólico do storage existe
if [ ! -L "/var/www/html/public/storage" ]; then
    echo "🔗 Criando link simbólico do storage..."
    php artisan storage:link
else
    echo "✅ Link simbólico do storage já existe"
fi

# Verificar se o diretório de relatórios existe
if [ ! -d "/var/www/html/storage/app/public/relatorios" ]; then
    echo "📁 Criando diretório de relatórios..."
    mkdir -p /var/www/html/storage/app/public/relatorios
fi

# Ajustar permissões
echo "🔒 Ajustando permissões..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/python/relatorios/output

echo "✅ Inicialização concluída!"

# Iniciar Apache
echo "🌐 Iniciando Apache..."
exec apache2-foreground 