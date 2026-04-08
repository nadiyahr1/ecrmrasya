<style>
    .order-detail-container {
        max-width: 800px;
        margin: 100px auto;
        padding: 0 20px;
        font-family: 'Inter', sans-serif;
    }

    /* .back-btn-wrapper {
        background: #fdfaf8;
        padding: 10px 15px;
        border-radius: 10px;
        display: inline-block;
        margin-bottom: 15px;
        border: 1px solid #eee;
    } */

    .back-link {
        text-decoration: none;
        color: #6F4E37;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 20px;
        transition: 0.3s;
        border: 1px solid #eee;
        padding: 10px 15px;
        background: #fdfaf8;
        border-radius: 10px;
        display: inline-block;
    }

    .back-link:hover {
        transform: translateX(-5px);
    }

    .main-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 1px solid #f0f0f0;
    }

    .card-header {
        padding: 30px;
        background: #fff;
        border-bottom: 1px dashed #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-body {
        padding: 30px;
    }

    .status-pill {
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-selesai {
        background: #d4edda;
        color: #155724;
    }

    .status-proses {
        background: #d1ecf1;
        color: #0c5460;
    }

    .item-list {
        margin-bottom: 25px;
    }

    .item-row {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f9f9f9;
    }

    .item-img {
        width: 70px;
        height: 70px;
        border-radius: 12px;
        object-fit: cover;
        margin-right: 20px;
        background: #f5f5f5;
    }

    .item-info {
        flex: 1;
    }

    .item-name {
        font-weight: 600;
        color: #333;
        margin: 0 0 5px 0;
    }

    .item-meta {
        font-size: 13px;
        color: #888;
        margin: 0;
    }

    .item-price {
        font-weight: 700;
        color: #333;
    }

    .summary-box {
        background: #fcf9f7;
        border-radius: 15px;
        padding: 20px;
        margin-top: 20px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 14px;
        color: #555;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px solid #eee;
        font-weight: 800;
        font-size: 18px;
        color: #6F4E37;
    }

    .review-section {
        margin-top: 30px;
        padding: 25px;
        background: #fff;
        border-radius: 20px;
        border: 1px solid #6F4E37;
    }

    .btn-submit-review {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        margin-top: 15px;
        transition: 0.3s;
    }

    .btn-submit-review:hover {
        background: #4b3525;
    }
</style>

<div class="order-detail-container">
    <a href="index.php?controller=pelanggan&action=profil&tab=riwayat" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Profil
    </a>
    <div class="main-card">
        <div class="card-header">
            <div>
                <h2 style="margin: 0; font-size: 22px; color: #333;">ID Pesanan #<?= $p['id_pesanan'] ?></h2>
                <p style="margin: 5px 0 0 0; color: #888; font-size: 14px;"><?= date('d F Y, H:i', strtotime($p['tgl_pesanan'])) ?></p>
            </div>
            <div class="status-pill status-<?= strtolower(strpos($p['status'], 'Selesai') !== false ? 'selesai' : 'proses') ?>">
                <?= $p['status'] ?>
            </div>
        </div>

        <div class="card-body">
            <h4 style="margin-bottom: 20px; font-size: 16px; color: #6F4E37;">Item Pesanan</h4>
            <div class="item-list">
                <?php foreach ($detail_menu as $dm): ?>
                    <div class="item-row">
                        <img src="assets/gambar/menu/<?= $dm['foto'] ?>" alt="<?= $dm['nama_menu'] ?>" class="item-img">
                        <div class="item-info">
                            <h4><?= htmlspecialchars($dm['nama_menu']) ?></h4>
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

                    // Cek apakah fasilitas menggunakan satuan orang atau jam
                    $kapasitas = !empty($df['jumlah_orang']) ? $df['jumlah_orang'] . " Orang" : ($df['durasi_jam'] . " Jam");
                ?>
                    <div class="item-row">
                        <img src="assets/gambar/fasilitas/<?= $df['foto_fasilitas'] ?>" class="item-img">
                        <div class="item-info">
                            <p class="item-name">[Fasilitas] <?= htmlspecialchars($df['nama_fasilitas']) ?></p>
                            <p class="item-meta" style="margin-bottom: 3px; color: #6F4E37;">📅 <?= $tgl ?> <?= $waktu ?></p>
                            <p class="item-meta">👥 <?= $kapasitas ?></p>
                        </div>
                        <div class="item-price">Rp <?= number_format($df['subtotal_sewa'] ?? 0) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php
            // Hitung Ulang Subtotal, Pajak, dan Diskon
            $total_item = 0;
            foreach ($detail_menu as $dm) $total_item += $dm['subtotal'];
            foreach ($detail_fas as $df) $total_item += $df['subtotal_sewa'] ?? $df['subtotal'] ?? 0;

            $pajak = $total_item * 0.1;
            $total_seharusnya = $total_item + $pajak;
            $diskon = $total_seharusnya - $p['total_transaksi'];
            ?>

            <div class="summary-box">
                <div class="summary-row" style="flex-wrap: wrap; margin-bottom: 20px;">
                    <span style="width: 50%;">Metode Pembayaran</span>
                    <span style="font-weight: 600; width: 50%; text-align: right; color: #6F4E37;">
                        <?php
                        $metode = $p['metode_pembayaran'];

                        // Jika mengandung teks " - VA: ", kita hanya ambil bagian depannya saja
                        if (strpos($metode, ' - VA: ') !== false) {
                            $parts = explode(' - VA: ', $metode);
                            // Menampilkan "Transfer (BANK)" saja
                            echo htmlspecialchars($parts[0]);
                        } else {
                            // Jika metode lain (seperti QRIS atau lainnya), tampilkan apa adanya
                            echo htmlspecialchars($metode);
                        }
                        ?>
                    </span>

                    <div style="width: 100%; margin-top: 15px;">
                        <?php
                        // Jika metode pembayarannya ada unsur kata "Transfer" (Midtrans)
                        if (strpos($p['metode_pembayaran'], 'Transfer') !== false):
                        ?>
                            <div style="background: #f8fafc; border-left: 4px solid #3b82f6; padding: 12px 15px; border-radius: 8px; margin-bottom: 15px;">
                                <strong style="color: #1e3a8a; font-size: 13px;">Status Pembayaran:</strong><br>
                                <?php if ($p['status'] != 'Belum Bayar' && $p['status'] != 'Dibatalkan'): ?>
                                    <span style="display: inline-block; margin-top: 5px; background: #d1fae5; color: #065f46; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">✅ Telah Lunas Dikonfirmasi</span>
                                <?php elseif ($p['status'] == 'Dibatalkan'): ?>
                                    <span style="display: inline-block; margin-top: 5px; background: #fee2e2; color: #991b1b; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">❌ Dibatalkan / Kadaluarsa</span>
                                <?php else: ?>
                                    <span style="display: inline-block; margin-top: 5px; background: #fef3c7; color: #92400e; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">⏳ Menunggu Pembayaran Anda</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($p['status'] == 'Belum Bayar' && !empty($p['snap_token'])): ?>
                            <div style="background: #fffbeb; border: 1px dashed #f59e0b; padding: 15px; border-radius: 8px; text-align: center;">
                                <p style="margin: 0 0 10px 0; font-size: 13px; color: #92400e;">Klik tombol di bawah untuk melihat nomor rekening / instruksi pembayaran.</p>
                                <button id="pay-button" style="background: #f59e0b; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s;">
                                    <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                                </button>
                            </div>

                            <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="Mid-client-COG378gmzSZyuqn5"></script>

                            <script type="text/javascript">
                                document.getElementById('pay-button').onclick = function() {
                                    // Snap Token diambil dari PHP
                                    snap.pay('<?= $p['snap_token'] ?>', {
                                        // Jika user menekan tombol silang (X) atau menutup popup
                                        onClose: function() {
                                            /* Mengarahkan kembali ke halaman detail pesanan yang sama */
                                            window.location.href = "index.php?controller=pelanggan&action=detail_pesanan&id=<?= $p['id_pesanan'] ?>";
                                        },
                                        // Tambahan: jika pembayaran sukses
                                        onSuccess: function(result) {
                                            window.location.href = "index.php?controller=pelanggan&action=detail_pesanan&id=<?= $p['id_pesanan'] ?>";
                                        },
                                        // Tambahan: jika pembayaran tertunda (VA muncul tapi belum dibayar)
                                        onPending: function(result) {
                                            window.location.href = "index.php?controller=pelanggan&action=detail_pesanan&id=<?= $p['id_pesanan'] ?>";
                                        }
                                    });
                                };
                            </script>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="summary-row">
                    <span>Tipe Pemesanan</span>
                    <span style="font-weight: 600; text-align: right;">
                        <?= htmlspecialchars($p['tipe_pemesanan']) ?>

                        <?php if ($p['tipe_pemesanan'] == 'Makan di Tempat' && !empty($p['no_meja'])): ?>
                            <br><small style="color: #6F4E37; font-size: 13px;">(Meja Nomor <?= htmlspecialchars($p['no_meja']) ?>)</small>
                        <?php endif; ?>
                    </span>
                </div>

                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ddd;">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span style="font-weight: 600;">Rp <?= number_format($total_item) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Pajak (10%)</span>
                        <span style="font-weight: 600;">Rp <?= number_format($pajak) ?></span>
                    </div>

                    <?php if ($diskon > 0): ?>
                        <div class="summary-row" style="color: #e53e3e;">
                            <span>Diskon Promo</span>
                            <span style="font-weight: 600;">- Rp <?= number_format($diskon) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($p['id_promo'])): ?>
                    <div style="background: #f0fdf4; border: 1px dashed #22c55e; padding: 10px 15px; border-radius: 8px; margin-top: 15px;">
                        <p style="margin: 0; font-size: 13px; color: #166534;">
                            <i class="fa-solid fa-tags"></i> <strong>Promo Digunakan:</strong> <br>
                            <?= htmlspecialchars($p['nama_promo']) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="total-row">
                    <span>Total Pembayaran</span>
                    <span>Rp <?= number_format($p['total_transaksi']) ?></span>
                </div>
                <?php
                // Tampilkan tombol Batal jika status masih Menunggu Konfirmasi atau kosong
                if ($p['status'] == 'Menunggu Konfirmasi' || $p['status'] == '' || $p['status'] == null):
                ?>
                    <div style="margin-top: 20px;">
                        <a href="index.php?controller=pelanggan&action=batalkan_pesanan&id=<?= $p['id_pesanan'] ?>"
                            onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok menu, kuota promo, dan poin Anda akan dikembalikan secara otomatis.')"
                            style="display: block; text-align: center; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px; transition: 0.3s;">
                            <i class="fa-solid fa-xmark"></i> Batalkan Pesanan Ini
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($p['status'] == 'Selesai'): ?>
                <div class="review-section">
                    <?php if (!$ulasan): ?>
                        <h4 style="margin-top: 0; color: #333;">Beri Ulasan Pesanan</h4>
                        <p style="font-size: 13px; color: #666;">Kepuasan kamu sangat berarti bagi kami di Rasya.co</p>
                        <form action="index.php?controller=pelanggan&action=simpan_ulasan" method="POST">
                            <input type="hidden" name="id_pesanan" value="<?= $p['id_pesanan'] ?>">
                            <textarea name="komentar" required placeholder="Ceritakan pengalamanmu (misal: kopinya enak, tempatnya nyaman)..."
                                style="width: 100%; box-sizing: border-box; border: 1px solid #ddd; border-radius: 12px; padding: 15px; min-height: 100px; font-family: inherit;"></textarea>
                            <button type="submit" class="btn-submit-review">Kirim Ulasan Sekarang</button>
                        </form>
                    <?php else: ?>
                        <div style="padding: 20px; background: #fdfaf8; border-radius: 15px; border: 1px solid #eee; position: relative;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="font-weight: bold; color: #6F4E37; font-size: 15px;">Ulasan Anda</span>
                                <small style="color: #999;"><?= date('d M Y', strtotime($ulasan['tgl_ulasan'])) ?></small>
                            </div>

                            <p style="margin: 0; font-size: 14px; color: #444; line-height: 1.6; font-style: italic;">
                                "<?= nl2br($ulasan['komentar']) ?>"
                            </p>

                            <?php if (!empty($ulasan['balasan_admin'])): ?>
                                <div style="margin-top: 20px; padding: 15px; background: white; border-radius: 10px; border-left: 3px solid #d4af37; box-shadow: 0 2px 5px rgba(0,0,0,0.03);">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                                        <i class="fa-solid fa-reply-all" style="color: #d4af37; transform: scaleX(-1);"></i>
                                        <span style="font-weight: 700; font-size: 13px; color: #333;">Balasan dari Rasya.co:</span>
                                    </div>
                                    <p style="margin: 0; font-size: 13px; color: #666; line-height: 1.5;">
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