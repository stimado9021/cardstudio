<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — CardStudio</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f0f11;
            color: #fff;
            overflow: hidden;
        }

        /* Fondo con glow */
        body::before {
            content: '';
            position: fixed;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(255, 77, 41, 0.18) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .login-card {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 50px 45px;
            width: 100%;
            max-width: 420px;
            backdrop-filter: blur(20px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5),
                        0 0 0 1px rgba(255,255,255,0.04);
            animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .logo span {
            background: linear-gradient(90deg, #ff4d29, #ff2a5f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            text-align: center;
            color: #a1a1aa;
            font-size: 0.9rem;
            margin-bottom: 40px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #a1a1aa;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .field input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s;
        }

        .field input:focus {
            outline: none;
            border-color: #ff4d29;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(255, 77, 41, 0.12);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            margin-top: 10px;
            background: linear-gradient(135deg, #ff4d29 0%, #ff2a5f 100%);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 25px rgba(255, 77, 41, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(255, 77, 41, 0.4);
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
            background: rgba(255, 77, 41, 0.15);
            border: 1px solid rgba(255, 77, 41, 0.3);
            color: #ff6b4a;
            font-size: 0.88rem;
            text-align: center;
            display: none;
        }

        .badge-admin {
            display: inline-block;
            background: rgba(255, 77, 41, 0.1);
            border: 1px solid rgba(255, 77, 41, 0.3);
            color: #ff4d29;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .login-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.78rem;
            color: #555;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <p class="logo">Card<span>Studio</span></p>
        <p class="subtitle">Panel de Administración</p>
        <div style="text-align:center; margin-bottom: 30px;">
            <span class="badge-admin">🔒 Acceso Restringido</span>
        </div>

        <div class="field">
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" placeholder="Ingresa tu usuario" autocomplete="username">
        </div>

        <div class="field">
            <label for="password">Contraseña</label>
            <input type="password" id="password" placeholder="••••••••" autocomplete="current-password">
        </div>

        <button class="btn-login" id="btnLogin" onclick="doLogin()">Entrar al Panel</button>

        <div id="errorMsg"></div>

        <p class="login-footer">CardStudio © 2025 — Solo personal autorizado</p>
    </div>

    <script>
        // Permitir login con Enter
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
                mostrarError('Por favor completa ambos campos.');
                return;
            }

            btn.textContent = 'Verificando...';
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
                    btn.textContent = '✓ Acceso concedido';
                    setTimeout(() => { window.location.href = 'dashboard.php'; }, 600);
                } else {
                    mostrarError(data.error || 'Credenciales incorrectas');
                    btn.textContent = 'Entrar al Panel';
                    btn.classList.remove('loading');
                }
            } catch (e) {
                mostrarError('Error de conexión. ¿Está el servidor encendido?');
                btn.textContent = 'Entrar al Panel';
                btn.classList.remove('loading');
            }
        }

        function mostrarError(msg) {
            const el = document.getElementById('errorMsg');
            el.textContent = '⚠ ' + msg;
            el.style.display = 'block';
        }
    </script>

</body>
</html>
