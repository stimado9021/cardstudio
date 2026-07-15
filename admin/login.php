<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — CardStudio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="../js/i18n.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1A1A1A;
            color: #fff;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        body::before {
            content: '';
            position: fixed;
            top: -30%;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(46,144,229,0.12) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .login-card {
            position: relative;
            z-index: 1;
            background: #222222;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 24px;
            padding: 48px 44px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.4);
            animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo {
            font-size: 1.6rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: #2E90E5;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            font-weight: 800;
        }

        .subtitle {
            text-align: center;
            color: #9CA3AF;
            font-size: 0.9rem;
            margin-bottom: 36px;
        }

        .badge-admin {
            display: inline-block;
            background: rgba(46,144,229,0.1);
            border: 1px solid rgba(46,144,229,0.25);
            color: #2E90E5;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 4px 14px;
            border-radius: 9999px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }

        .field input {
            width: 100%;
            padding: 13px 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.25s;
        }

        .field input:focus {
            outline: none;
            border-color: #2E90E5;
            background: rgba(255,255,255,0.08);
            box-shadow: 0 0 0 3px rgba(46,144,229,0.15);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
            background: #2E90E5;
            border: none;
            border-radius: 9999px;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.25s;
        }

        .btn-login:hover {
            background: #1B6BBF;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(46,144,229,0.3);
        }

        .btn-login:active { transform: translateY(0); }

        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        #errorMsg {
            margin-top: 16px;
            padding: 12px 16px;
            border-radius: 10px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.25);
            color: #F87171;
            font-size: 0.85rem;
            text-align: center;
            display: none;
        }

        .login-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.78rem;
            color: #6B7280;
        }

        .lang-bar {
            text-align: center;
            margin-top: 16px;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <p class="logo"><div class="logo-icon">C</div> CardStudio</p>
        <p class="subtitle" data-i18n="login_panel_title">Panel de Administración</p>
        <div style="text-align:center; margin-bottom: 28px;">
            <span class="badge-admin" data-i18n="login_access_restricted">Acceso Restringido</span>
        </div>

        <div class="field">
            <label for="usuario" data-i18n="admin_user">Usuario</label>
            <input type="text" id="usuario" data-i18n-placeholder="login_placeholder_user" placeholder="Ingresa tu usuario" autocomplete="username">
        </div>

        <div class="field">
            <label for="password" data-i18n="admin_pass">Contraseña</label>
            <input type="password" id="password" data-i18n-placeholder="login_placeholder_pass" placeholder="••••••••" autocomplete="current-password">
        </div>

        <button class="btn-login" id="btnLogin" onclick="doLogin()" data-i18n="login_btn_enter">Entrar al Panel</button>

        <div id="errorMsg"></div>

        <p class="login-footer">CardStudio &copy; 2025 — <span data-i18n="login_footer">Solo personal autorizado</span></p>

        <div class="lang-bar">
            <script>document.write(i18n.createSwitcher())</script>
        </div>
    </div>

    <script>
        document.getElementById('password').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') doLogin();
        });
        document.getElementById('usuario').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') doLogin();
        });

        async function doLogin() {
            const usuario  = document.getElementById('usuario').value.trim();
            const password = document.getElementById('password').value;
            const btn      = document.getElementById('btnLogin');
            const errMsg   = document.getElementById('errorMsg');

            if (!usuario || !password) {
                mostrarError(i18n.t('login_empty_fields'));
                return;
            }

            btn.textContent = i18n.t('login_verifying');
            btn.classList.add('loading');
            errMsg.style.display = 'none';

            try {
                const res  = await fetch('api_admin_auth.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ usuario, password })
                });
                const data = await res.json();

                if (data.success) {
                    btn.textContent = i18n.t('login_granted');
                    setTimeout(() => { window.location.href = 'dashboard.php'; }, 600);
                } else {
                    mostrarError(data.error || i18n.t('login_denied'));
                    btn.textContent = i18n.t('login_btn_enter');
                    btn.classList.remove('loading');
                }
            } catch (e) {
                mostrarError(i18n.t('login_error_connection'));
                btn.textContent = i18n.t('login_btn_enter');
                btn.classList.remove('loading');
            }
        }

        function mostrarError(msg) {
            const el = document.getElementById('errorMsg');
            el.textContent = msg;
            el.style.display = 'block';
        }
    </script>

</body>
</html>
