<?php include 'layout/header.php'; ?>

<div class="booking-container">
    <h2 class="booking-title">Form Booking Fasilitas</h2>

    <div class="booking-preview">
        <img src="assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>" alt="Preview">
        <div>
            <strong><?= $f['nama_fasilitas'] ?></strong>
            <span class="booking-price-badge">
                Rp <?= number_format($f['biaya'] ?? $f['harga'], 0, ',', '.') ?> / <?= $f['satuan'] ?>
            </span>
        </div>
    </div>

    <form action="index.php?controller=fasilitas&action=prosesBooking" method="POST">
        <input type="hidden" name="id_fasilitas" value="<?= $f['id_fasilitas'] ?>">

        <div class="form-group">
            <label class="form-label">Tanggal Penggunaan <span style="color:red;">*</span></label>
            <input type="date" name="tgl_sewa" required class="form-input" min="<?= date('Y-m-d') ?>">
        </div>

        <?php if ($f['satuan'] == 'Jam'): ?>
            <div class="form-group">
                <label class="form-label">Jam Mulai <span style="color:red;">*</span></label>
                <input type="time" name="jam_mulai" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Durasi (Jam) <span style="color:red;">*</span></label>
                <input type="number" name="durasi" min="1" value="1" required class="form-input">
            </div>
        <?php elseif ($f['satuan'] == 'Orang'): ?>
            <div class="form-group">
                <label class="form-label">Jumlah Orang <span style="color:red;">*</span></label>
                <input type="number" name="jumlah_orang" min="1" max="5" value="1" required class="form-input">
                <p style="font-size: 12px; color: #888; margin-top: 5px;">
                    <i class="fa-solid fa-circle-info"></i> Pemesanan dihitung per orang dan tidak terikat jam tertentu pada tanggal yang dipilih.
                    <br>
                    <i class="fa-solid fa-circle-exclamation"></i> Maksimal 5 orang per booking untuk fasilitas ini.
                </p>
            </div>
        <?php else: ?>
            <div class="form-group">
                <label class="form-label">Jumlah Pemesanan <span style="color:red;">*</span></label>
                <input type="number" name="jumlah_orang" min="1" value="1" required class="form-input">
            </div>
        <?php endif; ?>

        <div class="booking-actions">
            <a href="javascript:history.back()" class="btn-cancel-booking">Batal</a>
            <button type="submit" class="btn-submit-booking">Tambahkan ke Keranjang</button>
        </div>
    </form>
</div>

<?php include 'layout/footer.php'; ?>