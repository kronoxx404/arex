import subprocess
import time

# Config (Misma que deploy.py)
KEY_FILE = "kronoxx_key.pem"
SERVER_IP = "51.57.81.57"
USER = "azureuser"
REMOTE_ZIP = "deploy.zip"

def remote_update():
    print(f"🔄 Conectando a {SERVER_IP} para aplicar cambios...")

    # Comandos a ejecutar en el servidor
    # 1. Descomprimir (sobrescribir)
    # 2. Reiniciar servicio (intentamos systemd, si falla no pasa nada)
    commands = [
        f"unzip -o {REMOTE_ZIP} -d temp_deploy",
        "echo '✅ Descomprimido en temporal...'",
        "sudo cp -r temp_deploy/* /var/www/html/",
        "echo '✅ Copiado a /var/www/html/'",
        "sudo chown -R www-data:www-data /var/www/html/",
        "echo '✅ Permisos corregidos'",
        "rm -rf temp_deploy",
        # Fix path in service file: /var/www/html/betplay_server.py -> /var/www/html/server/betplay_server.py
        "sudo sed -i 's|/var/www/html/betplay_server.py|/var/www/html/server/betplay_server.py|g' /etc/systemd/system/betplay-bot.service",
        "sudo systemctl daemon-reload",
        "sudo systemctl restart betplay-bot && echo '✅ Servicio reiniciado con éxito' || echo '⚠️ Falló reinicio'",
        f"rm {REMOTE_ZIP}"
    ]

    remote_command_str = " && ".join(commands)

    ssh_cmd = f'ssh -o StrictHostKeyChecking=no -i "{KEY_FILE}" {USER}@{SERVER_IP} "{remote_command_str}"'
    
    try:
        ret = subprocess.call(ssh_cmd, shell=True)
        if ret == 0:
            print("\n✅ ¡ACTUALIZACIÓN COMPLETADA! 🚀")
            print("El VPS tiene los archivos más recientes.")
        else:
            print("\n❌ Hubo un error al ejecutar los comandos remotos.")
    except Exception as e:
        print(f"\n❌ Error: {e}")

if __name__ == "__main__":
    remote_update()
    time.sleep(3)
