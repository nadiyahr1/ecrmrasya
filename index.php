<?php
// Memanggil header yang berisi Navbar Dinamis
include 'layout/header.php';
require_once 'config/koneksi.php';

// Ambil data menu untuk ditampilkan di Landing Page
$query_menu = "SELECT m.*, k.nama_kategori FROM tb_menu m 
               JOIN tb_kategori k ON m.id_kategori = k.id_kategori 
               WHERE m.status_menu = 'Tersedia' LIMIT 6";
$menus = $conn->query($query_menu)->fetchAll();
?>

<div style="padding: 20px;">
    <header style="text-align: center; margin-bottom: 40px;">
        <h1>Selamat Datang di Rasya.co</h1>
        <p>Nikmati suasana cafe terbaik dengan program loyalitas istimewa.</p>
    </header>

    <section>
        <h2>Menu Favorit Kami</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            <?php foreach ($menus as $m) : ?>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <div style="height: 150px; background: #eee; margin-bottom: 10px; display: flex; align-items: center; justify-content: center;">
                    [Gambar Menu]
                </div>
                <small><?= $m['nama_kategori']; ?></small>
                <h3><?= $m['nama_menu']; ?></h3>
                <p style="color: #e44d26; font-weight: bold;">Rp <?= number_format($m['harga'], 0, ',', '.'); ?></p>
                
                <form action="tambah_keranjang.php" method="POST">
                    <input type="hidden" name="id_menu" value="<?= $m['id_menu']; ?>">
                    <button type="submit" style="width: 100%; padding: 10px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        🛒 Tambahkan ke Keranjang
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php include 'layout/footer.php'; ?>