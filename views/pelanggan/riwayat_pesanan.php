<style>
    /* CSS Khusus untuk Tab Riwayat */
    .history-card {
        background: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .history-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px dashed #eee;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }

    .history-id {
        font-weight: 700;
        color: #333;
        font-size: 16px;
    }

    .history-date {
        color: #888;
        font-size: 13px;
        display: block;
        margin-top: 4px;
    }

    .badge-status {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .status-menunggu {
        background: #fff3cd;
        color: #856404;
    }

    .status-diproses {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-selesai {
        background: #d4edda;
        color: #155724;
    }

    .status-batal {
        background: #f8d7da;
        color: #721c24;
    }

    .history-body {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .history-price {
        font-size: 20px;
        font-weight: bold;
        color: #6F4E37;
        /* Warna khas Rasya.co */
        margin: 0;
    }

    .history-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .btn-detail-pesanan {
        color: #6F4E37;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        padding: 8px 15px;
        border: 1px solid #6F4E37;
        border-radius: 8px;
        transition: 0.3s;
    }

    .btn-detail-pesanan:hover {
        background: #6F4E37;
        color: white;
    }

    .label-ulas {
        background: #ffc107;
        color: #333;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 8px;
        transition: 0.3s;
        box-shadow: 0 2px 5px rgba(255, 193, 7, 0.3);
    }

    .review-box {
        background: #fafafa;
        border-left: 4px solid #6F4E37;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
        font-size: 13px;
    }

    .reply-box {
        background: #e0f2fe;
        border-left: 4px solid #0284c7;
        padding: 10px 15px;
        border-radius: 8px;
        margin-top: 10px;
        font-size: 13px;
    }
</style>

<div class="riwayat-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h4 style="margin: 0; color: #6F4E37; font-size: 20px;">Daftar Pesanan Terakhir</h4>
    </div>

    <?php if (empty($riwayat)) : ?>
        <div style="text-align: center; padding: 50px 20px; background: #fff; border-radius: 16px; border: 1px dashed #ccc;">
            <div style="font-size: 40px; margin-bottom: 15px;">🛒</div>
            <h5 style="color: #333; margin: 0 0 10px 0;">Belum ada pesanan</h5>
            <p style="color: #888; font-size: 14px; margin-bottom: 20px;">Yuk, mulai pesan kopi favoritmu sekarang!</p>
            <a href="<?= $base_url ?>index.php?controller=menu&action=index" style="background: #6F4E37; color: white; padding: 10px 25px; border-radius: 30px; text-decoration: none; font-weight: bold; font-size: 14px;">Lihat Menu</a>
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
                    <div class="badge-status <?= $status_class ?>">
                        <?= $r['status'] ?>
                    </div>
                </div>

                <div class="history-body">
                    <div>
                        <p style="margin: 0; font-size: 13px; color: #888;">Total Belanja</p>
                        <p class="history-price">Rp <?= number_format($r['total_transaksi'], 0, ',', '.') ?></p>
                    </div>

                    <div class="history-actions">
                        <?php if ($r['status'] == 'Selesai' && empty($r['komentar'])): ?>
                            <span class="label-ulas" style="cursor: default; display: inline-flex; align-items: center; gap: 5px;">
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
                                style="display: inline-block; padding: 10px 15px; background: #fee2e2; color: #b91c1c; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: bold; margin-right: 5px; border: 1px solid #fecaca;">
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