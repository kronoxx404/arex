import subprocess
import sys
import os

KEY_FILE = "kronoxx_key.pem"
SERVER_IP = "51.57.81.57"
USER = "azureuser"

if not os.path.exists(KEY_FILE):
    print(f"Error: {KEY_FILE} not found.")
    sys.exit(1)

# Command to run on the server
# We use sudo php to ensure it runs with permissions if needed, though usually www-data owns it.
# using full path.
cmd = "php /var/www/html/setup/update_db_admin.php"

ssh_cmd = f'ssh -o StrictHostKeyChecking=no -i "{KEY_FILE}" {USER}@{SERVER_IP} "sudo {cmd}"'

print(f"🔌 Connecting to {SERVER_IP} to run DB migration...")
print(f"Command: {cmd}")

ret = subprocess.call(ssh_cmd, shell=True)

if ret == 0:
    print("✅ Migration script executed successfully.")
else:
    print("❌ Migration script execution failed.")
    sys.exit(1)
