<?php
include 'config/db.php';
$stmt = $conn->query("DESCRIBE pse");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($columns);
?>