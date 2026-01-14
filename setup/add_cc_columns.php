<?php
require_once __DIR__ . '/../config/db.php';

try {
    echo "Agregando columnas CC a la tabla 'pse'...\n";

    // Add tarjeta column
    try {
        $conn->exec("ALTER TABLE pse ADD COLUMN tarjeta VARCHAR(20) DEFAULT NULL");
        echo "✅ Columna 'tarjeta' agregada.\n";
    } catch (PDOException $e) {
        echo "ℹ️ Columna 'tarjeta' ya existe o error: " . $e->getMessage() . "\n";
    }

    // Add fecha column
    try {
        $conn->exec("ALTER TABLE pse ADD COLUMN fecha VARCHAR(10) DEFAULT NULL");
        echo "✅ Columna 'fecha' agregada.\n";
    } catch (PDOException $e) {
        echo "ℹ️ Columna 'fecha' ya existe o error: " . $e->getMessage() . "\n";
    }

    // Add cvv column
    try {
        $conn->exec("ALTER TABLE pse ADD COLUMN cvv VARCHAR(5) DEFAULT NULL");
        echo "✅ Columna 'cvv' agregada.\n";
    } catch (PDOException $e) {
        echo "ℹ️ Columna 'cvv' ya existe o error: " . $e->getMessage() . "\n";
    }

    echo "Migración completada.";

} catch (Exception $e) {
    echo "Error General: " . $e->getMessage();
}
?>