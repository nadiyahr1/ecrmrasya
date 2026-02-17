<?php
session_start();
include 'layout/header.php';
require_once 'config/koneksi.php';

// Proteksi: Pastikan hanya member yang bisa akses
if (!isset($_SESSION['id_member'])) {
    header("Location: login.php"); exit;
}

$id_m = $_SESSION['id_member'];

// Ambil riwayat pesanan dari yang terbaru
$query = "SELECT * FROM tb_pesanan WHERE id_member = ? ORDER BY tgl_pesanan DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$id_m]);
$riwayat = $stmt->fetchAll();
?>

<div style="padding: 40px; background: #fdfdfd; min-height: 80vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <h2 style="color: #6F4E37; margin-bottom: 5px;">Riwayat Pesanan Anda</h2>
        <p style="color: #888; margin-bottom: 30px;">Pantau status pesanan menu dan booking fasilitas Anda di sini.</p>

        <?php if (empty($riwayat)) : ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <img src="assets/img/empty-cart.png" width="150" style="opacity: 0.5;">
                <p style="margin-top: 20px; color: #666;">Anda belum memiliki riwayat pesanan.</p>
                <a href="index.php" style="display: inline-block; margin-top: 10px; padding: 10px 20px; background: #6F4E37; color: white; text-decoration: none; border-radius: 5px;">Mulai Pesan Sekarang</a>
            </div>
        <?php else : ?>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <?php foreach ($riwayat as $r) : ?>
                    <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 8px solid 
                        <?php 
                            if($r['status'] == 'Menunggu Verifikasi') echo '#ffc107';
                            elseif($r['status'] == 'Diproses' || $r['status'] == 'Dapat Diambil') echo '#17a2b8';
                            elseif($r['status'] == 'Selesai') echo '#28a745';
                            else echo '#dc3545';
                        ?>;">
                        
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                            <div>
                                <span style="font-size: 12px; color: #888; font-weight: bold;"><?= date('d M Y, H:i', strtotime($r['tgl_pesanan'])) ?></span>
                                <h4 style="margin: 5px 0; color: #333;"><?= $r['id_pesanan'] ?></h4>
                                <span style="font-size: 14px; color: #666;"><?= $r['tipe_pemesanan'] ?> 
                                    <?= ($r['id_meja']) ? " (Meja ".$r['id_meja'].")" : "" ?>
                                </span>
                            </div>
                            <div style="text-align: right;">
                                <div style="padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white; background: 
                                    <?php 
                                        if($r['status'] == 'Menunggu Verifikasi') echo '#ffc107';
                                        elseif($r['status'] == 'Diproses' || $r['status'] == 'Dapat Diambil') echo '#17a2b8';
                                        elseif($r['status'] == 'Selesai') echo '#28a745';
                                        else echo '#dc3545';
                                    ?>;">
                                    <?= $r['status'] ?>
                                </div>
                                <h3 style="margin-top: 10px; color: #6F4E37;">Rp <?= number_format($r['total_transaksi']) ?></h3>
                            </div>
                        </div>

                        <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 14px; color: #666;">
                                <strong>Metode:</strong> <?= $r['metode_pembayaran'] ?>
                                <?php if($r['catatan']): ?>
                                    <br><small><em>Catatan: "<?= $r['catatan'] ?>"</em></small>
                                <?php endif; ?>
                            </div>
                            <a href="detail_pesanan_pelanggan.php?id=<?= $r['id_pesanan'] ?>" style="color: #6F4E37; text-decoration: none; font-weight: bold; font-size: 14px;">Lihat Detail Item →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'layout/footer.php'; ?>