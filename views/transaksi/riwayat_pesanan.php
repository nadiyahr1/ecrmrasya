<?php include __DIR__ . '/../../layout/header.php'; ?>

<div style="padding: 40px; background: #fdfdfd; min-height: 80vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div style="max-width: 1000px; margin: 0 auto;">

        <h2 style="color: #6F4E37; margin-bottom: 5px;">Riwayat Pesanan Anda</h2>
        <p style="color: #888; margin-bottom: 30px;">Pantau status pesanan Anda di sini.</p>

        <?php if (empty($riwayat)) : ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 15px;">
                <p style="margin-top: 20px; color: #666;">Anda belum memiliki riwayat pesanan.</p>
                <a href="index.php" style="display: inline-block; margin-top: 10px; padding: 10px 20px; background: #6F4E37; color: white; text-decoration: none; border-radius: 5px;">
                    Mulai Pesan
                </a>
            </div>
        <?php else : ?>

            <div style="display: flex; flex-direction: column; gap: 20px;">
                <?php foreach ($riwayat as $r) : ?>
                    <?php
                    // warna status
                    if ($r['status'] == 'Menunggu Konfirmasi') $warna = '#ffc107';
                    elseif ($r['status'] == 'Diproses' || $r['status'] == 'Dapat Diambil') $warna = '#17a2b8';
                    elseif ($r['status'] == 'Selesai') $warna = '#28a745';
                    else $warna = '#dc3545';
                    ?>
                    <div style="background: white; border-radius: 15px; padding: 25px; border-left: 8px solid <?= $warna ?>;">

                        <!-- HEADER -->
                        <div style="display: flex; justify-content: space-between;">

                            <div>
                                <small style="color:#888;">
                                    <?= date('d M Y, H:i', strtotime($r['tgl_pesanan'])) ?>
                                </small>

                                <h4 style="margin:5px 0;">
                                    <?= $r['id_pesanan'] ?>
                                </h4>

                                <span style="color:#666;">
                                    <?= $r['tipe_pemesanan'] ?>
                                    <?= ($r['id_meja']) ? " (Meja " . $r['id_meja'] . ")" : "" ?>
                                </span>
                            </div>

                            <div style="text-align:right;">
                                <div style="background:<?= $warna ?>; color:white; padding:5px 15px; border-radius:20px;">
                                    <?= $r['status'] ?>
                                </div>

                                <h3 style="margin-top:10px; color:#6F4E37;">
                                    Rp <?= number_format($r['total_transaksi']) ?>
                                </h3>
                            </div>

                        </div>

                        <hr>

                        <!-- FOOTER -->
                        <div style="display:flex; justify-content:space-between; align-items:center;">

                            <div style="color:#666;">
                                <strong>Metode:</strong> <?= $r['metode_pembayaran'] ?>

                                <?php if (!empty($r['catatan'])): ?>
                                    <br>
                                    <small><em><?= $r['catatan'] ?></em></small>
                                <?php endif; ?>
                            </div>

                            <a href="index.php?controller=checkout&action=detail&id=<?= $r['id_pesanan'] ?>"
                                style="color:#6F4E37; font-weight:bold;">
                                Lihat Detail →
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../../layout/footer.php'; ?>