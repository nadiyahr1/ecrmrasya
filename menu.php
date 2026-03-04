<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/koneksi.php';
include 'layout/header.php'; 
?>

<style>
    /* Styling Dasar disesuaikan dengan tinggi fixed navbar */
    body { 
        padding-top: 65px; 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        background-color: #FAFAFA; 
        color: #333; 
        margin: 0; 
    }
    .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; min-height: 80vh; }
    .page-title { text-align: center; font-size: 36px; color: #333; margin-bottom: 10px; font-weight: bold; }
    .page-subtitle { text-align: center; color: #666; margin-bottom: 40px; font-size: 16px; }

    /* Categories Pill */
    .category-pills { display: flex; justify-content: center; gap: 15px; margin-bottom: 40px; flex-wrap: wrap; }
    .pill { padding: 8px 20px; background: white; border-radius: 30px; font-weight: bold; color: #555; text-decoration: none; border: 1px solid #ddd; transition: 0.3s; cursor: pointer; font-size: 15px; }
    .pill.active, .pill:hover { background: #6F4E37; color: white; border-color: #6F4E37; }

    /* Grid Container (Menggunakan auto-fill agar konsisten) */
    .grid-container { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
        gap: 30px; 
        margin-bottom: 40px; 
        justify-content: center; 
    }

    /* Card Menu Styling */
    .card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: 0.3s; border: 1px solid #eee; display: flex; flex-direction: column; max-width: 380px; width: 100%; margin: 0 auto; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .card-img { width: 100%; height: 220px; object-fit: cover; background: #eee; }
    .card-body { padding: 20px; flex: 1; display: flex; flex-direction: column; }
    .card-title { font-size: 20px; margin: 0 0 5px 0; color: #333; }
    .price { font-size: 18px; font-weight: bold; color: #6F4E37; display: block; margin-bottom: 15px; }

    /* Action Buttons (Detail & Keranjang) */
    .menu-action-group { display: flex; gap: 10px; width: 100%; justify-content: center; margin-top: auto; }
    .btn-menu-action { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; border-radius: 12px; font-size: 13px; font-weight: bold; text-decoration: none; transition: 0.3s; cursor: pointer; border: none; }
    .btn-detail { background: #f4f4f4; color: #333; }
    .btn-detail:hover { background: #e0e0e0; }
    .btn-add-cart { background: #6F4E37; color: white; }
    .btn-add-cart:hover { background: #5a3d2b; }
    .btn-add-cart:disabled { background: #ccc; cursor: not-allowed; }
</style>

<div class="container">
    <h1 class="page-title">Menu Spesial Rasya.co</h1>
    <p class="page-subtitle">Temukan sajian favorit untuk melengkapi harimu.</p>

    <div class="category-pills" id="menu-filters">
        <button class="pill active" data-filter="all">All Menu</button>
        <?php
        $kategori = $conn->query("SELECT * FROM tb_kategori")->fetchAll();
        foreach ($kategori as $k) {
            echo '<button class="pill" data-filter="'.htmlspecialchars($k['nama_kategori']).'">'.htmlspecialchars($k['nama_kategori']).'</button>';
        }
        ?>
    </div>

    <div class="grid-container" id="menu-grid">
        <?php
        // Ambil SEMUA menu dari database
        $menus = $conn->query("SELECT m.*, k.nama_kategori FROM tb_menu m JOIN tb_kategori k ON m.id_kategori = k.id_kategori ORDER BY m.id_menu DESC")->fetchAll();
        
        if (count($menus) > 0) {
            foreach ($menus as $m):
                $warna_status = ($m['status_menu'] == 'Tersedia') ? '#28a745' : '#dc3545';
        ?>
            <div class="card menu-item" data-category="<?= htmlspecialchars($m['nama_kategori']) ?>">
                <div style="position: relative;">
                    <img src="assets/gambar/menu/<?= htmlspecialchars($m['foto']) ?>" class="card-img" alt="<?= htmlspecialchars($m['nama_menu']) ?>">
                    
                    <span style="position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 10px; border-radius: 10px; font-size: 12px; font-weight: bold;">
                        <?= htmlspecialchars($m['nama_kategori']) ?>
                    </span>
                    
                    <span style="position: absolute; top: 10px; right: 10px; background: <?= $warna_status ?>; color: white; padding: 4px 10px; border-radius: 10px; font-size: 12px; font-weight: bold;">
                        <?= htmlspecialchars($m['status_menu']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <h3 class="card-title"><?= htmlspecialchars($m['nama_menu']) ?></h3>
                    <span class="price">Rp <?= number_format($m['harga']) ?></span>
                    
                    <div class="menu-action-group">
                        <a href="detail_menu.php?id=<?= $m['id_menu'] ?>" class="btn-menu-action btn-detail">
                            <i class="fa-solid fa-circle-info"></i> Detail
                        </a>

                        <?php if ($m['status_menu'] == 'Tersedia'): ?>
                            <form action="tambah_keranjang.php" method="POST" style="flex: 1; margin: 0;">
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
        <?php 
            endforeach; 
        } else {
            echo "<p style='text-align:center; width:100%; color:#888; grid-column: 1 / -1;'>Belum ada menu yang ditambahkan.</p>";
        }
        ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('#menu-filters .pill');
    const menuItems = document.querySelectorAll('.menu-item');

    function filterMenu(category) {
        menuItems.forEach(item => {
            // Sembunyikan semua item
            item.style.display = 'none';
            
            // Tampilkan item yang sesuai kategori TANPA batasan jumlah
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = 'flex'; 
            }
        });
    }

    // Jalankan filter 'all' saat pertama kali dimuat
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

<?php include 'layout/footer.php'; ?>