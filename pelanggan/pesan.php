<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Pelanggan') {
    header("Location: ../index.php"); exit;
}

$id_menu = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM tb_menu WHERE id_menu = ?");
$stmt->execute([$id_menu]);
$menu = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_member = $_SESSION['id_member'];
    $jumlah = $_POST['jumlah'];
    $total = $menu['harga'] * $jumlah;
    $id_pesanan = "RSY-" . date('YmdHis'); // Format ID Pesanan Unik

    try {
        $conn->beginTransaction();

        // 1. Simpan ke tb_pesanan
        $sql1 = "INSERT INTO tb_pesanan (id_pesanan, id_member, tgl_pesanan, total_transaksi, tipe_pemesanan, metode_pembayaran, status) 
                 VALUES (?, ?, NOW(), ?, 'Dine-in', 'Transfer', 'Menunggu Verifikasi')";
        $conn->prepare($sql1)->execute([$id_pesanan, $id_member, $total]);

        // 2. Simpan detailnya
        $sql2 = "INSERT INTO tb_detail_pesanan (id_pesanan, id_menu, jumlah, subtotal) VALUES (?, ?, ?, ?)";
        $conn->prepare($sql2)->execute([$id_pesanan, $id_menu, $jumlah, $total]);

        $conn->commit();
        echo "<script>alert('Pesanan berhasil dibuat! Silahkan bayar Rp " . number_format($total) . "'); window.location='index.php';</script>";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Gagal memesan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head><title>Konfirmasi Pesanan</title></head>
<body>
    <h2>Konfirmasi Pesanan</h2>
    <p>Menu: <b><?= $menu['nama_menu']; ?></b></p>
    <p>Harga: Rp <?= number_format($menu['harga']); ?></p>
    <form method="POST">
        Jumlah: <input type="number" name="jumlah" value="1" min="1" required>
        <button type="submit">Konfirmasi & Bayar</button>
    </form>
</body>
</html>