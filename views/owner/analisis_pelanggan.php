<style>
    /* Desain Grid untuk Level Member */
    .grid-level {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        width: 100%;
    }

    .level-card {
        flex: 1;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        border: 1px solid #f0f0f0;
        text-align: center;
        border-top: 5px solid var(--primary-brown);
    }

    .level-name {
        font-size: 15px;
        font-weight: 600;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }

    .level-count {
        font-size: 32px;
        font-weight: bold;
        color: #333;
    }

    /* Desain Grid untuk Tabel Analisis */
    .grid-tables {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 40px;
        width: 100%;
        white-space: nowrap;
    }

    .table-panel {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        border: 1px solid #f0f0f0;
        width: 100%;
        box-sizing: border-box;
    }

    .table-panel-title {
        margin: 0 0 15px 0;
        font-size: 16px;
        color: #333;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-custom th {
        text-align: left;
        padding: 12px 10px;
        border-bottom: 2px solid #eee;
        color: #888;
        font-size: 13px;
    }

    .table-custom td {
        padding: 12px 10px;
        border-bottom: 1px solid #f9f9f9;
        color: #444;
        font-size: 14px;
    }

    .badge-level {
        background: #fdf6e3;
        color: #b45309;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: bold;
        border: 1px solid #fde047;
    }

    .badge-warning {
        background: #fef08a;
        color: #854d0e;
        padding: 5px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: bold;
    }

    .badge-danger {
        background: #fecaca;
        color: #991b1b;
        padding: 5px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: bold;
    }
</style>

<div>
    <h3 style="margin: 0 0 25px 0; color: #777; font-size: 20px;">Pantau performa level member, pelanggan paling loyal, dan pelanggan yang perlu di-follow up.</h3>
</div>

<div style="font-size: 16px; color: #555; border-bottom: 2px solid #eee; padding-bottom: 8px; margin-bottom: 15px; display: inline-block;">
    Distribusi Level Member
</div>
<div class="grid-level">
    <?php if (!empty($performa_level)): ?>
        <?php foreach ($performa_level as $level): ?>
            <div class="level-card">
                <div class="level-name"><?= htmlspecialchars($level['nama_level']) ?></div>
                <div class="level-count"><?= number_format($level['total']) ?> <span style="font-size: 14px; color: #aaa; font-weight: normal;">Orang</span></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="level-card">
            <div class="level-name">Data Level Kosong</div>
            <div class="level-count">0</div>
        </div>
    <?php endif; ?>
</div>

<div style="overflow-x: auto; width: 100%; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
    <div class="grid-tables">

        <div class="table-panel">
            <div class="table-panel-title">
                <span>🏆 Peringkat 10 Pelanggan Loyal</span>
            </div>
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Kunjungan</th>
                        <th>Total Belanja</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($top_customers)): ?>
                        <?php $no = 1;
                        foreach ($top_customers as $top): ?>
                            <tr>
                                <td style="font-weight: bold; color: #6F4E37;"><?= $no++ ?></td>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($top['nama_member']) ?></div>
                                    <span class="badge-level"><?= htmlspecialchars($top['nama_level'] ?? 'Member') ?></span>
                                </td>
                                <td><?= number_format($top['jumlah_kunjungan']) ?>x</td>
                                <td style="font-weight: bold; color: #10b981;">Rp <?= number_format($top['total_belanja'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px; color: #888;">Belum ada data transaksi pelanggan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-panel">
            <div class="table-panel-title">
                <span>⚠️ Pelanggan Pasif (> 30 Hari)</span>
            </div>
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Nama Pelanggan</th>
                        <th>No. WhatsApp</th>
                        <th>Terakhir Transaksi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pelanggan_pasif)): ?>
                        <?php foreach ($pelanggan_pasif as $pasif): ?>
                            <tr>
                                <td style="font-weight: 600;"><?= htmlspecialchars($pasif['nama_member']) ?></td>
                                <td>
                                    <a href="https://wa.me/<?= $pasif['no_telp'] ?>" target="_blank" style="color: #25D366; text-decoration: none; font-size: 13px; font-weight: bold;">
                                        <?= htmlspecialchars($pasif['no_telp']) ?>
                                    </a>
                                </td>
                                <td style="font-size: 13px; color: #666;">
                                    <?= date('d M Y', strtotime($pasif['kunjungan_terakhir'])) ?>
                                </td>
                                <td>
                                    <?php if ($pasif['jumlah_hari'] >= 60): ?>
                                        <span class="badge-danger"><?= $pasif['jumlah_hari'] ?> Hari</span>
                                    <?php else: ?>
                                        <span class="badge-warning"><?= $pasif['jumlah_hari'] ?> Hari</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px; color: #888;">Semua pelanggan aktif bertransaksi bulan ini. Luar biasa!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>