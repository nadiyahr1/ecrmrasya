<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="detail-container">
    <a href="javascript:history.back()" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>

    <div class="detail-card">
        <div class="detail-img-wrapper">
            <img src="<?= $base_url ?>assets/gambar/menu/<?= htmlspecialchars($menu['foto']) ?>" alt="<?= htmlspecialchars($menu['nama_menu']) ?>">
        </div>

        <div class="detail-info">
            <div>
                <span class="badge-status-detail" style="background: <?= $menu['status_menu'] == 'Tersedia' ? '#27ae60' : '#e74c3c' ?>;">
                    <?= htmlspecialchars($menu['status_menu']) ?>
                </span>
                <span class="badge-kategori-detail" style="margin-left: 5px;">
                    <?= htmlspecialchars($menu['nama_kategori']) ?>
                </span>
            </div>

            <h1 class="detail-title"><?= htmlspecialchars($menu['nama_menu']) ?></h1>
            <div class="detail-price">Rp <?= number_format($menu['harga'], 0, ',', '.') ?></div>
            <p class="detail-desc">
                <?= nl2br(htmlspecialchars($menu['deskripsi_menu'])) ?>
            </p>

            <?php if ($menu['status_menu'] == 'Tersedia'): ?>
                <form action="index.php?controller=keranjang&action=tambah" method="POST" style="margin: 0;">
                    <input type="hidden" name="id_menu" value="<?= $menu['id_menu'] ?>">
                    
                    <div class="detail-qty-wrapper">
                        <label class="form-label" style="margin-bottom: 0;">Jumlah:</label>
                        <input type="number" name="jumlah" value="1" min="1" max="<?= $menu['stok'] ?>" class="detail-qty-input">
                        <span class="detail-stok-info">(Stok tersedia: <?= $menu['stok'] ?>)</span>
                    </div>

                    <button type="submit" class="btn-action-detail btn-add-detail">
                        <i class="fa-solid fa-cart-plus"></i> Masukkan ke Keranjang
                    </button>
                </form>
            <?php else: ?>
                <div style="margin-bottom: 20px;">
                    <span class="detail-stok-info" style="color: #e74c3c; font-weight: bold;">Maaf, stok sedang habis.</span>
                </div>
                <button disabled class="btn-action-detail btn-disabled-detail">
                    <i class="fa-solid fa-ban"></i> Stok Habis
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="reviews-section">
        <h2 class="reviews-title">Ulasan Pelanggan</h2>
        <?php
        if (!empty($ulasan)) {
            foreach ($ulasan as $ul):
        ?>
                <div class="review-item">
                    <div class="review-header">
                        <div class="review-avatar-menu">
                            <?= htmlspecialchars(substr($ul['nama_member'], 0, 1)) ?>
                        </div>
                        <div>
                            <span class="reviewer-name"><?= htmlspecialchars($ul['nama_member']) ?></span>
                            <span class="review-date"><?= date('d M Y - H:i', strtotime($ul['tgl_ulasan'])) ?> WIB</span>
                        </div>
                    </div>
                    <p class="review-text-menu">"<?= htmlspecialchars($ul['komentar']) ?>"</p>
                </div>
        <?php
            endforeach;
        } else {
            echo "<p class='empty-review-menu'>Belum ada ulasan untuk menu ini. Jadilah yang pertama memberikan ulasan setelah memesan!</p>";
        }
        ?>
    </div>
</div>
