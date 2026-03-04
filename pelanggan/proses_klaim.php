<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_p = $_POST['id_pesanan'];
    $id_m = $_SESSION['id_member'];

    // 1. Cari data pesanan tersebut
    $stmt = $conn->prepare("SELECT * FROM tb_pesanan WHERE id_pesanan = ?");
    $stmt->execute([$id_p]);
    $order = $stmt->fetch();

    if (!$order) {
        echo "<script>alert('ID Pesanan tidak ditemukan. Periksa kembali struk Anda.'); window.location='profil.php';</script>";
        exit;
    }

    // 2. Cek apakah sudah pernah diklaim (id_member sudah terisi di pesanan tersebut)
    if ($order['id_member'] != null) {
        echo "<script>alert('Poin untuk pesanan ini sudah diklaim!'); window.location='profil.php';</script>";
        exit;
    }

    // 3. Cek apakah status sudah Selesai
    if ($order['status'] != 'Selesai') {
        echo "<script>alert('Pesanan belum selesai. Silahkan klaim setelah pesanan selesai.'); window.location='profil.php';</script>";
        exit;
    }

    // 4. Hitung Poin (Rp 1.000 = 1 Poin)
    $poin_klaim = floor($order['total_transaksi'] / 1000);

    try {
        $conn->beginTransaction();

        // A. Hubungkan pesanan ini ke member yang mengklaim
        $conn->prepare("UPDATE tb_pesanan SET id_member = ? WHERE id_pesanan = ?")->execute([$id_m, $id_p]);

        // B. Tambahkan poin ke saldo member
        $conn->prepare("UPDATE tb_member SET total_poin = total_poin + ? WHERE id_member = ?")->execute([$poin_klaim, $id_m]);

        // C. Catat di History Poin
        $ket = "Klaim poin manual dari transaksi " . $id_p;
        $conn->prepare("INSERT INTO tb_history_poin (id_member, poin, tipe, keterangan) VALUES (?, ?, 'Masuk', ?)")
             ->execute([$id_m, $poin_klaim, $ket]);

        $conn->commit();
        echo "<script>alert('Selamat! Anda berhasil mengklaim $poin_klaim poin.'); window.location='profil.php';</script>";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Gagal klaim: " . $e->getMessage();
    }
}
?>