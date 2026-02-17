<?php
// Memulai session untuk mengecek siapa yang masuk
session_start();

// Keamanan: Jika yang masuk bukan Admin, tendang balik ke login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Rasya.co</title>
</head>
<body>
    <h1>Selamat Datang, <?php echo $_SESSION['nama']; ?>!</h1>
    <p>Ini adalah halaman utama Admin untuk mengelola sistem E-CRM Cafe Rasya.co.</p>
    
    <hr>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="../logout.php">Keluar (Logout)</a></li>
    </ul>

    <div style="margin-top: 20px; padding: 15px; background: #e2e2e2;">
        <h3>Statistik Ringkas</h3>
        <p>Di sini nantinya kamu bisa menampilkan jumlah member, total pesanan, dll.</p>
    </div>
</body>
</html>