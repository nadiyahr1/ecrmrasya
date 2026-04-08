<div class="page-container">
    <div class="fas-header">
        <h2 class="page-title">Fasilitas Rasya Cafe</h2>
        <p class="page-subtitle">Nikmati berbagai fasilitas unggulan kami untuk pengalaman yang tak terlupakan.</p>
    </div>

    <div class="fas-grid">
        <?php foreach ($fasilitas as $f): ?>
            <div class="fas-card">
                <div class="fas-img-wrapper">
                    <img src="<?= $base_url ?? '' ?>assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>" alt="<?= htmlspecialchars($f['nama_fasilitas']) ?>" class="fas-img">
                    <div class="fas-price-badge">
                        Rp <?= number_format($f['biaya'] ?? $f['harga']) ?> / <?= $f['satuan'] ?>
                    </div>
                </div>

                <div class="fas-body">
                    <h3 class="fas-name"><?= htmlspecialchars($f['nama_fasilitas']) ?></h3>
                    
                    <div class="fas-desc-wrapper">
                        <p class="fas-desc">
                            <?= htmlspecialchars($f['deskripsi_fasilitas'] ?? $f['deskripsi']) ?>
                        </p>
                    </div>

                    <div class="fas-action-group">
                        <a href="index.php?controller=fasilitas&action=detailFasilitas&id=<?= $f['id_fasilitas'] ?>" class="btn-fas btn-fas-detail">
                            <i class="fa-solid fa-circle-info"></i> Detail
                        </a>
                        <a href="index.php?controller=fasilitas&action=booking&id=<?= $f['id_fasilitas'] ?>" class="btn-fas btn-fas-book">
                            <i class="fa-solid fa-calendar-check"></i> Booking
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>