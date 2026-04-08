<?php include 'layout/header.php'; ?>

<div class="page-container">
    <a href="index.php?controller=home&action=index#promo" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
    </a>

    <div class="detail-promo-card">
        <div class="promo-img-wrapper">
            <img src="assets/gambar/promo/<?= htmlspecialchars($promo['foto_promo']) ?>" alt="<?= htmlspecialchars($promo['nama_promo']) ?>">
        </div>

        <div class="promo-info">
            <div class="badge-tipe">Promo <?= str_replace('_', ' ', $promo['tipe_promo']) ?></div>
            <h1 class="promo-detail-title"><?= htmlspecialchars($promo['nama_promo']) ?></h1>

            <div class="promo-date">
                <i class="fa-regular fa-calendar-days"></i>
                Berlaku: <?= date('d M', strtotime($promo['tgl_mulai'])) ?> - <?= date('d M Y', strtotime($promo['tgl_selesai'])) ?>
            </div>

            <p class="promo-desc">
                <?= nl2br(htmlspecialchars($promo['deskripsi'])) ?>
            </p>

            <div class="syarat-box">
                <h4>Syarat & Ketentuan</h4>
                <ul class="syarat-list">
                    <li><i class="fa-solid fa-check-circle"></i>
                        Potongan yang didapat: <strong>
                            <?php
                            if ($promo['tipe_potongan'] == 'Persen') echo $promo['potongan'] . '%';
                            elseif ($promo['tipe_potongan'] == 'Nominal') echo 'Rp ' . number_format($promo['potongan']);
                            else echo 'Free Item / Hadiah';
                            ?>
                        </strong>
                    </li>

                    <?php if ($promo['kuota'] > 0): ?>
                        <li><i class="fa-solid fa-check-circle"></i> Kuota penggunaan terbatas hanya untuk <?= $promo['kuota'] ?> orang.</li>
                    <?php else: ?>
                        <li><i class="fa-solid fa-check-circle"></i> Promo ini berlaku tanpa batas kuota (Unlimited).</li>
                    <?php endif; ?>

                    <?php if ($promo['tipe_promo'] == 'Level'): ?>
                        <li><i class="fa-solid fa-check-circle"></i> Hanya berlaku untuk member dengan level tertentu.</li>
                    <?php elseif ($promo['tipe_promo'] == 'Tukar_Poin'): ?>
                        <li><i class="fa-solid fa-check-circle"></i> Promo ini membutuhkan <strong><?= $promo['min_poin'] ?> Poin</strong> untuk diklaim.</li>
                    <?php endif; ?>

                    <?php if (!empty($promo['syarat_ketentuan'])): ?>
                        <li>
                            <i class="fa-solid fa-circle-info"></i>
                            <div>
                                <?= nl2br(htmlspecialchars($promo['syarat_ketentuan'])) ?>
                            </div>
                        </li>
                    <?php endif; ?>

                </ul>
            </div>

            <?php if (!empty($promo['kode_promo'])): ?>
                <div class="kode-box">
                    <p>Gunakan kode promo di bawah ini saat Checkout</p>
                    <span class="kode-text" id="textKode"><?= htmlspecialchars($promo['kode_promo']) ?></span>
                    <button class="btn-copy" onclick="copyKode()">
                        <i class="fa-regular fa-copy"></i> <span id="btnText">Salin Kode</span>
                    </button>
                </div>
            <?php else: ?>
                <div class="kode-box">
                    <p>Promo ini akan otomatis memotong harga pesanan Anda jika syarat terpenuhi, tanpa perlu memasukkan kode.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    function copyKode() {
        // Ambil teks kode
        var kode = document.getElementById("textKode").innerText;

        // Salin ke clipboard
        navigator.clipboard.writeText(kode).then(function() {
            // Ubah tombol jadi warna hijau sukses
            var btn = document.querySelector('.btn-copy');
            var btnText = document.getElementById('btnText');
            var icon = btn.querySelector('i');

            btn.classList.add('copied');
            btnText.innerText = 'Tersalin!';
            icon.className = 'fa-solid fa-check';

            // Kembalikan ke semula setelah 3 detik
            setTimeout(function() {
                btn.classList.remove('copied');
                btnText.innerText = 'Salin Kode';
                icon.className = 'fa-regular fa-copy';
            }, 3000);
        }).catch(function(err) {
            alert('Gagal menyalin kode. Silakan salin manual.');
        });
    }
</script>

<?php include 'layout/footer.php'; ?>