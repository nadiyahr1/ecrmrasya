<link rel="stylesheet" href="assets/css/pelanggan-order.css">

<div class="order-detail-container">
    <a href="index.php?controller=pelanggan&action=profil&tab=riwayat" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Profil
    </a>
    <div class="main-card">
        <div class="card-header">
            <div>
                <h2 class="order-id-title">ID Pesanan #<?= $p['id_pesanan'] ?></h2>
                <p class="order-date-subtitle"><?= date('d F Y, H:i', strtotime($p['tgl_pesanan'])) ?></p>
            </div>
            <?php
            // Logika class status sinkron dengan riwayat
            $status_class = 'status-menunggu';
            $status_text = strtolower($p['status']);
            if (strpos($status_text, 'proses') !== false || strpos($status_text, 'diambil') !== false) {
                $status_class = 'status-diproses';
            } elseif (strpos($status_text, 'selesai') !== false) {
                $status_class = 'status-selesai';
            } elseif (strpos($status_text, 'batal') !== false || strpos($status_text, 'tolak') !== false) {
                $status_class = 'status-batal';
            }
            ?>
            <div class="badge-order-status <?= $status_class ?>">
                <?= $p['status'] ?>
            </div>
        </div>

        <div class="card-body">
            <h4 class="section-title-md">Item Pesanan</h4>
            <div class="item-list">
                <?php foreach ($detail_menu as $dm): ?>
                    <div class="item-row">
                        <img src="assets/gambar/menu/<?= $dm['foto'] ?>" alt="<?= $dm['nama_menu'] ?>" class="item-img">
                        <div class="item-info">
                            <h4 class="item-name"><?= htmlspecialchars($dm['nama_menu']) ?></h4>
                            <?php $harga_sat = $dm['jumlah'] > 0 ? ($dm['subtotal'] / $dm['jumlah']) : 0; ?>
                            <p class="item-meta">
                                <?= $dm['jumlah'] ?> x Rp <?= number_format($harga_sat, 0, ',', '.') ?>
                            </p>
                        </div>
                        <div class="item-price">
                            Rp <?= number_format($dm['subtotal'], 0, ',', '.') ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php foreach ($detail_fas as $df):
                    $tgl = date('d M Y', strtotime($df['tgl_sewa']));
                    $jam_m = !empty($df['jam_mulai']) ? date('H:i', strtotime($df['jam_mulai'])) : '';
                    $jam_s = !empty($df['jam_selesai']) ? date('H:i', strtotime($df['jam_selesai'])) : '';
                    $waktu = $jam_m ? "($jam_m - $jam_s)" : "";
                    $kapasitas = !empty($df['jumlah_orang']) ? $df['jumlah_orang'] . " Orang" : ($df['durasi_jam'] . " Jam");
                ?>
                    <div class="item-row">
                        <img src="assets/gambar/fasilitas/<?= $df['foto_fasilitas'] ?>" class="item-img">
                        <div class="item-info">
                            <p class="item-name">[Fasilitas] <?= htmlspecialchars($df['nama_fasilitas']) ?></p>
                            <p class="item-meta item-meta-fasilitas">📅 <?= $tgl ?> <?= $waktu ?></p>
                            <p class="item-meta">👥 <?= $kapasitas ?></p>
                        </div>
                        <div class="item-price">Rp <?= number_format($df['subtotal_sewa'] ?? 0) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php
            $total_item = 0;
            foreach ($detail_menu as $dm) $total_item += $dm['subtotal'];
            foreach ($detail_fas as $df) $total_item += $df['subtotal_sewa'] ?? $df['subtotal'] ?? 0;

            $pajak = $total_item * 0.1;
            $total_seharusnya = $total_item + $pajak;
            $diskon = $total_seharusnya - $p['total_transaksi'];
            ?>

            <div class="summary-box">
                <div class="summary-row summary-row-wrap">
                    <span class="summary-label-half">Metode Pembayaran</span>
                    <span class="summary-value-half">
                        <?php
                        $metode = $p['metode_pembayaran'];
                        if (strpos($metode, ' - VA: ') !== false) {
                            $parts = explode(' - VA: ', $metode);
                            echo htmlspecialchars($parts[0]);
                        } else {
                            echo htmlspecialchars($metode);
                        }
                        ?>
                    </span>

                    <div style="width: 100%; margin-top: 15px;">
                        <?php if (strpos($p['metode_pembayaran'], 'Transfer') !== false): ?>
                            <div class="payment-status-box">
                                <strong class="payment-status-title">Status Pembayaran:</strong><br>
                                <?php if ($p['status'] != 'Belum Bayar' && $p['status'] != 'Dibatalkan'): ?>
                                    <span class="badge-payment lunas">✅ Telah Lunas Dikonfirmasi</span>
                                <?php elseif ($p['status'] == 'Dibatalkan'): ?>
                                    <span class="badge-payment batal">❌ Dibatalkan / Kadaluarsa</span>
                                <?php else: ?>
                                    <span class="badge-payment menunggu">⏳ Menunggu Pembayaran Anda</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($p['status'] == 'Belum Bayar' && !empty($p['snap_token'])): ?>
                            <div class="payment-instruction-box">
                                <p class="payment-instruction-text">Klik tombol di bawah untuk melihat nomor rekening / instruksi pembayaran.</p>
                                <button id="pay-button" class="btn-pay-now">
                                    <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                                </button>
                            </div>

                            <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="Mid-client-COG378gmzSZyuqn5"></script>
                            <script type="text/javascript">
                                document.getElementById('pay-button').onclick = function() {
                                    snap.pay('<?= $p['snap_token'] ?>', {
                                        onClose: function() { window.location.href = "index.php?controller=pelanggan&action=detail_pesanan&id=<?= $p['id_pesanan'] ?>"; },
                                        onSuccess: function(result) { window.location.href = "index.php?controller=pelanggan&action=detail_pesanan&id=<?= $p['id_pesanan'] ?>"; },
                                        onPending: function(result) { window.location.href = "index.php?controller=pelanggan&action=detail_pesanan&id=<?= $p['id_pesanan'] ?>"; }
                                    });
                                };
                            </script>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="summary-row">
                    <span>Tipe Pemesanan</span>
                    <span class="summary-value" style="text-align: right;">
                        <?= htmlspecialchars($p['tipe_pemesanan']) ?>
                        <?php if ($p['tipe_pemesanan'] == 'Makan di Tempat' && !empty($p['no_meja'])): ?>
                            <br><small class="item-meta-fasilitas">(Meja Nomor <?= htmlspecialchars($p['no_meja']) ?>)</small>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="summary-divider">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span class="summary-value">Rp <?= number_format($total_item) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Pajak (10%)</span>
                        <span class="summary-value">Rp <?= number_format($pajak) ?></span>
                    </div>

                    <?php if ($diskon > 0): ?>
                        <div class="summary-row text-danger">
                            <span>Diskon Promo</span>
                            <span class="summary-value">- Rp <?= number_format($diskon) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($p['id_promo'])): ?>
                    <div class="promo-box">
                        <p class="promo-text">
                            <i class="fa-solid fa-tags"></i> <strong>Promo Digunakan:</strong> <br>
                            <?= htmlspecialchars($p['nama_promo']) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="total-row">
                    <span>Total Pembayaran</span>
                    <span>Rp <?= number_format($p['total_transaksi']) ?></span>
                </div>
                
                <?php if ($p['status'] == 'Menunggu Konfirmasi' || $p['status'] == '' || $p['status'] == null): ?>
                    <div>
                        <a href="index.php?controller=pelanggan&action=batalkan_pesanan&id=<?= $p['id_pesanan'] ?>"
                            onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok menu, kuota promo, dan poin Anda akan dikembalikan secara otomatis.')"
                            class="btn-cancel-order-full">
                            <i class="fa-solid fa-xmark"></i> Batalkan Pesanan Ini
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($p['status'] == 'Selesai'): ?>
                <div class="review-section">
                    <?php if (!$ulasan): ?>
                        <h4 class="review-prompt-title">Beri Ulasan Pesanan</h4>
                        <p class="review-prompt-desc">Kepuasan kamu sangat berarti bagi kami di Rasya.co</p>
                        <form action="index.php?controller=pelanggan&action=simpan_ulasan" method="POST">
                            <input type="hidden" name="id_pesanan" value="<?= $p['id_pesanan'] ?>">
                            <textarea name="komentar" required placeholder="Ceritakan pengalamanmu (misal: kopinya enak, tempatnya nyaman)..." class="review-textarea-detail"></textarea>
                            <button type="submit" class="btn-submit-review">Kirim Ulasan Sekarang</button>
                        </form>
                    <?php else: ?>
                        <div class="review-box">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span class="review-title">Ulasan Anda</span>
                                <small class="review-date"><?= date('d M Y', strtotime($ulasan['tgl_ulasan'])) ?></small>
                            </div>

                            <p class="review-text">
                                "<?= nl2br($ulasan['komentar']) ?>"
                            </p>

                            <?php if (!empty($ulasan['balasan_admin'])): ?>
                                <div class="admin-reply-box">
                                    <div class="admin-reply-header">
                                        <i class="fa-solid fa-reply-all admin-reply-icon"></i>
                                        <span class="admin-reply-title">Balasan dari Rasya.co:</span>
                                    </div>
                                    <p class="admin-reply-text">
                                        <?= nl2br($ulasan['balasan_admin']) ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>