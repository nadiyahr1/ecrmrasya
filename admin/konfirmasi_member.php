<?php
session_start();
require_once '../config/koneksi.php';

// Proteksi: Hanya Admin yang bisa buka halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

// Logika untuk mengubah status menjadi 'Aktif'
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("UPDATE tb_member SET status_akun = 'Aktif' WHERE id_member = ?");
    $stmt->execute([$id]);
    echo "<script>alert('Member berhasil diaktifkan!'); window.location='konfirmasi_member.php';</script>";
}

// Ambil data member yang masih 'Pending'
$stmt = $conn->query("SELECT * FROM tb_member WHERE status_akun = 'Pending'");
$members = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Member - Rasya.co</title>
</head>
<body>
    <h2>Daftar Member Baru (Menunggu Verifikasi)</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Nama</th>
            <th>Username</th>
            <th>No. Telp</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($members as $m) : ?>
        <tr>
            <td><?= $m['nama_member']; ?></td>
            <td><?= $m['username']; ?></td>
            <td><?= $m['no_telp']; ?></td>
            <td>
                <a href="konfirmasi_member.php?id=<?= $m['id_member']; ?>" onclick="return confirm('Aktifkan member ini?')">Setujui</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (count($members) == 0) : ?>
        <tr>
            <td colspan="4" align="center">Tidak ada pendaftar baru.</td>
        </tr>
        <?php endif; ?>
    </table>
    <br>
    <a href="dashboard.php">Kembali ke Dashboard</a>
</body>
</html>