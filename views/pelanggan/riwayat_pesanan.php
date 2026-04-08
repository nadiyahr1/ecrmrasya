<link rel="stylesheet" href="assets/css/pelanggan-order.css">

<div class="riwayat-container">
    <div class="riwayat-header">
        <h4 class="riwayat-title">Daftar Pesanan Terakhir</h4>
    </div>

    <?php if (empty($riwayat)) : ?>
        <div class="empty-history-box">
            <div class="empty-history-icon">🛒</div>
            <h5 class="empty-history-title">Belum ada pesanan</h5>
            <p class="empty-history-desc">Yuk, mulai pesan kopi favoritmu sekarang!</p>
            <a href="<?= $base_url ?>index.php?controller=menu&action=index" class="btn-lihat-menu">Lihat Menu</a>
        </div>
    <?php else : ?>

        <?php foreach ($riwayat as $r) :
            // Menentukan class CSS berdasarkan status
            $status_class = 'status-menunggu'; // default
            $status_text = strtolower($r['status']);
            if (strpos($status_text, 'proses') !== false || strpos($status_text, 'diambil') !== false) {
                $status_class = 'status-diproses';
            } elseif (strpos($status_text, 'selesai') !== false) {
                $status_class = 'status-selesai';
            } elseif (strpos($status_text, 'batal') !== false || strpos($status_text, 'tolak') !== false) {
                $status_class = 'status-batal';
            }
        ?>
            <div class="history-card">
                <div class="history-header">
                    <div>
                        <div class="history-id"><?= $r['id_pesanan'] ?></div>
                        <span class="history-date"><i class="fa-regular fa-calendar"></i> <?= date('d M Y - H:i', strtotime($r['tgl_pesanan'])) ?></span>
                    </div>
                    <div class="badge-order-status <?= $status_class ?>">
                        <?= $r['status'] ?>
                    </div>
                </div>

                <div class="history-body">
                    <div>
                        <p class="history-total-label">Total Belanja</p>
                        <p class="history-price">Rp <?= number_format($r['total_transaksi'], 0, ',', '.') ?></p>
                    </div>

                    <div class="history-actions">
                        <?php if ($r['status'] == 'Selesai' && empty($r['komentar'])): ?>
                            <span class="label-ulas label-ulas-disabled">
                                Beri ulasan <strong>+5 Poin</strong>
                                <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        <?php endif; ?>

                        <?php
                        // Logika Cerdas Pembatalan
                        $st = $r['status'];
                        $metode = $r['metode_pembayaran'];
                        $boleh_batal = false;

                        if (strpos($metode, 'Transfer') !== false) {
                            // Jika Transfer: Boleh batal jika belum bayar ke Midtrans
                            if ($st == 'Belum Bayar') {
                                $boleh_batal = true;
                            }
                        } else {
                            // Jika Bayar di Kasir: Boleh batal jika Admin belum memprosesnya
                            if ($st == 'Menunggu Konfirmasi' || $st == '' || $st == null) {
                                $boleh_batal = true;
                            }
                        }

                        if ($boleh_batal):
                        ?>
                            <a href="index.php?controller=pelanggan&action=batalkan_pesanan&id=<?= $r['id_pesanan'] ?>"
                                onclick="return confirm('Yakin ingin membatalkan pesanan? Stok dan poin akan dikembalikan.')"
                                class="btn-cancel-order">
                                <i class="fa-solid fa-xmark"></i> Batalkan
                            </a>
                        <?php endif; ?>
                        
                        <a href="index.php?controller=pelanggan&action=detail_pesanan&id=<?= $r['id_pesanan'] ?>" class="btn-detail-pesanan">Lihat Detail</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>