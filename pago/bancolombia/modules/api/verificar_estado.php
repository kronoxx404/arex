<?php
header('Content-Type: application/json');

// 1. CARGAR CONFIGURACIÓN GLOBAL
$config = require '../../../../config/config.php';

if (!$config || !is_array($config)) {
    echo json_encode(['error' => 'Error de configuración']);
    exit();
}

$db_host = $config['db_host'];
$db_name = $config['db_name'];
$db_user = $config['db_user'];
$db_pass = $config['db_pass'];
$db_port = $config['db_port'];

try {
    $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No se proporcionó ID de cliente']);
    exit();
}

$clienteId = $_GET['id'];

try {
    // Usar tabla 'pse' en lugar de 'clientes'
    $sql = "SELECT estado FROM pse WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $clienteId]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        echo json_encode(['estado' => $cliente['estado']]);
    } else {
        echo json_encode(['error' => 'Cliente no encontrado']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la consulta']);
}
?>