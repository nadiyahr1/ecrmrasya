<?php include 'layout/header.php'; ?>

<?php

$cek_menu = empty($data_menu);
$cek_fasilitas = empty($data_fasilitas);

if ($cek_menu && $cek_fasilitas): ?>
    <div style='padding:50px; text-align:center;'>
        <h2 style='color: #6F4E37;'>Keranjang Belanja Kosong</h2>
        <p style='color: #888;'>Silahkan pilih menu atau fasilitas terlebih dahulu.</p>
        <div style='margin-top: 20px;'>
            <a href='<?= $base_url ?>index.php?controller=menu&action=index' style='color:white; background:#6F4E37; padding:10px 20px; border-radius:6px;'>Lihat Menu</a>
            <a href='fasilitas/fasilitas_publik.php' style='color:white; background:#6F4E37; padding:10px 20px; border-radius:6px;'>Lihat Fasilitas</a>
        </div>
    </div>
<?php include 'layout/footer.php';
    exit;
endif; ?>

<div style="max-width: 1000px; margin: 30px auto; padding: 70px 30px; background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <h2 style="color: #333; margin-bottom: 25px;">🛒 Keranjang Belanja Anda</h2>

    <form action="index.php?controller=checkout&action=index" method="POST" id="form-keranjang">

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #eee;">
                    <th width="30" align="center">
                        <input type="checkbox" id="check-all" checked>
                    </th>
                    <th align="left">Item</th>
                    <th align="center">Harga</th>
                    <th align="center">Jumlah / Durasi</th>
                    <th align="right">Subtotal</th>
                    <th width="50"></th>
                </tr>
            </thead>

            <tbody>

                <!-- ================= MENU ================= -->
                <?php foreach ($data_menu as $m): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td align="center">
                            <input type="checkbox"
                                name="selected_menu[]"
                                value="<?= $m['id_menu'] ?>"
                                class="item-checkbox"
                                data-subtotal="<?= $m['subtotal'] ?>"
                                checked>
                        </td>

                        <td>
                            <div style="display:flex; align-items:center; gap:15px;">
                                <img src="<?= $base_url ?>assets/gambar/menu/<?= $m['foto'] ?>" width="60">
                                <strong><?= $m['nama_menu'] ?></strong>
                            </div>
                        </td>

                        <td align="center">
                            Rp <?= number_format($m['harga'], 0, ',', '.') ?>
                        </td>

                        <td align="center">
                            <!-- UPDATE via CONTROLLER -->
                            <form action="index.php?controller=keranjang&action=update" method="POST">
                                <input type="hidden" name="id_menu" value="<?= $m['id_menu'] ?>">
                                <input type="number" name="qty" value="<?= $m['qty'] ?>" min="1" style="width:60px;">
                                <button type="submit">Update</button>
                            </form>
                        </td>

                        <td align="right">
                            Rp <?= number_format($m['subtotal'], 0, ',', '.') ?>
                        </td>

                        <td align="center">
                            <a href="index.php?controller=keranjang&action=hapusMenu&id=<?= $m['id_menu'] ?>"
                                style="color:red;">✕</a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <!-- ================= FASILITAS ================= -->
                <?php foreach ($data_fasilitas as $f): ?>
                    <tr style="border-bottom:1px solid #eee; background:#fffcf5;">
                        <td align="center">
                            <input type="checkbox"
                                name="selected_fasilitas[]"
                                value="<?= $f['id_fasilitas'] ?>"
                                class="item-checkbox"
                                data-subtotal="<?= $f['subtotal'] ?>"
                                checked>
                        </td>

                        <td>
                            <div style="display:flex; gap:15px;">
                                <img src="<?= $base_url ?>assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>" width="60">
                                <div>
                                    <strong>[Fasilitas] <?= $f['nama_fasilitas'] ?></strong><br>
                                    <small>
                                        <?= date('d M Y', strtotime($f['tgl_sewa'])) ?>
                                        <?= ($f['satuan'] == 'Jam') ? "| " . $f['jam_mulai'] : "" ?>
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td align="center">
                            Rp <?= number_format($f['harga'], 0, ',', '.') ?>
                        </td>

                        <td align="center">
                            <?= $f['pengali'] ?> <?= $f['satuan'] ?>
                        </td>

                        <td align="right">
                            Rp <?= number_format($f['subtotal'], 0, ',', '.') ?>
                        </td>

                        <td align="center">
                            <a href="index.php?controller=keranjang&action=hapusFasilitas&id=<?= $f['id_fasilitas'] ?>"
                                style="color:red;">✕</a>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4" align="right"><strong>Total:</strong></td>
                    <td align="right" id="total_bayar_display">Rp 0</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top:30px; display:flex; justify-content:space-between;">
            <a href="<?= $base_url ?>index.php?controller=home&action=index"">← Kembali</a>
            <button type="submit" id="btn-checkout">Checkout →</button>
        </div>

    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const checkAll = document.getElementById('check-all');
        const items = document.querySelectorAll('.item-checkbox');
        const totalDisplay = document.getElementById('total_bayar_display');

        function hitungTotal() {
            let total = 0;

            items.forEach(cb => {
                if (cb.checked) {
                    total += parseFloat(cb.dataset.subtotal);
                }
            });

            totalDisplay.innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        checkAll.addEventListener('change', function() {
            items.forEach(cb => cb.checked = this.checked);
            hitungTotal();
        });

        items.forEach(cb => {
            cb.addEventListener('change', hitungTotal);
        });

        hitungTotal();
    });
    document.getElementById('form-keranjang').addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.item-checkbox:checked');

        if (checked.length === 0) {
            alert('Pilih minimal 1 item!');
            e.preventDefault();
        }
    });
</script>

<?php include 'layout/footer.php'; ?>