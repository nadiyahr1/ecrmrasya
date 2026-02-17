<?php
session_start();
include 'layout/header.php';
require_once 'config/koneksi.php';

if (!isset($_SESSION['id_member'])) {
    header("Location: login.php"); exit;
}

$id_p = $_GET['id'];
$id_m = $_SESSION['id_member'];

// 1. Ambil Data Utama Pesanan (Pastikan milik member yang login)
$stmt = $conn->prepare("SELECT p.*, m.nama_member FROM tb_pesanan p JOIN tb_member m ON p.id_member = m.id_member WHERE p.id_pesanan = ? AND p.id_member = ?");
$stmt->execute([$id_p, $id_m]);
$p = $stmt->fetch();

if (!$p) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='riwayat_pesanan.php';</script>"; exit;
}

// 2. Ambil Detail Menu
$stmt_m = $conn->prepare("SELECT d.*, m.nama_menu, m.foto FROM tb_detail_pesanan d JOIN tb_menu m ON d.id_menu = m.id_menu WHERE d.id_pesanan = ?");
$stmt_m->execute([$id_p]);
$detail_menu = $stmt_m->fetchAll();

// 3. Ambil Detail Fasilitas
$stmt_f = $conn->prepare("SELECT d.*, f.nama_fasilitas, f.foto_fasilitas FROM tb_detail_pesanan d JOIN tb_fasilitas f ON d.id_fasilitas = f.id_fasilitas WHERE d.id_pesanan = ?");
$stmt_f->execute([$id_p]);
$detail_fas = $stmt_f->fetchAll();
?>

<div style="padding: 40px; background: #fdfdfd; min-height: 80vh;">
    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <a href="riwayat_pesanan.php" style="text-decoration: none; color: #6F4E37;">← Kembali ke Riwayat</a>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
            <h2>Detail Pesanan #<?= $id_p ?></h2>
            <span style="padding: 5px 15px; background: #eee; border-radius: 20px; font-size: 14px;"><?= $p['status'] ?></span>
        </div>
        <p style="color: #888;"><?= date('d F Y, H:i', strtotime($p['tgl_pesanan'])) ?></p>
        <hr>

        <div style="margin-top: 20px;">
            <h4>Item yang Dipesan:</h4>
            
            <?php foreach($detail_menu as $dm): ?>
            <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 15px;">
                <img src="assets/img/menu/<?= $dm['foto'] ?: 'default.jpg' ?>" width="60" height="60" style="border-radius: 8px; object-fit: cover;">
                <div style="flex: 1;">
                    <strong><?= $dm['nama_menu'] ?></strong><br>
                    <small><?= $dm['jumlah'] ?> x Rp <?= number_format($dm['subtotal']/$dm['jumlah']) ?></small>
                </div>
                <strong>Rp <?= number_format($dm['subtotal']) ?></strong>
            </div>
            <?php endforeach; ?>

            <?php foreach($detail_fas as $df): ?>
            <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 15px; background: #fffcf5; padding: 10px; border-radius: 8px;">
                <img src="assets/img/fasilitas/<?= $df['foto_fasilitas'] ?: 'default_fas.jpg' ?>" width="60" height="60" style="border-radius: 8px; object-fit: cover;">
                <div style="flex: 1;">
                    <strong>[Fasilitas] <?= $df['nama_fasilitas'] ?></strong><br>
                    <small><?= $df['tgl_booking'] ?> | <?= $df['durasi_jam'] ?> Jam</small>
                </div>
                <strong>Rp <?= number_format($df['subtotal']) ?></strong>
            </div>
            <?php endforeach; ?>
        </div>

        <hr style="margin: 30px 0;">
        
        <div style="display: flex; justify-content: space-between;">
            <div>
                <p><strong>Metode Pembayaran:</strong> <?= $p['metode_pembayaran'] ?></p>
                <p><strong>Tipe:</strong> <?= $p['tipe_pemesanan'] ?> <?= $p['id_meja'] ? "(Meja ".$p['id_meja'].")" : "" ?></p>
            </div>
            <div style="text-align: right;">
                <h3 style="color: #6F4E37;">Total Bayar: Rp <?= number_format($p['total_transaksi']) ?></h3>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>