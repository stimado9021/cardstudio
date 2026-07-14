<?php require_once 'auth_guard.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Diseños - CardStudio</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="../js/i18n.js"></script>
    <style>
        body {
            margin: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
            font-family: 'Montserrat', sans-serif;
            background-color: #f0f2f5;
        }

        .sidebar {
            width: 200px;
            background: #1a1a1a;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
            flex-shrink: 0;
        }

        .logo {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .logo span {
            color: #ff4d29;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            padding: 40px;
            overflow-y: auto;
        }

        h1 {
            color: #333;
            margin-top: 0;
            margin-bottom: 30px;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
        }

        .card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .card-img-wrapper {
            width: 100%;
            height: 350px;
            overflow: hidden;
            background-color: #eee;
            position: relative;
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0 0 5px 0;
            color: #222;
        }

        .card-category {
            font-size: 0.85rem;
            color: #777;
            margin-bottom: 15px;
        }

        .btn-edit {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #ff4d29;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.3s;
            box-sizing: border-box;
        }

        .btn-edit:hover {
            background-color: #e63e1c;
        }

        .no-designs {
            text-align: center;
            color: #666;
            font-size: 1.2rem;
            grid-column: 1 / -1;
            padding: 50px 0;
        }

        .lang-bar {
            padding: 10px 20px;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                padding: 15px 20px;
                box-sizing: border-box;
            }

            .sidebar .logo {
                margin-bottom: 0;
                font-size: 1.2rem;
            }

            .sidebar nav {
                display: flex;
                padding: 0 !important;
                gap: 15px;
                align-items: center;
            }

            .sidebar nav div {
                margin-bottom: 0 !important;
                font-size: 0.9rem;
            }

            .sidebar nav div:first-child {
                display: none;
            }

            .main-content {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo">Card<span>Studio</span></div>
        <nav style="padding: 0 20px;">
            <div style="color: #888; font-size: 0.8rem; margin-bottom: 10px;" data-i18n="menu">MENÚ</div>
            <div onclick="location.href='crear-diseno.html'" style="margin-bottom: 15px; cursor: pointer; opacity: 0.7;" data-i18n="menu_editor">🎨 Editor Maestro</div>
            <div style="margin-bottom: 15px; cursor: pointer; opacity: 0.7;" onclick="location.href='dashboard.php'" data-i18n="menu_designs">📁 Mis Diseños</div>
            <div onclick="location.href='marketing.php'" style="margin-bottom: 15px; cursor: pointer; opacity: 0.7;" data-i18n="menu_marketing">📧 Marketing Masivo</div>
            <div onclick="logout()" style="margin-top:auto; cursor: pointer; opacity: 0.7; color:#ff4d29;" data-i18n="menu_logout">🚪 Cerrar sesión</div>
        </nav>
        <div class="lang-bar">
            <script>document.write(i18n.createSwitcher())</script>
        </div>
    </div>

    <div class="main-content">
        <h1 data-i18n="dashboard_title">Mis Diseños</h1>
        <div class="grid-container" id="designsGrid">
        </div>
    </div>

    <script src="js/dashboard.js"></script>
    <script>
        async function logout() {
            await fetch('api_admin_auth.php?action=logout');
            window.location.href = 'login.php';
        }
    </script>
</body>

</html>
