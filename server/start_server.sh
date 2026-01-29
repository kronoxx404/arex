#!/bin/bash
# Wrapper manual para Xvfb y Python
exec > /tmp/betplay_boot.log 2>&1


# Limpiar locks anteriores
rm -f /tmp/.X99-lock /tmp/.X11-unix/X99

# Iniciar Xvfb en background
echo "🖥️ Iniciando Xvfb en :99..."
Xvfb :99 -screen 0 1280x1024x24 &
XVFB_PID=$!

# Esperar a que Xvfb arranque
sleep 2

# Exportar display
export DISPLAY=:99

echo "🚀 Iniciando Python Server..."
# Iniciar Python
/usr/bin/python3 /var/www/html/server/betplay_server.py

# Al salir python, matar Xvfb
kill $XVFB_PID
