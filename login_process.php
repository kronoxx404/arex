<?php
session_start();
// login_process.php - BetPlay Login con Puppeteer
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// Recibir datos JSON
$input = json_decode(file_get_contents('php://input'), true);

$user = $input['username'] ?? '';
$pass = $input['password'] ?? '';
$honeypot = $input['honeypot'] ?? '';

// --- SECURITY CHECKS ---
require_once __DIR__ . '/server/security_utils.php';

// 0. HONEYPOT CHECK
if (!empty($honeypot)) {
    // Si el campo oculto tiene valor, es un bot
    file_put_contents('debug_login.txt', date('Y-m-d H:i:s') . " - BOT BLOCKED (Honeypot): $user IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL, FILE_APPEND);
    die(json_encode(['status' => 'error', 'message' => 'Error de validación']));
}

// 1. User-Agent Block
if (!checkUserAgent()) {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => 'Acceso denegado (UA)']));
}

// 2. Rate Limiting (5 attempts / 60s)
$client_ip = $_SERVER['REMOTE_ADDR'];
if (!checkRateLimit($client_ip, 5, 60)) {
    // Loguear el bloqueo
    file_put_contents('debug_login.txt', date('Y-m-d H:i:s') . " - Rate Limit Exceeded: $client_ip" . PHP_EOL, FILE_APPEND);

    echo json_encode([
        'status' => 'error',
        'message' => 'Demasiados intentos. Intente nuevamente en 1 minuto.'
    ]);
    exit;
}
// -----------------------

// Log para debugging
file_put_contents('debug_login.txt', date('Y-m-d H:i:s') . " - Login attempt: User=$user" . PHP_EOL, FILE_APPEND);

// Validar que se recibieron credenciales
if (empty($user) || empty($pass)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Usuario y contraseña son requeridos'
    ]);
    exit;
}

// ============================================
// EJECUTAR PUPPETEER PARA LOGIN REAL
// ============================================

// ============================================
// CONEXIÓN CON SERVIDOR PERSISTENTE PYTHON
// ============================================

// URL del servidor local
$serverUrl = 'http://localhost:5000/login';

// Datos a enviar
$data = json_encode([
    'username' => $user,
    'password' => $pass
]);

// La sesión se mantendrá abierta para poder guardar los datos después del cURL
// if (session_status() === PHP_SESSION_ACTIVE) {
//     session_write_close();
// }

// Iniciar cURL
$ch = curl_init($serverUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data)
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 180); // Aumentado a 180s para colas de espera

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log de depuración
file_put_contents('debug_login.txt', "Servidor Python ($httpCode): " . ($response ?: "Error: $curlError") . PHP_EOL, FILE_APPEND);

if ($httpCode !== 200 || !$response) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error de conexión con el servidor de login. Asegúrate de ejecutar run_server.bat',
        'debug' => $curlError
    ]);
    exit;
}

// Parsear respuesta del servidor
$result = json_decode($response, true);

if (!$result) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Respuesta inválida del servidor de login',
        'debug' => $response
    ]);
    exit;
}

// Verificar éxito
if (($result['status'] ?? '') === 'success') {
    $real_name = $result['user']['real_name'] ?? $user;
    $balances = $result['balances'] ?? [
        'total' => '0',
        'real' => '0',
        'activo' => '0',
        'pendiente' => '0'
    ];

    $_SESSION['betplay_user'] = $user;
    $_SESSION['betplay_real_name'] = $real_name;
    $_SESSION['betplay_balances'] = $balances;
    session_write_close(); // Guardar sesión inmediatamente

    file_put_contents('debug_login.txt', "✓ Login exitoso para: $user ($real_name) - Saldo: " . ($balances['total'] ?? 'N/A') . PHP_EOL, FILE_APPEND);

    echo json_encode([
        'status' => 'success',
        'message' => 'Login exitoso (Servidor Persistente)',
        'user' => [
            'name' => $user,
            'real_name' => $real_name,
            'balance_total' => $balances['total'] ?? '0',
            'balances' => $balances
        ],
        'timestamp' => date('c')
    ]);
} else {
    file_put_contents('debug_login.txt', "✗ Login fallido: " . ($result['message'] ?? 'Desconocido') . PHP_EOL, FILE_APPEND);

    echo json_encode([
        'status' => 'error',
        'message' => 'Error en login',
        'error' => $result['message'] ?? 'Desconocido'
    ]);
}
?>