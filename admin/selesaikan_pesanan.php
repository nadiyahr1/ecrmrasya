<?php
session_start();
require_once '../config/koneksi.php';

if (isset($_GET['id'])) {
    $id_p = $_GET['id'];

    // 1. Ambil data pesanan untuk tahu siapa pembelinya dan berapa totalnya
    $stmt = $conn->prepare("SELECT id_member, total_transaksi FROM tb_pesanan WHERE id_pesanan = ?");
    $stmt->execute([$id_p]);
    $order = $stmt->fetch();

    if ($order) {
        $id_m = $order['id_member'];
        $total = $order['total_transaksi'];
        
        // RUMUS POIN: Setiap belanja Rp 1.000 dapat 1 Poin (Bisa kamu ganti sendiri)
        $poin_baru = floor($total / 1000);

        try {
            $conn->beginTransaction();

            // A. Update status pesanan jadi Selesai
            $conn->prepare("UPDATE tb_pesanan SET status = 'Selesai' WHERE id_pesanan = ?")->execute([$id_p]);

            // B. Jika yang beli adalah Member, tambahkan poinnya
            if ($id_m != null) {
                // Tambah total poin di tb_member
                $conn->prepare("UPDATE tb_member SET total_poin = total_poin + ? WHERE id_member = ?")->execute([$poin_baru, $id_m]);

                // Catat riwayat poin di tb_history_poin
                $sql_histori = "INSERT INTO tb_history_poin (id_member, poin, tipe, keterangan) VALUES (?, ?, 'Masuk', ?)";
                $ket = "Poin dari transaksi " . $id_p;
                $conn->prepare($sql_histori)->execute([$id_m, $poin_baru, $ket]);
            }

            $conn->commit();
            echo "<script>alert('Pesanan Selesai! Member mendapatkan $poin_baru poin.'); window.location='data_pesanan.php';</script>";
        } catch (Exception $e) {
            $conn->rollBack();
            echo "Gagal: " . $e->getMessage();
        }
    }
}