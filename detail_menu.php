<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/koneksi.php';

// Cek apakah ada parameter ID yang dikirim melalui URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Pilih menu terlebih dahulu!'); window.location='menu.php';</script>";
    exit;
}

$id_menu = $_GET['id'];

// Ambil data menu spesifik beserta kategorinya
$stmt = $conn->prepare("SELECT m.*, k.nama_kategori FROM tb_menu m JOIN tb_kategori k ON m.id_kategori = k.id_kategori WHERE m.id_menu = ?");
$stmt->execute([$id_menu]);
$menu = $stmt->fetch();

// Jika menu tidak ditemukan di database
if (!$menu) {
    echo "<script>alert('Menu tidak ditemukan!'); window.location='menu.php';</script>";
    exit;
}

include 'layout/header.php';
?>

<style>
    body {
        padding-top: 80px;
        /* Jarak untuk fixed navbar */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #FAFAFA;
        color: #333;
        margin: 0;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
        min-height: 80vh;
    }

    /* Tombol Kembali */
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #6F4E37;
        text-decoration: none;
        font-weight: bold;
        margin-bottom: 20px;
        transition: 0.3s;
    }

    .btn-back:hover {
        color: #5a3d2b;
        transform: translateX(-5px);
    }

    /* Layout Detail Utama */
    .detail-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        display: flex;
        gap: 40px;
        padding: 30px;
        margin-bottom: 40px;
    }

    /* Bagian Gambar */
    .detail-img-wrapper {
        flex: 1;
        border-radius: 15px;
        overflow: hidden;
        background: #eee;
        display: flex;
        align-items: center;
        justify-content: center;
        max-height: 400px;
    }

    .detail-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Bagian Informasi */
    .detail-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .detail-category {
        background: #333;
        color: white;
        padding: 5px 15px;
        border-radius: 15px;
        font-size: 14px;
        font-weight: bold;
        display: inline-block;
        margin-bottom: 15px;
        width: fit-content;
    }

    .detail-title {
        font-size: 32px;
        margin: 0 0 15px 0;
        color: #333;
    }

    .detail-price {
        font-size: 28px;
        font-weight: bold;
        color: #6F4E37;
        margin-bottom: 20px;
        border-bottom: 2px solid #eee;
        padding-bottom: 20px;
    }

    .detail-desc {
        font-size: 16px;
        color: #666;
        line-height: 1.8;
        margin-bottom: 30px;
    }

    /* Form Keranjang */
    /* Container form menggunakan flex agar sejajar tengah */
    .cart-form {
        display: flex;
        align-items: flex-end;
        /* Menjajakan bagian bawah elemen agar sejajar */
        gap: 15px;
        background: #fffcf5;
        padding: 20px;
        border-radius: 15px;
        border: 1px solid #f9eed7;
    }

    /* Pastikan input dan button memiliki tinggi (height) yang sama */
    .qty-input {
        width: 70px;
        height: 48px;
        /* Kunci tinggi input */
        padding: 0;
        /* Hapus padding agar tidak merusak tinggi */
        border: 2px solid #ddd;
        border-radius: 8px;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        box-sizing: border-box;
        /* Sangat penting agar border dihitung dalam height */
    }

    .btn-submit {
        flex: 1;
        height: 48px;
        /* Samakan tinggi dengan .qty-input */
        background: #6F4E37;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-sizing: border-box;
    }

    .btn-submit:hover {
        background: #5a3d2b;
    }

    .btn-disabled {
        background: #ccc;
        cursor: not-allowed;
        flex: 1;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    /* Bagian Ulasan */
    .reviews-section {
        background: white;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        padding: 30px;
    }

    .reviews-title {
        font-size: 24px;
        margin-bottom: 20px;
        border-bottom: 2px solid #eee;
        padding-bottom: 15px;
    }

    .review-item {
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }

    .review-item:last-child {
        border-bottom: none;
    }

    .reviewer-name {
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
        display: block;
    }

    .review-date {
        font-size: 12px;
        color: #888;
        display: block;
        margin-bottom: 10px;
    }

    .review-text {
        color: #555;
        font-style: italic;
        line-height: 1.6;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .detail-card {
            flex-direction: column;
            padding: 20px;
        }

        .cart-form {
            flex-direction: column;
            align-items: stretch;
        }

        .qty-input {
            width: 100%;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
    }
</style>

<div class="container">
    <a href="javascript:history.back()" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>

    <div class="detail-card">
        <div class="detail-img-wrapper">
            <img src="assets/gambar/menu/<?= htmlspecialchars($menu['foto']) ?>" alt="<?= htmlspecialchars($menu['nama_menu']) ?>" onerror="this.src='https://via.placeholder.com/500x400?text=Foto+Menu'">
        </div>

        <div class="detail-info">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span class="detail-category"><?= htmlspecialchars($menu['nama_kategori']) ?></span>

                <?php if ($menu['status_menu'] == 'Tersedia' && $menu['stok'] > 0): ?>
                    <span style="color: #28a745; font-weight: bold;">
                        <i class="fa-solid fa-boxes-stacked"></i> Stok: <?= $menu['stok'] ?> Porsi
                    </span>
                <?php elseif ($menu['status_menu'] == 'Tersedia' && $menu['stok'] <= 0): ?>
                    <span style="color: #dc3545; font-weight: bold;">
                        <i class="fa-solid fa-circle-xmark"></i> Stok Habis
                    </span>
                <?php else: ?>
                    <span style="color: #dc3545; font-weight: bold;">
                        <i class="fa-solid fa-circle-xmark"></i> Tidak Tersedia
                    </span>
                <?php endif; ?>
            </div>

            <h1 class="detail-title"><?= htmlspecialchars($menu['nama_menu']) ?></h1>
            <div class="detail-price">Rp <?= number_format($menu['harga']) ?></div>

            <h3 style="margin-top: 0; margin-bottom: 10px; font-size: 18px;">Deskripsi Menu:</h3>
            <p class="detail-desc">
                <?= nl2br(htmlspecialchars($menu['deskripsi'] ?? 'Deskripsi detail untuk menu ini belum ditambahkan oleh admin. Namun kami pastikan rasanya tidak akan mengecewakan Anda!')) ?>
            </p>

            <?php if ($menu['status_menu'] == 'Tersedia' && $menu['stok'] > 0): ?>
                <form action="tambah_keranjang.php" method="POST" class="cart-form">
                    <input type="hidden" name="id_menu" value="<?= $menu['id_menu'] ?>">
                    <div style="display: flex; flex-direction: column; gap: 5px;">
                        <label style="font-size: 12px; font-weight: bold; color: #888;">Jumlah</label>
                        <input type="number" name="jumlah" value="1" min="1" max="50" class="qty-input" required>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang
                    </button>
                </form>
            <?php else: ?>
                <div class="cart-form">
                    <button disabled class="btn-disabled">
                        <i class="fa-solid fa-ban"></i> Maaf, Stok Sedang Habis
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="reviews-section">
        <h2 class="reviews-title">Ulasan Pelanggan</h2>

        <?php
        try {
            // Kueri ulasan: Mencari ulasan yang terkait dengan id_menu ini melalui detail pesanan
            $stmt_ulasan = $conn->prepare("
                SELECT u.komentar, u.tgl_ulasan, mem.nama_member 
                FROM tb_ulasan u 
                JOIN tb_pesanan p ON u.id_pesanan = p.id_pesanan 
                JOIN tb_detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
                JOIN tb_member mem ON p.id_member = mem.id_member
                WHERE dp.id_menu = ? AND u.status_tampil = 'Y'
                ORDER BY u.id_ulasan DESC LIMIT 10
            ");
            $stmt_ulasan->execute([$id_menu]);
            $ulasan = $stmt_ulasan->fetchAll();

            if (count($ulasan) > 0) {
                foreach ($ulasan as $ul):
        ?>
                    <div class="review-item">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 8px;">
                            <div style="width: 40px; height: 40px; background: #f4f4f4; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #6F4E37;">
                                <?= htmlspecialchars(substr($ul['nama_member'], 0, 1)) ?>
                            </div>
                            <div>
                                <span class="reviewer-name"><?= htmlspecialchars($ul['nama_member']) ?></span>
                                <span class="review-date"><?= date('d M Y - H:i', strtotime($ul['tgl_ulasan'])) ?> WIB</span>
                            </div>
                        </div>
                        <p class="review-text">"<?= htmlspecialchars($ul['komentar']) ?>"</p>
                    </div>
        <?php
                endforeach;
            } else {
                echo "<p style='color: #888; font-style: italic;'>Belum ada ulasan untuk menu ini. Jadilah yang pertama memberikan ulasan setelah memesan!</p>";
            }
        } catch (Exception $e) {
            // Fallback jika kueri relasi tabel ulasan belum sempurna
            echo "<p style='color: #888; font-style: italic;'>Ulasan sedang tidak dapat dimuat saat ini.</p>";
        }
        ?>
    </div>
</div>

<?php include 'layout/footer.php'; ?>