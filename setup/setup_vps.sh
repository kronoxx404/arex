#!/bin/bash
# Script de Instalación Automática para BetGod (VPS Ubuntu)

echo "🚀 Iniciando configuración del servidor..."

# 1. Actualizar sistema e instalar Apache, PHP y MariaDB
echo "📦 Instalando programas..."
sudo apt update -y
sudo apt install -y apache2 mariadb-server php php-mysql php-curl php-gd php-mbstring php-xml libapache2-mod-php unzip

# 2. Configurar Apache (Habilitar .htaccess y mod_rewrite)
echo "⚙️ Configurando Apache..."
sudo a2enmod rewrite
# Permitir .htaccess en /var/www/html
sudo sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
sudo systemctl restart apache2

# 3. Configurar Base de Datos (Igual que en local: admin / Jeyco420@)
echo "🗄️ Configurando Base de Datos..."
sudo mysql -e "CREATE DATABASE IF NOT EXISTS aire;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'admin'@'localhost' IDENTIFIED BY 'Jeyco420@';"
sudo mysql -e "GRANT ALL PRIVILEGES ON aire.* TO 'admin'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# 4. Limpiar carpeta html por defecto
echo "🧹 Limpiando directorio web..."
sudo rm -rf /var/www/html/*
sudo chown -R ubuntu:ubuntu /var/www/html # Si el usuario es ubuntu
sudo chown -R azureuser:azureuser /var/www/html # Si el usuario es azureuser (Azure default)
sudo chmod -R 755 /var/www/html

echo "✅ ¡Servidor configurado exitosamente!"
echo "Ahora puedes subir los archivos."
