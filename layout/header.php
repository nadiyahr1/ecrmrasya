<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/koneksi.php';
global $conn; 

$base_url = "http://localhost/ecrmrasya/";

$nama_tampil = "";
$level_tampil = "";

if (isset($_SESSION['id_member'])) {
    $id_log = $_SESSION['id_member'];
    
    if ($conn) {
        $stmt_nav = $conn->prepare("
            SELECT m.nama_member, l.nama_level 
            FROM tb_member m 
            JOIN tb_level_member l ON m.id_level = l.id_level 
            WHERE m.id_member = ?
        ");
        $stmt_nav->execute([$id_log]);
        $user_nav = $stmt_nav->fetch();

        if ($user_nav) {
            $nama_tampil = $user_nav['nama_member'];
            $level_tampil = $user_nav['nama_level'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rasya.co - E-CRM Cafe</title>

    <link rel="stylesheet" href="<?= $base_url ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<style>
    nav {
        display: flex;
        justify-content: space-between;
        padding: 0px 30px;
        height: 65px;
        background: #6F4E37;
        color: white;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 9999;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        box-sizing: border-box;
    }

    nav a {
        color: #fdfdfd;
        text-decoration: none;
        margin: 0 10px;
        font-weight: 500;
    }

    nav a:hover {
        color: #D4AF37;
    }

    .logo,
    .menu,
    .auth-buttons {
        display: flex;
        align-items: center;
        height: 100%;
    }

    .logo a {
        display: flex;
        align-items: center;
        text-decoration: none;
        padding-right: 20px;
        padding-left: 40px;
        filter: drop-shadow(2px 2px 5px rgba(0, 0, 0, 0.5));
    }

    .logo-img {
        max-height: 100px;
        width: auto;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .level-badge {
        background: #28a745;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
        margin-left: 5px;
    }

    .profile-nav {
        position: relative;
        display: flex;
        align-items: center;
        height: auto;
        cursor: pointer;
        padding: 8px 5px;
        border-radius: 20px;
        transition: 0.3s;
        user-select: none;
    }

    .profile-nav:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 45px;
        background: white;
        min-width: 180px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        z-index: 1000;
        overflow: hidden;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-menu a {
        color: #333 !important;
        padding: 12px 16px;
        display: block;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        text-align: left;
        margin: 0;
    }
</style>

<body>
    <nav>
        <div class="logo">
            <a href="<?= $base_url ?>index.php?controller=home&action=index">
                <img src="<?= $base_url ?>assets/gambar/logo_rasya.png" alt="Logo Rasya.co" class="logo-img">
            </a>
        </div>

        <div class="menu">
            <a href="<?= $base_url ?>index.php?controller=home&action=index">Beranda</a>
            <a href="<?= $base_url ?>index.php?controller=menu&action=index">Menu Cafe</a>
            <a href="<?= $base_url ?>fasilitas/fasilitas_publik.php">Fasilitas</a>
            <a href="<?= $base_url ?>index.php?controller=keranjang&action=index">
                <i class="fa-solid fa-cart-shopping"></i> Keranjang
            </a>
        </div>

        <div class="auth-buttons">
            <?php if (isset($_SESSION['role'])): ?>
                <div class="profile-nav">
                    <span>
                        Halo, <strong><?= $nama_tampil ?></strong>
                        <span class="level-badge"><?= $level_tampil ?></span> ▾
                    </span>
                    <div class="dropdown-menu">
                        <a href="<?= $base_url ?>index.php?controller=pelanggan&action=profil">
                            👤 Profil Saya
                        </a>
                        <a href="logout.php" style="color: red !important;">
                            🚪 Keluar
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= $base_url ?>index.php?controller=auth&action=login">Login</a>
                <a href="<?= $base_url ?>index.php?controller=auth&action=register"
                    style="background: #0275d8; padding: 6px 15px; border-radius: 6px;">
                    Daftar
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileBtn = document.querySelector('.profile-nav');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            if (profileBtn) {
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    this.classList.toggle('active');
                    dropdownMenu.classList.toggle('show');
                });
            }

            document.addEventListener('click', function() {
                if (profileBtn && dropdownMenu.classList.contains('show')) {
                    profileBtn.classList.remove('active');
                    dropdownMenu.classList.remove('show');
                }
            });
        });
    </script>

</body>