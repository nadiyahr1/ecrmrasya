<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/koneksi.php';

// Cek parameter ID Fasilitas
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Pilih fasilitas terlebih dahulu!'); window.location='index.php';</script>";
    exit;
}

$id_f = $_GET['id'];

// Ambil data fasilitas
$stmt = $conn->prepare("SELECT * FROM tb_fasilitas WHERE id_fasilitas = ?");
$stmt->execute([$id_f]);
$fasilitas = $stmt->fetch();

if (!$fasilitas) {
    echo "<script>alert('Fasilitas tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

include '../layout/header.php';
?>

<style>
    body { padding-top: 80px; font-family: 'Segoe UI', sans-serif; background-color: #FAFAFA; }
    .container { max-width: 1000px; margin: 0 auto; padding: 20px; min-height: 80vh; }
    
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #6F4E37; text-decoration: none; font-weight: bold; margin-bottom: 20px; transition: 0.3s; }
    .btn-back:hover { transform: translateX(-5px); }

    .detail-card { background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); display: flex; gap: 40px; padding: 30px; margin-bottom: 40px; }
    
    .detail-img-wrapper { flex: 1; border-radius: 15px; overflow: hidden; background: #eee; max-height: 400px; }
    .detail-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    
    .detail-info { flex: 1; display: flex; flex-direction: column; justify-content: center; }
    .status-badge { padding: 5px 15px; border-radius: 15px; font-size: 14px; font-weight: bold; display: inline-block; margin-bottom: 15px; width: fit-content; color: white; }
    
    .detail-title { font-size: 32px; margin: 0 0 15px 0; color: #333; }
    .detail-price { font-size: 28px; font-weight: bold; color: #6F4E37; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 20px; }
    .detail-desc { font-size: 16px; color: #666; line-height: 1.8; margin-bottom: 30px; }

    .booking-form { background: #fdf8f5; padding: 20px; border-radius: 15px; border: 1px solid #f2e3d5; text-align: center; }
    .btn-booking { width: 100%; background: #6F4E37; color: white; border: none; padding: 15px; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .btn-booking:hover { background: #5a3d2b; }

    @media (max-width: 768px) { .detail-card { flex-direction: column; } }
</style>

<div class="container">
    <a href="javascript:history.back()" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>

    <div class="detail-card">
        <div class="detail-img-wrapper">
            <img src="../assets/gambar/fasilitas/<?= htmlspecialchars($fasilitas['foto_fasilitas']) ?>" alt="<?= htmlspecialchars($fasilitas['nama_fasilitas']) ?>">
        </div>
        
        <div class="detail-info">
            <?php 
                $bg = ($fasilitas['status_fasilitas'] == 'Tersedia') ? '#28a745' : '#dc3545';
            ?>
            <span class="status-badge" style="background: <?= $bg ?>;">
                <?= htmlspecialchars($fasilitas['status_fasilitas']) ?>
            </span>
            
            <h1 class="detail-title"><?= htmlspecialchars($fasilitas['nama_fasilitas']) ?></h1>
            <div class="detail-price">Rp <?= number_format($fasilitas['harga']) ?> / <?= htmlspecialchars($fasilitas['satuan']) ?></div>
            
            <h3 style="margin-top: 0; margin-bottom: 10px; font-size: 18px;">Informasi Fasilitas:</h3>
            <p class="detail-desc">
                <?= nl2br(htmlspecialchars($fasilitas['deskripsi'])) ?>
            </p>

            <div class="booking-form">
                <?php if ($fasilitas['status_fasilitas'] == 'Tersedia'): ?>
                    <p style="font-size: 14px; color: #888; margin-bottom: 15px;">Tertarik menggunakan fasilitas ini?</p>
                    <a href="reservasi.php?id=<?= $fasilitas['id_fasilitas'] ?>" class="btn-booking" style="text-decoration: none; display: block;">
                        <i class="fa-solid fa-calendar-check"></i> Reservasi Sekarang
                    </a>
                <?php else: ?>
                    <button disabled class="btn-booking" style="background: #ccc; cursor: not-allowed;">
                        <i class="fa-solid fa-ban"></i> Sedang Tidak Tersedia
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>