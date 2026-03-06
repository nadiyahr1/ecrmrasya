<?php
include 'layout/header.php';

// Inisialisasi variabel untuk perhitungan (Data lainnya sudah dikirim otomatis dari Controller)
$sub_m = 0;
$qty_m = 0;
$sub_f = 0;
?>

<form action="index.php?controller=checkout&action=simpanPesanan" method="POST">

    <?php foreach ($selected_menu as $id): ?>
        <input type="hidden" name="checkout_menu[]" value="<?= $id ?>">
    <?php endforeach; ?>

    <?php foreach ($selected_fasilitas as $id): ?>
        <input type="hidden" name="checkout_fasilitas[]" value="<?= $id ?>">
    <?php endforeach; ?>

    <div style="padding: 70px 20px; display: flex; gap: 30px; align-items: flex-start; background: #fdfdfd; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <div style="flex: 2; background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h2 style="margin: 0;">Checkout</h2>

            <section style="margin-bottom: 30px;">
                <h3 style="border-left: 5px solid #6F4E37; padding-left: 10px;">1. Informasi Pemesan</h3>
                <div style="margin-left: 35px;">
                    <strong><?= $user['nama_member'] ?></strong> (Member <?= $user['nama_level'] ?>) <br>
                    <small><?= $user['no_telp'] ?></small>
                </div>
            </section>

            <section style="margin-bottom: 30px;">
                <h3 style="border-left: 5px solid #6F4E37; padding-left: 10px;">2. Detail Pesanan</h3>
                <div style="margin-left: 35px;">

                    <?php
                        foreach ($selected_menu as $id):
                            if (isset($_SESSION['keranjang'][$id])):
                                $q = $_SESSION['keranjang'][$id];
                                $m = $menus[$id];
                                $sub_m += ($m['harga'] * $q);
                                $qty_m += $q;
                    ?>
                                <div style="display: flex; align-items: center; margin-bottom: 15px; border-bottom: 1px dashed #eee; padding-bottom: 10px;">
                                    <img src="assets/gambar/menu/<?= $m['foto'] ?>" width="70" height="70" style="border-radius: 10px; object-fit: cover; margin-right: 15px; border: 1px solid #eee;">
                                    <div style="flex: 1;">
                                        <strong><?= $m['nama_menu'] ?></strong><br>
                                        <small>Rp <?= number_format($m['harga']) ?> x <?= $q ?></small>
                                    </div>
                                    <strong>Rp <?= number_format($m['harga'] * $q) ?></strong>
                                </div>
                    <?php
                            endif;
                        endforeach;
                    ?>

                    <?php
                        foreach ($selected_fasilitas as $id):
                            if (isset($_SESSION['keranjang_fasilitas'][$id])):
                                $b = $_SESSION['keranjang_fasilitas'][$id];
                                $f = $fasilitas_data[$id];

                                $subtotal_item_f = $f['harga'] * $b['pengali'];
                                $sub_f += $subtotal_item_f;
                    ?>
                                <div style="display: flex; align-items: center; margin-bottom: 15px; border-bottom: 1px dashed #eee; padding-bottom: 10px;">
                                    <img src="assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>" width="70" height="70" style="border-radius: 10px; object-fit: cover; margin-right: 15px; border: 1px solid #eee;">
                                    <div style="flex: 1;">
                                        <strong>[Fasilitas] <?= $f['nama_fasilitas'] ?></strong><br>
                                        <small><?= date('d M Y', strtotime($b['tgl_sewa'])) ?> <?= ($b['satuan'] == 'Jam') ? "| Pukul " . $b['jam_mulai'] : "" ?> | <?= $b['pengali'] ?> <?= $b['satuan'] ?></small>
                                    </div>
                                    <strong>Rp <?= number_format($subtotal_item_f) ?></strong>
                                </div>
                    <?php
                            endif;
                        endforeach;
                    ?>
                </div>
            </section>

            <section style="margin-bottom: 30px;">
                <h3 style="border-left: 5px solid #6F4E37; padding-left: 10px;">3. Tipe Pemesanan</h3>
                <div style="margin-left: 35px;">
                    <?php if ($ada_fasilitas): ?>
                        <p style="color: #6F4E37; font-weight: bold;">Makan di Tempat (Dine-in) <small style="font-weight: normal; color: #888;">*Otomatis karena ada booking fasilitas</small></p>
                        <input type="hidden" name="tipe_pemesanan" value="Makan di Tempat">
                    <?php else: ?>
                        <label><input type="radio" name="tipe_pemesanan" value="Ambil di Cafe" checked onclick="toggleMeja(false)"> Ambil di Cafe (Take-Away)</label> <br>
                        <label><input type="radio" name="tipe_pemesanan" value="Makan di Tempat" onclick="toggleMeja(true)"> Makan di Tempat (Dine-in)</label>
                    <?php endif; ?>

                    <div id="mejaBox" style="margin-top: 10px; display: <?= $ada_fasilitas ? 'block' : 'none' ?>;">
                        <label>Pilih Nomor Meja:</label>
                        <select name="id_meja" style="padding: 5px;">
                            <?php
                            foreach ($mejas as $mj) echo "<option value='{$mj['id_meja']}'>Meja {$mj['no_meja']}</option>";
                            ?>
                        </select>
                    </div>
                </div>
            </section>

            <section style="margin-bottom: 30px;">
                <h3 style="border-left: 5px solid #6F4E37; padding-left: 10px;">4. Metode Pembayaran</h3>
                <div style="margin-left: 35px;">
                    <select name="metode" style="width: 100%; padding: 12px; border-radius: 5px; border: 1px solid #ccc;">
                        <option value="Kasir">Bayar di Kasir / QRIS</option>
                        <option value="Transfer">Transfer Bank (BCA/Mandiri)</option>
                    </select>
                </div>
            </section>

            <section style="margin-bottom: 30px;">
                <h3 style="border-left: 5px solid #6F4E37; padding-left: 10px;">5. Voucher & Loyalitas</h3>
                <div style="margin-left: 35px; background: #f9f9f9; border-radius: 10px; padding: 15px;">
                    <div style="margin-bottom: 15px;">
                        <label>Punya Kode Promo / Kupon?</label><br>
                        <div style="display: flex; gap: 10px; margin-top: 10px;">
                            <input type="text" id="kode_kupon" placeholder="Masukkan kode promo..." style="flex: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                            <button type="button" onclick="cekKupon()" style="padding: 10px 20px; background: #333; color: white; border: none; border-radius: 5px; cursor: pointer;">Cek Kode</button>
                        </div>
                    </div>

                    <p>Atau tukar poin Anda: <strong><?= number_format($user['poin']) ?> Poin</strong></p>
                    <button type="button" onclick="openVoucherModal()" style="padding: 10px 15px; background: #6F4E37; color: white; border: none; cursor: pointer; border-radius: 5px;">
                        🎁 Pilih Hadiah Member
                    </button>

                    <div id="v_terpilih" style="display: none; margin-top: 15px; padding: 10px; background: #e8f5e9; color: #2e7d32; border-radius: 5px; font-weight: bold;">
                        <span id="v_nama"></span> <a href="javascript:void(0)" onclick="removeVoucher()" style="color:red; font-size:12px; margin-left:10px;">(Batalkan)</a>
                    </div>
                </div>
            </section>

            <section>
                <h3 style="border-left: 5px solid #6F4E37; padding-left: 10px;">6. Catatan</h3>
                <div style="margin-left: 35px;">
                    <label><input type="checkbox" onclick="toggleNote(this)"> Tambahkan catatan tambahan</label>
                    <textarea id="box_note" name="catatan" style="display: none; width: 100%; height: 80px; margin-top: 10px; padding: 10px; border-radius: 5px;" placeholder="Contoh: Kopi kurangi gula, atau request posisi meja..."></textarea>
                </div>
            </section>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                <a href="index.php?controller=keranjang&action=index" style="padding: 12px 25px; background: #eca445; color: #333; text-decoration: none; border-radius: 8px; font-weight: bold; border: 1px solid #ddd;">
                    <i class="fa-solid fa-arrow-left"></i> ← Kembali ke Keranjang
                </a>
            </div>
        </div>

        <div style="flex: 1; position: sticky; top: 20px;">
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-top: 5px solid #6F4E37;">
                <h3 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee;">Rincian Pembayaran</h3>
                <input type="hidden" name="id_voucher" id="id_v_hidden" value="">

                <div style="line-height: 2.2; font-size: 15px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Subtotal Menu (<?= $qty_m ?> item)</span> <span>Rp <?= number_format($sub_m) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Subtotal Fasilitas</span> <span>Rp <?= number_format($sub_f) ?></span>
                    </div>
                    <?php
                    $pajak = ($sub_m + $sub_f) * 0.1;
                    $disc_level = ($sub_m + $sub_f) * ($user['diskon'] / 100);
                    ?>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Pajak & Layanan (10%)</span> <span>Rp <?= number_format($pajak) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; color: #2e7d32;">
                        <span>Diskon Member (<?= $user['nama_level'] ?>)</span> <span>-Rp <?= number_format($disc_level) ?></span>
                    </div>
                    <div id="row_v" style="display: none; justify-content: space-between; color: #2e7d32; font-weight: bold;">
                        <span>Potongan Promo/Poin</span> <span id="v_val_text">-Rp 0</span>
                    </div>
                </div>

                <hr style="margin: 20px 0;">

                <?php $total_awal = ($sub_m + $sub_f + $pajak) - $disc_level; ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h3 style="margin: 0; color: #333;">Total Bayar</h3>
                    <h2 style="margin: 0; color: #6F4E37;" id="total_akhir">Rp <?= number_format($total_awal) ?></h2>
                </div>
                <button type="submit" style="width: 100%; padding: 18px; background: #28a745; color: white; border: none; border-radius: 10px; font-weight: bold; font-size: 18px; cursor: pointer; transition: 0.3s;">
                    BUAT PESANAN
                </button>

            </div>
        </div>
    </div>
