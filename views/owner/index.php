<style>
    /* Menggunakan Grid dengan 2 kolom sama besar */
    .stat-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 20px 20px;
        /* Padding besar agar card tinggi dan gagah */
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        border: 1px solid #f0f0f0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* transition: transform 0.2s ease; */
    }

    /* Garis warna di sebelah kiri */
    .stat-card.green {
        border-left: 6px solid #10b981;
    }

    .stat-card.blue {
        border-left: 6px solid #3b82f6;
    }

    .stat-card.orange {
        border-left: 6px solid #f59e0b;
    }

    .stat-card.purple {
        border-left: 6px solid #8b5cf6;
    }

    .stat-number {
        font-size: 20px;
        font-weight: bold;
        color: #333;
        margin: 12px 0 5px 0;
    }

    .stat-label {
        font-size: 12px;
        color: #888;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 1px;
    }

    /* Judul di atas setiap baris */
    .section-title {
        margin-top: 10px;
        margin-bottom: 15px;
        color: #555;
        font-size: 17px;
        border-bottom: 2px solid #eee;
        padding-bottom: 8px;
        display: inline-block;
    }

    /* Desain Panel Bawah */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 40px;
    }

    .card-panel {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        border: 1px solid #f0f0f0;
    }

    .table-mini {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .table-mini th {
        text-align: left;
        font-size: 13px;
        color: #888;
        padding: 12px 10px;
        border-bottom: 2px solid #eee;
    }

    .table-mini td {
        padding: 12px 10px;
        font-size: 14px;
        border-bottom: 1px solid #f9f9f9;
        color: #444;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: bold;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }
</style>

<!-- <div>
    <h2 style="margin: 0 0 5px 0; color: #333;">Dashboard Pemilik</h2>
    <p style="margin: 0 0 25px 0; color: #777; font-size: 14px;">Ringkasan performa bisnis dan retensi pelanggan Rasya.co.</p>
</div> -->

<div class="welcome-section">
    <h2>Selamat Datang, <?= $_SESSION['nama'] ?? 'Owner' ?>!</h2>
    <p>Berikut adalah ringkasan analitik pelanggan dan performa E-CRM Rasya.co.</p>
</div>

<div class="section-title">Performa Bulan <?= $nama_bulan_ini ?></div>
<div class="stat-row">
    <div class="stat-card green">
        <div class="stat-label">Total Omzet Pendapatan</div>
        <div class="stat-number">Rp <?= number_format($omzet_bulan_ini ?? 0, 0, ',', '.') ?></div>
    </div>
    <div class="stat-card blue">
        <div class="stat-label">Total Pesanan Selesai</div>
        <div class="stat-number"><?= number_format($total_pesanan ?? 0) ?> Transaksi</div>
    </div>
</div>

<div class="section-title" style="margin-top: 15px;">Akumulasi Tahun <?= $tahun_ini ?></div>
<div class="stat-row">
    <div class="stat-card orange">
        <div class="stat-label">Total Member</div>
        <div class="stat-number"><?= number_format($total_member ?? 0) ?> Orang</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-label">Total Poin Terpakai</div>
        <div class="stat-number"><?= number_format($poin_terpakai ?? 0) ?> Poin</div>
    </div>
</div>


<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; margin-bottom: 20px;">
    
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #333;"><i class="fa-solid fa-medal" style="color:#f59e0b;"></i> Top 5 Pelanggan Loyal</h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left;">
                    <th style="padding: 10px; border-bottom: 2px solid #eee;">Nama Pelanggan</th>
                    <th style="padding: 10px; border-bottom: 2px solid #eee;">Level</th>
                    <th style="padding: 10px; border-bottom: 2px solid #eee;">Kunjungan</th>
                    <th style="padding: 10px; border-bottom: 2px solid #eee; text-align: right;">Total Belanja</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($top_customers)): ?>
                    <tr><td colspan="4" style="text-align: center; padding: 15px; color: #888;">Belum ada data pelanggan.</td></tr>
                <?php else: ?>
                    <?php foreach($top_customers as $top): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; font-weight: bold; color: #333;"><?= htmlspecialchars($top['nama_member']) ?></td>
                        <td style="padding: 10px;">
                            <span style="background: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;"><?= $top['nama_level'] ?></span>
                        </td>
                        <td style="padding: 10px;"><?= $top['jumlah_kunjungan'] ?> kali</td>
                        <td style="padding: 10px; text-align: right; color: #10b981; font-weight: bold;">Rp <?= number_format($top['total_belanja'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #333;"><i class="fa-solid fa-chart-pie" style="color:#3b82f6;"></i> Persebaran Level Member</h3>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php 
            // Hitung total semua member untuk persentase
            $total_semua = 0;
            foreach($level_member as $lvl) { $total_semua += $lvl['total']; }
            
            if($total_semua == 0): ?>
                <div style="text-align: center; padding: 15px; color: #888;">Belum ada member.</div>
            <?php else: ?>
                <?php foreach($level_member as $lvl): 
                    $persen = round(($lvl['total'] / $total_semua) * 100);
                ?>
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 5px; font-weight: bold;">
                        <span><?= $lvl['nama_level'] ?></span>
                        <span><?= $lvl['total'] ?> Orang (<?= $persen ?>%)</span>
                    </div>
                    <div style="width: 100%; background: #f1f5f9; height: 8px; border-radius: 4px; overflow: hidden;">
                        <div style="width: <?= $persen ?>%; background: #6F4E37; height: 100%; border-radius: 4px;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0; font-size: 16px; color: #333;"><i class="fa-solid fa-user-clock" style="color:#ef4444;"></i> Peringatan Pelanggan Pasif (> 30 Hari)</h3>
        <a href="index.php?controller=owner&action=analisis_pelanggan" style="font-size: 13px; color: #3b82f6; text-decoration: none; font-weight: bold;">Lihat Semua Data &rarr;</a>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
        <?php if(empty($pelanggan_pasif)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 15px; color: #10b981; font-weight: bold;">Luar biasa! Semua pelanggan aktif berkunjung bulan ini.</div>
        <?php else: ?>
            <?php foreach($pelanggan_pasif as $pasif): ?>
            <div style="border: 1px solid #fee2e2; background: #fffcfc; padding: 15px; border-radius: 8px; display: flex; align-items: center; gap: 15px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #fee2e2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
                <div>
                    <div style="font-weight: bold; font-size: 14px; color: #333;"><?= htmlspecialchars($pasif['nama_member']) ?></div>
                    <div style="font-size: 12px; color: #ef4444; margin-top: 3px;">Terakhir datang: <?= $pasif['jumlah_hari'] ?> hari yang lalu</div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>