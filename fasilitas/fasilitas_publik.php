<?php
include '../layout/header.php';
require_once '../config/koneksi.php';

// Ambil semua fasilitas yang tersedia
$fasilitas = $conn->query("SELECT * FROM tb_fasilitas WHERE status_fasilitas = 'Tersedia'")->fetchAll();
?>

<div style="padding: 20px;">
    <h2 style="text-align: center;">Fasilitas Seru di Rasya.co</h2>
    <p style="text-align: center;">Booking sekarang untuk pengalaman nongkrong yang lebih asik!</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 30px;">
        <?php foreach ($fasilitas as $f) : ?>
        <div style="background: white; border: 1px solid #ddd; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="height: 180px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                [ Foto <?= $f['nama_fasilitas'] ?> ]
            </div>
            <div style="padding: 15px;">
                <h3><?= $f['nama_fasilitas'] ?></h3>
                <p style="color: #6F4E37; font-weight: bold; font-size: 1.1em;">
                    Rp <?= number_format($f['harga_per_jam'], 0, ',', '.') ?> <span style="font-size: 0.8em; color: #888;">/ jam</span>
                </p>
                
                <a href="booking_detail.php?id=<?= $f['id_fasilitas'] ?>" 
                   style="display: block; text-align: center; padding: 10px; background: #6F4E37; color: white; text-decoration: none; border-radius: 5px;">
                   Book Now
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../layout/footer.php'; ?>