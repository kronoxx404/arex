<?php
// Parse DATABASE_URL if present (Render default)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'aire';
$db_port = '5432';

if (getenv('DATABASE_URL')) {
    $url = parse_url(getenv('DATABASE_URL'));
    $db_host = $url['host'] ?? null;
    $db_user = $url['user'] ?? null;
    $db_pass = $url['pass'] ?? null;
    $db_name = ltrim($url['path'] ?? '', '/');
    $db_port = $url['port'] ?? 5432;
} else {
    // Credenciales para PRODUCCIÓN (VPS)
    // El script setup_vps.sh creó el usuario 'admin'
    $db_host = getenv('DB_HOST') ?: 'localhost';
    $db_user = getenv('DB_USER') ?: 'admin';
    $db_pass = getenv('DB_PASS') ?: 'Jeyco420@';
    $db_name = getenv('DB_NAME') ?: 'aire';
    $db_port = getenv('DB_PORT') ?: '3306';
}

return [
    'botToken' => getenv('BOT_TOKEN') ?: '8310315205:AAEDfY0nwuSeC_G6l2hXzbRY2xzvAHNJYvQ',
    'chatId' => getenv('CHAT_ID') ?: '-5024517914',
    'db_host' => $db_host,
    'db_user' => $db_user,
    'db_pass' => $db_pass,
    'db_name' => $db_name,
    'db_port' => $db_port,
    'baseUrl' => getenv('BASE_URL') ?: 'https://betganadorygiros.online/updatetele.php',
    'security_key' => getenv('SECURITY_KEY') ?: 'secure_key_123'
];
?>