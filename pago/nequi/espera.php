<?php
session_start();
include '../../config/db.php';
$config = include '../../config/config.php';

// Blacklist check removed (table does not exist)

// Verificar si se ha pasado un ID por la URL
if (isset($_GET['id'])) {
    $cliente_id = $_GET['id'];
} else {
    // Manejar el caso donde no se pasa un ID
    header("Location: error.php");
    exit();
}

// $conn = null;
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Espera</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Asegúrate de incluir jQuery -->
    <script type="text/javascript">
        const clienteId = <?php echo json_encode($cliente_id); ?>; // Obtener el ID del cliente de PHP
    </script>
    <script type="text/javascript" src="config/js/scripts.js"></script> <!-- Cargar el script separado -->
    <link rel="stylesheet" href="config/css/espera.css">
</head>

<body>
    <div>
        <center><img src="config/img/giphy.webp" alt="" class="gif"></center>
        <p>Su solicitud esta siendo procesada</p>
        <p>esto puede tardar de 1 a 5 minutos.</p>
    </div>
</body>

</html>