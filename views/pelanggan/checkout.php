<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'layout/header.php';

// Inisialisasi variabel perhitungan
$sub_m = 0;
$qty_m = 0;
$sub_f = 0;
// Tentukan persen pajak, misal 10%
$persen_pajak = 10;
// Pajak dihitung nanti setelah subtotal menu & fasilitas terjumlah di bawah

// Ambil info user
$nama_pelanggan = $_SESSION['nama_member'] ?? 'Pelanggan';
$telp_pelanggan = $_SESSION['no_telp'] ?? '-';
$poin_pelanggan = $_SESSION['poin'] ?? 0;
?>

<div class="page-container">
    <h1 class="page-title" style="text-align: left; margin-bottom: 30px;">Checkout Pesanan</h1>

    <form action="index.php?controller=checkout&action=simpanPesanan" method="POST" id="formCheckout">
        <div class="checkout-wrapper">

            <div class="checkout-main">

                <div class="co-section">
                    <h3 class="co-section-title"><i class="fa-regular fa-address-card"></i> Informasi Pemesan</h3>
                    <div class="co-user-info">
                        <strong><?= htmlspecialchars($nama_pelanggan) ?></strong>
                        <i class="fa-solid fa-phone" style="font-size: 12px; margin-right: 5px;"></i> <?= htmlspecialchars($telp_pelanggan) ?>
                    </div>
                </div>

                <div class="co-section">
                    <h3 class="co-section-title"><i class="fa-solid fa-bag-shopping"></i> Detail Pesanan</h3>
                    <div class="co-item-list">

                        <?php if (!empty($data_menu)): ?>
                            <?php foreach ($data_menu as $m):
                                $sub_m += $m['subtotal'];
                            ?>
                                <input type="hidden" name="selected_menu[]" value="<?= $m['id_menu'] ?>">

                                <div class="co-item-card">
                                    <img src="<?= $base_url ?>assets/gambar/menu/<?= $m['foto'] ?>" class="co-item-img" alt="Menu">
                                    <div class="co-item-details">
                                        <div class="co-item-name"><?= htmlspecialchars($m['nama_menu']) ?></div>
                                        <div class="co-item-price-qty">
                                            Rp <?= number_format($m['harga'], 0, ',', '.') ?> &nbsp; x <?= $m['qty'] ?>
                                        </div>
                                    </div>
                                    <div class="co-item-subtotal">
                                        Rp <?= number_format($m['subtotal'], 0, ',', '.') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($data_fasilitas)): ?>
                            <?php foreach ($data_fasilitas as $f):
                                $sub_f += $f['subtotal'];
                            ?>
                                <input type="hidden" name="checkout_fasilitas[]" value="<?= $f['id_fasilitas'] ?>">

                                <div class="co-item-card">
                                    <img src="<?= $base_url ?>assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>" class="co-item-img" alt="Fasilitas">
                                    <div class="co-item-details">
                                        <div class="co-item-name">[Fasilitas] <?= htmlspecialchars($f['nama_fasilitas']) ?></div>
                                        <div class="co-item-price-qty">
                                            Rp <?= number_format($f['harga'], 0, ',', '.') ?> &nbsp; x <?= $f['pengali'] ?> <?= $f['satuan'] ?>
                                        </div>
                                    </div>
                                    <div class="co-item-subtotal">
                                        Rp <?= number_format($f['subtotal'], 0, ',', '.') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php
                        // <!-- HITUNG TOTAL DI SINI (Letakkan SETELAH semua perulangan foreach selesai) -->
                        $subtotal_kotor = $sub_m + $sub_f;
                        $pajak = $subtotal_kotor * ($persen_pajak / 100);
                        $totalBase = $subtotal_kotor + $pajak; // Ini adalah angka yang akan dikirim ke JS 
                        ?>

                    </div>
                </div>

                <div class="co-section">
                    <h3 class="co-section-title"><i class="fa-solid fa-bell-concierge"></i> Tipe Pemesanan</h3>
                    <div class="co-radio-group">
                        <label class="co-radio-card">
                            <input type="radio" name="tipe_pemesanan" value="Dine-in" id="tipe_dinein" required onchange="toggleMeja()">
                            <div class="co-radio-content">
                                <i class="fa-solid fa-store"></i>
                                <span>Makan di Tempat<br>(Dine In)</span>
                            </div>
                        </label>
                        <label class="co-radio-card">
                            <input type="radio" name="tipe_pemesanan" value="Takeaway" id="tipe_takeaway" onchange="toggleMeja()">
                            <div class="co-radio-content">
                                <i class="fa-solid fa-bag-shopping"></i>
                                <span>Ambil Sendiri<br>(Take Away)</span>
                            </div>
                        </label>
                    </div>

                    <div id="box_meja" style="display: none; margin-top: 20px;">
                        <label style="font-weight: bold; font-size: 14px; color: #555;">Pilih Nomor Meja:</label>
                        <select name="id_meja" id="input_meja" class="co-input">
                            <option value="">-- Pilih Meja Kosong --</option>
                            <?php if (!empty($meja_tersedia)): ?>
                                <?php foreach ($meja_tersedia as $meja): ?>
                                    <option value="<?= htmlspecialchars($meja['id_meja']) ?>">
                                        Meja <?= htmlspecialchars($meja['no_meja'] ?? $meja['id_meja']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Maaf, saat ini semua meja penuh.</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="co-section">
                    <h3 class="co-section-title"><i class="fa-solid fa-wallet"></i> Metode Pembayaran</h3>
                    <div class="co-radio-group">
                        <label class="co-radio-card">
                            <input type="radio" name="metode" value="Transfer" required>
                            <div class="co-radio-content">
                                <i class="fa-solid fa-money-bill-transfer"></i>
                                <span>Transfer Bank / E-Wallet</span>
                            </div>
                        </label>
                        <label class="co-radio-card">
                            <input type="radio" name="metode" value="Tunai">
                            <div class="co-radio-content">
                                <i class="fa-solid fa-cash-register"></i>
                                <span>Bayar di Kasir<br>(Tunai)</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="co-section">
                    <h3 class="co-section-title"><i class="fa-solid fa-ticket"></i> Voucher / Promo</h3>

                    <div class="co-voucher-box" style="display: flex; gap: 8px;">
                        <input type="text" id="kode_promo" class="co-voucher-input" style="flex: 1;" placeholder="Masukkan kode promo...">
                        <input type="hidden" name="id_promo" id="id_promo_input" value="">

                        <button type="button" class="btn-voucher-action" style="background:#6F4E37; color:white; border: none; padding: 10px 15px; border-radius: 8px; font-weight: bold; cursor: pointer;" onclick="pakaiPromoManual()">Pakai</button>
                        <button type="button" class="btn-voucher-action" style="background:#f5f5f5; color:#333; border: 1px solid #ddd; padding: 10px 15px; border-radius: 8px; cursor: pointer;" onclick="bukaModalVoucher()">Lihat Pilihan Promo</button>
                    </div>
                    <div id="info_promo_diklaim" style="display: none; margin-top: 15px; padding: 12px; background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 8px; color: #2e7d32;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
                            <strong>Promo Digunakan:</strong> <span id="text_nama_promo"></span>
                        </div>
                        <div id="info_tukar_poin" style="display: none; font-size: 13px; margin-top: 5px; color: #d32f2f;">
                            <i class="fa-solid fa-coins"></i> Tukar <strong id="jml_poin_tukar">0</strong> Poin
                        </div>
                    </div>
                    <div class="co-points-info">
                        <i class="fa-solid fa-coins"></i> Poin Anda saat ini: <strong><?= number_format($poin_pelanggan, 0, ',', '.') ?> Pts</strong>
                    </div>
                    <p style="font-size: 12px; color: #888; margin-top: 10px;">* Potongan harga voucher akan dihitung otomatis saat Anda membuat pesanan.</p>
                </div>

                <div class="co-section">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: bold; color: #333;">
                        <input type="checkbox" id="check_catatan" onchange="toggleCatatan()" style="width: 18px; height: 18px;">
                        Tambahan Catatan (Opsional)
                    </label>
                    <div id="box_catatan" style="display: none; margin-top: 15px;">
                        <textarea name="catatan" class="co-input" rows="3" placeholder="Contoh: Sambal dipisah, Jangan terlalu pedas..."></textarea>
                    </div>
                </div>

                <a href="index.php?controller=keranjang&action=index" class="btn-back" style="margin-top: 10px; display: inline-flex;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Keranjang
                </a>
            </div>

            <div class="checkout-sidebar">
                <h3 class="co-section-title" style="border-bottom: none; padding-bottom: 0;"><i class="fa-solid fa-receipt"></i> Rincian Pembayaran</h3>

                <div class="summary-line">
                    <span>Subtotal Menu</span>
                    <span>Rp <?= number_format($sub_m, 0, ',', '.') ?></span>
                </div>
                <div class="summary-line">
                    <span>Subtotal Fasilitas</span>
                    <span>Rp <?= number_format($sub_f, 0, ',', '.') ?></span>
                </div>
                <div class="summary-line">
                    <span>Pajak (<?= $persen_pajak ?>%)</span>
                    <span id="nilai_pajak_display">Rp <?= number_format($pajak, 0, ',', '.') ?></span>
                </div>

                <div class="co-summary-row text-success" id="baris_free_produk" style="display: none; justify-content: space-between; color: #e74c3c; font-weight: bold;">
                    <span>Free Produk</span>
                    <span id="nama_free_produk"></span>
                </div>
                <div class="summary-line" id="baris_diskon" style="display: none; color: #e74c3c; font-weight: bold; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; width: 100%;">
                        <span id="label_diskon">Potongan</span>
                        <span id="nilai_diskon">- Rp 0</span>
                    </div>
                    <div id="info_gratis_produk" style="display: none; font-size: 12px; color: #2e7d32; margin-top: 4px; text-align: right;">
                        <i class="fa-solid fa-gift"></i> Gratis: <span id="nama_produk_free"></span>
                    </div>
                </div>

                <div class="summary-total">
                    <span>Total Tagihan</span>
                    <span id="total_akhir">Rp <?= number_format($totalBase, 0, ',', '.') ?></span>
                </div>

                <button type="submit" class="btn-bayar">Buat Pesanan Sekarang</button>
            </div>
        </div>
    </form>
</div>

<div id="modalVoucher" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(3px);">
    <div style="background: white; padding: 25px; border-radius: 16px; width: 90%; max-width: 450px; max-height: 80vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
            <h3 style="margin: 0; color: #333;"><i class="fa-solid fa-tags"></i> Pilih Promo/Voucher</h3>
            <button type="button" onclick="tutupModalVoucher()" style="background: #f5f5f5; border: none; width: 35px; height: 35px; border-radius: 50%; font-size: 18px; cursor: pointer; color: #555;"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php if (!empty($promo_tersedia)): ?>
                <?php foreach ($promo_tersedia as $p): ?>
                    <div style="border: 1px solid #eaeaea; padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s;" onmouseover="this.style.borderColor='#6F4E37'" onmouseout="this.style.borderColor='#eaeaea'">
                        <div>
                            <strong style="display: block; color: #333; margin-bottom: 5px; font-size: 15px;"><?= htmlspecialchars($p['nama_promo']) ?></strong>
                            <span style="font-size: 13px; color: #888;">Kode: <strong style="color: #6F4E37;"><?= htmlspecialchars($p['kode_promo']) ?></strong></span><br>
                            <span style="font-size: 12px; color: #e74c3c; display: block; margin-top: 5px;"><i class="fa-regular fa-calendar-xmark"></i> Berlaku s/d <?= date('d M Y', strtotime($p['tgl_selesai'])) ?></span>
                        </div>
                        <button type="button" onclick="pilihVoucherModal('<?= htmlspecialchars($p['kode_promo']) ?>', '<?= $p['id_promo'] ?>')" style="background: #6F4E37; color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: bold; transition: 0.3s;" onmouseover="this.style.background='#5a3d2b'" onmouseout="this.style.background='#6F4E37'">Pilih</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 30px 20px; color: #888;">
                    <i class="fa-solid fa-ticket-simple" style="font-size: 40px; color: #ddd; margin-bottom: 10px;"></i>
                    <p style="margin: 0;">Maaf, tidak ada promo yang tersedia saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    let pajakPersen = <?= $persen_pajak ?>; // Ambil dari PHP agar sinkron
    let totalBelanjaDasar = <?= $subtotal_kotor ?>; // Subtotal SEBELUM pajak
    let diskonPromoAktif = 0;

    // Promo
    let tipePromoAktif = '';
    let namaFreeProdukAktif = '';
    let nilaiAsliPromo = 0;

    function toggleMeja() {
        let isDineIn = document.getElementById('tipe_dinein').checked;
        let boxMeja = document.getElementById('box_meja');
        let inputMeja = document.getElementById('input_meja');

        if (isDineIn) {
            boxMeja.style.display = 'block';
            inputMeja.setAttribute('required', 'required');
        } else {
            boxMeja.style.display = 'none';
            inputMeja.removeAttribute('required');
            inputMeja.value = "";
        }
    }

    function toggleCatatan() {
        let isChecked = document.getElementById('check_catatan').checked;
        document.getElementById('box_catatan').style.display = isChecked ? 'block' : 'none';
    }

    function bukaModalVoucher() {
        document.getElementById('modalVoucher').style.display = 'flex';
    }

    function tutupModalVoucher() {
        document.getElementById('modalVoucher').style.display = 'none';
    }

    // Pemicu dari tombol "Pakai" manual
    function pakaiPromoManual() {
        let kode = document.getElementById('kode_promo').value;
        if (kode.trim() === '') {
            alert('Silakan masukkan kode promo terlebih dahulu!');
            return;
        }
        prosesCekPromo(kode, ''); // id_promo dikosongkan agar dicari via kode
    }

    // Pemicu dari tombol "Pilih" di modal
    function pilihVoucherModal(kode, id_promo) {
        document.getElementById('kode_promo').value = kode;
        tutupModalVoucher();
        prosesCekPromo(kode, id_promo);
    }

    // Fungsi Utama AJAX (Live Check)
    function prosesCekPromo(kode_promo, id_promo) {
        let formData = new FormData();
        formData.append('total_belanja', totalBelanjaDasar);
        if (kode_promo) formData.append('kode_promo', kode_promo);
        if (id_promo) formData.append('id_promo', id_promo);

        let listMenu = document.querySelectorAll('input[name="selected_menu[]"]');
        listMenu.forEach(menu => {
            formData.append('selected_menu[]', menu.value);
        });

        fetch('index.php?controller=checkout&action=cekPromo', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    alert(res.message); // Muncul pop-up sukses

                    const info = res.data; // Mengambil objek data dari server
                    diskonPromoAktif = parseFloat(info.potongan);
                    tipePromoAktif = info.tipe_potongan;
                    namaFreeProdukAktif = info.nama_bonus;
                    nilaiAsliPromo = parseFloat(info.nilai_asli_potongan);

                    // --- PENANGANAN ANTI-CRASH (Cek elemen sebelum diisi) ---
                    let inputPromo = document.getElementById('id_promo_input');
                    if (inputPromo) inputPromo.value = res.data.id_promo;

                    let infoPromo = document.getElementById('info_promo_diklaim');
                    if (infoPromo) infoPromo.style.display = 'block';

                    let textNama = document.getElementById('text_nama_promo');
                    if (textNama) textNama.innerText = res.data.nama_promo;

                    let boxPoin = document.getElementById('info_tukar_poin');
                    let valPoin = document.getElementById('jml_poin_tukar');

                    if (parseInt(res.data.min_poin) > 0) {
                        if (boxPoin) boxPoin.style.display = 'block';
                        if (valPoin) valPoin.innerText = res.data.min_poin;
                    } else {
                        if (boxPoin) boxPoin.style.display = 'none';
                    }

                    let barisDiskon = document.getElementById('baris_diskon');
                    let boxFree = document.getElementById('info_gratis_produk');
                    let labelPromo = document.getElementById('label_promo');
                    let textProdukFree = document.getElementById('nama_produk_free');

                    if (barisDiskon) barisDiskon.style.display = 'flex';
                    if (boxFree) boxFree.style.display = 'none';

                    let potongan = parseFloat(res.data.potongan);

                    if (res.data.tipe_potongan === 'Persen') {
                        diskonPromoAktif = totalBelanjaDasar * (potongan / 100);
                        if (labelPromo) labelPromo.innerText = "Diskon (" + potongan + "%)";
                    } else if (res.data.tipe_potongan === 'Nominal') {
                        diskonPromoAktif = potongan;
                        if (labelPromo) labelPromo.innerText = "Potongan Harga";
                    } else if (res.data.tipe_potongan === 'Produk') {
                        diskonPromoAktif = 0;
                        if (labelPromo) labelPromo.innerText = "Promo Produk";
                        if (boxFree) boxFree.style.display = 'block';
                        if (textProdukFree) textProdukFree.innerText = res.data.info_produk;
                    }

                    perbaruiTampilanTotal();
                } else {
                    alert(res.message);
                    batalkanPromo();
                }
            })
            .catch(err => {
                console.error("Detail Error JS:", err);
                // Alert diganti agar kita tahu bahwa yang error adalah JS-nya, bukan Backend
                alert("Ada sedikit masalah tampilan, tetapi backend aman. Cek console browser.");
                batalkanPromo();
            });
    }

    function batalkanPromo() {
        let inputPromo = document.getElementById('id_promo_input');
        if (inputPromo) inputPromo.value = '';
        diskonPromoAktif = 0;

        let infoPromo = document.getElementById('info_promo_diklaim');
        if (infoPromo) infoPromo.style.display = 'none';

        let boxFree = document.getElementById('info_gratis_produk');
        if (boxFree) boxFree.style.display = 'none';

        perbaruiTampilanTotal();
    }

    // Update Angka di Layar (Anti-Crash)
    function perbaruiTampilanTotal() {
        // Hitung Pajak Berdasarkan Subtotal Dasar
        let nilaiPajak = totalBelanjaDasar * (pajakPersen / 100);

        // Total Akhir = (Subtotal + Pajak) - Diskon
        let totalAkhir = (totalBelanjaDasar + nilaiPajak) - diskonPromoAktif;
        if (totalAkhir < 0) totalAkhir = 0;

        // Update Tampilan Pajak di Layar
        let pajakDisplay = document.getElementById('nilai_pajak_display');
        if (pajakDisplay) {
            pajakDisplay.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(nilaiPajak);
        }

        let barisDiskon = document.getElementById('baris_diskon');
        let nilaiDiskon = document.getElementById('nilai_diskon');
        let labelPromo = document.getElementById('label_diskon'); // Pastikan ID ini sesuai dengan <span> atau <p> labelnya

        let barisFreeProduk = document.getElementById('baris_free_produk');
        let namaFreeProduk = document.getElementById('nama_free_produk');

        let inputPromo = document.getElementById('id_promo_input');
        let idPromoTerpakai = inputPromo ? inputPromo.value : '';

        if (idPromoTerpakai !== '') {

            // 1. JIKA TIPE PRODUK (FREE)
            if (tipePromoAktif === 'Produk') {
                if (barisFreeProduk) {
                    barisFreeProduk.style.display = 'flex';
                    if (namaFreeProduk) namaFreeProduk.innerText = namaFreeProdukAktif;
                }
                if (barisDiskon) barisDiskon.style.display = 'none';
            }

            // 2. JIKA TIPE PERSEN ATAU NOMINAL
            else {
                if (barisFreeProduk) barisFreeProduk.style.display = 'none';
                if (barisDiskon) barisDiskon.style.display = 'flex';

                // --- BAGIAN INI UNTUK MENGUBAH TEKS LABEL ---
                if (labelPromo) {
                    if (tipePromoAktif === 'Persen') {
                        labelPromo.innerText = `Potongan (${nilaiAsliPromo}%)`;
                    } else if (tipePromoAktif === 'Nominal') {
                        // Format angka nominal agar ada titik ribuan (misal: 5.000)
                        let formatNominal = new Intl.NumberFormat('id-ID').format(nilaiAsliPromo);
                        labelPromo.innerText = `Potongan (${formatNominal})`;
                    } else {
                        labelPromo.innerText = 'Potongan';
                    }
                }

                // Tampilkan nominal diskon yang dihitung
                if (nilaiDiskon) {
                    nilaiDiskon.innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(diskonPromoAktif);
                }
            }
        } else {
            // Sembunyikan semua jika tidak ada promo
            if (barisDiskon) barisDiskon.style.display = 'none';
            if (barisFreeProduk) barisFreeProduk.style.display = 'none';
        }

        // Update Total Akhir
        let totalAkhirElemen = document.getElementById('total_akhir');
        if (totalAkhirElemen) {
            totalAkhirElemen.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalAkhir);
        }
    }
</script>

<?php include 'layout/footer.php'; ?>