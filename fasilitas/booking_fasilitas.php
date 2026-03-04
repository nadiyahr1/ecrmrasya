<?php
include '../layout/header.php';
require_once '../config/koneksi.php';

$id_f = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM tb_fasilitas WHERE id_fasilitas = ?");
$stmt->execute([$id_f]);
$f = $stmt->fetch();
?>

<div style="max-width: 500px; margin: 50px auto; padding: 30px; background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
    <h2 style="color: #6F4E37; margin-top: 0;">Form Pemesanan</h2>
    <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
        <img src="../assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>" width="80" height="80" style="border-radius: 10px; object-fit: cover;">
        <div>
            <strong style="font-size: 18px;"><?= $f['nama_fasilitas'] ?></strong><br>
            <span style="color: #6F4E37; font-weight: bold;">Rp <?= number_format($f['harga']) ?> / <?= $f['satuan'] ?></span>
        </div>
    </div>

    <form action="tambah_keranjang_fasilitas.php" method="POST">
        <input type="hidden" name="id_fasilitas" value="<?= $f['id_fasilitas'] ?>">
        <input type="hidden" name="satuan" value="<?= $f['satuan'] ?>">

        <div style="margin-bottom: 20px;">
            <label style="display:block; font-weight: bold; margin-bottom: 8px;">Pilih Tanggal</label>
            <input type="date" name="tgl_sewa" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;">
        </div>

        <?php if ($f['satuan'] == 'Jam'): ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                <div>
                    <label style="display:block; font-weight: bold; margin-bottom: 8px;">Jam Mulai</label>
                    <input type="time" name="jam_mulai" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;">
                </div>
                <div>
                    <label style="display:block; font-weight: bold; margin-bottom: 8px;">Durasi (Jam)</label>
                    <input type="number" name="durasi" min="1" placeholder="Cth: 2" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;">
                </div>
            </div>
        <?php else: ?>
            <div style="margin-bottom: 25px;">
                <label style="display:block; font-weight: bold; margin-bottom: 8px;">Jumlah Orang</label>
                <input type="number" name="jumlah_orang" min="1" placeholder="Cth: 5" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;">
                <input type="hidden" name="jam_mulai" value="00:00">
                <input type="hidden" name="durasi" value="1">
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 10px;">
            <a href="fasilitas_publik.php" style="flex: 1; text-align: center; padding: 15px; background: #eee; color: #333; text-decoration: none; border-radius: 8px; font-weight: bold;">Batal</a>
            <button type="submit" style="flex: 2; padding: 15px; background: #6F4E37; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                Konfirmasi & Masuk Keranjang
            </button>
        </div>
    </form>
</div>