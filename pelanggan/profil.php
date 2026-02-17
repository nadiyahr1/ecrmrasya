<?php
session_start();
require_once '../config/koneksi.php';
include '../layout/header.php';

$id_m = $_SESSION['id_member'];
$stmt = $conn->prepare("SELECT m.*, l.nama_level FROM tb_member m JOIN tb_level_member l ON m.id_level = l.id_level WHERE m.id_member = ?");
$stmt->execute([$id_m]);
$user = $stmt->fetch();

// Logika Tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'riwayat';
?>

<div style="max-width: 900px; margin: 30px auto; padding: 0 20px;">
        <div style="background: linear-gradient(135deg, #d4af37, #f1c40f); padding: 30px; border-radius: 20px; color: white; box-shadow: 0 10px 20px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0; font-size: 14px; text-transform: uppercase; letter-spacing: 2px;"><?= $user['nama_level'] ?> MEMBER</p>
                <h2 style="margin: 10px 0; font-size: 32px;"><?= $user['nama_member'] ?></h2>
                <h1 style="margin: 0; font-size: 40px; font-weight: bold;"><?= number_format($user['total_poin']) ?> <span style="font-size: 18px;">Poin</span></h1>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0; font-size: 12px; opacity: 0.8;"><?= $user['email'] ?? 'Member@rasya.co' ?></p>
                <p style="margin: 0; font-size: 12px; opacity: 0.8;"><?= $user['no_telp'] ?></p>
            </div>
        </div>

        <div style="margin-top: 25px;">
            <div style="background: rgba(255,255,255,0.3); height: 8px; border-radius: 10px;">
                <div style="background: white; width: <?= min(($user['total_poin']/1000)*100, 100) ?>%; height: 100%; border-radius: 10px;"></div>
            </div>
            <p style="font-size: 12px; margin-top: 8px;">Poin dibutuhkan untuk level selanjutnya: 1,000 Poin</p>
        </div>
        <a href="edit_profil.php" style="position: absolute; bottom: 20px; right: 20px; background: rgba(0,0,0,0.2); color: white; padding: 8px 20px; border-radius: 30px; text-decoration: none; font-size: 14px;">EDIT PROFIL</a>
    </div>

    <div style="display: flex; gap: 30px; margin: 40px 0 20px 0; border-bottom: 2px solid #eee;">
        <a href="?tab=riwayat" style="padding-bottom: 10px; text-decoration: none; color: <?= $tab == 'riwayat' ? '#6F4E37' : '#888' ?>; border-bottom: 3px solid <?= $tab == 'riwayat' ? '#6F4E37' : 'transparent' ?>; font-weight: bold;">Riwayat Pesanan</a>
        <a href="?tab=voucher" style="padding-bottom: 10px; text-decoration: none; color: <?= $tab == 'voucher' ? '#6F4E37' : '#888' ?>; border-bottom: 3px solid <?= $tab == 'voucher' ? '#6F4E37' : 'transparent' ?>; font-weight: bold;">Voucher Saya</a>
        <a href="?tab=favorit" style="padding-bottom: 10px; text-decoration: none; color: <?= $tab == 'favorit' ? '#6F4E37' : '#888' ?>; border-bottom: 3px solid <?= $tab == 'favorit' ? '#6F4E37' : 'transparent' ?>; font-weight: bold;">Menu Favorit</a>
    </div>

    <div style="background: white; padding: 20px; border-radius: 15px;">
        <?php if($tab == 'riwayat'): ?>
            <h4>Daftar Pesanan Terakhir</h4>
            <?php elseif($tab == 'voucher'): ?>
                        <?php 
            $promos = $conn->query("SELECT * FROM tb_promo WHERE tipe_promo = 'Loyalty'")->fetchAll();
            foreach($promos as $p): 
            ?>
                <div style="display: flex; justify-content: space-between; align-items: center; border: 1px solid #eee; padding: 15px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <div style="font-size: 24px;">☕</div>
                        <div>
                            <strong style="font-size: 16px;"><?= $p['nama_promo'] ?></strong><br>
                            <small style="color: #888;">Berlaku s.d. 31 Mar 2026</small>
                        </div>
                    </div>
                    <button style="background: #6F4E37; color: white; border: none; padding: 8px 20px; border-radius: 8px; cursor: pointer;">Gunakan</button>
                </div>
            <?php endforeach; ?>

        <?php elseif($tab == 'favorit'): ?>
            <h4>Menu yang Sering Kamu Pesan</h4>
            <p style="color: #999;">Belum ada menu favorit.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../layout/footer.php'; ?>