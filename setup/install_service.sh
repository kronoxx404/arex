#!/bin/bash
echo "🤖 Instalando y Arrancando el Bot..."

# 1. Copiar archivo de servicio
SERVICE_PATH="/var/www/html/betplay-bot.service"
if [ -f "$SERVICE_PATH" ]; then
    sudo cp $SERVICE_PATH /etc/systemd/system/
    echo "📄 Servicio copiado a systemd"
else
    echo "❌ ERROR: No encuentro betplay-bot.service"
    exit 1
fi

# 2. Recargar demonios y arrancar
sudo systemctl daemon-reload
sudo systemctl enable betplay-bot
sudo systemctl restart betplay-bot

# 3. Verificación
echo "⏳ Esperando 5 segundos para verificar..."
sleep 5
STATUS=$(sudo systemctl is-active betplay-bot)

if [ "$STATUS" == "active" ]; then
    echo "✅ ¡EL BOT ESTÁ VIVO Y CORRIENDO! (Active)"
else
    echo "⚠️ Ojo: El estado es $STATUS. Revisa logs con: sudo journalctl -u betplay-bot -f"
fi
