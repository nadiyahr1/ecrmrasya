<style>
    /* Menggunakan ulang style tabel yang sama agar konsisten */
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

    .btn-cetak {
        background: #10b981;
    }

    .pagination-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        font-size: 14px;
        color: #555;
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

<div style="margin-bottom: 20px;">
    <h2 style="margin: 0; color: #333;">Laporan Aktivitas Member & Poin</h2>
    <p style="margin: 5px 0 0; color: #666; font-size: 14px;">Rekapitulasi perolehan dan penukaran poin pelanggan Rasya.co.</p>
</div>

<form action="index.php" method="GET" class="filter-bar">
    <input type="hidden" name="controller" value="laporan">
    <input type="hidden" name="action" value="laporanMember">

    <div class="filter-group">
        <span>Tampilkan</span>
        <select name="limit" class="input-control" onchange="this.form.submit()" style="padding: 6px;">
            <option value="10" <?= ($limit == 10) ? 'selected' : '' ?>>10</option>
            <option value="25" <?= ($limit == 25) ? 'selected' : '' ?>>25</option>
            <option value="50" <?= ($limit == 50) ? 'selected' : '' ?>>50</option>
        </select>
        <span>entri</span>
    </div>

    <div class="filter-group">
        <label>Periode:</label>
        <input type="date" name="mulai" value="<?= htmlspecialchars($tgl_mulai) ?>" class="input-control" required>
        <span>s/d</span>
        <input type="date" name="selesai" value="<?= htmlspecialchars($tgl_selesai) ?>" class="input-control" required>
        <button type="submit" class="btn-action btn-filter">Filter</button>
        <a href="index.php?controller=laporan&action=cetak&type=member&mulai=<?= $tgl_mulai ?>&selesai=<?= $tgl_selesai ?>" target="_blank" class="btn-action btn-cetak">Cetak PDF</a>
    </div>
</form>

<div style="overflow-x: auto; width: 100%; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
    <table class="table-laporan">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="18%">Tanggal</th>
                <th width="25%">Nama Member</th>
                <th width="15%">Jenis Aktivitas</th>
                <th width="12%" style="text-align: center;">Nominal Poin</th>
                <th width="25%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($laporan)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #888;">
                        Tidak ada aktivitas poin pada rentang tanggal ini.
                    </td>
                </tr>
            <?php else: ?>
                <?php
                $no = $offset + 1;
                foreach ($laporan as $row):
                    // Asumsi field dari tabel tb_history_poin
                    // Jika nama field database Anda berbeda (misal 'jenis_transaksi' atau 'jumlah_poin'), silakan sesuaikan
                    $jenis = $row['jenis'] ?? 'Tambah';
                    $poin = $row['poin'] ?? 0;
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d M Y, H:i', strtotime($row['tgl_perubahan'])) ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_member']) ?></strong></td>
                        <td>
                            <?php if (strtolower($jenis) == 'tambah' || strtolower($jenis) == 'masuk'): ?>
                                <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                    Perolehan Poin
                                </span>
                            <?php else: ?>
                                <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                    Penukaran Poin
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; font-weight: bold; font-size: 15px; <?= (strtolower($jenis) == 'tambah' || strtolower($jenis) == 'masuk') ? 'color: #16a34a;' : 'color: #dc2626;' ?>">
                            <?= (strtolower($jenis) == 'tambah' || strtolower($jenis) == 'masuk') ? '+' : '-' ?><?= number_format($poin) ?>
                        </td>
                        <td style="font-size: 13px; color: #555;">
                            <?= htmlspecialchars($row['keterangan'] ?? '-') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
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

    <div style="display: flex; gap: 5px;">
        <?php if ($halaman_aktif > 1): ?>
            <a href="index.php?controller=laporan&action=laporanMember&mulai=<?= $tgl_mulai ?>&selesai=<?= $tgl_selesai ?>&limit=<?= $limit ?>&halaman=<?= $halaman_aktif - 1 ?>" class="page-link">Sebelumnya</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
            <?php if (abs($halaman_aktif - $i) < 3 || $i == 1 || $i == $total_halaman): ?>
                <a href="index.php?controller=laporan&action=laporanMember&mulai=<?= $tgl_mulai ?>&selesai=<?= $tgl_selesai ?>&limit=<?= $limit ?>&halaman=<?= $i ?>"
                    class="page-link <?= ($halaman_aktif == $i) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php elseif (abs($halaman_aktif - $i) == 3): ?>
                <span style="padding: 6px 5px; color: #888;">...</span>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($halaman_aktif < $total_halaman): ?>
            <a href="index.php?controller=laporan&action=laporanMember&mulai=<?= $tgl_mulai ?>&selesai=<?= $tgl_selesai ?>&limit=<?= $limit ?>&halaman=<?= $halaman_aktif + 1 ?>" class="page-link">Selanjutnya</a>
        <?php endif; ?>
    </div>
</div>