<style>
    .welcome-section {
        margin-bottom: 30px;
    }

    .welcome-section h2 {
        margin: 0;
        color: #333;
        font-size: 24px;
    }

    .welcome-section p {
        margin: 5px 0 0 0;
        color: #888;
        font-size: 15px;
    }

    .alert-verifikasi {
        background: #fff4e5;
        border-left: 5px solid #ffa117;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 10px rgba(255, 161, 23, 0.1);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #eee;
    }

    .stat-card h3 {
        margin: 0 0 5px 0;
        font-size: 28px;
        color: #333;
    }

    .stat-card p {
        margin: 0;
        color: #888;
        font-size: 14px;
        font-weight: 500;
    }

    .shortcut-section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #eee;
    }

    .shortcut-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .shortcut-table th {
        background: #f9f9f9;
        padding: 12px;
        text-align: left;
        font-size: 13px;
        color: #555;
        border-bottom: 2px solid #eee;
    }

    .shortcut-table td {
        padding: 12px;
        border-bottom: 1px solid #f5f5f5;
        font-size: 14px;
    }

    .btn-proses-cepat {
        background: #6F4E37;
        color: white;
        padding: 6px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 12px;
        font-weight: bold;
    }

    .btn-proses-cepat:hover {
        background: #5a3e2b;
    }
</style>

<div class="welcome-section">
    <h2>Selamat Datang, <?= $_SESSION['nama'] ?? 'Admin' ?>! 👋</h2>
    <p>Berikut adalah ringkasan operasional Café Rasya.co hari ini.</p>
</div>

<?php if (isset($member_pending) && $member_pending > 0): ?>
    <div class="alert-verifikasi">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="font-size: 30px;">👥</div>
            <div>
                <h4 style="margin: 0; color: #856404;">Perlu Verifikasi Akun</h4>
                <p style="margin: 3px 0 0 0; font-size: 14px; color: #856404;">
                    Ada <strong><?= $member_pending ?> calon member</strong> baru yang menunggu persetujuan Anda.
                </p>
            </div>
        </div>
        <a href="index.php?controller=admin&action=data_pelanggan"
            style="background: #ffa117; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px; transition: 0.3s;">
            Proses Sekarang
        </a>
    </div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?= $pesanan_hari_ini ?></h3>
        <p>Pesanan Masuk</p>
    </div>
    <div class="stat-card">
        <h3><?= $member_baru ?></h3>
        <p>Member Baru</p>
    </div>
    <div class="stat-card">
        <h3>Rp <?= number_format($omzet_harian, 0, ',', '.') ?></h3>
        <p>Pendapatan Hari Ini</p>
    </div>
    <div class="stat-card">
            <h3 style="color: <?= $jml_stok_tipis > 0 ? : '#333' ?>;"><?= $jml_stok_tipis ?> Menu</h3>
            <p>Peringatan Stok Rendah (< 10)</p>
        </div>
</div>

<div class="shortcut-section">
    <h4 style="margin: 0; color: #333;">🔥 Pesanan Menunggu Konfirmasi</h4>
    <div style="overflow-x: auto;">
        <?php if (!empty($pesanan_menunggu)): ?>
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
                    <?php foreach ($pesanan_menunggu as $pm): ?>
                        <tr>
                            <td style="font-weight: bold;">#<?= $pm['id_pesanan'] ?></td>
                            <td><?= $pm['nama_member'] ? $pm['nama_member'] : '<i>Umum</i>' ?></td>
                            <td><?= date('d/m/y, H:i', strtotime($pm['tgl_pesanan'])) ?> WIB</td>
                            <td style="font-weight: bold;">Rp <?= number_format($pm['total_transaksi'], 0, ',', '.') ?></td>
                            <td>
                                <a href="index.php?controller=admin&action=data_pesanan&tab=Menunggu Konfirmasi&highlight=<?= $pm['id_pesanan'] ?>#<?= $pm['id_pesanan'] ?>" class="btn-proses-cepat">Proses</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 20px; color: #888;">
                <span style="font-size: 40px; display: block; margin-bottom: 10px;">🎉</span>
                Semua pesanan sudah tertangani!
            </div>
        <?php endif; ?>
    </div>
</div>