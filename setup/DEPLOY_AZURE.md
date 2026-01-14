# Guía de Despliegue en Azure (Linux VM)

Esta guía detalla paso a paso cómo desplegar el proyecto **BetPlay Bot** en una Máquina Virtual Linux (Ubuntu 20.04 o 22.04) en Azure.

## 1. Preparación de la VM

1.  **Crear la VM**:

    - En el portal de Azure, crea una nueva **Virtual Machine**.
    - **Imagen**: Ubuntu Server 20.04 LTS o 22.04 LTS.
    - **Tamaño**: Recomendado al menos **Standard_B2s** (2 vCPUs, 4GB RAM) para soportar el navegador Chrome/Selenium.
    - **Puertos de entrada**: Abre los puertos `80` (HTTP), `443` (HTTPS) y `22` (SSH) en el _Networking_ tab.

2.  **Conexión SSH**:
    - Conéctate a tu VM: `ssh usuario@tu-ip-publica`

## 2. Instalación de Dependencias

Ejecuta los siguientes comandos para actualizar el sistema e instalar lo necesario:

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Apache (Servidor Web) y utilidades
sudo apt install -y apache2 zip unzip git curl libmcrypt-dev

# Instalar PHP y módulos necesarios
sudo apt install -y php libapache2-mod-php php-mysql php-pgsql php-curl php-json php-gd php-mbstring php-xml php-zip

# Instalar Python 3 y PIP
sudo apt install -y python3 python3-pip python3-venv

# Instalar Google Chrome (Para Selenium)
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
sudo apt install -y ./google-chrome-stable_current_amd64.deb
```

## 3. Configuración del Proyecto

1.  **Subir el código**:

    - Puedes clonar tu repositorio o subir el archivo `.zip` del proyecto a `/var/www/html/`.
    - **Importante**: Asegúrate de que la estructura de carpetas sea la reorganizada (`assets`, `config`, `server`, etc.).

    ```bash
    # Ejemplo si subes un zip
    sudo rm /var/www/html/index.html
    sudo unzip betgod.zip -d /var/www/html/
    ```

2.  **Permisos**:

    - Asegúrate de que Apache tenga permisos sobre los archivos.

    ```bash
    sudo chown -R www-data:www-data /var/www/html/
    sudo chmod -R 755 /var/www/html/
    ```

3.  **Configurar Apache**:
    - Edita la configuración si es necesario (ej. para permitir `.htaccess`): `sudo nano /etc/apache2/sites-available/000-default.conf`
    - Asegúrate de tener `AllowOverride All` en el directorio `/var/www/html`.
    - Activa mod_rewrite: `sudo a2enmod rewrite`
    - Reinicia Apache: `sudo systemctl restart apache2`

## 4. Base de Datos (MySQL/MariaDB)

El proyecto está configurado para usar MySQL (según `install_db_mysql.php`) (o PostgreSQL según `database.sql`, ajusta según necesidad. Aquí asumo MySQL por ser más común en este stack).

1.  **Instalar MariaDB Server**:

    ```bash
    sudo apt install -y mariadb-server
    ```

2.  **Configuración inicial**:

    ```bash
    sudo mysql_secure_installation
    # Sigue los pasos (Set root password, Remove anonymous users, etc.)
    ```

3.  **Crear Base de Datos y Usuario**:
    Accede a MySQL: `sudo mysql -u root -p`

    ```sql
    CREATE DATABASE betgod_db;
    CREATE USER 'betuser'@'localhost' IDENTIFIED BY 'TuContraseñaSegura';
    GRANT ALL PRIVILEGES ON betgod_db.* TO 'betuser'@'localhost';
    FLUSH PRIVILEGES;
    EXIT;
    ```

4.  **Importar Tablas**:
    Usa el script proporcionado en `database/database.sql` (Ajustado para MySQL si es necesario, ya que el original tiene sintaxis PostgreSQL `SERIAL`).

    _Si usas MySQL, el SQL sería:_

    ```sql
    USE betgod_db;

    CREATE TABLE IF NOT EXISTS nequi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        estado INTEGER DEFAULT 1,
        ip_address VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS pse (
        id INT AUTO_INCREMENT PRIMARY KEY,
        estado INTEGER DEFAULT 1,
        ip_address VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ```

5.  **Actualizar Configuración**:
    Edita `config/config.php` y `config/db.php` con los datos de tu base de datos (`betgod_db`, `betuser`, contraseña).

## 5. Configuración del Servidor Python (Bot)

1.  **Instalar dependencias de Python**:
    Navega a la carpeta del servidor e instala los requerimientos.

    ```bash
    cd /var/www/html/server
    sudo pip3 install -r requirements.txt
    # Si no tienes requirements.txt, instala manualmente:
    sudo pip3 install undetected-chromedriver selenium flask requests
    ```

2.  **Probar el Bot**:
    Ejecuta manualmente para verificar que Chrome arranque (headless).
    ```bash
    python3 betplay_server.py
    ```

## 6. Configurar como Servicio (Systemd)

Para que el bot de Python se ejecute automáticamente y se reinicie si falla:

1.  **Copiar archivo de servicio**:
    El proyecto ya incluye `betplay-bot.service` (revisa la carpeta raíz o `setup/`).

    ```bash
    sudo cp /var/www/html/betplay-bot.service /etc/systemd/system/
    ```

2.  **Ajustar rutas en el servicio**:
    Edita el archivo si es necesario: `sudo nano /etc/systemd/system/betplay-bot.service`

    - Verifica `WorkingDirectory=/var/www/html/server`
    - Verifica `ExecStart=/usr/bin/python3 /var/www/html/server/betplay_server.py`

3.  **Activar el servicio**:

    ```bash
    sudo systemctl daemon-reload
    sudo systemctl enable betplay-bot
    sudo systemctl start betplay-bot
    ```

4.  **Verificar estado**:
    ```bash
    sudo systemctl status betplay-bot
    ```

## 7. Verificación Final

1.  Abre tu navegador y entra a `http://tu-ip-publica/`.
2.  Deberías ver la página de inicio de BetPlay.
3.  Prueba navegar y verificar que el bot responda (si usas funciones que llaman al backend Python).

---

**Nota**: Recuerda configurar HTTPS usando Certbot (LetsEncrypt) para producción:
`sudo apt install certbot python3-certbot-apache`
`sudo certbot --apache`
