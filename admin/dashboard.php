<?php require_once 'auth_guard.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Diseños - CardStudio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="../js/i18n.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            margin: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #F9FAFB;
            color: #111111;
            -webkit-font-smoothing: antialiased;
        }

        .sidebar {
            width: 220px;
            background: #1A1A1A;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 24px 0;
            flex-shrink: 0;
        }

        .logo {
            text-align: center;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: #2E90E5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.85rem;
            font-weight: 800;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            padding: 36px 40px;
            overflow-y: auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        h1 {
            font-size: 1.6rem;
            font-weight: 600;
            color: #111111;
            margin: 0;
        }

        .filter-bar {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filter-bar label {
            font-weight: 500;
            color: #6B7280;
            font-size: 0.85rem;
        }

        .filter-bar select {
            padding: 10px 36px 10px 14px;
            border: 1px solid #E5E7EB;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-family: 'Inter', sans-serif;
            background: white;
            color: #111111;
            cursor: pointer;
            min-width: 220px;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            transition: all 0.2s;
        }

        .filter-bar select:focus {
            outline: none;
            border-color: #2E90E5;
            box-shadow: 0 0 0 3px rgba(46,144,229,0.1);
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 24px;
        }

        .card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #E5E7EB;
            transition: all 0.3s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.1);
            border-color: transparent;
        }

        .card-img-wrapper {
            width: 100%;
            height: 320px;
            overflow: hidden;
            background: #F3F4F6;
            position: relative;
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .card-body {
            padding: 18px;
        }

        .card-title {
            font-weight: 600;
            font-size: 1rem;
            margin: 0 0 4px 0;
            color: #111111;
        }

        .card-category {
            font-size: 0.8rem;
            color: #9CA3AF;
            margin-bottom: 14px;
        }

        .btn-edit {
            display: block;
            width: 100%;
            padding: 10px;
            background: #2E90E5;
            color: white;
            text-align: center;
            border: none;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s;
            box-sizing: border-box;
            font-family: inherit;
        }

        .btn-edit:hover {
            background: #1B6BBF;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(46,144,229,0.3);
        }

        .no-designs {
            text-align: center;
            color: #9CA3AF;
            font-size: 1.1rem;
            grid-column: 1 / -1;
            padding: 60px 0;
        }

        .lang-bar {
            padding: 10px 20px;
        }

        /* Sidebar nav */
        .sidebar-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 24px;
            font-size: 0.88rem;
            color: #9CA3AF;
            cursor: pointer;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav-item:hover {
            color: white;
            background: rgba(255,255,255,0.04);
        }

        .sidebar-nav-item.active {
            color: #2E90E5;
            background: rgba(46,144,229,0.08);
            border-left-color: #2E90E5;
        }

        .sidebar-label {
            padding: 0 24px;
            font-size: 0.68rem;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .sidebar-nav-item.logout {
            margin-top: auto;
            color: #F87171;
        }

        .sidebar-nav-item.logout:hover {
            color: #EF4444;
            background: rgba(239,68,68,0.08);
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
                padding: 12px 16px;
                box-sizing: border-box;
            }

            .logo { margin-bottom: 0; font-size: 1.1rem; }
            .sidebar-nav-item, .sidebar-label { display: none; }
            .sidebar .lang-bar { display: block; padding: 0; }

            .sidebar > nav {
                display: flex;
                gap: 12px;
                align-items: center;
            }

            .main-content {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo">
            <div class="logo-icon">C</div>
            CardStudio
        </div>
        <div class="sidebar-label" data-i18n="menu">MENÚ</div>
        <div class="sidebar-nav-item" onclick="location.href='crear-diseno.html'" data-i18n="menu_editor">&#127912; Editor Maestro</div>
        <div class="sidebar-nav-item active" onclick="location.href='dashboard.php'" data-i18n="menu_designs">&#128193; Mis Diseños</div>
        <div class="sidebar-nav-item" onclick="location.href='marketing.php'" data-i18n="menu_marketing">&#128231; Marketing Masivo</div>
        <div class="sidebar-nav-item logout" onclick="logout()" data-i18n="menu_logout">&#128682; Cerrar sesión</div>
        <div class="lang-bar">
            <script>document.write(i18n.createSwitcher())</script>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1 data-i18n="dashboard_title">Mis Diseños</h1>
            <div class="filter-bar">
                <label for="categoryFilter" data-i18n="filter_label">Filtrar por:</label>
                <select id="categoryFilter" onchange="filtrarPorCategoria()">
                    <option value="all" data-i18n="filter_all">Todas las categorías</option>
                </select>
            </div>
        </div>
        <div class="grid-container" id="designsGrid">
        </div>
    </div>

    <script src="js/dashboard.js"></script>
    <script>
        async function logout() {
            await fetch('api_admin_auth.php?action=logout');
            window.location.href = '../index.html';
        }
    </script>
</body>

</html>
