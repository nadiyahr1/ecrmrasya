<?php
// admin/data_pelanggan.php

// 1. TANGKAP INPUTAN PENCARIAN & PAGINATION
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit  = 10;
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman_aktif - 1) * $limit;

// 2. QUERY HITUNG TOTAL MEMBER (Untuk Statistik & Pagination)
$where_sql = "1=1";
$params = [];
if (!empty($search)) {
    $where_sql .= " AND (m.nama_member LIKE ? OR m.email LIKE ? OR m.no_telp LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt_total = $conn->prepare("SELECT COUNT(*) FROM tb_member m WHERE $where_sql");
$stmt_total->execute($params);
$total_member = $stmt_total->fetchColumn();
$total_halaman = ceil($total_member / $limit);

// 3. AMBIL DATA MEMBER & LEVEL (E-CRM Core)
$query = "SELECT m.*, l.nama_level, l.diskon 
          FROM tb_member m 
          JOIN tb_level_member l ON m.id_level = l.id_level 
          WHERE $where_sql 
          ORDER BY m.total_poin DESC, m.nama_member ASC 
          LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$members = $stmt->fetchAll();
?>

<style>
    /* Style Statistik di Atas */
    .stats-container { display: flex; gap: 20px; margin-bottom: 25px; }
    .stat-card { flex: 1; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #6F4E37; }
    .stat-card h4 { margin: 0; color: #64748b; font-size: 13px; text-transform: uppercase; }
    .stat-card h2 { margin: 10px 0 0 0; color: #334155; }

    /* Style Tabel */
    .table-pelanggan { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .table-pelanggan th { background: #f8f9fa; padding: 15px; text-align: left; font-size: 13px; color: #555; border-bottom: 1px solid #eee; }
    .table-pelanggan td { padding: 15px; font-size: 14px; border-bottom: 1px solid #eee; vertical-align: middle; }
    
    .badge-level { padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; color: white; }
    .level-Bronze { background: #cd7f32; }
    .level-Silver { background: #c0c0c0; color: #333; }
    .level-Gold { background: #ffd700; color: #333; }

    .search-container { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .btn-riwayat { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; cursor: pointer; }
</style>

<h2 style="margin-top: 0; color: #333;">Data Pelanggan Member</h2>

<div class="stats-container">
    <div class="stat-card">
        <h4>Total Member</h4>
        <h2><?= number_format($total_member) ?> Orang</h2>
    </div>
    <div class="stat-card" style="border-left-color: #ffd700;">
        <h4>Total Poin Beredar</h4>
        <?php 
            $tp = $conn->query("SELECT SUM(total_poin) FROM tb_member")->fetchColumn();
            echo "<h2>" . number_format($tp ?: 0) . " Poin</h2>";
        ?>
    </div>
</div>

<div class="search-container">
    <form method="GET" action="index.php" style="display: flex; gap: 8px;">
        <input type="hidden" name="page" value="data_pelanggan">
        <input type="text" name="search" placeholder="Cari nama, email, atau telp..." value="<?= htmlspecialchars($search) ?>" style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; width: 300px;">
        <button type="submit" style="padding: 8px 15px; background: #6F4E37; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cari</button>
    </form>
</div>

<table class="table-pelanggan">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Pelanggan</th>
            <th>Kontak</th>
            <th>Total Poin</th>
            <th>Level</th>
            <th>Tgl Daftar</th>
            <th>Opsi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = $offset + 1; foreach($members as $m): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><strong><?= $m['nama_member'] ?></strong><br><small style="color: #888;">ID Member: MBR-<?= $m['id_member'] ?></small></td>
            <td><?= $m['no_telp'] ?></td>
            <td style="font-weight: bold; color: #6F4E37;"><?= number_format($m['total_poin']) ?> Poin</td>
            <td><span class="badge-level level-<?= $m['nama_level'] ?>"><?= $m['nama_level'] ?></span></td>
            <td><?= date('d/m/Y', strtotime($m['tgl_daftar'])) ?></td>
            <td>
                <button onclick="lihatRiwayat('<?= $m['id_member'] ?>', '<?= $m['nama_member'] ?>')" class="btn-riwayat">Riwayat Poin</button>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if(count($members) == 0): ?>
            <tr><td colspan="7" style="text-align: center; color: #888; padding: 40px;">Data pelanggan tidak ditemukan.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if($total_halaman > 1): ?>
<div style="display: flex; justify-content: center; margin-top: 20px; gap: 5px;">
    <?php for($i = 1; $i <= $total_halaman; $i++): ?>
        <a href="?page=data_pelanggan&search=<?= urlencode($search) ?>&halaman=<?= $i ?>" 
           style="padding: 8px 12px; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px; <?= ($i == $halaman_aktif) ? 'background:#6F4E37; color:white;' : 'background:white;' ?>">
           <?= $i ?>
        </a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<div id="modalRiwayat" class="modal-overlay" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background: white; padding: 0; border-radius: 12px; width: 90%; max-width: 500px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
        <div style="background: #6F4E37; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="judulModalMember" style="margin: 0;">Riwayat Poin</h3>
            <button onclick="tutupModalMember()" style="color: white; border: none; background: transparent; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <div id="isiModalMember" style="padding: 20px; max-height: 400px; overflow-y: auto;">
            </div>
    </div>
</div>

<script>
    function lihatRiwayat(id, nama) {
        document.getElementById('modalRiwayat').style.display = 'flex';
        document.getElementById('judulModalMember').innerText = "Riwayat: " + nama;
        document.getElementById('isiModalMember').innerHTML = "<i>Memuat data...</i>";
        
        // Menampilkan detail riwayat penggunaan poin
        fetch('riwayat_poin_pelanggan.php?id=' + id)
            .then(res => res.text())
            .then(html => { document.getElementById('isiModalMember').innerHTML = html; });
    }
    function tutupModalMember() { document.getElementById('modalRiwayat').style.display = 'none'; }
</script>