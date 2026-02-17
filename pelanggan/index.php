<?php
session_start();
require_once '../config/koneksi.php';

// Proteksi: Jika yang masuk bukan Pelanggan, tendang balik
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Pelanggan') {
    header("Location: ../index.php");
    exit;
}

// Ambil data terbaru member dari database (terutama poin dan level)
$id_member = $_SESSION['id_member'];
$stmt = $conn->prepare("SELECT m.*, l.nama_level FROM tb_member m JOIN tb_level_member l ON m.id_level = l.id_level WHERE m.id_member = ?");
$stmt->execute([$id_member]);
$data = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Member Area - Rasya.co</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 500px; }
        .poin-box { background: #ffc107; padding: 10px; border-radius: 5px; display: inline-block; margin-top: 10px; font-weight: bold; }
        .status-badge { background: #28a745; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px; }
    </style>
</head>
<body>

    <div class="card">
        <h2>Halo, <?php echo $data['nama_member']; ?>! <span class="status-badge">Member <?php echo $data['nama_level']; ?></span></h2>
        <p>Selamat datang di program loyalitas Rasya.co.</p>
        
        <div class="poin-box">
            Total Poin Anda: <?php echo $data['total_poin']; ?> Poin
        </div>

        <hr>
        <h4>Menu Member:</h4>
        <ul>
            <li><a href="daftar_menu.php">Lihat Menu Cafe</a></li>
            <li><a href="#">Riwayat Pesanan</a></li>
            <li><a href="#">Tukar Poin</a></li>
            <li><a href="../logout.php">Keluar (Logout)</a></li>
        </ul>
    </div>

</body>
</html>