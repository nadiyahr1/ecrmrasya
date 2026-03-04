<?php
// admin/ulasan.php
// HANYA UNTUK TAMPILAN KONTEN (Di-include oleh index.php)

// Mengambil data ulasan sekaligus nama member yang memberi ulasan
$query = "SELECT u.*, m.nama_member FROM tb_ulasan u LEFT JOIN tb_member m ON u.id_member = m.id_member ORDER BY u.tgl_ulasan DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$ulasan = $stmt->fetchAll();
?>

<style>
    /* Style box komentar */
    .ulasan-box {
        background: #f9fafb;
        padding: 10px;
        border-radius: 6px;
        border-left: 4px solid #d1d5db;
        margin-bottom: 5px;
        font-size: 13px;
    }

    .balasan-box {
        background: #eff6ff;
        padding: 10px;
        border-radius: 6px;
        border-left: 4px solid #3b82f6;
        font-size: 13px;
        margin-left: 20px;
    }

    /* Style Badge Status */
    .badge-y {
        background: #dcfce7;
        color: #166534;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: bold;
    }

    .badge-n {
        background: #fee2e2;
        color: #991b1b;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: bold;
    }

    /* Style Tombol Aksi */
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        margin-right: 4px;
        font-size: 15px;
        text-decoration: none;
        transition: 0.2s;
    }

    .table-ulasan {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-ulasan th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-size: 13px;
        border-bottom: 1px solid #eee;
    }

    .table-ulasan td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        vertical-align: top;
    }

    .btn-reply { background: #e0f2fe; color: #0369a1; }
    .btn-reply:hover { background: #bae6fd; color: #0284c7; }
    
    .btn-toggle { transition: opacity 0.2s; }
    .btn-toggle:hover { opacity: 0.8; }
    
    .btn-delete-icon { background: #fee2e2; color: #991b1b; }
    .btn-delete-icon:hover { background: #fecaca; color: #dc2626; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="margin:0; color: #333;">Manajemen Ulasan Pelanggan</h2>
</div>

<table class="table-ulasan">
    <thead>
        <tr>
            <th width="20">No</th>
            <th width="80">Tanggal</th>
            <th width="120">ID Pesanan</th>
            <th width="150">Pelanggan</th>
            <th>Komentar & Balasan</th>
            <th width="90" style="text-align: center;">Tampil (Web)</th>
            <th width="120">Opsi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($ulasan as $u): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d/m/Y', strtotime($u['tgl_ulasan'])) ?></td>
                <td><strong>#<?= $u['id_pesanan'] ?></strong></td>
                <td><?= $u['nama_member'] ? $u['nama_member'] : 'Member Terhapus' ?></td>
                <td>
                    <div class="ulasan-box">
                        <strong>Komentar:</strong><br>
                        <?= nl2br(htmlspecialchars($u['komentar'])) ?>
                    </div>

                    <?php if (!empty($u['balasan_admin'])): ?>
                        <div class="balasan-box">
                            <strong>Balasan Rasya.co:</strong><br>
                            <?= nl2br(htmlspecialchars($u['balasan_admin'])) ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td align="center">
                    <?php if ($u['status_tampil'] == 'Y'): ?>
                        <span class="badge-y">Ditampilkan</span>
                    <?php else: ?>
                        <span class="badge-n">Disembunyikan</span>
                    <?php endif; ?>
                </td>
                <td style="white-space: nowrap;">
                    <button type="button" class="btn-icon btn-reply" title="Balas Ulasan Ini" onclick="balasUlasan('<?= $u['id_ulasan'] ?>', '<?= addslashes($u['balasan_admin']) ?>')">
                        <i class="fa-solid fa-comment-dots"></i>
                    </button>

                    <?php if ($u['status_tampil'] == 'Y'): ?>
                        <a href="toggle_ulasan.php?id=<?= $u['id_ulasan'] ?>&status=N" class="btn-icon btn-toggle" style="background: #fef3c7; color: #92400e;" title="Klik untuk Menyembunyikan dari Publik">
                            <i class="fa-solid fa-eye-slash"></i>
                        </a>
                    <?php else: ?>
                        <a href="toggle_ulasan.php?id=<?= $u['id_ulasan'] ?>&status=Y" class="btn-icon btn-toggle" style="background: #dcfce7; color: #166534;" title="Klik untuk Menampilkan ke Publik">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    <?php endif; ?>

                    <a href="hapus/hapus_ulasan.php?id=<?= $u['id_ulasan'] ?>" class="btn-icon btn-delete-icon" title="Hapus Ulasan Permanen" onclick="return confirm('Yakin ingin menghapus ulasan ini secara permanen?')">
                        <i class="fa-solid fa-trash-can"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="modalBalas" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div style="background:white; padding:25px; border-radius:12px; width:500px; box-shadow:0 5px 20px rgba(0,0,0,0.3);">
        <h3 id="judulBalas" style="margin-top:0;">Balas Ulasan Pelanggan</h3>
        <form id="formBalas" action="balas_ulasan.php" method="POST">
            <input type="hidden" name="id_ulasan" id="id_ulasan_b">

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:13px; margin-bottom:8px; font-weight: bold; color: #555;">Ketik Balasan Admin</label>
                <textarea name="balasan_admin" id="balasan_a" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box; height: 120px;" placeholder="Terima kasih atas kunjungannya ke Rasya.co..." required></textarea>
            </div>

            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" style="flex:1; background:#3b82f6; color:white; padding:10px; border:none; border-radius:6px; font-weight:bold; cursor: pointer;">Simpan Balasan</button>
                <button type="button" onclick="tutupModalBalas()" style="flex:1; background:#eee; padding:10px; border:none; border-radius:6px; cursor: pointer;">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function balasUlasan(id, balasan) {
        document.getElementById('modalBalas').style.display = 'flex';
        document.getElementById('id_ulasan_b').value = id;
        document.getElementById('balasan_a').value = balasan;
    }

    function tutupModalBalas() {
        document.getElementById('modalBalas').style.display = 'none';
    }
</script>
<script src="https://kit.fontawesome.com/9e7ed11248.js" crossorigin="anonymous"></script>