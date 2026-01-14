<?php
// install_db_mysql.php
// Script para instalar la estructura de la base de datos MySQL (MariaDB)

echo "<h1>Instalador de Base de Datos (MySQL)</h1>";

// 1. Cargar Configuración
// Forzamos credenciales del VPS para asegurar que funcione el instalador
$host = 'localhost';
$port = '3306';
$dbname = 'aire';
$user = 'admin';
$pass = 'Jeyco420@';

echo "Intentando conectar a MySQL...<br>";
echo "Host: $host | User: $user | DB: $dbname<br><br>";

// 2. Conectar al servidor MySQL para crear la DB si no existe
try {
    // Conexión sin especificar base de datos
    $dsn_admin = "mysql:host=$host;port=$port;charset=utf8mb4";
    $conn_admin = new PDO($dsn_admin, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Crear base de datos si no existe
    $sql_db = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn_admin->exec($sql_db);
    echo "<span style='color:green'>Base de datos '$dbname' verificada/creada.</span><br>";

    $conn_admin = null; // Cerrar conexión administrativa

} catch (PDOException $e) {
    echo "<span style='color:red'>Error conectando a MySQL (Admin): " . $e->getMessage() . "</span><br>";
    die("Verifique que XAMPP (MySQL) esté corriendo.");
}

// 3. Conectar a la base de datos destino y crear tablas
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $conn = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    echo "Conectado a '$dbname'. Creando tablas...<br>";

    // SQL para tablas (Sintaxis MySQL)
    $sql_tables = "
    CREATE TABLE IF NOT EXISTS nequi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        estado INT DEFAULT 1,
        ip_address VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS pse (
        id INT AUTO_INCREMENT PRIMARY KEY,
        estado INT DEFAULT 1,
        ip_address VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;
    ";

    // Ejecutar queries
    $conn->exec($sql_tables);

    echo "<h2 style='color:green'>¡Instalación MySQL Completada! ✅</h2>";
    echo "Tablas 'nequi' y 'pse' creadas.<br>";
    echo "<a href='test_db.php'>Probar Conexión Nuevamente</a>";

} catch (PDOException $e) {
    echo "<h3 style='color:red'>Error Fatal:</h3> " . $e->getMessage();
}
?>