<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/koneksi.php';

// Ambil data member jika sudah login untuk menampilkan Level
$nama_tampil = "";
$level_tampil = "";
if (isset($_SESSION['role']) && $_SESSION['role'] === 'Pelanggan') {
    $id_m = $_SESSION['id_member'];
    $stmt = $conn->prepare("SELECT m.nama_member, l.nama_level FROM tb_member m JOIN tb_level_member l ON m.id_level = l.id_level WHERE m.id_member = ?");
    $stmt->execute([$id_m]);
    $user_data = $stmt->fetch();
    $nama_tampil = $user_data['nama_member'];
    $level_tampil = $user_data['nama_level'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rasya.co - E-CRM Cafe</title>
    <style>
        nav { display: flex; justify-content: space-between; padding: 15px; background: #333; color: white; }
        nav a { color: white; text-decoration: none; margin: 0 10px; }
        .auth-buttons { display: flex; align-items: center; }
        .level-badge { background: #28a745; padding: 2px 8px; border-radius: 10px; font-size: 12px; margin-left: 5px; }
    </style>
</head>
<body>
<nav>
    <div class="logo"><strong>Rasya.co</strong></div>
    <div class="menu">
        <a href="index.php">Beranda</a>
        <a href="menu_publik.php">Menu Cafe</a>
        <a href="fasilitas/fasilitas_publik.php">Fasilitas</a>
        <a href="keranjang.php">🛒 Keranjang</a>
    </div>
    <div class="auth-buttons">
        <?php if (isset($_SESSION['role'])): ?>
            <span>Halo, <strong><?= $nama_tampil ?></strong></span>
            <span class="level-badge"><?= $level_tampil ?></span>
            <a href="riwayat_pesanan.php">Riwayat Pesanan</a>
            <a href="logout.php" style="margin-left:15px;">Keluar</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="registrasi.php" style="background: #0275d8; padding: 5px 10px; border-radius: 4px;">Daftar</a>
        <?php endif; ?>
    </div>
</nav>