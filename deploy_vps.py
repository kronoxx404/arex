import os
import zipfile
import subprocess
import time
import sys

# Config from server/deploy.py
KEY_FILE = "kronoxx_key.pem"
SERVER_IP = "51.57.81.57"
USER = "azureuser"
LOCAL_ZIP = f"betgod_full_deploy_{int(time.time())}.zip"
REMOTE_ZIP = "betgod_deploy.zip"
REMOTE_DIR = "/var/www/html" # Standard for Apache

EXCLUSIONS = [
    ".git", ".vscode", ".idea", "__pycache__", "node_modules", 
    "venv", "env", ".DS_Store", "deploy.py", "betgod_deploy_v2.zip"
]

def zip_project():
    print(f"📦 Zipping project to {LOCAL_ZIP}...")
    try:
        with zipfile.ZipFile(LOCAL_ZIP, 'w', zipfile.ZIP_DEFLATED) as zipf:
            for root, dirs, files in os.walk("."):
                # Modify dirs in-place to skip excluded directories
                dirs[:] = [d for d in dirs if d not in EXCLUSIONS]
                
                for file in files:
                    # Skip the zip file itself and other excluded files
                    if file == LOCAL_ZIP or file in EXCLUSIONS or file.endswith(".zip") or file.endswith(".log"):
                        continue
                        
                    file_path = os.path.join(root, file)
                    arcname = os.path.relpath(file_path, ".")
                    zipf.write(file_path, arcname)
        
        size_mb = os.path.getsize(LOCAL_ZIP) / (1024 * 1024)
        print(f"✅ Zip created: {size_mb:.2f} MB")
        return True
    except Exception as e:
        print(f"❌ Error creating zip: {e}")
        return False

def upload_and_deploy():
    print(f"📤 Uploading to {USER}@{SERVER_IP}...")
    
    # 1. Upload Zip
    scp_cmd = f'scp -o StrictHostKeyChecking=no -i "{KEY_FILE}" {LOCAL_ZIP} {USER}@{SERVER_IP}:~/{REMOTE_ZIP}'
    if subprocess.call(scp_cmd, shell=True) != 0:
        print("❌ SCP Upload failed.")
        return False
    print("✅ Upload successful.")

    # 2. Deploy on Server
    print("🚀 Executing remote deployment commands...")
    
    remote_commands = (
        f"sudo rm -rf {REMOTE_DIR}/* && "
        f"sudo unzip -o ~/{REMOTE_ZIP} -d {REMOTE_DIR}/ && "
        f"sudo chown -R www-data:www-data {REMOTE_DIR}/ && "
        f"sudo chmod -R 755 {REMOTE_DIR}/ && "
        f"echo '✅ Deployed to {REMOTE_DIR}'"
    )
    
    ssh_cmd = f'ssh -o StrictHostKeyChecking=no -i "{KEY_FILE}" {USER}@{SERVER_IP} "{remote_commands}"'
    
    if subprocess.call(ssh_cmd, shell=True) != 0:
        print("❌ Remote deployment commands failed.")
        return False
    
    print("✅ Deployment Complete!")
    return True

def cleanup():
    if os.path.exists(LOCAL_ZIP):
        os.remove(LOCAL_ZIP)
        print("🧹 Local zip cleaned up.")

if __name__ == "__main__":
    if not os.path.exists(KEY_FILE):
        print(f"❌ Key file '{KEY_FILE}' not found in current directory.")
        sys.exit(1)
        
    if zip_project():
        if upload_and_deploy():
            print("\n🎉 SUCCESS! Project updated on VPS.")
        cleanup()
