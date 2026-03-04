<?php
// admin/selesaikan_pesanan.php
// File ini dipanggil melalui index.php, jadi tidak perlu session_start() atau require koneksi lagi.

if (isset($_GET['id'])) {
    $id_p = $_GET['id'];

    // 1. Ambil data pesanan untuk tahu siapa pembelinya dan berapa totalnya
    $stmt = $conn->prepare("SELECT id_member, total_transaksi FROM tb_pesanan WHERE id_pesanan = ?");
    $stmt->execute([$id_p]);
    $order = $stmt->fetch();

    if ($order) {
        $id_m = $order['id_member'];
        $total = $order['total_transaksi'];

        // RUMUS POIN: Setiap belanja Rp 1.000 dapat 1 Poin
        $poin_baru = floor($total / 1000);

        try {
            $conn->beginTransaction();

            // A. Update status pesanan jadi Selesai
            $conn->prepare("UPDATE tb_pesanan SET status = 'Selesai' WHERE id_pesanan = ?")->execute([$id_p]);

            // B. Jika yang beli adalah Member (bukan pelanggan umum), tambahkan poinnya
            if ($id_m != null) {
                // Tambah total poin di tb_member
                $conn->prepare("UPDATE tb_member SET total_poin = total_poin + ? WHERE id_member = ?")->execute([$poin_baru, $id_m]);

                // Catat riwayat poin di tb_history_poin
                $sql_histori = "INSERT INTO tb_history_poin (id_member, poin, tipe, keterangan) VALUES (?, ?, 'Masuk', ?)";
                $ket = "Poin dari transaksi " . $id_p;
                $conn->prepare($sql_histori)->execute([$id_m, $poin_baru, $ket]);
            
                // Logika Kenaikan Level Otomatis
                $current_poin = $conn->query("SELECT total_poin FROM tb_member WHERE id_member = $id_m")->fetchColumn();

                if ($current_poin >= 2000) {
                    $new_level = 3; // Gold
                } elseif ($current_poin >= 1000) {
                    $new_level = 2; // Silver
                } else {
                    $new_level = 1; // Bronze
                }

                $conn->prepare("UPDATE tb_member SET id_level = ? WHERE id_member = ?")->execute([$new_level, $id_m]);
                
                $pesan_alert = "Pesanan Selesai! Member mendapatkan $poin_baru poin.";
            } else {
                $pesan_alert = "Pesanan Selesai! (Pelanggan Umum tidak mendapat poin)";
            }

            $conn->commit();
            
            // PERBAIKAN REDIRECT: Arahkan kembali melalui index.php ke tab Selesai
            echo "<script>alert('$pesan_alert'); window.location='index.php?page=data_pesanan&tab=Selesai';</script>";
            
        } catch (Exception $e) {
            $conn->rollBack();
            echo "<script>alert('Gagal memproses pesanan: " . $e->getMessage() . "'); window.location='index.php?page=data_pesanan';</script>";
        }
    }
}
?>