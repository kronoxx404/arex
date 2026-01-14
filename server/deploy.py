import os
import zipfile
import subprocess
import shutil
import time

# Config
KEY_FILE = "kronoxx_key.pem"
SERVER_IP = "51.57.81.57"
USER = "azureuser"
# Usamos un nombre temporal unico para evitar errores de archivo bloqueado
LOCAL_ZIP = f"deploy_{int(time.time())}.zip"
REMOTE_ZIP = "deploy.zip"

EXCLUSIONS = [
    "deploy.zip", # Excluir el posible archivo bloqueado
    KEY_FILE,
    ".git",
    "node_modules",
    "__pycache__",
    ".vscode",
    "server_log.txt",
    "dist_temp",
    "deploy.py"
]

def zip_project():
    print(f"📦 Comprimiendo proyecto en {LOCAL_ZIP}...")
    
    try:
        with zipfile.ZipFile(LOCAL_ZIP, 'w', zipfile.ZIP_DEFLATED) as zipf:
            for root, dirs, files in os.walk("."):
                # Filtrar carpetas excluidas
                dirs[:] = [d for d in dirs if d not in EXCLUSIONS]
                
                for file in files:
                    # Excluir archivos que empiecen por deploy_ (nuestros zips temp)
                    if file.startswith("deploy_") and file.endswith(".zip"):
                        continue
                        
                    if file in EXCLUSIONS:
                        continue
                    if file.endswith(".pyc"):
                         continue
                    
                    file_path = os.path.join(root, file)
                    arcname = os.path.relpath(file_path, ".")
                    zipf.write(file_path, arcname)
        
        size = os.path.getsize(LOCAL_ZIP) / 1024
        print(f"✅ Zip creado: {size:.2f} KB")
        return True
    except Exception as e:
        print(f"❌ Error creando zip: {e}")
        return False

def upload_to_server():
    print(f"📤 Subiendo a {SERVER_IP}...")
    
    # Upload Zip -> Rename to deploy.zip on destination
    scp_cmd = f'scp -i "{KEY_FILE}" {LOCAL_ZIP} {USER}@{SERVER_IP}:~/{REMOTE_ZIP}'
    print(f"Ejecutando SCP...")
    ret = subprocess.call(scp_cmd, shell=True)
    
    if ret == 0:
        print("✅ Archivo subido exitosamente")
        
        # Upload setup script too just in case
        subprocess.call(f'scp -i "{KEY_FILE}" setup_bot_env.sh {USER}@{SERVER_IP}:~', shell=True)
        subprocess.call(f'scp -i "{KEY_FILE}" install_service.sh {USER}@{SERVER_IP}:~', shell=True)
        # Upload domain setup
        subprocess.call(f'scp -i "{KEY_FILE}" setup_domain.sh {USER}@{SERVER_IP}:~', shell=True)
        # Upload security setup
        subprocess.call(f'scp -i "{KEY_FILE}" setup_security.sh {USER}@{SERVER_IP}:~', shell=True)
        print("✅ Scripts de instalación subidos/actualizados")
        return True
    else:
        print("❌ Falló la subida (SCP error)")
        return False

def cleanup():
    try:
        if os.path.exists(LOCAL_ZIP):
            os.remove(LOCAL_ZIP)
            print("🧹 Archivo temporal limpiado")
    except:
        pass

if __name__ == "__main__":
    if zip_project():
        upload_to_server()
        cleanup()
        print("\n🚀 LISTO! Ahora ejecuta en el servidor:")
        print("bash setup_bot_env.sh")
