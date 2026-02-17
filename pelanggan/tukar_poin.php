<?php
session_start();
include '../layout/header.php';
require_once '../config/koneksi.php';

$id_m = $_SESSION['id_member'];
$promos = $conn->query("SELECT * FROM tb_promo WHERE tipe_promo = 'Loyalty'")->fetchAll();
$member = $conn->query("SELECT total_poin FROM tb_member WHERE id_member = $id_m")->fetch();
?>

<div style="padding: 20px;">
    <h2>🎁 Katalog Reward Member</h2>
    <p>Tukarkan poin kamu dengan voucher diskon menarik.</p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        <?php foreach($promos as $p): ?>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center;">
            <div style="font-size: 40px;">🎫</div>
            <h3><?= $p['nama_promo'] ?></h3>
            <p style="color: #28a745; font-weight: bold;">Diskon Rp <?= number_format($p['nominal_potongan']) ?></p>
            <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                <strong><?= $p['poin_dibutuhkan'] ?> Poin</strong>
            </div>
            <p style="font-size: 12px; color: #888;"><?= $p['keterangan'] ?></p>
            
            <button disabled style="width: 100%; padding: 10px; background: #eee; border: none; border-radius: 5px;">
                Gunakan Saat Checkout
            </button>
        </div>
        <?php endforeach; ?>
    </div>
</div>