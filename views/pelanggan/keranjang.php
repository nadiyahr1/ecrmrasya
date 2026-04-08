<?php include 'layout/header.php'; ?>

<?php
$cek_menu = empty($data_menu);
$cek_fasilitas = empty($data_fasilitas);

if ($cek_menu && $cek_fasilitas): ?>
    <div class="page-container">
        <div class="empty-cart">
            <i class="fa-solid fa-cart-shopping"></i>
            <h2>Keranjang Belanja Kosong</h2>
            <p>Silakan pilih menu atau fasilitas terlebih dahulu untuk melanjutkan.</p>
            <div class="empty-cart-actions">
                <a href="<?= $base_url ?>index.php?controller=menu&action=index" class="btn-checkout">Lihat Menu</a>
                <a href="<?= $base_url ?>index.php?controller=fasilitas&action=index" class="btn-outline-cart">Lihat Fasilitas</a>
            </div>
        </div>
    </div>
<?php 
    exit;
endif; ?>

<div class="page-container">
    <div class="cart-container">
        <h2 class="cart-title">
            <i class="fa-solid fa-basket-shopping"></i> Keranjang Belanja
        </h2>
        
        <form action="index.php?controller=checkout&action=index" method="POST" id="form-keranjang">
            
            <div class="cart-table-wrapper">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th width="40" style="text-align: center;">
                                <input type="checkbox" id="check-all" checked>
                            </th>
                            <th style="text-align: left;">Item Pesanan</th>
                            <th style="text-align: center;">Harga</th>
                            <th style="text-align: center;">Jml / Durasi</th>
                            <th style="text-align: right;">Subtotal</th>
                            <th width="60" style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($data_menu as $m): ?>
                            <tr>
                                <td class="col-check" style="text-align: center;">
                                    <?php 
                                        // Cek apakah item ini ada di dalam session selected_menu
                                        // Jika $selected_menu null (baru pertama kali buka), defaultnya centang semua
                                        $is_checked = ($selected_menu === null || in_array($m['id_menu'], $selected_menu)) ? 'checked' : '';
                                    ?>
                                    <input type="checkbox"
                                        name="selected_menu[]"
                                        value="<?= $m['id_menu'] ?>"
                                        class="item-checkbox"
                                        data-subtotal="<?= $m['subtotal'] ?>"
                                        <?= $is_checked ?>>
                                </td>

                                <td class="col-item">
                                    <div class="cart-item-card">
                                        <div class="cart-item-image">
                                            <img src="<?= $base_url ?>assets/gambar/menu/<?= $m['foto'] ?>" class="item-img" alt="Menu">
                                        </div>
                                        <div class="cart-item-details">
                                            <strong class="item-name"><?= htmlspecialchars($m['nama_menu']) ?></strong>
                                            <span class="item-price-mobile">Rp <?= number_format($m['harga'], 0, ',', '.') ?></span>
                                        </div>
                                    </div>
                                </td>

                                <td class="col-price" style="text-align: center;">
                                    Rp <?= number_format($m['harga'], 0, ',', '.') ?>
                                </td>

                                <td class="col-qty" style="text-align: center;">
                                    <input type="number"
                                        value="<?= $m['qty'] ?>"
                                        min="1"
                                        class="qty-input"
                                        onchange="updateQtyOtomatis('<?= $m['id_menu'] ?>', this.value)">
                                </td>

                                <td class="col-subtotal" style="text-align: right; font-weight: bold; color: #6F4E37;">
                                    Rp <?= number_format($m['subtotal'], 0, ',', '.') ?>
                                </td>

                                <td class="col-action" style="text-align: center;">
                                    <a href="index.php?controller=keranjang&action=hapusMenu&id=<?= $m['id_menu'] ?>" class="btn-delete" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php foreach ($data_fasilitas as $f): ?>
                            <tr class="fasilitas-row">
                                <td class="col-check" style="text-align: center;">
                                    <?php 
                                        $is_checked_f = ($selected_fasilitas === null || in_array($f['id_fasilitas'], $selected_fasilitas)) ? 'checked' : '';
                                    ?>
                                    <input type="checkbox"
                                        name="selected_fasilitas[]"
                                        value="<?= $f['id_fasilitas'] ?>"
                                        class="item-checkbox"
                                        data-subtotal="<?= $f['subtotal'] ?>"
                                        <?= $is_checked_f ?>>
                                </td>

                                <td class="col-item">
                                     <div class="cart-item-card">
                                        <div class="cart-item-image">
                                            <img src="<?= $base_url ?>assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>" class="item-img" alt="Fasilitas">
                                        </div>
                                        <div class="cart-item-details">
                                            <strong class="item-name">[Fasilitas] <?= htmlspecialchars($f['nama_fasilitas']) ?></strong>
                                            
                                            <span style="font-size: 12px; color: #888; display: block; margin-top: 4px;">
                                                <i class="fa-regular fa-calendar"></i> <?= date('d M Y', strtotime($f['tgl_sewa'])) ?>
                                                <?= ($f['satuan'] == 'Jam') ? "&nbsp;|&nbsp; <i class='fa-regular fa-clock'></i> " . $f['jam_mulai'] : "" ?>
                                            </span>

                                            <span class="item-price-mobile">Rp <?= number_format($f['harga'], 0, ',', '.') ?></span>
                                        </div>
                                    </div>
                                </td>

                                <td class="col-price" style="text-align: center;">
                                    Rp <?= number_format($f['harga'], 0, ',', '.') ?>
                                </td>

                                <td class="col-qty" style="text-align: center;">
                                    <?= $f['pengali'] ?> <?= $f['satuan'] ?>
                                </td>

                                <td class="col-subtotal" style="text-align: right; font-weight: bold; color: #6F4E37;">
                                    Rp <?= number_format($f['subtotal'], 0, ',', '.') ?>
                                </td>

                                <td class="col-action" style="text-align: center;">
                                    <a href="index.php?controller=keranjang&action=hapusFasilitas&id=<?= $f['id_fasilitas'] ?>" class="btn-delete" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right; font-size: 18px;"><strong>Total Bayar:</strong></td>
                            <td style="text-align: right; font-size: 20px; font-weight: bold; color: #6F4E37;" id="total_bayar_display">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="cart-footer">
                <a href="<?= $base_url ?>index.php?controller=home&action=index" class="btn-back-cart">
                    <i class="fa-solid fa-arrow-left"></i> Kembali Belanja
                </a>
                <button type="submit" id="btn-checkout" class="btn-checkout">
                    Lanjut ke Checkout <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    function updateQtyOtomatis(id_menu, qty) {
        if (qty < 1) return; // Proteksi agar tidak nol

        let formData = new FormData();
        formData.append('id_menu', id_menu);
        formData.append('qty', qty);

        // Kumpulkan semua checkbox yang sedang dicentang saat ini
        document.querySelectorAll('input[name="selected_menu[]"]:checked').forEach(cb => {
            formData.append('selected_menu[]', cb.value);
        });
        document.querySelectorAll('input[name="selected_fasilitas[]"]:checked').forEach(cb => {
            formData.append('selected_fasilitas[]', cb.value);
        });

        fetch('index.php?controller=keranjang&action=update', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
    }

    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all');
        const items = document.querySelectorAll('.item-checkbox');
        const totalDisplay = document.getElementById('total_bayar_display');

        function hitungTotal() {
            let total = 0;
            let allChecked = true; // Flag untuk check-all

            items.forEach(cb => {
                if (cb.checked) {
                    total += parseFloat(cb.dataset.subtotal);
                } else {
                    allChecked = false; // Jika ada 1 yang tidak dicentang, flag jadi false
                }
            });

            // Perbarui tampilan harga dan status checkbox "Pilih Semua"
            totalDisplay.innerText = 'Rp ' + total.toLocaleString('id-ID');
            
            // Jangan ubah status check-all jika tidak ada item sama sekali
            if (items.length > 0) {
                checkAll.checked = allChecked;
            }
        }

        checkAll.addEventListener('change', function() {
            items.forEach(cb => cb.checked = this.checked);
            hitungTotal();
        });

        items.forEach(cb => {
            cb.addEventListener('change', hitungTotal);
        });

        hitungTotal(); // Hitung total saat halaman pertama kali dimuat
    });

    document.getElementById('form-keranjang').addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        if (checked.length === 0) {
            alert('Pilih minimal 1 item pesanan!');
            e.preventDefault();
        }
    });
</script>

<?php include 'layout/footer.php'; ?>