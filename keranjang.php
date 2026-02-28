<?php
session_start();
require_once 'controllers/KeranjangController.php';
include 'layout/header.php';

// Cek apakah kedua keranjang (menu & fasilitas) benar-benar kosong
$cek_menu = !isset($_SESSION['keranjang']) || empty($_SESSION['keranjang']);
$cek_fasilitas = !isset($_SESSION['keranjang_fasilitas']) || empty($_SESSION['keranjang_fasilitas']);

if ($cek_menu && $cek_fasilitas) {
    echo "<div style='padding:50px; text-align:center;'>
            <h2 style='color: #6F4E37;'>Keranjang Belanja Kosong</h2>
            <p style='color: #888;'>Silahkan pilih menu atau fasilitas terlebih dahulu.</p>
            <div style='margin-top: 20px;'>
                <a href='menu.php' style='color:white; background: #6F4E37; padding: 10px 20px; text-decoration: none; border-radius: 6px; margin-right: 10px;'>Lihat Menu</a>
                <a href='fasilitas/fasilitas_publik.php' style='color:white; background: #6F4E37; padding: 10px 20px; text-decoration: none; border-radius: 6px;'>Lihat Fasilitas</a>
            </div>
          </div>";
    include 'layout/footer.php';
    exit;
}
?>

<div style="max-width: 1000px; margin: 30px auto; padding: 70px 30px; background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <h2 style="color: #333; margin-bottom: 25px;">🛒 Keranjang Belanja Anda</h2>

    <form action="checkout.php" method="POST" id="form-keranjang">
        <table border="0" cellpadding="15" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #eee;">
                    <th width="30" align="center">
                        <input type="checkbox" id="check-all" checked style="transform: scale(1.2); cursor: pointer;">
                    </th>
                    <th align="left">Item (Menu/Fasilitas)</th>
                    <th align="center">Harga</th>
                    <th align="center">Jumlah / Durasi</th>
                    <th align="right">Subtotal</th>
                    <th width="50"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // 1. TAMPILKAN MENU MAKANAN
                if (!$cek_menu) :
                    foreach ($data_menu as $m) :
                        // $stmt = $conn->prepare("SELECT * FROM tb_menu WHERE id_menu = ?");
                        // $stmt->execute([$id_menu]);
                        // $m = $stmt->fetch();
                        $subtotal = $m['subtotal'];
                        $qty = $m['qty'];
                ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td align="center">
                                <input type="checkbox" name="selected_menu[]" value="<?= $m['id_menu'] ?>" class="item-checkbox" data-subtotal="<?= $subtotal ?>" checked style="transform: scale(1.2); cursor: pointer;">
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <img src="assets/gambar/menu/<?= $m['foto']; ?>" width="60" height="60" style="border-radius: 8px; object-fit: cover;">
                                    <strong><?= $m['nama_menu']; ?></strong>
                                </div>
                            </td>
                            <td align="center">Rp <?= number_format($m['harga'], 0, ',', '.'); ?></td>
                            <td align="center"><?= $qty; ?> Item</td>
                            <td align="right" style="font-weight: 500;">Rp <?= number_format($m['subtotal'], 0, ',', '.'); ?></td>
                            <td align="center">
                                <a href="hapus_keranjang.php?id=<?= $m['id_menu'] ?>" style="color: #ef4444; text-decoration: none; font-size: 18px;" title="Hapus">✕</a>
                            </td>
                        </tr>
                <?php endforeach;
                endif; ?>

                <?php if (!$cek_fasilitas) :
                    foreach ($data_fasilitas as $f):
                        // $f = $conn->query("SELECT * FROM tb_fasilitas WHERE id_fasilitas = $id")->fetch();
                        $subtotal_f = $f['subtotal'];
                ?>
                        <tr style="border-bottom: 1px solid #eee; background: #fffcf5;">
                            <td align="center">
                                <input type="checkbox" name="selected_fasilitas[]" value="<?= $f['id_fasilitas'] ?>" class="item-checkbox" data-subtotal="<?= $subtotal_f ?>" checked style="transform: scale(1.2); cursor: pointer;">
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <img src="assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>" width="60" height="60" style="border-radius: 8px; object-fit: cover;">
                                    <div>
                                        <strong>[Fasilitas] <?= $f['nama_fasilitas'] ?></strong><br>
                                        <small style="color: #888;">
                                            <?= date('d M Y', strtotime($f['tgl_sewa'])) ?>
                                            <?= ($f['satuan'] == 'Jam') ? "| Pukul " . $f['jam_mulai'] : "" ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td align="center">Rp <?= number_format($f['harga'], 0, ',', '.'); ?></td>
                            <td align="center"><?= $f['pengali'] ?> <?= $f['satuan'] ?></td>
                            <td align="right" style="font-weight: 500;">Rp <?= number_format($f['subtotal'], 0, ',', '.'); ?></td>
                            <td align="center">
                                <a href="fasilitas/hapus_keranjang_fasilitas.php?id=<?= $f['id_fasilitas'] ?>" style="color: #ef4444; text-decoration: none; font-size: 18px;" title="Hapus">✕</a>
                            </td>
                        </tr>
                <?php endforeach;
                endif; ?>
            </tbody>
            <tfoot>
                <tr style="font-size: 18px;">
                    <td colspan="4" align="right" style="padding-top: 25px; font-weight: bold;">Total Pembayaran:</td>
                    <td align="right" style="padding-top: 25px; color: #6F4E37; font-weight: bold; font-size: 20px;" id="total_bayar_display">
                        Rp 0
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 35px; display: flex; justify-content: space-between; align-items: center;">
            <a href="index.php" style="padding: 12px 25px; background: #eca445; color: #333; text-decoration: none; border-radius: 8px; font-weight: bold; border: 1px solid #ddd;">← Kembali Belanja</a>
            <button type="submit" id="btn-checkout" style="padding: 14px 40px; background: #28a745; color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 16px; cursor: pointer; box-shadow: 0 4px 10px rgba(40, 167, 69, 0.2); transition: 0.3s;">
                Lanjutkan ke Checkout →
            </button>
        </div>
    </form>
