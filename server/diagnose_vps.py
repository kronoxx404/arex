import subprocess

# Config
KEY_FILE = "kronoxx_key.pem"
SERVER_IP = "51.57.81.57"
USER = "azureuser"

def check_status():
    print(f"🔍 Diagnosticar estado en {SERVER_IP}...")
    
    commands = [
        "echo '--- STATUS ---'",
        "sudo systemctl status betplay-bot --no-pager",
        "echo '--- LAST LOGS ---'",
        "sudo journalctl -u betplay-bot -n 30 --no-pager",
        "echo '--- PORT 5000 ---'",
        "sudo netstat -tuln | grep 5000 || echo 'Port 5000 NOT listening'"
    ]
    
    remote_command_str = " ; ".join(commands)
    ssh_cmd = f'ssh -o StrictHostKeyChecking=no -i "{KEY_FILE}" {USER}@{SERVER_IP} "{remote_command_str}"'
    
    subprocess.call(ssh_cmd, shell=True)

if __name__ == "__main__":
    check_status()
