import subprocess
import time

KEY_FILE = "kronoxx_key.pem"
SERVER_IP = "51.57.81.57"
USER = "azureuser"
LOCAL_SERVICE = "server/betplay-bot.service"
REMOTE_SERVICE = "betplay-bot.service"

def fix_service():
    print(f"🔧 Reparando servicio en {SERVER_IP}...")

    # 1. Upload Service File
    scp_cmd = f'scp -o StrictHostKeyChecking=no -i "{KEY_FILE}" {LOCAL_SERVICE} {USER}@{SERVER_IP}:~/{REMOTE_SERVICE}'
    if subprocess.call(scp_cmd, shell=True) != 0:
        print("❌ Error subiendo archivo de servicio")
        return

    print("✅ Archivo de servicio subido")

    # 2. Install and Restart
    commands = [
        f"sudo mv ~/{REMOTE_SERVICE} /etc/systemd/system/{REMOTE_SERVICE}",
        "sudo systemctl daemon-reload",
        "sudo systemctl restart betplay-bot",
        "sudo systemctl status betplay-bot --no-pager"
    ]
    
    remote_command_str = " && ".join(commands)
    ssh_cmd = f'ssh -o StrictHostKeyChecking=no -i "{KEY_FILE}" {USER}@{SERVER_IP} "{remote_command_str}"'
    
    subprocess.call(ssh_cmd, shell=True)

if __name__ == "__main__":
    fix_service()
