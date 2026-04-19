<?php include 'layout/header.php'; ?>

<div class="page-container">
    <h1 class="page-title">Menu Spesial Rasya.co</h1>
    <p class="page-subtitle">Temukan sajian favorit untuk melengkapi harimu.</p>

    <div class="category-pills" id="menu-filters">
        <button class="pill active" data-filter="all">All Menu</button>
        <?php foreach ($kategori as $k): ?>
            <button class="pill" data-filter="<?= htmlspecialchars($k['nama_kategori']) ?>">
                <?= htmlspecialchars($k['nama_kategori']) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="menu-grid-container" id="menu-grid">
        <?php if (count($menus) > 0): ?>
            <?php foreach ($menus as $m): ?>
                <?php $warna_status = ($m['status_menu'] == 'Tersedia') ? '#28a745' : '#dc3545'; ?>

                <div class="menu-card menu-item" data-category="<?= htmlspecialchars($m['nama_kategori']) ?>">
                    <div class="menu-img-wrapper">
                        <img src="<?= $base_url ?>assets/gambar/menu/<?= htmlspecialchars($m['foto']) ?>" class="menu-card-img">
                        <span class="badge-category">
                            <?= htmlspecialchars($m['nama_kategori']) ?>
                        </span>
                        <span class="badge-status" style="background: <?= $warna_status ?>;">
                            <?= htmlspecialchars($m['status_menu']) ?>
                        </span>
                    </div>
                    


                    <div class="menu-card-body">
                        <h3 class="menu-card-title"><?= htmlspecialchars($m['nama_menu']) ?></h3>
                        <span class="menu-price">Rp <?= number_format($m['harga']) ?></span>

                        <div class="menu-action-group">
                            <a href="index.php?controller=menu&action=detailMenu&id=<?= $m['id_menu'] ?>" class="btn-menu-action btn-detail">
                                <i class="fa-solid fa-circle-info"></i> Detail
                            </a>

                            <?php if ($m['status_menu'] == 'Tersedia'): ?>
                                <form action="<?= $base_url ?>index.php?controller=keranjang&action=tambah" method="POST" style="flex: 1; margin: 0;">
                                    <input type="hidden" name="id_menu" value="<?= $m['id_menu'] ?>">
                                    <input type="hidden" name="jumlah" value="1">
                                    <button type="submit" class="btn-menu-action btn-add-cart" style="width: 100%;">
                                        <i class="fa-solid fa-cart-plus"></i> Pesan
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled class="btn-menu-action btn-add-cart" style="flex: 1;">
                                    <i class="fa-solid fa-ban"></i> Habis
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style='text-align:center; width:100%; color:#888; grid-column: 1 / -1;'>Belum ada menu yang ditambahkan.</p>
        <?php endif; ?>
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
                const category = this.dataset.filter;
                filterMenu(category);
            });
        });
    });
</script>

<?php require_once 'layout/footer.php'; ?>