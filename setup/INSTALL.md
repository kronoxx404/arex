# BetPlay Login Automation - Instrucciones de Instalación

## 📋 Requisitos

- Node.js (v16 o superior)
- npm (viene con Node.js)
- PHP con `shell_exec` habilitado
- Windows 10/11

## 🚀 Instalación

### Paso 1: Verificar Node.js

Abre PowerShell y ejecuta:

```powershell
node --version
```

Si no tienes Node.js, descárgalo de: https://nodejs.org/

### Paso 2: Instalar dependencias

En la carpeta del proyecto:

```powershell
cd C:\Users\jeyco\OneDrive\Desktop\betgod
npm install
```

Esto instalará Puppeteer y Chrome automáticamente.

### Paso 3: Probar el script de Node.js

```powershell
node betplay-login.js 1116042197 "Andrey29$"
```

Deberías ver un JSON con los tokens si el login es exitoso.

### Paso 4: Iniciar el servidor PHP

```powershell
php -S localhost:8000
```

### Paso 5: Probar en el navegador

Abre: http://localhost:8000

Ingresa las credenciales y prueba el login.

## 🐛 Solución de Problemas

### Error: "node no se reconoce como comando"
- Instala Node.js desde https://nodejs.org/
- Reinicia PowerShell después de instalar

### Error: "Cannot find module 'puppeteer'"
- Ejecuta: `npm install` en la carpeta del proyecto

### Error: "shell_exec() has been disabled"
- Edita `php.ini` y quita `shell_exec` de `disable_functions`

### El navegador no se abre
- Puppeteer está en modo headless (invisible)
- Para ver el navegador, cambia `headless: 'new'` a `headless: false` en `betplay-login.js`

## 📝 Notas

- El primer `npm install` tarda ~2 minutos (descarga Chrome)
- Cada login tarda 5-10 segundos
- Los tokens se guardan en la respuesta JSON
