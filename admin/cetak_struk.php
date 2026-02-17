<?php
require_once '../config/koneksi.php';
$id = $_GET['id'];
$p = $conn->query("SELECT p.*, m.nama_member FROM tb_pesanan p LEFT JOIN tb_member m ON p.id_member = m.id_member WHERE id_pesanan = '$id'")->fetch();
$details = $conn->query("SELECT d.*, m.nama_menu FROM tb_detail_pesanan d JOIN tb_menu m ON d.id_menu = m.id_menu WHERE id_pesanan = '$id'")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Struk Rasya.co</title>
    <style>
        body { font-family: 'Courier New', monospace; width: 300px; font-size: 12px; }
        .center { text-align: center; }
        hr { border-top: 1px dashed black; }
    </style>
</head>
<body onload="window.print()">
    <div class="center">
        <strong>CAFE RASYA.CO</strong><br>
        Struk Pesanan: <?= $id ?><br>
        <?= $p['tgl_pesanan'] ?>
    </div>
    <hr>
    Pelanggan: <?= $p['nama_member'] ?? 'Umum' ?><br>
    Tipe: <?= $p['tipe_pemesanan'] ?> (Meja: <?= $p['id_meja'] ?? '-' ?>)
    <hr>
    <?php foreach($details as $d): ?>
        <?= $d['nama_menu'] ?> x<?= $d['jumlah'] ?> <br>
    <?php endforeach; ?>
    <hr>
    <strong>TOTAL: Rp <?= number_format($p['total_transaksi']) ?></strong>
    <p class="center">-- Terima Kasih --</p>
</body>
</html>