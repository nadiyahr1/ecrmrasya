<?php

$hari_ini = date('Y-m-d');

// 1. Mengambil Data untuk 4 Kotak Statistik
$pesanan_hari_ini = $conn->query("SELECT COUNT(*) FROM tb_pesanan WHERE DATE(tgl_pesanan) = '$hari_ini'")->fetchColumn();

// Asumsi kolom tanggal daftar di tb_member adalah tgl_daftar
$member_baru = $conn->query("SELECT COUNT(*) FROM tb_member WHERE DATE(tgl_daftar) = '$hari_ini'")->fetchColumn();

// Omzet hanya dihitung dari pesanan yang statusnya sudah 'Selesai'
$omzet_harian = $conn->query("SELECT SUM(total_transaksi) FROM tb_pesanan WHERE DATE(tgl_pesanan) = '$hari_ini' AND status = 'Selesai'")->fetchColumn();
$omzet_harian = $omzet_harian ? $omzet_harian : 0; // Jika tidak ada penjualan, set 0

// Peringatan Stok
$stok_menipis = $conn->query("SELECT * FROM tb_menu WHERE stok < 10 ORDER BY stok ASC")->fetchAll();
$jml_stok_tipis = count($stok_menipis);

// 2. Mengambil Data untuk Shortcut Pesanan "Menunggu Konfirmasi"
$pesanan_menunggu = $conn->query("SELECT p.*, m.nama_member 
                                  FROM tb_pesanan p 
                                  LEFT JOIN tb_member m ON p.id_member = m.id_member 
                                  WHERE p.status = 'Menunggu Konfirmasi' OR p.status = '' OR p.status IS NULL
                                  ORDER BY p.tgl_pesanan ASC LIMIT 5")->fetchAll();
?>

<style>
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 20px; margin-bottom: 30px; }
    .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #eee; display: flex; flex-direction: column; justify-content: center; }
    .stat-card h3 { margin: 0 0 5px 0; font-size: 28px; color: #333; }
    .stat-card p { margin: 0; color: #888; font-size: 14px; font-weight: 500; }
    
    /* Shortcut Table Style */
    .shortcut-section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #eee; }
    .shortcut-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .shortcut-table th { background: #f9f9f9; padding: 12px; text-align: left; font-size: 13px; color: #555; border-bottom: 2px solid #eee; }
    .shortcut-table td { padding: 12px; font-size: 14px; border-bottom: 1px solid #eee; }
    
    .btn-proses-cepat { background: #3b82f6; color: white; padding: 6px 12px; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: bold; }
    .btn-proses-cepat:hover { background: #2563eb; }
</style>

<div>
    <h2 style="margin: 0 0 5px 0; color: #333;">Ringkasan Statistik Harian</h2>
    <p style="color: #888; margin-top: 0; font-size: 14px;">Memantau performa Rasya.co pada tanggal <?= date('d F Y') ?></p>

    <div class="stats-grid">
        <div class="stat-card" style="border-left: 4px solid #3b82f6;">
            <h3><?= $pesanan_hari_ini ?></h3>
            <p>Total Pesanan Masuk</p>
        </div>
        <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
            <h3><?= $member_baru ?></h3>
            <p>Member Baru Hari Ini</p>
        </div>
        <div class="stat-card" style="border-left: 4px solid #10b981;">
            <h3 style="color: #10b981;">Rp <?= number_format($omzet_harian, 0, ',', '.') ?></h3>
            <p>Omzet Harian Selesai</p>
        </div>
        <div class="stat-card" style="border-left: 4px solid <?= $jml_stok_tipis > 0 ? '#ef4444' : '#f59e0b' ?>;">
            <h3 style="color: <?= $jml_stok_tipis > 0 ? '#ef4444' : '#333' ?>;"><?= $jml_stok_tipis ?> Menu</h3>
            <p>Peringatan Stok Rendah (< 10)</p>
        </div>
    </div>

    <div class="shortcut-section">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: #333;">⚠️ Perlu Diproses Segera</h3>
            <a href="index.php?page=data_pesanan&tab=Menunggu Konfirmasi" style="font-size: 13px; color: #6F4E37; text-decoration: none; font-weight: bold;">Lihat Semua Pesanan →</a>
        </div>
        
        <?php if(count($pesanan_menunggu) > 0): ?>
            <table class="shortcut-table">
                <thead>
                    <tr>
                        <th>No. Order</th>
                        <th>Pelanggan</th>
                        <th>Waktu</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pesanan_menunggu as $pm): ?>
                    <tr>
                        <td style="font-weight: bold;">#<?= $pm['id_pesanan'] ?></td>
                        <td><?= $pm['nama_member'] ? $pm['nama_member'] : '<i>Umum</i>' ?></td>
                        <td><?= date('H:i', strtotime($pm['tgl_pesanan'])) ?> WIB</td>
                        <td style="font-weight: bold;">Rp <?= number_format($pm['total_transaksi'], 0, ',', '.') ?></td>
                        <td>
                            <a href="index.php?page=data_pesanan&tab=Menunggu Konfirmasi" class="btn-proses-cepat">Proses Sekarang</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 20px; color: #888;">
                <span style="font-size: 40px; display: block; margin-bottom: 10px;">🎉</span>
                Tidak ada pesanan yang menunggu konfirmasi. Semua sudah tertangani!
            </div>
        <?php endif; ?>
    </div>
</div>