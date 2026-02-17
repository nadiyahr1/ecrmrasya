<?php
include '../layout/header.php';
require_once '../config/koneksi.php';

$id = $_GET['id'];
$f = $conn->query("SELECT * FROM tb_fasilitas WHERE id_fasilitas = $id")->fetch();
?>

<div style="max-width: 500px; margin: 30px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
    <h3>Form Booking: <?= $f['nama_fasilitas'] ?></h3>
    <hr>
    <form action="tambah_keranjang_fasilitas.php" method="POST">
        <input type="hidden" name="id_fasilitas" value="<?= $id ?>">
        <input type="hidden" id="harga_jam" value="<?= $f['harga_per_jam'] ?>">

        <label>Tanggal Pakai:</label><br>
        <input type="date" name="tgl_booking" required style="width: 100%; padding: 8px; margin: 10px 0;"><br>

        <label>Jam Mulai:</label><br>
        <input type="time" name="jam_mulai" id="jam_mulai" required style="width: 100%; padding: 8px; margin: 10px 0;"><br>

        <label>Durasi (Jam):</label><br>
        <input type="number" name="durasi" id="durasi" min="1" value="1" required 
               style="width: 100%; padding: 8px; margin: 10px 0;" oninput="hitungTotal()">

        <div style="background: #f9f9f9; padding: 15px; margin-top: 15px; border-radius: 5px;">
            <p>Estimasi Selesai: <strong id="jam_selesai">-</strong></p>
            <h4>Total Harga: Rp <span id="display_total"><?= number_format($f['harga_per_jam']) ?></span></h4>
        </div>

        <button type="submit" style="width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 15px;">
            Add to Cart
        </button>
    </form>
</div>

<script>
function hitungTotal() {
    var harga = document.getElementById('harga_jam').value;
    var durasi = document.getElementById('durasi').value;
    var total = harga * durasi;
    document.getElementById('display_total').innerText = total.toLocaleString('id-ID');
}
</script>

<?php include '../layout/footer.php'; ?>