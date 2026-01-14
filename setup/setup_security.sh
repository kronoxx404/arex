#!/bin/bash

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}🛡️ Configurando Seguridad del Servidor (Firewall + Fail2Ban)...${NC}"

# 1. Instalar UFW y Fail2Ban
sudo apt-get update
sudo apt-get install -y ufw fail2ban

# 2. Configurar UFW (Firewall Local)
echo -e "${GREEN}🧱 Configurando Firewall UFW...${NC}"
# Denegar todo por defecto
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Permitir SSH, HTTP, HTTPS
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Habilitar (sin pedir confirmación)
echo "y" | sudo ufw enable

echo -e "${GREEN}✅ Firewall UFW Activo. Reglas:${NC}"
sudo ufw status verbose

# 3. Configurar Fail2Ban (Protección contra fuerza bruta SSH)
echo -e "${GREEN}👮 Configurando Fail2Ban...${NC}"

# Crear copia de configuración local si no existe
if [ ! -f /etc/fail2ban/jail.local ]; then
    sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
fi

# Configurar protección SSH en jail.local
# (Esto busca intentos fallidos y banea la IP por 1 hora después de 5 intentos)
cat <<EOF | sudo tee /etc/fail2ban/jail.d/custom_ssh.conf
[sshd]
enabled = true
port = ssh
filter = sshd
logpath = /var/log/auth.log
maxretry = 5
bantime = 3600
findtime = 600
EOF

# Reiniciar Fail2Ban
sudo systemctl restart fail2ban
sudo systemctl enable fail2ban

echo -e "${GREEN}✅ Fail2Ban Activo y Protegiendo SSH.${NC}"
echo -e "${GREEN}🎉 Servidor Blindado!${NC}"
