<?php
// server/security_utils.php

/**
 * Sistema simple de Rate Limiting basado en archivos.
 * Limita el número de peticiones por IP en un intervalo de tiempo.
 */
function checkRateLimit($ip, $limit = 5, $time_window = 60)
{
    $file = __DIR__ . '/rate_limit.json';
    $data = [];

    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true) ?? [];
    }

    // Limpiar entradas antiguas
    $now = time();
    foreach ($data as $logged_ip => $attempts) {
        if ($attempts['expire'] < $now) {
            unset($data[$logged_ip]);
        }
    }

    // Verificar IP actual
    if (isset($data[$ip])) {
        $data[$ip]['count']++;
        if ($data[$ip]['count'] > $limit) {
            // Guardar antes de bloquear
            file_put_contents($file, json_encode($data));
            return false; // Bloqueado
        }
    } else {
        $data[$ip] = [
            'count' => 1,
            'expire' => $now + $time_window
        ];
    }

    // Guardar estado actualizado
    file_put_contents($file, json_encode($data));
    return true; // Permitido
}

/**
 * Bloqueo de User-Agents maliciosos conocidos.
 */
function checkUserAgent()
{
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $blocked_agents = [
        'curl',
        'python',
        'postman',
        'httpclient',
        'wget',
        'java',
        'libwww-perl',
        'go-http-client'
    ];

    foreach ($blocked_agents as $agent) {
        if (stripos($ua, $agent) !== false) {
            // Loguear intento de bloqueo (opcional)
            file_put_contents(__DIR__ . '/blocked_bots.txt', date('Y-m-d H:i:s') . " - UA Blocked: " . $ua . " IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL, FILE_APPEND);
            return false;
        }
    }
    return true;
}
?>