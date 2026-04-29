<style>
    .full-container {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    /* Styling Grid Statistik */
    .stat-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
        width: 100%;
    }

    .stat-card {
        background: white;
        padding: 25px 30px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
    }

    .stat-card.blue {
        border-left: 5px solid #3b82f6;
    }

    .stat-card.purple {
        border-left: 5px solid #8b5cf6;
    }

    .stat-number {
        font-size: 28px;
        font-weight: bold;
        color: #333;
        margin: 8px 0 0 0;
    }

    .stat-label {
        font-size: 13px;
        color: #888;
        text-transform: uppercase;
        font-weight: 600;
    }

    /* Styling Form Filter Top Bar */
    .filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #555;
    }

    .input-control {
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
    }

    .input-control:focus {
        border-color: #6F4E37;
    }

    .btn-action {
        padding: 8px 15px;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        font-size: 13px;
        text-decoration: none;
        color: white;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-filter {
        background: #3b82f6;
    }

    /* Styling Tabel senada dengan Penjualan */
    .table-laporan {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .table-laporan th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-size: 13px;
        color: #555;
        border-bottom: 2px solid #eee;
        white-space: nowrap;
    }

    .table-laporan td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        vertical-align: middle;
        color: #333;
        white-space: nowrap;
    }

    .table-laporan tr:hover {
        background: #fdfdfd;
    }

    .badge-promo {
        background: #f3e8ff;
        color: #6b21a8;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: bold;
    }

    /* Styling Pagination */
    .pagination-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        font-size: 14px;
        color: #555;
    }

    .pagination-buttons {
        display: flex;
        gap: 5px;
    }

    .page-link {
        padding: 6px 12px;
        border: 1px solid #cbd5e1;
        background: white;
        color: #333;
        text-decoration: none;
        border-radius: 4px;
        transition: 0.2s;
    }

    .page-link:hover {
        background: #f1f5f9;
    }

    .page-link.active {
        background: #6F4E37;
        color: white;
        border-color: #6F4E37;
    }
</style>

<div class="full-container">
    <div style="margin-bottom: 20px;">
        <h2 style="margin: 0; color: #333;">Laporan Penggunaan Promo</h2>
        <p style="margin: 5px 0 0; color: #666; font-size: 14px;">Pantau promo yang paling sering digunakan dan total diskon yang diberikan.</p>
    </div>

    <form id="filterForm" action="index.php" method="GET" class="filter-bar">
        <input type="hidden" name="controller" value="laporan">
        <input type="hidden" name="action" value="laporanPromo">

        <div class="filter-group">
            <span>Tampilkan</span>
            <select name="limit" class="input-control" onchange="this.form.submit()" style="padding: 6px;">
                <option value="10" <?= ($limit == 10) ? 'selected' : '' ?>>10</option>
                <option value="25" <?= ($limit == 25) ? 'selected' : '' ?>>25</option>
                <option value="50" <?= ($limit == 50) ? 'selected' : '' ?>>50</option>
                <option value="100" <?= ($limit == 100) ? 'selected' : '' ?>>100</option>
            </select>
            <span>entri</span>
        </div>

        <div class="filter-group">
            <label>Periode:</label>
            <input type="date" name="mulai" value="<?= htmlspecialchars($tgl_mulai) ?>" class="input-control" required>
            <span>s/d</span>
            <input type="date" name="selesai" value="<?= htmlspecialchars($tgl_selesai) ?>" class="input-control" required>
            <button type="submit" class="btn-action btn-filter">Filter Data</button>
        </div>
    </form>

    <div class="stat-row">
        <div class="stat-card blue">
            <div class="stat-label">Total Promo Digunakan</div>
            <div class="stat-number"><?= number_format($total_pemakaian ?? 0) ?> Kali</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">Total Nominal Diskon Diberikan</div>
            <div class="stat-number">Rp <?= number_format($total_diskon ?? 0, 0, ',', '.') ?></div>
        </div>
    </div>
    <div style="overflow-x: auto; width: 100%; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">

        <table class="table-laporan">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal Transaksi</th>
                    <th width="18%">Nama Pelanggan</th>
                    <th width="20%">Nama Promo</th>
                    <th width="12%">Tipe Promo</th>
                    <th width="15%">Diskon Diberikan</th>
                    <th width="20%">Total Belanja (Akhir)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($riwayat_promo)): ?>
                    <?php
                    $no = $offset + 1;
                    foreach ($riwayat_promo as $baris):
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d M Y, H:i', strtotime($baris['tgl_pesanan'])) ?></td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($baris['nama_member'] ?? 'Non-Member') ?></td>
                            <td><span class="badge-promo"><?= htmlspecialchars($baris['nama_promo']) ?></span></td>
                            <td>
                                <?php if ($baris['tipe_promo'] == 'Umum'): ?>
                                    <span class="badge-tipe-umum">Umum</span>
                                <?php elseif ($baris['tipe_promo'] == 'Level'): ?>
                                    <span class="badge-tipe-level">Level</span>
                                <?php else: ?>
                                    <span class="badge-tipe-poin">Tukar Poin</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: bold; color: #d97706;">
                                Rp <?= number_format($baris['nominal_diskon'], 0, ',', '.') ?>
                            </td>
                            <td style="font-weight: bold; color: #16a34a;">
                                Rp <?= number_format($baris['total_transaksi'], 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #888;">
                            Tidak ada transaksi yang menggunakan promo pada rentang tanggal ini.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination-bar">
        <div>
            <?php
            $awal = ($total_data > 0) ? $offset + 1 : 0;
            $akhir = min($offset + $limit, $total_data);
            ?>
            Menampilkan <strong><?= $awal ?></strong> sampai <strong><?= $akhir ?></strong> dari <strong><?= $total_data ?></strong> entri
        </div>

        <div class="pagination-buttons">
            <?php if ($halaman_aktif > 1): ?>
                <a href="index.php?controller=laporan&action=laporanPromo&mulai=<?= $tgl_mulai ?>&selesai=<?= $tgl_selesai ?>&limit=<?= $limit ?>&halaman=<?= $halaman_aktif - 1 ?>" class="page-link">Sebelumnya</a>
            <?php else: ?>
                <span class="page-link" style="color: #ccc; cursor: not-allowed;">Sebelumnya</span>
            <?php endif; ?>

            <?php for ($i = 1; $i <= ($total_halaman ?? 1); $i++): ?>
                <?php if (abs($halaman_aktif - $i) < 3 || $i == 1 || $i == $total_halaman): ?>
                    <a href="index.php?controller=laporan&action=laporanPromo&mulai=<?= $tgl_mulai ?>&selesai=<?= $tgl_selesai ?>&limit=<?= $limit ?>&halaman=<?= $i ?>"
                        class="page-link <?= ($halaman_aktif == $i) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php elseif (abs($halaman_aktif - $i) == 3): ?>
                    <span style="padding: 6px 5px; color: #888;">...</span>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($halaman_aktif < ($total_halaman ?? 1)): ?>
                <a href="index.php?controller=laporan&action=laporanPromo&mulai=<?= $tgl_mulai ?>&selesai=<?= $tgl_selesai ?>&limit=<?= $limit ?>&halaman=<?= $halaman_aktif + 1 ?>" class="page-link">Selanjutnya</a>
            <?php else: ?>
                <span class="page-link" style="color: #ccc; cursor: not-allowed;">Selanjutnya</span>
            <?php endif; ?>
        </div>
    </div>
</div>