</div>

<script>
    /**
     * Logika JavaScript untuk menghitung total harga berdasarkan checkbox yang dipilih
     */
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const displayTotal = document.getElementById('total_bayar_display');
        const btnCheckout = document.getElementById('btn-checkout');

        function hitungTotalDinamic() {
            let total = 0;
            let isAnyChecked = false;

            itemCheckboxes.forEach(function(cb) {
                if (cb.checked) {
                    total += parseFloat(cb.getAttribute('data-subtotal'));
                    isAnyChecked = true;
                }
            });

            // Update teks Total Pembayaran
            displayTotal.innerText = 'Rp ' + total.toLocaleString('id-ID');

            // Matikan tombol checkout jika tidak ada yang dicentang
            if (!isAnyChecked) {
                btnCheckout.disabled = true;
                btnCheckout.style.backgroundColor = '#ccc';
                btnCheckout.style.cursor = 'not-allowed';
                btnCheckout.style.boxShadow = 'none';
            } else {
                btnCheckout.disabled = false;
                btnCheckout.style.backgroundColor = '#28a745';
                btnCheckout.style.cursor = 'pointer';
                btnCheckout.style.boxShadow = '0 4px 10px rgba(40, 167, 69, 0.2)';
            }
        }

        // Event listener untuk tombol "Pilih Semua"
        checkAll.addEventListener('change', function() {
            itemCheckboxes.forEach(function(cb) {
                cb.checked = checkAll.checked;
            });
            hitungTotalDinamic();
        });

        // Event listener untuk masing-masing checkbox item
        itemCheckboxes.forEach(function(cb) {
            cb.addEventListener('change', function() {
                // Jika ada satu yang di-uncheck, hilangkan centang pada 'Pilih Semua'
                if (!this.checked) {
                    checkAll.checked = false;
                } else {
                    // Cek apakah semua item sudah tercentang manual
                    const allChecked = Array.from(itemCheckboxes).every(c => c.checked);
                    if (allChecked) checkAll.checked = true;
                }
                hitungTotalDinamic();
            });
        });

        // Hitung total saat halaman pertama kali dimuat
        hitungTotalDinamic();
    });
</script>

<?php include 'layout/footer.php'; ?>