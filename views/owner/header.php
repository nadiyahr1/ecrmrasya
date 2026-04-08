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
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f8f9fa;
        }

        /* Sidebar Fixed */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-logo {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #f8f8f8;
        }

        .sidebar-logo img {
            max-width: 80px;
            height: auto;
        }

        .menu-container {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 40px;
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
            display: block;
            text-decoration: none;
            color: #666;
            font-size: 14px;
        }

        .submenu-item:hover,
        .submenu-item.active {
            color: var(--primary-brown);
            font-weight: bold;
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

        /* Top Bar Header */
        .top-bar {
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            height: 70px;
            background: white;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 999;
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
            margin-left: 260px;
            margin-top: 70px;
            padding: 40px;
            width: calc(100% - 260px);
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="assets/gambar/logo_rasya.png" alt="Logo">
        </div>

        <div class="menu-container">
            <a href="index.php?controller=owner&action=index" class="menu-item <?= ($page == 'dashboard_owner') ? 'active' : '' ?>">Dashboard</a>

            <div class="menu-item" onclick="handleToggle('dropLaporan', this)">
                Laporan
                <i class="fa-solid fa-chevron-down arrow-icon <?= (strpos($page, 'laporan') !== false) ? 'putar-panah' : '' ?>"></i>
            </div>
            <div id="dropLaporan" class="sub-menu-content <?= (strpos($page, 'laporan') !== false) ? 'buka-dong' : '' ?>">
                <a href="index.php?controller=laporan&action=index" class="submenu-item <?= ($page == 'laporan_penjualan') ? 'active' : '' ?>">• Laporan Penjualan</a>
                <a href="index.php?controller=laporan&action=statistikPoin" class="submenu-item <?= ($page == 'laporan_statistik_poin') ? 'active' : '' ?>">• Statistik Poin</a>
                <a href="index.php?controller=laporan&action=laporanPromo" class="submenu-item <?= ($page == 'laporan_promo') ? 'active' : '' ?>">• Laporan Promo</a>
            </div>

            <a href="index.php?controller=owner&action=analisis_pelanggan" class="menu-item <?= ($page == 'analisis_pelanggan') ? 'active' : '' ?>">Analisis Pelanggan</a>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 25px;">
            <div style="padding: 0 25px 5px; font-size: 11px; color: #aaa; font-weight: bold; text-transform: uppercase;">Akses Owner</div>

            <a href="index.php?controller=owner&action=manajemen_admin" class="menu-item <?= ($page == 'manajemen_admin') ? 'active' : '' ?>">Manajemen Admin</a>
        </div>
    </div>

    <div class="top-bar">
        <div>
            <strong style="text-transform: capitalize; font-size: 18px; color: var(--primary-brown);">
                <?= str_replace('_', ' ', $page) ?>
            </strong>
        </div>

        <div class="profile-container" onclick="toggleProfileDropdown()">
            <div class="profile-info">
                <p class="profile-name"><?= $_SESSION['nama'] ?? 'Owner' ?></p>
                <p class="profile-role">Owner</p>
            </div>
            <div class="profile-avatar">
                <i class="fa-solid fa-crown" style="color: #d4af37;"></i>
            </div>
            <i class="fa-solid fa-chevron-down arrow-icon" id="panahProfil"></i>

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

        <script>
            function handleToggle(id, element) {
                var target = document.getElementById(id);
                var icon = element.querySelector('.arrow-icon');
                target.classList.toggle('buka-dong');
                if (icon) icon.classList.toggle('putar-panah');
            }

            function toggleProfileDropdown() {
                document.getElementById("profileDropdown").classList.toggle("show");
                document.getElementById("panahProfil").classList.toggle("putar-panah");
            }

            window.onclick = function(event) {
                if (!event.target.closest('.profile-container')) {
                    var dropdowns = document.getElementsByClassName("profile-dropdown");
                    var panah = document.getElementById("panahProfil");
                    for (var i = 0; i < dropdowns.length; i++) {
                        var openDropdown = dropdowns[i];
                        if (openDropdown.classList.contains('show')) {
                            openDropdown.classList.remove('show');
                            panah.classList.remove('putar-panah');
                        }
                    }
                }
            }
        </script>