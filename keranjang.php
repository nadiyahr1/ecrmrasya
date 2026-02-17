<?php
session_start();
include 'layout/header.php';
require_once 'config/koneksi.php';

// Cek apakah kedua keranjang (menu & fasilitas) benar-benar kosong
$cek_menu = !isset($_SESSION['keranjang']) || empty($_SESSION['keranjang']);
$cek_fasilitas = !isset($_SESSION['keranjang_fasilitas']) || empty($_SESSION['keranjang_fasilitas']);

if ($cek_menu && $cek_fasilitas) {
    echo "<div style='padding:50px; text-align:center;'>
            <h2>Keranjang Belanja Kosong</h2>
            <p>Silahkan pilih menu atau fasilitas terlebih dahulu.</p>
            <a href='index.php'>Lihat Menu</a> | <a href='fasilitas/fasilitas_publik.php'>Lihat Fasilitas</a>
          </div>";
    include 'layout/footer.php';
    exit;
}
?>

<div style="padding: 20px;">
    <h2>🛒 Keranjang Belanja Anda</h2>
    
    <details>
        <summary>Cek Data Session (Klik untuk melihat)</summary>
        <pre><?php print_r($_SESSION['keranjang_fasilitas']); ?></pre>
    </details>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead>
            <tr style="background: #eee;">
                <th>Item (Menu/Fasilitas)</th>
                <th>Harga</th>
                <th>Jumlah / Durasi</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_bayar = 0;

            // 1. Tampilkan Menu Makanan jika ada
            if (!$cek_menu) :
                foreach ($_SESSION['keranjang'] as $id_menu => $qty) :
                    $stmt = $conn->prepare("SELECT * FROM tb_menu WHERE id_menu = ?");
                    $stmt->execute([$id_menu]);
                    $m = $stmt->fetch();
                    $subtotal = $m['harga'] * $qty;
                    $total_bayar += $subtotal;
            ?>
                    <tr>
                        <td><?= $m['nama_menu']; ?></td>
                        <td>Rp <?= number_format($m['harga'], 0, ',', '.'); ?></td>
                        <td><?= $qty; ?> Item</td>
                        <td>Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                    </tr>
            <?php endforeach; endif; ?>

            // 2. Tampilkan Fasilitas jika ada
            <?php if (!$cek_fasilitas) : ?>
                <?php foreach ($_SESSION['keranjang_fasilitas'] as $id_f => $book) :
                    $stmt_f = $conn->prepare("SELECT * FROM tb_fasilitas WHERE id_fasilitas = ?");
                    $stmt_f->execute([$id_f]);
                    $f = $stmt_f->fetch();

                    $sub_f = $f['harga_per_jam'] * $book['durasi'];
                    $total_bayar += $sub_f;
                ?>
                    <tr style="background: #fff9f0;">
                        <td>
                            <strong>[Fasilitas] <?= $f['nama_fasilitas']; ?></strong><br>
                            <small>Tgl: <?= $book['tgl_booking']; ?> | Jam: <?= $book['jam_mulai']; ?></small>
                        </td>
                        <td>Rp <?= number_format($f['harga_per_jam'], 0, ',', '.'); ?> / jam</td>
                        <td><?= $book['durasi']; ?> Jam</td>
                        <td>Rp <?= number_format($sub_f, 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #f9f9f9;">
                <td colspan="3" align="right">Total yang harus dibayar:</td>
                <td>Rp <?= number_format($total_bayar, 0, ',', '.'); ?></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; text-align: right;">
        <a href="index.php" style="padding: 10px; background: #888; color: white; text-decoration: none; border-radius: 4px;">Kembali Belanja</a>
        <a href="proses_checkout.php" style="padding: 10px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">Lanjutkan ke Checkout</a>
    </div>
</div>

<?php include 'layout/footer.php'; ?>