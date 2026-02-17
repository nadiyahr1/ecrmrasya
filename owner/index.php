<?php
session_start();
require_once '../config/koneksi.php';

// Proteksi: Hanya Owner yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Owner') {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Owner Dashboard - Rasya.co</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .summary-box { display: flex; gap: 20px; }
        .box { padding: 20px; background: #0275d8; color: white; border-radius: 8px; flex: 1; text-align: center; }
    </style>
</head>
<body>
    <h1>Dashboard Owner</h1>
    <p>Selamat Datang, <?php echo $_SESSION['nama']; ?>. Berikut ringkasan performa Rasya.co.</p>

    <div class="summary-box">
        <div class="box">
            <h3>Laporan Penjualan</h3>
            <p>Fitur laporan akan muncul di sini.</p>
        </div>
        <div class="box" style="background: #5cb85c;">
            <h3>Analisis CRM</h3>
            <p>Statistik poin & member.</p>
        </div>
    </div>

    <hr>
    <a href="../logout.php">Keluar (Logout)</a>
</body>
</html>