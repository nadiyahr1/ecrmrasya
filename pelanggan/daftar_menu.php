<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Pelanggan') {
    header("Location: ../index.php");
    exit;
}

// Ambil semua menu yang tersedia
$query = "SELECT m.*, k.nama_kategori 
          FROM tb_menu m 
          JOIN tb_kategori k ON m.id_kategori = k.id_kategori 
          WHERE m.status_menu = 'Tersedia' AND m.stok > 0";
$menus = $conn->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Menu - Rasya.co</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .menu-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .harga {
            color: #e44d26;
            font-weight: bold;
        }

        .kategori {
            font-size: 12px;
            color: #888;
        }
    </style>
</head>

<body>
    <a href="index.php">⬅ Kembali ke Dashboard</a>
    <h2>Menu Cafe Rasya.co</h2>

    <div class="menu-grid">
        <?php foreach ($menus as $m) : ?>
            <div class="menu-card">
                <span class="kategori"><?= $m['nama_kategori']; ?></span>
                <h3><?= $m['nama_menu']; ?></h3>
                <p class="harga">Rp <?= number_format($m['harga'], 0, ',', '.'); ?></p>
                <p>Stok: <?= $m['stok']; ?></p>
                <!-- <button disabled>Pesan Sekarang</button> -->
                <a href="pesan.php?id=<?= $m['id_menu']; ?>" style="padding: 5px 10px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">Pesan Sekarang</a>
                <br><small>(Fitur pesan akan kita buat nanti)</small>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>