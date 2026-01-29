@echo off
echo Conectando al servidor para ver logs en vivo...
echo Presiona Ctrl+C para salir.
ssh -o StrictHostKeyChecking=no -i "kronoxx_key.pem" azureuser@51.57.81.57 "sudo journalctl -u betplay-bot -f"
pause
