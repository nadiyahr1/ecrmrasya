<?php

require_once 'config/koneksi.php';

// AMBIL DATA
$kategori = $conn->query("SELECT * FROM tb_kategori")->fetchAll();

$menus = $conn->query("
    SELECT m.*, k.nama_kategori 
    FROM tb_menu m 
    JOIN tb_kategori k ON m.id_kategori = k.id_kategori 
    ORDER BY m.id_menu DESC
")->fetchAll();

$fasilitas = $conn->query("SELECT * FROM tb_fasilitas LIMIT 3")->fetchAll();

$promos = $conn->query("
    SELECT * FROM tb_promo 
    WHERE tipe_promo = 'Loyalty' 
    LIMIT 3
")->fetchAll();

try {
    $ulasan = $conn->query("
        SELECT u.komentar, u.tgl_ulasan, m.nama_member 
        FROM tb_ulasan u 
        JOIN tb_pesanan p ON u.id_pesanan = p.id_pesanan 
        JOIN tb_member m ON p.id_member = m.id_member 
        WHERE u.status_tampil = 'Y'
        ORDER BY u.id_ulasan DESC 
        LIMIT 5
    ")->fetchAll();
} catch (Exception $e) {
    $ulasan = [];
}

include 'layout/header.php';
?>

<style>
    body {
        padding-top: 65px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #FAFAFA;
        color: #333;
        margin: 0;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .section-title {
        text-align: center;
        font-size: 32px;
        color: #333;
        margin-bottom: 40px;
        font-weight: bold;
    }

    .menu-action-group {
        display: flex;
        gap: 10px;
        width: 100%;
        justify-content: center;
        margin-top: 15px;
    }

    .btn-menu-action {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: bold;
        text-decoration: none;
        transition: 0.3s;
        cursor: pointer;
        border: none;
    }

    .btn-detail {
        background: #f4f4f4;
        color: #333;
    }

    .btn-detail:hover {
        background: #e0e0e0;
    }

    .btn-add-cart {
        background: #6F4E37;
        color: white;
    }

    .btn-add-cart:hover {
        background: #5a3d2b;
    }

    .btn-add-cart:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .btn-primary {
        background: #6F4E37;
        color: white;
        padding: 12px 30px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: bold;
        display: inline-block;
        transition: 0.3s;
        border: 2px solid #6F4E37;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(111, 78, 55, 0.2);
    }

    .btn-primary:hover {
        background: #5a3d2b;
        border-color: #5a3d2b;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(111, 78, 55, 0.3);
    }

    .hero {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/gambar/banner1.jpeg') center/cover no-repeat;
        color: white;
        padding: 120px 20px;
        text-align: left;
        border-radius: 0 0 30px 30px;
        margin-bottom: 60px;
    }

    .hero h1 {
        font-size: 48px;
        max-width: 600px;
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .about-section {
        display: flex;
        gap: 40px;
        align-items: center;
        margin-bottom: 80px;
    }

    .about-content {
        flex: 1;
    }

    .about-image {
        flex: 1;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        height: 400px;
    }

    .about-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
        justify-content: center;
    }

    .card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        transition: 0.3s;
        border: 1px solid #eee;
        display: flex;
        flex-direction: column;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .card-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        background: #eee;
    }

    .card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .card-title {
        font-size: 20px;
        margin: 0 0 10px 0;
        color: #333;
    }

    .card.menu-item {
        max-width: 380px;
        width: 100%;
        margin: 0 auto;
    }

    .price {
        font-size: 18px;
        font-weight: bold;
        color: #6F4E37;
    }

    .category-pills {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }

    .pill {
        padding: 8px 20px;
        background: white;
        border-radius: 30px;
        font-weight: bold;
        color: #555;
        text-decoration: none;
        border: 1px solid #ddd;
        transition: 0.3s;
        cursor: pointer;
        font-size: 15px;
    }

    .pill.active,
    .pill:hover {
        background: #6F4E37;
        color: white;
        border-color: #6F4E37;
    }

    .review-scroll {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        padding-bottom: 20px;
        scroll-snap-type: x mandatory;
    }

    .review-card {
        min-width: 300px;
        max-width: 350px;
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        scroll-snap-align: start;
        border-left: 5px solid #6F4E37;
    }

    .contact-section {
        background: #6F4E37;
        color: white;
        padding: 60px 20px;
        border-radius: 30px;
        margin-bottom: 60px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .about-section {
            flex-direction: column;
        }

        .hero h1 {
            font-size: 32px;
        }
    }
</style>


<div class="hero">
    <div class="container">
        <h1>Rasakan Kehangatan dalam Setiap Cerita. <br> Pesan Sekarang!</h1>
        <p style="font-size: 18px; margin-bottom: 30px; max-width: 500px;">
            Nikmati sajian kopi terbaik dan suasana nyaman hanya di Rasya.co.
        </p>
        <a href="#our-menu" class="btn-primary" style="font-size: 18px; padding: 15px 35px;">
            Pesan Sekarang
        </a>
    </div>
</div>

<div class="container">

    <!-- ABOUT -->
    <div class="about-section" id="about">
        <div class="about-image">
            <img src="assets/gambar/banner1.jpeg">
        </div>
        <div class="about-content">
            <h2 class="section-title" style="text-align:left;">About Us</h2>
            <p>
                Rasya.co adalah tempat terbaik untuk berkumpul...
            </p>
        </div>
    </div>

    <!-- MENU -->
    <div id="our-menu">
        <h2 class="section-title">Our Menu</h2>

        <div class="category-pills">
            <button class="pill active" data-filter="all">All Menu</button>
            <?php foreach ($kategori as $k): ?>
                <button class="pill" data-filter="<?= $k['nama_kategori'] ?>">
                    <?= $k['nama_kategori'] ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="grid-container">
            <?php foreach ($menus as $m):
                $status = ($m['stok'] <= 0) ? 'Habis' : $m['status_menu'];
            ?>
                <div class="card menu-item">
                    <img src="assets/gambar/menu/<?= $m['foto'] ?>" class="card-img">

                    <div class="card-body">
                        <h3><?= $m['nama_menu'] ?></h3>
                        <span class="price">Rp <?= number_format($m['harga']) ?></span>

                        <div class="menu-action-group">

                            <!-- DETAIL -->
                            <a href="detail_menu.php?id=<?= $m['id_menu'] ?>" class="btn-menu-action btn-detail">
                                Detail
                            </a>

                            <!-- TAMBAH KE KERANJANG (SUDAH MVC) -->
                            <?php if ($status == 'Tersedia'): ?>
                                <form action="index.php?controller=keranjang&action=tambah" method="POST">
                                    <input type="hidden" name="id_menu" value="<?= $m['id_menu'] ?>">
                                    <button type="submit" class="btn-menu-action btn-add-cart">
                                        Pesan
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled class="btn-menu-action btn-add-cart">Habis</button>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- FASILITAS -->
    <div id="our-facilities">
        <h2 class="section-title">Our Facilities</h2>

        <div class="grid-container">
            <?php foreach ($fasilitas as $f): ?>
                <div class="card">
                    <img src="assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>">
                    <div class="card-body">
                        <h3><?= $f['nama_fasilitas'] ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- PROMO -->
    <div id="promo">
        <h2 class="section-title">Promo</h2>

        <div class="grid-container">
            <?php foreach ($promos as $p): ?>
                <div class="card">
                    <div class="card-body">
                        <h3><?= $p['nama_promo'] ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ULASAN -->
    <div id="ulasan">
        <h2 class="section-title">Ulasan</h2>

        <?php foreach ($ulasan as $u): ?>
            <div class="card">
                <p>"<?= $u['komentar'] ?>"</p>
                <small><?= $u['nama_member'] ?></small>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php include __DIR__ . '/../../layout/footer.php'; ?>