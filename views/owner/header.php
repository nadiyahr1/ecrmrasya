<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Owner Rasya.co - E-CRM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-brown: #6F4E37;
            --sidebar-bg: #ffffff;
            --text-color: #333;
            --border: #e0e0e0;
            --sidebar-width: 260px;
            --sidebar-mini-width: 75px;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f8f9fa;
            overflow-x: hidden;
        }

        /* Sidebar Fixed */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            height: 100vh;
            position: fixed;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease;
            overflow-x: hidden;
            top: 0;
            left: 0;
        }

        .sidebar-logo {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #f8f8f8;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .sidebar-logo img {
            max-width: 80px;
            height: auto;
            transition: 0.3s;
        }

        .menu-container {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 40px;
        }

        .menu-icon {
            width: 30px;
            text-align: center;
            font-size: 16px;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .menu-header {
            padding: 15px 25px 5px;
            font-size: 11px;
            color: #aaa;
            font-weight: bold;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .menu-item {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--text-color);
            cursor: pointer;
            border-left: 4px solid transparent;
            transition: 0.2s;
            user-select: none;
            white-space: nowrap;
        }

        .menu-item:hover,
        .menu-item.active {
            background: #f5f5f5;
            color: var(--primary-brown);
            border-left-color: var(--primary-brown);
            font-weight: bold;
        }

        .sub-menu-content {
            display: none;
            background: #fafafa;
        }

        .buka-dong {
            display: block !important;
        }

        .submenu-item {
            padding: 10px 25px 10px 55px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #666;
            font-size: 14px;
            white-space: nowrap;
        }

        .submenu-item .menu-icon {
            font-size: 8px;
            width: 20px;
            margin-right: 5px;
        }

        .submenu-item:hover {
            color: var(--primary-brown);
        }

        .arrow-icon {
            margin-left: auto;
            font-size: 10px;
            transition: transform 0.3s ease;
            color: #888;
        }

        .putar-panah {
            transform: rotate(180deg);
        }

        /* EFEK MINI SIDEBAR */
        body.sidebar-mini .sidebar {
            width: var(--sidebar-mini-width);
        }

        body.sidebar-mini .top-bar {
            left: var(--sidebar-mini-width);
        }

        body.sidebar-mini .content {
            margin-left: var(--sidebar-mini-width);
            width: calc(100% - var(--sidebar-mini-width));
        }

        body.sidebar-mini .sidebar-logo img {
            max-width: 45px;
        }

        body.sidebar-mini .menu-text,
        body.sidebar-mini .arrow-icon,
        body.sidebar-mini .menu-header {
            display: none;
        }

        body.sidebar-mini .menu-item {
            padding: 15px 0;
            justify-content: center;
        }

        body.sidebar-mini .menu-icon {
            margin-right: 0;
            font-size: 20px;
        }

        body.sidebar-mini .submenu-item {
            padding: 12px 0;
            justify-content: center;
        }

        /* TOP BAR */
        .top-bar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: 70px;
            background: white;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 999;
            transition: left 0.3s ease;
        }

        .hamburger-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--primary-brown);
            cursor: pointer;
            padding: 5px;
            margin-right: 15px;
            transition: 0.2s;
        }

        .hamburger-btn:hover {
            color: #333;
        }

        .profile-container {
            position: relative;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 15px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .profile-container:hover {
            background: #f1f5f9;
        }

        .profile-info {
            text-align: right;
            line-height: 1.2;
        }

        .profile-name {
            font-size: 14px;
            font-weight: bold;
            color: var(--text-color);
            margin: 0;
        }

        .profile-role {
            font-size: 11px;
            color: #888;
            margin: 0;
            text-transform: uppercase;
        }

        .profile-avatar {
            width: 38px;
            height: 38px;
            background: #e2e8f0;
            color: #475569;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .profile-dropdown {
            display: none;
            position: absolute;
            top: 60px;
            right: 0;
            background: white;
            min-width: 180px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            border: 1px solid #eee;
            overflow: hidden;
            z-index: 1001;
        }

        .profile-dropdown.show {
            display: block;
        }

        .dropdown-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #475569;
            font-size: 14px;
            transition: 0.2s;
        }

        .dropdown-item:hover {
            background: #f8fafc;
            color: var(--primary-brown);
        }

        .dropdown-divider {
            height: 1px;
            background: #eee;
            margin: 0;
        }

        .content {
            margin-left: var(--sidebar-width);
            margin-top: 70px;
            padding: 40px;
            width: calc(100% - var(--sidebar-width));
            box-sizing: border-box;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="assets/gambar/logo_rasya.png" alt="Logo">
        </div>

        <div class="menu-container">
            <a href="index.php?controller=owner&action=index" class="menu-item <?= ($page == 'dashboard_owner') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line menu-icon"></i>
                <span class="menu-text">Dashboard</span>
            </a>

            <div class="menu-header">Analisis & Laporan</div>

            <a href="index.php?controller=laporan&action=index" class="menu-item <?= ($page == 'laporan_penjualan') ? 'active' : '' ?>">
                <i class="fa-solid fa-file-lines menu-icon"></i>
                <span class="menu-text">Laporan Penjualan</span>
            </a>

            <a href="index.php?controller=laporan&action=laporanPromo" class="menu-item <?= ($page == 'laporan_promo') ? 'active' : '' ?>">
                <i class="fa-solid fa-tags menu-icon"></i>
                <span class="menu-text">Laporan Promo</span>
            </a>

            <a href="index.php?controller=owner&action=analisis_pelanggan" class="menu-item <?= ($page == 'analisis_pelanggan') ? 'active' : '' ?>">
                <i class="fa-solid fa-users-viewfinder menu-icon"></i>
                <span class="menu-text">Analisis Pelanggan</span>
            </a>

            <a href="index.php?controller=laporan&action=statistikPoin" class="menu-item <?= ($page == 'laporan_statistik_poin') ? 'active' : '' ?>">
                <i class="fa-solid fa-coins menu-icon"></i>
                <span class="menu-text">Statistik Poin</span>
            </a>

            <div class="menu-header">Sistem</div>

            <a href="index.php?controller=owner&action=manajemen_admin" class="menu-item <?= ($page == 'manajemen_admin') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-tie menu-icon"></i>
                <span class="menu-text">Manajemen Admin</span>
            </a>
        </div>
    </div>

    <div class="top-bar">
        <div style="display: flex; align-items: center;">
            <button class="hamburger-btn" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <strong style="text-transform: capitalize; font-size: 18px; color: var(--primary-brown);">
                <?= str_replace('_', ' ', $page) ?>
            </strong>
        </div>

        <div class="profile-container" onclick="toggleProfileDropdown()">
            <div class="profile-info">
                <p class="profile-name"><?= $_SESSION['nama'] ?? 'Owner' ?></p>
                <p class="profile-role"><?= $_SESSION['role'] ?? 'Owner' ?></p>
            </div>
            <div class="profile-avatar">
                <i class="fa-solid fa-crown" style="color: #f59e0b;"></i>
            </div>
            <i class="fa-solid fa-chevron-down arrow-icon" id="panahProfil" style="margin-left: 0;"></i>

            <div id="profileDropdown" class="profile-dropdown">
                <a href="index.php?controller=owner&action=profil" class="dropdown-item">
                    <i class="fa-solid fa-user-pen"></i> Profil Saya
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item" style="color: #ef4444;">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </a>
            </div>
        </div>
    </div>

    <div class="content">