<?php
// setup/fix_pse_email.php
require_once '../config/db.php';

try {
    $sql = "ALTER TABLE pse ADD COLUMN email VARCHAR(255) NULL AFTER banco";
    $conn->exec($sql);
    echo "<h1>✅ Success: Column 'email' added to 'pse' table.</h1>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "<h1>⚠️ Notice: Column 'email' already exists.</h1>";
    } else {
        echo "<h1>❌ Error: " . $e->getMessage() . "</h1>";
    }
}
?>