<?php include __DIR__ . '/../../layout/header.php'; ?>

<div style="padding: 40px; background: #fdfdfd; min-height: 80vh;">
    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px;">

        <a href="index.php?controller=checkout&action=riwayat" style="color:#6F4E37;">← Kembali</a>

        <div style="display:flex; justify-content:space-between; margin-top:20px;">
            <h2>Detail Pesanan #<?= $p['id_pesanan'] ?></h2>
            <span><?= $p['status'] ?></span>
        </div>

        <p><?= date('d F Y, H:i', strtotime($p['tgl_pesanan'])) ?></p>

        <hr>

        <h4>Item Pesanan</h4>

        <!-- MENU -->
        <?php foreach($detail_menu as $dm): ?>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <img src="assets/gambar/menu/<?= $dm['foto'] ?>" width="60">
                <div style="flex:1;">
                    <?= $dm['nama_menu'] ?><br>
                    <?= $dm['jumlah'] ?> x Rp <?= number_format($dm['subtotal']/$dm['jumlah']) ?>
                </div>
                <strong>Rp <?= number_format($dm['subtotal']) ?></strong>
            </div>
        <?php endforeach; ?>

        <!-- FASILITAS -->
        <?php foreach($detail_fas as $df): ?>
            <div style="display:flex; gap:10px; margin-bottom:10px; background:#fffcf5;">
                <img src="assets/gambar/fasilitas/<?= $df['foto_fasilitas'] ?>" width="60">
                <div style="flex:1;">
                    [Fasilitas] <?= $df['nama_fasilitas'] ?><br>
                    <?= $df['tgl_sewa'] ?? '-' ?>
                </div>
                <strong>Rp <?= number_format($df['subtotal'] ?? 0) ?></strong>
            </div>
        <?php endforeach; ?>

        <hr>

        <div style="display:flex; justify-content:space-between;">
            <div>
                Metode: <?= $p['metode_pembayaran'] ?><br>
                Tipe: <?= $p['tipe_pemesanan'] ?>
            </div>

            <div style="text-align:right;">
                Subtotal: Rp <?= number_format($subtotal_produk) ?><br>
                Pajak: Rp <?= number_format($pajak_layanan) ?><br>

                <?php if($diskon > 0): ?>
                    Diskon: -Rp <?= number_format($diskon) ?><br>
                <?php endif; ?>

                <h3>Total: Rp <?= number_format($p['total_transaksi']) ?></h3>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../../layout/footer.php'; ?>