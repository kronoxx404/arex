#!/bin/bash
# Script para instalar entorno del Bot (Python + Chrome)

echo "🐍 Instalando entorno Python y Chrome..."

# 1. Instalar dependencias del sistema
sudo apt update
sudo apt install -y python3-pip python3-venv unzip xvfb libnss3 libfontconfig1 python3-dev

# 2. Instalar Google Chrome
echo "🌐 Bajando Google Chrome..."
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
sudo apt install -y ./google-chrome-stable_current_amd64.deb
rm google-chrome-stable_current_amd64.deb

# 3. Configurar Entorno Virtual (VENV) - Obligatorio en Ubuntu 24.04
echo "📦 Configurando entorno virtual Python..."
sudo rm -rf /var/www/bot_env
sudo mkdir -p /var/www/bot_env
sudo chown -R $USER:$USER /var/www/bot_env

python3 -m venv /var/www/bot_env
source /var/www/bot_env/bin/activate

# Instalar librerías DENTRO del entorno virtual
pip install --upgrade pip
# Patch para Python 3.12 (distutils rmovido)
pip install setuptools 
pip install undetected-chromedriver==3.5.5 selenium==4.16.0 requests==2.31.0 flask

# 4. Descomprimir código del proyecto en /var/www/html
echo "📂 Desplegando código..."
if [ -f "deploy.zip" ]; then
    sudo rm -rf /var/www/html/*
    sudo unzip -o deploy.zip -d /var/www/html
    sudo chown -R www-data:www-data /var/www/html
    sudo chmod -R 755 /var/www/html
    echo "✅ Código desplegado en /var/www/html"
else
    echo "⚠️ No encontré deploy.zip. Asegúrate de subirlo primero."
fi

echo "✅ Entorno del Bot listo."
