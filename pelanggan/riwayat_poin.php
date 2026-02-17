<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Pelanggan') {
    header("Location: ../index.php"); exit;
}

$id_member = $_SESSION['id_member'];
// Ambil riwayat poin terbaru
$stmt = $conn->prepare("SELECT * FROM tb_history_poin WHERE id_member = ? ORDER BY tgl_perubahan DESC");
$stmt->execute([$id_member]);
$histori = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head><title>Riwayat Poin - Rasya.co</title></head>
<body>
    <a href="index.php">⬅ Kembali</a>
    <h2>Riwayat Poin Anda</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Tanggal</th>
            <th>Poin</th>
            <th>Tipe</th>
            <th>Keterangan</th>
        </tr>
        <?php foreach ($histori as $h) : ?>
        <tr>
            <td><?= $h['tgl_perubahan']; ?></td>
            <td><?= ($h['tipe'] == 'Masuk' ? '+' : '-') . $h['poin']; ?></td>
            <td><?= $h['tipe']; ?></td>
            <td><?= $h['keterangan']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>