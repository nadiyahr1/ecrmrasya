<?php include 'layout/header.php'; ?>

<div class="detail-container">
    <a href="javascript:history.back()" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>

    <div class="detail-card">
        <div class="detail-img-wrapper">
            <img src="assets/gambar/fasilitas/<?= htmlspecialchars($fasilitas['foto_fasilitas']) ?>" alt="<?= htmlspecialchars($fasilitas['nama_fasilitas']) ?>">
        </div>

        <div class="detail-info">
            <span class="badge-fasilitas">Fasilitas</span>
            
            <h1 class="detail-title"><?= htmlspecialchars($fasilitas['nama_fasilitas']) ?></h1>
            
            <div class="detail-price">
                Rp <?= number_format($fasilitas['biaya'] ?? $fasilitas['harga'], 0, ',', '.') ?>
                <span style="font-size: 16px; color: #888; font-weight: normal;">/ <?= htmlspecialchars($fasilitas['satuan']) ?></span>
            </div>

            <p class="detail-desc">
                <?= nl2br(htmlspecialchars($fasilitas['deskripsi'])) ?>
            </p>

            <a href="index.php?controller=fasilitas&action=booking&id=<?= $fasilitas['id_fasilitas'] ?>" class="btn-action-detail btn-add-detail">
                <i class="fa-regular fa-calendar-check"></i> Booking Fasilitas Sekarang
            </a>
        </div>
    </div>

    <div class="reviews-section">
        <h2 class="reviews-title">Ulasan Pelanggan</h2>
        <?php if (!empty($ulasan)): ?>
            <div style="margin-top: 20px;">
                <?php foreach ($ulasan as $ul): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <div class="review-avatar-fas">
                                <?= htmlspecialchars(substr($ul['nama_member'], 0, 1)) ?>
                            </div>
                            <div>
                                <span class="reviewer-name"><?= htmlspecialchars($ul['nama_member']) ?></span>
                                <span class="review-date"><i class="fa-regular fa-clock"></i> <?= date('d M Y - H:i', strtotime($ul['tgl_ulasan'])) ?> WIB</span>
                            </div>
                        </div>
                        <p class="review-text-fas">
                            "<?= nl2br(htmlspecialchars($ul['komentar'])) ?>"
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-review-fas">
                <i class="fa-regular fa-face-smile" style="font-size: 30px; margin-bottom: 10px; color: #ccc;"></i>
                <p style="margin: 0; font-style: italic;">Belum ada ulasan untuk fasilitas ini. Jadilah yang pertama memberikan ulasan setelah menggunakan fasilitas kami!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'layout/footer.php'; ?>