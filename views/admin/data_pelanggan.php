<style>
    .stats-container {
        display: flex;
        gap: 20px;
        margin-bottom: 25px;
    }

    .stat-card {
        flex: 1;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border-left: 5px solid #6F4E37;
    }

    .stat-card h4 {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        text-transform: uppercase;
    }

    .stat-card h2 {
        margin: 10px 0 0 0;
        color: #334155;
    }

    .table-pelanggan {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-pelanggan th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-size: 13px;
        color: #555;
        border-bottom: 1px solid #ddd;
    }

    .table-pelanggan td {
        padding: 15px;
        font-size: 14px;
        color: #333;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }

    .table-pelanggan tr:hover {
        background: #fdfaf8;
    }

    .badge-level {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    .badge-bronze {
        background: #fdf5e6;
        color: #cd7f32;
        border: 1px solid #cd7f32;
    }

    .badge-silver {
        background: #f8f9fa;
        color: #6c757d;
        border: 1px solid #6c757d;
    }

    .badge-gold {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #f59e0b;
    }

    .btn-riwayat {
        background: #e0f2fe;
        color: #0284c7;
        border: 1px solid #bae6fd;
        padding: 6px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 13px;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-riwayat:hover {
        background: #0284c7;
        color: white;
    }
</style>

<div class="stats-container">
    <div class="stat-card">
        <h4>Total Pelanggan</h4>
        <h2><?= $total_member ?> <small style="font-size: 14px; color: #888;">Akun</small></h2>
    </div>
    <div class="stat-card">
        <h4>Poin Beredar</h4>
        <h2><?= number_format($total_poin_beredar) ?> <small style="font-size: 14px; color: #888;">Poin</small></h2>
    </div>
</div>

<div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h3 style="margin: 0; color: #333;">Daftar Pelanggan Terdaftar</h3>

    <form method="GET" action="index.php" style="display: flex; align-items: center; gap: 15px;">
        <input type="hidden" name="controller" value="admin">
        <input type="hidden" name="action" value="data_pelanggan">

        <div style="color: #555; font-size: 14px;">
            Tampilkan
            <select name="limit" onchange="this.form.submit()" style="padding: 8px; border: 1px solid #ddd; border-radius: 5px; outline: none; cursor: pointer; margin: 0 5px;">
                <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
            </select> Data
        </div>

        <div style="display: flex; gap: 5px;">
            <input type="text" name="search" placeholder="Cari nama atau telp..." value="<?= htmlspecialchars($search) ?>"
                style="padding: 10px 15px; width: 250px; border: 1px solid #ddd; border-radius: 8px; outline: none;">
            <button type="submit" style="padding: 10px 20px; background: #6F4E37; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
                Cari
            </button>
        </div>
    </form>
</div>

<div style="overflow-x: auto;">
    <table class="table-pelanggan">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pelanggan</th>
                <th>Kontak</th>
                <th>Level</th>
                <th>Total Poin</th>
                <th>Tgl Daftar</th>
                <!-- <th>T. Transaksi</th> -->
                <!-- <th>T. Belanja (Rp)</th> -->
                <th>Status</th>
                <th>Opsi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($members)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #888;">Data tidak ditemukan.</td>
                </tr>
            <?php else: ?>
                <?php $no = $offset + 1;
                foreach ($members as $m): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($m['nama_member']) ?></strong><br>
                            <small>ID Member: MBR-<?= $m['id_member'] ?></small>
                        </td>
                        <td><?= $m['no_telp'] ?></td>
                        <td>
                            <?php
                            $lvl = strtolower($m['nama_level']);
                            $badge = ($lvl == 'gold') ? 'badge-gold' : (($lvl == 'silver') ? 'badge-silver' : 'badge-bronze');
                            ?>
                            <span class="badge-level <?= $badge ?>"><?= $m['nama_level'] ?></span>
                        </td>
                        <td style="font-weight: bold; color: #6F4E37;"><?= number_format($m['poin']) ?> Poin</td>
                        <td><?= date('d/m/Y', strtotime($m['tgl_daftar'])) ?></td>
                        <!-- <td><?= $m['jml_transaksi'] ?>x</td> -->
                        <!-- <td>Rp <?= number_format($m['total_belanja']) ?></td> -->
                        <td>
                            <?php if (isset($m['status_akun']) && $m['status_akun'] == 'Aktif'): ?>
                                <span style="background: #22c55e; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Aktif</span>
                            <?php else: ?>
                                <span style="background: #eab308; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Menunggu</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <button onclick="lihatRiwayat('<?= $m['id_member'] ?>', '<?= addslashes($m['nama_member']) ?>')" class="btn-riwayat">Riwayat Poin</button>

                            <?php if (isset($m['status_akun']) && $m['status_akun'] == 'Pending'): ?>
                                <a href="index.php?controller=admin&action=verifikasiPelanggan&id=<?= $m['id_member']; ?>"
                                    style="background: #3b82f6; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; margin-left: 5px; display: inline-block;"
                                    onclick="return confirm('Apakah Anda yakin ingin memverifikasi dan mengaktifkan akun ini?');">
                                    Verifikasi
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
    <div style="font-size: 14px; color: #666;">
        Menampilkan <?= count($members) ?> dari <?= $total_member ?> pelanggan
    </div>
    <div style="display: flex; gap: 5px;">

        <?php if ($halaman_aktif > 1): ?>
            <a href="index.php?controller=admin&action=data_pelanggan&halaman=<?= $halaman_aktif - 1 ?>&search=<?= urlencode($search) ?>&limit=<?= $limit ?>"
                style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #333; background: white; font-size: 14px;">
                &laquo; Prev
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
            <a href="index.php?controller=admin&action=data_pelanggan&halaman=<?= $i ?>&search=<?= urlencode($search) ?>&limit=<?= $limit ?>"
                style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #333; font-size: 14px; <?= ($i == $halaman_aktif) ? 'background: #6F4E37; color: white; border-color: #6F4E37;' : 'background: white;' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($halaman_aktif < $total_halaman): ?>
            <a href="index.php?controller=admin&action=data_pelanggan&halaman=<?= $halaman_aktif + 1 ?>&search=<?= urlencode($search) ?>&limit=<?= $limit ?>"
                style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #333; background: white; font-size: 14px;">
                Next &raquo;
            </a>
        <?php endif; ?>

    </div>
</div>

<div id="modalRiwayat" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 600px; overflow: hidden;">
        <div style="background: #6F4E37; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="judulModalMember" style="margin: 0;">Riwayat Poin</h3>
            <button onclick="tutupModalMember()" style="color: white; border: none; background: transparent; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <div id="isiModalMember" style="padding: 20px; max-height: 400px; overflow-y: auto;"></div>
    </div>
</div>

<script>
    function lihatRiwayat(id, nama) {
        document.getElementById('modalRiwayat').style.display = 'flex';
        document.getElementById('judulModalMember').innerText = "Riwayat: " + nama;
        document.getElementById('isiModalMember').innerHTML = "<i>⏳ Loading...</i>";
        fetch('index.php?controller=admin&action=riwayat_poin_pelanggan&id=' + id)
            .then(res => res.text())
            .then(html => {
                document.getElementById('isiModalMember').innerHTML = html;
            });
    }

    function tutupModalMember() {
        document.getElementById('modalRiwayat').style.display = 'none';
    }
</script>