</form>

<div id="vModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(3px);">
    <div style="background: white; margin: 8% auto; padding: 25px; width: 450px; border-radius: 15px; position: relative;">
        <h3>🎁 Pilihan Voucher Hadiah</h3>
        <p style="font-size: 14px;">Tukarkan poin loyalitas Anda dengan diskon spesial.</p>
        <hr>
        <div style="max-height: 350px; overflow-y: auto; padding-right: 5px;">
            <?php
            foreach ($promos as $p):

                $min_poin    = $p['min_poin'];
                $tipe_potongan = $p['tipe_potongan'];
                $potongan  = $p['potongan'];

                $cukup = ($user['poin'] >= $min_poin);

                // LOGIKA PERHITUNGAN PERSEN VS NOMINAL
                if ($tipe_potongan == 'Persen' || $tipe_potongan == '%') {
                    // Jika persen, kalikan dengan $total_awal
                    $potongan_rp = $total_awal * ($potongan / 100);
                    $label_potongan = $potongan . "% (Maks: Rp " . number_format($potongan_rp) . ")";
                } else {
                    // Jika nominal, nilainya tetap
                    $potongan_rp = $potongan;
                    $label_potongan = "Rp " . number_format($potongan_rp);
                }

                // Proteksi: Diskon tidak boleh lebih besar dari total bayar
                if ($potongan_rp > $total_awal) {
                    $potongan_rp = $total_awal;
                }
            ?>
                <div style="padding: 15px; border: 2px solid <?= $cukup ? '#6F4E37' : '#eee' ?>; border-radius: 12px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; opacity: <?= $cukup ? '1' : '0.5' ?>; background: <?= $cukup ? '#fffcf5' : '#fafafa' ?>;">
                    <div>
                        <strong style="color: #6F4E37;"><?= $p['nama_promo'] ?></strong> <br>
                        <small>Butuh <?= $min_poin ?> Poin | Potongan <?= $label_potongan ?></small>
                    </div>
                    <?php if ($cukup): ?>
                        <button type="button" onclick="pilihV('<?= $p['id_promo'] ?>', '<?= $p['nama_promo'] ?>', <?= $potongan_rp ?>)" style="background: #6F4E37; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-weight: bold;">Tukar</button>
                    <?php else: ?>
                        <button disabled style="padding: 8px 12px; border-radius: 6px; font-size: 11px; background: #ddd; color: #888; border: none;">Poin Kurang</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <button onclick="closeVoucherModal()" style="width: 100%; margin-top: 15px; padding: 12px; border: none; background: #eee; border-radius: 8px; cursor: pointer; font-weight: bold;">Tutup</button>
    </div>
