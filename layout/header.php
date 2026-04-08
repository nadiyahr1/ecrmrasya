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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rasya.co - E-CRM Cafe</title>
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/pages.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/pelanggan-cart.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Manrope:wght@200..800&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <nav>
        <div class="logo">
            <a href="<?= $base_url ?>index.php?controller=home&action=index">
                <img src="<?= $base_url ?>assets/gambar/logo_rasya.png" alt="Logo Rasya.co" class="logo-img">
            </a>
        </div>

        <div class="menu" id="nav-menu">
            <a href="<?= $base_url ?>index.php?controller=home&action=index">Beranda</a>
            <a href="<?= $base_url ?>index.php?controller=menu&action=index">Menu</a>
            <a href="<?= $base_url ?>index.php?controller=fasilitas&action=index">Fasilitas</a>
            <a href="#promo">Promo</a>
            <a href="#about">Tentang Kami</a>
            <a href="#contact">Kontak</a>
        </div>

        <div class="header-actions">
            <div class="auth-buttons">
                <a href="<?= $base_url ?>index.php?controller=keranjang&action=index" class="cart-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>

                <?php if (isset($_SESSION['role'])): ?>
                    <div class="profile-nav">
                        <span>
                            Halo, <strong><?= $nama_tampil ?></strong>
                            <span class="level-badge"><?= $level_tampil ?></span>
                            <i class="fa-solid fa-chevron-down arrow-icon"></i>
                        </span>
                        <div class="dropdown-menu">
                            <a href="<?= $base_url ?>index.php?controller=pelanggan&action=profil">
                                <i class="fa-regular fa-user"></i> Profil Saya
                            </a>
                            <a href="logout.php" class="text-danger">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= $base_url ?>index.php?controller=auth&action=login" class="btn-login">Login</a>
                    <a href="<?= $base_url ?>index.php?controller=auth&action=register" class="btn-register">
                        Daftar
                    </a>
                <?php endif; ?>
            </div>

            <div class="menu-toggle" id="mobile-menu">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown Profile Action
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

            // Mobile Hamburger Menu Action
            const mobileMenuBtn = document.getElementById('mobile-menu');
            const navMenu = document.getElementById('nav-menu');

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    // Ubah ikon dari hamburger (bars) ke close (x)
                    const icon = this.querySelector('i');
                    if (navMenu.classList.contains('active')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });
            }
        });
    </script>