#!/bin/bash
DOMAIN="betganadorygiros.online"
EMAIL="admin@betganadorygiros.online"

echo "🌐 Configurando Dominio: $DOMAIN"

# 1. Instalar Certbot
sudo apt install -y certbot python3-certbot-apache

# 2. Crear Configuración Apache
echo "📝 Creando VirtualHost..."
sudo bash -c "cat > /etc/apache2/sites-available/betgod.conf <<EOF
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAdmin $EMAIL
    DocumentRoot /var/www/html

    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF"

# 3. Activar Sitio
sudo a2dissite 000-default.conf
sudo a2ensite betgod.conf
sudo systemctl reload apache2
echo "✅ Apache configurado (Puerto 80)"

# 4. Instalar SSL (Certbot)
echo "🔒 Solicitando SSL (HTTPS)..."
echo "⚠️ Asegúrate de que el dominio $DOMAIN apunte a la IP del servidor antes de continuar."
sudo certbot --apache -d $DOMAIN --non-interactive --agree-tos -m $EMAIL --redirect

echo "✅ ¡Dominio y SSL Configurados!"
echo "👉 Entra a: https://$DOMAIN/install_db_mysql.php"
