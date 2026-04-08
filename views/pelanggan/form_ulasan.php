<?php include __DIR__ . '/../../layout/header.php'; ?>
<link rel="stylesheet" href="assets/css/pelanggan-order.css">

<div class="form-ulasan-container">
    
    <h3 class="ulasan-header-title">Ulasan Pesanan #<?= $id_p ?></h3>
    <p class="ulasan-header-date"><?= date('d M Y', strtotime($p['tgl_pesanan'])) ?></p>

    <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 20px;">

    <?php foreach($detail_menu as $dm): ?>
        <div class="ulasan-item-row">
            <img src="assets/gambar/menu/<?= $dm['foto'] ?>" class="ulasan-item-img">
            <div class="ulasan-item-name"><?= $dm['nama_menu'] ?></div>
        </div>
    <?php endforeach; ?>

    <?php foreach($detail_fas as $df): ?>
        <div class="ulasan-item-row">
            <img src="assets/gambar/fasilitas/<?= $df['foto_fasilitas'] ?>" class="ulasan-item-img">
            <div class="ulasan-item-name"><?= $df['nama_fasilitas'] ?></div>
        </div>
    <?php endforeach; ?>

    <form action="index.php?controller=checkout&action=simpanUlasan" method="POST">
        <input type="hidden" name="id_pesanan" value="<?= $id_p ?>">
        <textarea name="komentar" class="ulasan-textarea" placeholder="Bagaimana rasa kopi dan pelayanannya? Ceritakan pengalamanmu di sini..." required></textarea>
        <button type="submit" class="btn-submit-ulasan">Kirim Ulasan</button>
    </form>
</div>

<?php include __DIR__ . '/../../layout/footer.php'; ?>