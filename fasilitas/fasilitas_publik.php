<?php

include '../layout/header.php';
require_once '../config/koneksi.php';

// Ambil semua fasilitas yang tersedia dari database
$fasilitas = $conn->query("SELECT * FROM tb_fasilitas WHERE status_fasilitas = 'Tersedia'")->fetchAll();
?>

<div style="max-width: 1100px; margin: 40px auto; padding: 50px 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div style="text-align: center; margin-bottom: 50px;">
        <h2 style="color: #6F4E37; font-size: 32px; margin-bottom: 10px;">Fasilitas Rasya.co</h2>
        <p style="color: #888; font-size: 16px;">Nikmati berbagai fasilitas unggulan kami untuk pengalaman yang tak terlupakan bersama teman dan keluarga.</p>
        <div style="width: 80px; height: 4px; background: #6F4E37; margin: 20px auto; border-radius: 2px;"></div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;">
        <?php foreach ($fasilitas as $f): ?>
            <div style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; transition: transform 0.3s ease;">
                
                <div style="position: relative;">
                    <img src="../assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>" alt="<?= $f['nama_fasilitas'] ?>" style="width: 100%; height: 230px; object-fit: cover;">
                    <div style="position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.9); padding: 5px 15px; border-radius: 30px; font-weight: bold; color: #6F4E37; font-size: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        Rp <?= number_format($f['harga']) ?> / <?= $f['satuan'] ?>
                    </div>
                </div>

                <div style="padding: 25px;">
                    <h3 style="margin: 0 0 10px 0; color: #333; font-size: 22px;"><?= $f['nama_fasilitas'] ?></h3>
                    
                    <div style="height: 60px; overflow: hidden; margin-bottom: 20px;">
                        <p style="color: #777; font-size: 14px; line-height: 1.5; margin: 0;">
                            <?= $f['deskripsi'] ?>
                        </p>
                    </div>

                    <a href="booking_fasilitas.php?id=<?= $f['id_fasilitas'] ?>" 
                       style="display: block; text-align: center; padding: 14px; background: #6F4E37; color: white; text-decoration: none; border-radius: 12px; font-weight: bold; font-size: 15px; transition: background 0.3s;">
                       Pesan Fasilitas Ini
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../layout/footer.php'; ?>