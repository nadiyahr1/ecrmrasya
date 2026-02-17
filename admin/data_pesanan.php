<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

// Ambil semua pesanan dan gabungkan dengan nama member
$query = "SELECT p.*, m.nama_member FROM tb_pesanan p 
          LEFT JOIN tb_member m ON p.id_member = m.id_member 
          ORDER BY p.tgl_pesanan DESC";
$pesanan = $conn->query($query)->fetchAll();

// Filter Status
$status_filter = isset($_GET['s']) ? $_GET['s'] : '';
$query = "SELECT p.*, m.nama_member FROM tb_pesanan p 
          LEFT JOIN tb_member m ON p.id_member = m.id_member";

if ($status_filter != '') {
    $query .= " WHERE p.status = '$status_filter'";
}

$query .= " ORDER BY p.tgl_pesanan DESC";
$pesanan = $conn->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Data Pesanan - Admin</title>
</head>

<body>
    <h2>Daftar Pesanan Masuk</h2>
    <div style="margin-bottom: 20px;">
        <strong>Filter Status:</strong>
        <a href="data_pesanan.php">Semua</a> |
        <a href="data_pesanan.php?s=Menunggu Verifikasi">Menunggu Verifikasi</a> |
        <a href="data_pesanan.php?s=Diproses">Diproses</a> |
        <a href="data_pesanan.php?s=Dapat Diambil">Dapat Diambil</a> |
        <a href="data_pesanan.php?s=Selesai">Selesai</a>
    </div>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID Pesanan</th>
            <th>Nama Pelanggan</th>
            <th>Total Bayar</th>
            <th>Meja</th>
            <th>Catatan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($pesanan as $p) : ?>
            <tr>
                <td><?= $p['id_pesanan']; ?></td>
                <td><?= $p['nama_member'] ?? 'Non-Member'; ?></td>
                <td>Rp <?= number_format($p['total_transaksi']); ?></td>
                <td><?= $p['id_meja'] ? 'Meja ' . $p['id_meja'] : 'Pick-up'; ?></td>
                <td><small><?= $p['catatan']; ?></small></td>
                <td><b><?= $p['status']; ?></b></td>
                <td>
                    <?php if ($p['status'] == 'Menunggu Verifikasi') : ?>
                        <a href="verifikasi_pesanan.php?id=<?= $p['id_pesanan']; ?>">Verifikasi & Cetak</a>
                    <?php elseif ($p['status'] == 'Diproses' || $p['status'] == 'Dapat Diambil') : ?>
                        <a href="selesaikan_pesanan.php?id=<?= $p['id_pesanan']; ?>"
                            onclick="return confirm('Selesaikan pesanan ini? Poin akan otomatis ditambahkan ke member.')"
                            style="color: green; font-weight: bold;">
                            Selesaikan & Beri Poin
                        </a>
                    <?php else : ?>
                        <span style="color: gray;">Selesai ✅</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br><a href="dashboard.php">Kembali ke Dashboard</a>
</body>

</html>