<?php
// admin/data_pesanan.php

// 1. LOGIKA UPDATE STATUS
if (isset($_POST['update_status'])) {
    $id_p = $_POST['id_pesanan'];
    $st_baru = $_POST['status_baru'];

    if ($st_baru == 'Selesai') {
        echo "<script>window.location.href='index.php?page=selesaikan_pesanan&id=$id_p';</script>"; 
        exit;
    } else {
        $stmt = $conn->prepare("UPDATE tb_pesanan SET status = ? WHERE id_pesanan = ?");
        if($stmt->execute([$st_baru, $id_p])) {
            $tab = isset($_GET['tab']) ? $_GET['tab'] : 'Semua';
            $hal = isset($_GET['halaman']) ? $_GET['halaman'] : 1;
            echo "<script>alert('Status Berhasil Diperbarui!'); window.location.href='index.php?page=data_pesanan&tab=$tab&halaman=$hal';</script>";
            exit;
        }
    }
}

// 2. TANGKAP INPUTAN FILTER, PENCARIAN, & PAGINATION
$status_filter = isset($_GET['tab']) ? $_GET['tab'] : 'Semua';
$search        = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit         = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset        = ($halaman_aktif - 1) * $limit;

// 3. SUSUN QUERY DINAMIS
$where_sql = "1=1";
$params = [];

if ($status_filter == 'Menunggu Konfirmasi') {
    $where_sql .= " AND (p.status = 'Menunggu Konfirmasi' OR p.status = '' OR p.status IS NULL)";
} elseif ($status_filter == 'Sedang Diproses') {
    $where_sql .= " AND p.status IN ('Konfirmasi', 'Sedang Diproses', 'Pesanan Siap')";
} elseif ($status_filter == 'Selesai') {
    $where_sql .= " AND p.status = 'Selesai'";
}

