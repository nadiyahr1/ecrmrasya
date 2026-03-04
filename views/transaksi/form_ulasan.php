<?php include __DIR__ . '/../../layout/header.php'; ?>

<div style="max-width: 500px; margin: 40px auto; padding: 25px; background: white; border-radius: 15px;">
    
    <h3>Ulasan Pesanan #<?= $id_p ?></h3>
    <p><?= date('d M Y', strtotime($p['tgl_pesanan'])) ?></p>

    <hr>

    <!-- MENU -->
    <?php foreach($detail_menu as $dm): ?>
        <div style="display:flex; gap:10px;">
            <img src="assets/gambar/menu/<?= $dm['foto'] ?>" width="60">
            <div><?= $dm['nama_menu'] ?></div>
        </div>
    <?php endforeach; ?>

    <!-- FASILITAS -->
    <?php foreach($detail_fas as $df): ?>
        <div style="display:flex; gap:10px;">
            <img src="assets/gambar/fasilitas/<?= $df['foto_fasilitas'] ?>" width="60">
            <div><?= $df['nama_fasilitas'] ?></div>
        </div>
    <?php endforeach; ?>

    <form action="index.php?controller=checkout&action=simpanUlasan" method="POST">
        <input type="hidden" name="id_pesanan" value="<?= $id_p ?>">

        <textarea name="komentar" required style="width:100%; height:120px;"></textarea>

        <button type="submit">Kirim Ulasan</button>
    </form>

</div>

<?php include __DIR__ . '/../../layout/footer.php'; ?>