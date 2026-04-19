<div class="hero">
    <div class="container">
        <h1>Rasakan Kehangatan dalam Setiap Cerita. <br> Pesan Sekarang!</h1>
        <p class="hero-subtitle">
            Nikmati sajian kopi terbaik dan suasana nyaman hanya di Rasya.co.
        </p>
        <a href="#our-menu" class="btn-primary btn-primary-large">
            Pesan Sekarang
        </a>
    </div>
</div>

<div class="container">

    <div class="about-section" id="about">
        <div class="about-image">
            <img src="assets/gambar/banner1.jpeg">
        </div>
        <div class="about-content">
            <h2 class="section-title text-left">About Us</h2>
            <p>
                Rasya.co adalah tempat terbaik untuk berkumpul dengan keluarga, teman, maupun pasangan. Menyajikan kopi pilihan yang diracik oleh barista profesional dengan suasana cafe yang hangat dan instagramable.
            </p>
        </div>
    </div>

    <div id="our-menu">
        <h2 class="section-title">Our Menu
            <div class="line-style"></div>
        </h2>

        <div class="category-pills" id="menu-filters">
            <button class="pill active" data-filter="all">All Menu</button>
            <?php foreach ($kategori as $k): ?>
                <button class="pill" data-filter="<?= htmlspecialchars($k['nama_kategori']) ?>">
                    <?= htmlspecialchars($k['nama_kategori']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="grid-container menu-grid-custom" id="menu-grid">
            <?php foreach ($menus as $m): ?>
                <?php $warna_status = ($m['status_menu'] == 'Tersedia') ? '#28a745' : '#dc3545'; ?>

                <div class="card menu-item" data-category="<?= htmlspecialchars($m['nama_kategori']) ?>">
                    <div class="menu-image-wrapper">
                        <img src="<?= $base_url ?>assets/gambar/menu/<?= htmlspecialchars($m['foto']) ?>" alt="<?= htmlspecialchars($m['nama_menu']) ?>">
                        <span class="menu-category-badge">
                            <?= htmlspecialchars($m['nama_kategori']) ?>
                        </span>
                        <span class="menu-status-badge" style="background: <?= $warna_status ?>;">
                            <?= htmlspecialchars($m['status_menu']) ?></span>
                    </div>

                    <div class="menu-info">
                        <h3 class="card-title"><?= htmlspecialchars($m['nama_menu']) ?></h3>
                        <span class="price">Rp <?= number_format($m['harga']) ?></span>

                        <div class="menu-action-group">
                            <a href="index.php?controller=menu&action=detailMenu&id=<?= $m['id_menu'] ?>" class="btn-menu-action btn-detail">
                                <i class="fa-solid fa-circle-info"></i> Detail
                            </a>

                            <?php if ($m['status_menu'] == 'Tersedia'): ?>
                                <form action="index.php?controller=keranjang&action=tambah" method="POST" class="form-add-cart">
                                    <input type="hidden" name="id_menu" value="<?= $m['id_menu'] ?>">
                                    <input type="hidden" name="jumlah" value="1">
                                    <button type="submit" class="btn-menu-action btn-add-cart">
                                        <i class="fa-solid fa-cart-plus"></i> Pesan
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled class="btn-menu-action btn-add-cart btn-habis">
                                    <i class="fa-solid fa-ban"></i> Habis
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-30">
            <a href="index.php?controller=menu&action=index" class="btn-selengkapnya">Selengkapnya</a>
        </div>
    </div>

    <div id="our-facilities" class="mb-60">
        <h2 class="section-title">Our Facilities
            <div class="line-style"></div>
        </h2>

        <div class="grid-container facilities-grid">
            <?php foreach ($fasilitas as $f): ?>
                <div class="facility-card">
                    <div class="relative-wrapper">
                        <img src="<?= $base_url ?>assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>" alt="<?= htmlspecialchars($f['nama_fasilitas']) ?>" class="facility-img">
                        <div class="facility-price-badge">
                            Rp <?= number_format($f['biaya'] ?? $f['harga']) ?> / <?= $f['satuan'] ?>
                        </div>
                    </div>

                    <div class="facility-card-body">
                        <h3 class="facility-title"><?= htmlspecialchars($f['nama_fasilitas']) ?></h3>
                        <div class="facility-desc-wrapper">
                            <p class="facility-desc">
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

        <div class="text-center mt-30">
            <a href="index.php?controller=fasilitas&action=index" class="btn-selengkapnya">Selengkapnya</a>
        </div>
    </div>

    <div id="promo" class="mb-80">
        <h2 class="section-title">Promo Menarik
            <div class="line-style"></div>
        </h2>

        <div class="promo-wrapper">
            <?php if (!empty($promos)): ?>
                <?php foreach ($promos as $p): ?>
                    <div class="promo-card" onclick="window.location.href='index.php?controller=promo&action=detailPromo&id=<?= $p['id_promo'] ?>'">
                        <img src="assets/gambar/promo/<?= $p['foto_promo'] ?>" class="promo-img" alt="<?= htmlspecialchars($p['nama_promo']) ?>">
                        <div class="promo-overlay">
                            <span class="btn-lihat-promo">Lihat Detail</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="promo-empty">
                    <p>Belum ada promo yang tersedia saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
        <p class="ulasan-hint">
            <i class="fas fa-arrows-left-right"></i> Geser untuk melihat promo lebih banyak
        </p>
    </div>

    <div id="ulasan" class="ulasan-section">
        <div class="container">
            <h2 class="section-title">Apa Kata Mereka?</h2>
            <div class="ulasan-wrapper">
                <?php foreach ($ulasan as $u): ?>
                    <div class="ulasan-card">
                        <p class="ulasan-text">"<?= htmlspecialchars($u['komentar']) ?>"</p>
                        <div class="ulasan-name">- <?= htmlspecialchars($u['nama_member']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="ulasan-hint">
                <i class="fas fa-arrows-left-right"></i> Geser untuk melihat lebih banyak
            </p>
        </div>
    </div>

    <div id="contact" class="container">
        <div class="contact-container">
            <div class="contact-info-centered">
                <h2 class="section-title">Hubungi Kami</h2>
                <p class="contact-desc text-center">Punya pertanyaan mengenai menu atau fasilitas kami? Jangan ragu untuk menghubungi kami melalui kontak di bawah ini.</p>

                <div class="contact-grid">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div class="info-text">
                            <h4>Lokasi</h4>
                            <p>Jl. Contoh No. 123, Kisaran Barat,<br>Sumatera Utara</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-phone-alt"></i>
                        <div class="info-text">
                            <h4>Telepon / WA</h4>
                            <p>+62 812-3456-7890</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div class="info-text">
                            <h4>Email Support</h4>
                            <p>info@rasyacafe.com</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div class="info-text">
                            <h4>Jam Operasional</h4>
                            <p>Senin - Minggu:<br>10.00 - 22.00 WIB</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('#menu-filters .pill');
        const menuItems = document.querySelectorAll('.menu-item');

        function filterMenu(category) {
            menuItems.forEach(item => {
                item.style.display = 'none';
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'flex';
                }
            });
        }

        filterMenu('all');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filterMenu(this.dataset.filter);
            });
        });
    });
</script>

<?php include __DIR__ . '/../../layout/footer.php'; ?>