</div>

<script>
    let totalBase = <?= $total_awal ?>;

    function toggleMeja(show) {
        // PERBAIKAN: Mengubah ID agar sesuai dengan ID elemen HTML kamu (mejaBox)
        document.getElementById('mejaBox').style.display = show ? 'block' : 'none';
    }

    function toggleNote(cek) {
        document.getElementById('box_note').style.display = cek.checked ? 'block' : 'none';
    }

    function openVoucherModal() {
        document.getElementById('vModal').style.display = 'block';
    }

    function closeVoucherModal() {
        document.getElementById('vModal').style.display = 'none';
    }

    function pilihV(id, nama, nominal) {
        document.getElementById('id_v_hidden').value = id;
        document.getElementById('v_nama').innerText = "Berhasil: " + nama + " (-Rp " + nominal.toLocaleString('id-ID') + ")";
        document.getElementById('v_terpilih').style.display = 'block';

        document.getElementById('row_v').style.display = 'flex';
        document.getElementById('v_val_text').innerText = "-Rp " + nominal.toLocaleString('id-ID');

        document.getElementById('total_akhir').innerText = "Rp " + (totalBase - nominal).toLocaleString('id-ID');
        closeVoucherModal();
    }

    function cekKupon() {
        let kode = document.getElementById('kode_kupon').value;
        if (kode === "") {
            alert("Masukkan kode terlebih dahulu!");
            return;
        }
        alert("Sedang mengecek kode: " + kode + "... \n(Fitur ini akan dihubungkan ke tb_promo tipe Umum)");
    }

    function removeVoucher() {
        document.getElementById('id_v_hidden').value = "";
        document.getElementById('v_terpilih').style.display = 'none';
        document.getElementById('row_v').style.display = 'none';
        document.getElementById('total_akhir').innerText = "Rp " + totalBase.toLocaleString('id-ID');
    }
</script>

<?php include 'layout/footer.php'; ?>