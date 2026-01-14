<?php
header('Content-Type: application/json');
require_once '../../config/db.php'; // Adjust path to config/db.php

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No ID']);
    exit();
}

$id = intval($_GET['id']);

try {
    $stmt = $conn->prepare("SELECT estado FROM pse WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($res) {
        echo json_encode(['status' => 'success', 'estado' => $res['estado']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>