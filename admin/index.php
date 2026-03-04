<?php
session_start();
require_once '../config/koneksi.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php"); exit;
}

// Menentukan file mana yang akan dimuat (Default: dashboard.php)
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$file_target = $page . ".php";

// Ambil notifikasi untuk Top Bar
$notif_pesanan = $conn->query("SELECT COUNT(*) FROM tb_pesanan WHERE status = 'Menunggu Konfirmasi'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Rasya.co - E-CRM</title>
    <style>
        :root { --primary-brown: #6F4E37; --sidebar-bg: #ffffff; --text-color: #333; --border: #e0e0e0; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background: #f8f9fa; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: var(--sidebar-bg); border-right: 1px solid var(--border); min-height: 100vh; position: fixed; z-index: 1000; }
        .sidebar-logo { padding: 25px; text-align: center; border-bottom: 1px solid #f8f8f8; }
        .sidebar-logo img { max-width: 140px; }
        .menu-header { padding: 15px 25px 5px; font-size: 11px; color: #aaa; font-weight: bold; text-transform: uppercase; }
        .menu-item { padding: 12px 25px; display: flex; align-items: center; text-decoration: none; color: var(--text-color); cursor: pointer; border-left: 4px solid transparent; transition: 0.2s; }
        .menu-item:hover, .menu-item.active { background: #f5f5f5; color: var(--primary-brown); border-left-color: var(--primary-brown); font-weight: bold; }
        .dropdown-container { display: none; background: #fafafa; }
        .submenu-item { padding: 10px 25px 10px 55px; display: block; text-decoration: none; color: #666; font-size: 14px; }
        .arrow { margin-left: auto; font-size: 12px; }

        /* Top Bar */
        .top-bar { position: fixed; top: 0; left: 260px; right: 0; height: 70px; background: white; display: flex; align-items: center; justify-content: space-between; padding: 0 30px; border-bottom: 1px solid var(--border); z-index: 999; }
        
        /* Area Konten */
        .content { margin-left: 260px; margin-top: 70px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-logo"><img src="../assets/gambar/logo-rasya.png" alt="Logo"></div>
        <div style="padding: 15px 0;">
            <a href="index.php" class="menu-item <?= $page == 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="index.php?page=data_pesanan" class="menu-item <?= $page == 'data_pesanan' ? 'active' : '' ?>">Pesanan</a>
            
            <div class="menu-header">Manajemen Data</div>
            <a href="index.php?page=data_pelanggan" class="menu-item">Data Pelanggan</a>
            
            <div class="menu-item" onclick="toggleDropdown('dropMenu', this)">Data Menu <span class="arrow">▼</span></div>
            <div id="dropMenu" class="dropdown-container">
                <a href="index.php?page=kategori_menu" class="submenu-item">Kategori Menu</a>
                <a href="index.php?page=menu" class="submenu-item">Daftar Menu</a>
            </div>
            
            <a href="index.php?page=fasilitas" class="menu-item">Data Fasilitas</a>
            <a href="index.php?page=promo" class="menu-item">Data Promo</a>
            
            <div class="menu-header">Lainnya</div>
            <a href="index.php?page=ulasan" class="menu-item">Ulasan</a>
            <a href="index.php?page=laporan" class="menu-item">Laporan</a>
            <a href="../logout.php" class="menu-item" style="color: red;">Keluar</a>
        </div>
    </div>

    <div class="top-bar">
        <div style="display:flex; align-items:center; gap:15px;"><span>☰</span> <strong style="text-transform: capitalize;"><?= str_replace('_', ' ', $page) ?></strong></div>
        <div style="display:flex; align-items:center; gap:20px;">
            <div style="position:relative;">🔔 <small style="position:absolute; top:-5px; right:-5px; background:red; color:white; padding:1px 4px; border-radius:50%; font-size:9px;"><?= $notif_pesanan ?></small></div>
            <div style="border-left:1px solid #eee; padding-left:20px; display:flex; align-items:center; gap:10px;">
                <div style="text-align:right;"><div style="font-size:13px; font-weight:bold;"><?= $_SESSION['nama'] ?></div><small style="color:#888;">Administrator</small></div>
                <span>👤</span>
            </div>
        </div>
    </div>

    <div class="content">
        <?php 
            if (file_exists($file_target)) {
                include $file_target;
            } else {
                echo "<h2>Halaman $file_target tidak ditemukan.</h2>";
            }
        ?>
    </div>

    <script>
        function toggleDropdown(id, el) {
            var drop = document.getElementById(id);
            var arrow = el.querySelector('.arrow');
            if (drop.style.display === "block") { drop.style.display = "none"; arrow.style.transform = "rotate(0deg)"; }
            else { drop.style.display = "block"; arrow.style.transform = "rotate(180deg)"; }
        }
    </script>
</body>
</html>