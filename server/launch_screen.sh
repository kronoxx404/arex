#!/bin/bash
# Lanza el bot dentro de una sesión screen llamada 'betbot'
# Útil si systemd falla por problemas de TTY/Environment

SESSION_NAME="betbot"

# Matar sesión anterior si existe
screen -X -S $SESSION_NAME quit || true

# Limpieza
rm -f /tmp/.X99-lock
killall -9 Xvfb python3

# Lanzar nueva sesión detachada
# Dentro de screen, corremos el wrapper start_server.sh (que ya maneja Xvfb)
screen -dmS $SESSION_NAME bash -c "/var/www/html/server/start_server.sh; exec bash"

echo "✅ Bot lanzado en screen '$SESSION_NAME'. Usa: screen -r $SESSION_NAME para ver."
