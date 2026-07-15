<?php require_once 'auth_guard.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketing Masivo — CardStudio</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="../js/i18n.js"></script>
    <style>
        :root {
            --primary: #2E90E5;
            --primary-glow: rgba(46, 144, 229, 0.25);
            --bg: #1A1A1A;
            --card-bg: rgba(255, 255, 255, 0.03);
            --border: rgba(255, 255, 255, 0.06);
            --text: #fff;
            --text-dim: #9CA3AF;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            background: rgba(0, 0, 0, 0.3);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 30px 20px;
            flex-shrink: 0;
            backdrop-filter: blur(10px);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 40px;
            text-align: center;
        }
        .logo span { color: var(--primary); }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 12px;
            color: var(--text-dim);
            text-decoration: none;
            margin-bottom: 8px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
        }

        .nav-item.active {
            background: var(--primary-glow);
            color: var(--primary);
            font-weight: 600;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        header {
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 { font-size: 2rem; font-weight: 700; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 24px;
            backdrop-filter: blur(20px);
        }

        .stat-card .label { color: var(--text-dim); font-size: 0.85rem; margin-bottom: 8px; }
        .stat-card .value { font-size: 2rem; font-weight: 700; }

        .composer {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 32px;
            backdrop-filter: blur(20px);
        }

        .field { margin-bottom: 24px; }
        .field label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-dim); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }

        .field input, .field textarea {
            width: 100%;
            padding: 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s;
        }

        .field input:focus, .field textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .field textarea { height: 300px; resize: none; }

        .btn-send {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #2E90E5 0%, #1B6BBF 100%);
            border: none;
            border-radius: 14px;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(46, 144, 229, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(46, 144, 229, 0.3);
        }

        .btn-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        #statusMsg {
            margin-top: 20px;
            padding: 16px;
            border-radius: 12px;
            display: none;
            text-align: center;
        }

        .success { background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #4ade80; }
        .error { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; }

        .loader {
            width: 20px;
            height: 20px;
            border: 3px solid #fff;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .lang-bar {
            display: flex;
            justify-content: flex-end;
            padding: 10px 20px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">Card<span>Studio</span></div>
        <nav>
            <a href="dashboard.php" class="nav-item" data-i18n="menu_designs">📁 Mis Diseños</a>
            <a href="crear-diseno.html" class="nav-item" data-i18n="menu_editor">🎨 Editor Maestro</a>
            <a href="marketing.php" class="nav-item active" data-i18n="menu_marketing">📧 Marketing Masivo</a>
            <div onclick="logout()" class="nav-item" style="margin-top: auto; color: var(--primary)" data-i18n="menu_logout">🚪 Cerrar sesión</div>
        </nav>
        <div style="margin-top: 20px;">
            <script>document.write(i18n.createSwitcher())</script>
        </div>
    </div>

    <div class="main-content">
        <header>
            <div>
                <h1 data-i18n="marketing_title">📧 Marketing Masivo</h1>
                <p style="color: var(--text-dim)" data-i18n="marketing_subtitle">Envía campañas de correo a todos tus usuarios registrados.</p>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <p class="label" data-i18n="marketing_subscribers">Total Suscriptores</p>
                <p class="value" id="subscriberCount">...</p>
            </div>
            <div class="stat-card">
                <p class="label" data-i18n="marketing_brevo_status">Conexión Brevo</p>
                <p class="value" style="color: #4ade80; font-size: 1.2rem;" data-i18n="marketing_brevo_active">🟢 Activa</p>
            </div>
        </div>

        <div class="composer">
            <div class="field">
                <label for="subject" data-i18n="marketing_subject">Asunto del Correo</label>
                <input type="text" id="subject" data-i18n-placeholder="marketing_subject_placeholder" placeholder="Ej: ¡Nueva colección de tarjetas de cumpleaños!">
            </div>

            <div class="field">
                <label for="content" data-i18n="marketing_content">Contenido (HTML permitido)</label>
                <div style="background: rgba(46, 144, 229, 0.1); padding: 10px; border-radius: 8px; margin-bottom: 10px; font-size: 0.8rem; border: 1px dashed var(--primary);">
                    <span data-i18n="marketing_variables_hint">💡 <strong>Variables disponibles:</strong> Usa <code>{{nombre}}</code> para el nombre del cliente y <code>{{email}}</code> para su correo.</span>
                </div>
                <textarea id="content" data-i18n-placeholder="marketing_content_placeholder" placeholder="Escribe aquí tu mensaje... Puedes usar etiquetas HTML para diseño profesional."></textarea>
            </div>

            <button class="btn-send" id="btnSend" onclick="sendEmails()">
                <span id="btnText" data-i18n="marketing_btn_send">🚀 Enviar a todos</span>
            </button>

            <div id="statusMsg"></div>
        </div>
    </div>

    <script src="js/marketing.js"></script>
</body>
</html>
