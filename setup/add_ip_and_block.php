<?php
include '../config/db.php';

try {
    // Add 'ip' column to 'pse' table
    $stmt = $conn->query("SHOW COLUMNS FROM pse LIKE 'ip'");
    $exists = $stmt->fetch();
    if (!$exists) {
        $conn->exec("ALTER TABLE pse ADD COLUMN ip VARCHAR(45) DEFAULT NULL");
        echo "Column 'ip' added to 'pse' table.<br>";
    } else {
        echo "Column 'ip' already exists in 'pse' table.<br>";
    }

    // Create 'blocked_ips' table
    $sql = "CREATE TABLE IF NOT EXISTS blocked_ips (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip VARCHAR(45) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Table 'blocked_ips' created or already exists.<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>