if (!empty($search)) {
    $where_sql .= " AND (p.id_pesanan LIKE ? OR m.nama_member LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// 4. HITUNG TOTAL DATA
$stmt_count = $conn->prepare("SELECT COUNT(*) FROM tb_pesanan p LEFT JOIN tb_member m ON p.id_member = m.id_member WHERE $where_sql");
$stmt_count->execute($params);
$total_data = $stmt_count->fetchColumn();
$total_halaman = ceil($total_data / $limit);

// 5. AMBIL DATA (Diperbarui dengan LEFT JOIN tb_meja)
$query = "SELECT p.*, m.nama_member, mj.no_meja 
          FROM tb_pesanan p 
          LEFT JOIN tb_member m ON p.id_member = m.id_member 
          LEFT JOIN tb_meja mj ON p.id_meja = mj.id_meja
          WHERE $where_sql 
          ORDER BY p.tgl_pesanan DESC 
          LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$pesanan = $stmt->fetchAll();
?>

<style>
    .tab-menu { border-bottom: 2px solid #eee; margin-bottom: 20px; display: flex; gap: 20px; }
    .tab-link { padding: 10px 15px; text-decoration: none; color: #666; font-weight: 500; margin-bottom: -2px; border-bottom: 2px solid transparent; transition: 0.3s; }
    .tab-link:hover, .tab-link.active { color: #3b82f6; border-bottom: 2px solid #3b82f6; font-weight: bold; }
    .table-pesanan { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
    .table-pesanan th { background: #f8f9fa; padding: 15px; text-align: left; font-size: 13px; color: #555; border-bottom: 1px solid #eee; }
    .table-pesanan td { padding: 15px; font-size: 14px; border-bottom: 1px solid #eee; color: #333; vertical-align: middle; }
    
    .badge { padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; display: inline-block; text-align: center; }
    .badge-menunggu { background: #fef3c7; color: #d97706; }
    .badge-konfirmasi { background: #e0e7ff; color: #4338ca; }
    .badge-diproses { background: #dbeafe; color: #1d4ed8; }
    .badge-siap { background: #fce7f3; color: #be185d; }
    .badge-selesai { background: #d1fae5; color: #059669; }
    .badge-batal { background: #fee2e2; color: #b91c1c; }
    
    .controls-container { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; font-size: 14px; color: #555; }
    .search-input { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; width: 250px; outline: none; }
    .limit-select, .select-status { padding: 8px; border: 1px solid #ccc; border-radius: 4px; outline: none; cursor: pointer; }
    .select-status { border-color: #6F4E37; color: #6F4E37; font-size: 12px; font-weight: bold; }
    .btn-cari { padding: 8px 15px; background: #6F4E37; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
    
    .pagination-container { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; color: #555; font-size: 14px; }
    .pagination { display: flex; list-style: none; padding: 0; margin: 0; gap: 5px; }
    .page-link { padding: 8px 12px; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px; background: white; }
    .page-link.active { background: #6F4E37; color: white; border-color: #6F4E37; font-weight: bold; pointer-events: none; }

    /* STYLE MODAL (POP-UP) */
    .modal-overlay { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); justify-content: center; align-items: center; }
    .modal-content { background-color: #fff; padding: 0; border-radius: 12px; width: 90%; max-width: 600px; box-shadow: 0 5px 20px rgba(0,0,0,0.2); animation: modalMuncul 0.3s ease-out; overflow: hidden; max-height: 90vh; display: flex; flex-direction: column; }
    @keyframes modalMuncul { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-header { background: #6F4E37; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
    .modal-header h3 { margin: 0; font-size: 18px; }
    .close-btn { color: white; font-size: 24px; font-weight: bold; cursor: pointer; border: none; background: transparent; }
    .close-btn:hover { color: #fca5a5; }
    .modal-body { padding: 20px; overflow-y: auto; font-size: 14px; }
</style>

<h2 style="margin-top: 0; color: #333;">Data Pesanan</h2>

<div class="tab-menu">
    <a href="?page=data_pesanan&tab=Semua" class="tab-link <?= $status_filter == 'Semua' ? 'active' : '' ?>">Semua</a>
    <a href="?page=data_pesanan&tab=Menunggu Konfirmasi" class="tab-link <?= $status_filter == 'Menunggu Konfirmasi' ? 'active' : '' ?>">Menunggu Konfirmasi</a>
    <a href="?page=data_pesanan&tab=Sedang Diproses" class="tab-link <?= $status_filter == 'Sedang Diproses' ? 'active' : '' ?>">Sedang Diproses</a>
    <a href="?page=data_pesanan&tab=Selesai" class="tab-link <?= $status_filter == 'Selesai' ? 'active' : '' ?>">Selesai</a>
</div>

<form method="GET" action="index.php">
    <input type="hidden" name="page" value="data_pesanan">
    <input type="hidden" name="tab" value="<?= $status_filter ?>">
    <div class="controls-container">
        <div>
            Tampilkan 
            <select name="limit" class="limit-select" onchange="this.form.submit()">
                <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
            </select> Data
        </div>
        <div style="display: flex; gap: 5px;">
            <input type="text" name="search" class="search-input" placeholder="🔍 Cari nama atau no. order..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn-cari">Cari</button>
        </div>
    </div>
</form>

<table class="table-pesanan">
    <thead>
        <tr>
            <th>No</th>
            <th>No. Order</th>
            <th>Nama Pelanggan</th>
            <th>Tanggal Order</th>
            <th>Tipe Pemesanan</th>
            <th>Total Transaksi</th>
            <th>Status</th>
            <th>Opsi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = $offset + 1; foreach($pesanan as $p): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td style="font-weight: bold; color: #6F4E37;">#<?= $p['id_pesanan'] ?></td>
            <td><?= $p['nama_member'] ?: '<i>Umum</i>' ?></td>
            <td><?= date('d/m/y, H:i', strtotime($p['tgl_pesanan'])) ?></td>
            
            <td>
                <?php 
                if (!empty($p['tipe_pemesanan'])) {
                    echo $p['tipe_pemesanan'];
                    
                    // Jika Makan di Tempat dan ada Nomor Meja, tampilkan di bawahnya
                    if ($p['tipe_pemesanan'] == 'Makan di Tempat' && !empty($p['no_meja'])) {
                        echo "<br><small style='color: #6F4E37; font-weight: bold; font-size: 12px;'>(Meja " . $p['no_meja'] . ")</small>";
                    }
                } else {
                    echo '<span style="color:red;">(Kosong)</span>';
                }
                ?>
            </td>
            
            <td style="font-weight: bold;">Rp <?= number_format($p['total_transaksi'], 0, ',', '.') ?></td>
            <td>
                <?php 
                    $st = trim($p['status']); 
                    if($st == 'Menunggu Konfirmasi' || $st == '') echo '<span class="badge badge-menunggu">Menunggu Konfirmasi</span>';
                    elseif($st == 'Konfirmasi') echo '<span class="badge badge-konfirmasi">Konfirmasi</span>';
                    elseif($st == 'Sedang Diproses') echo '<span class="badge badge-diproses">Sedang Diproses</span>';
                    elseif($st == 'Pesanan Siap') echo '<span class="badge badge-siap">Pesanan Siap</span>';
                    elseif($st == 'Selesai') echo '<span class="badge badge-selesai">Selesai</span>';
                    elseif($st == 'Dibatalkan') echo '<span class="badge badge-batal">Dibatalkan</span>';
                    else echo '<span class="badge" style="background:#eee; color:#333;">'.$st.'</span>';
                ?>
            </td>
            <td style="display: flex; gap: 8px; align-items: center;">
                <button type="button" onclick="bukaDetail('<?= $p['id_pesanan'] ?>')" style="background:#f1f5f9; border: 1px solid #cbd5e1; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:bold; color:#475569; cursor:pointer;">Detail</button>
                
                <?php if($st != 'Selesai' && $st != 'Dibatalkan'): ?>
                    <form action="" method="POST" style="margin: 0;">
                        <input type="hidden" name="id_pesanan" value="<?= $p['id_pesanan'] ?>">
                        <input type="hidden" name="update_status" value="1">
                        <select name="status_baru" class="select-status" onchange="this.form.submit()">
                            <option value="" disabled selected>Update Status ▾</option>
                            <?php if($st == 'Menunggu Konfirmasi' || $st == ''): ?>
                                <option value="Konfirmasi">Konfirmasi</option>
                                <option value="Dibatalkan">Batalkan</option>
                            <?php elseif($st == 'Konfirmasi'): ?>
                                <option value="Sedang Diproses">Sedang Diproses</option>
                            <?php elseif($st == 'Sedang Diproses'): ?>
                                <option value="Pesanan Siap">Pesanan Siap</option>
                            <?php elseif($st == 'Pesanan Siap'): ?>
                                <option value="Selesai">Selesai</option>
                            <?php endif; ?>
                        </select>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if(count($pesanan) == 0): ?>
        <tr><td colspan="8" style="text-align: center; color: #888; padding: 30px;">Tidak ada data pesanan yang ditemukan.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if($total_halaman > 1): ?>
<div class="pagination-container">
    <div>Menampilkan <?= count($pesanan) ?> dari total <?= $total_data ?> data pesanan.</div>
    <ul class="pagination">
        <?php if($halaman_aktif > 1): ?>
            <li><a href="?page=data_pesanan&tab=<?= $status_filter ?>&limit=<?= $limit ?>&search=<?= urlencode($search) ?>&halaman=<?= $halaman_aktif - 1 ?>" class="page-link">« Prev</a></li>
        <?php endif; ?>
        <?php for($i = 1; $i <= $total_halaman; $i++): ?>
            <li><a href="?page=data_pesanan&tab=<?= $status_filter ?>&limit=<?= $limit ?>&search=<?= urlencode($search) ?>&halaman=<?= $i ?>" class="page-link <?= ($i == $halaman_aktif) ? 'active' : '' ?>"><?= $i ?></a></li>
        <?php endfor; ?>
        <?php if($halaman_aktif < $total_halaman): ?>
            <li><a href="?page=data_pesanan&tab=<?= $status_filter ?>&limit=<?= $limit ?>&search=<?= urlencode($search) ?>&halaman=<?= $halaman_aktif + 1 ?>" class="page-link">Next »</a></li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>

<div id="modalDetail" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="judulModal">Detail Pesanan</h3>
            <button class="close-btn" onclick="tutupDetail()">&times;</button>
        </div>
        <div class="modal-body" id="isiModalDetail">
            <div style="text-align: center; padding: 30px; color: #666;">
                <i>Memuat data pesanan...</i>
            </div>
        </div>
    </div>
</div>

<script>
    function bukaDetail(id_pesanan) {
        document.getElementById('modalDetail').style.display = 'flex';
        document.getElementById('judulModal').innerText = 'Detail Order #' + id_pesanan;
        document.getElementById('isiModalDetail').innerHTML = '<div style="text-align: center; padding: 30px;"><i>⏳ Mengambil data dari server...</i></div>';

        fetch('detail_pesanan.php?id=' + id_pesanan)
            .then(response => response.text())
            .then(data => {
                document.getElementById('isiModalDetail').innerHTML = data;
            })
            .catch(error => {
                document.getElementById('isiModalDetail').innerHTML = '<div style="color:red; text-align:center;">Gagal mengambil data. Terjadi kesalahan.</div>';
            });
    }

    function tutupDetail() {
        document.getElementById('modalDetail').style.display = 'none';
    }

    window.onclick = function(event) {
        var modal = document.getElementById('modalDetail');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>