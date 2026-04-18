<style>
    .btn-add {
        background: #6F4E37;
        color: white;
        padding: 10px 15px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        display: inline-block;
        margin-bottom: 20px;
        border: none;
        cursor: pointer;
    }

    .table-kategori {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-kategori th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-size: 13px;
        border-bottom: 1px solid #eee;
        color: #555;
    }

    .table-kategori td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        color: #333;
    }

    .btn-action {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
        border: none;
    }

    .btn-edit {
        background: #e0e7ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
    }

    .btn-delete {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="color: #333; margin: 0;">Manajemen Kategori Menu</h2>
    <button class="btn-add" onclick="bukaModalTambah()" style="margin: 0;">+ Tambah Kategori</button>
</div>

<div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;">
    <form method="GET" action="index.php" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <input type="hidden" name="controller" value="admin">
        <input type="hidden" name="action" value="kategori_menu">
        
        <div style="color: #555; font-size: 14px;">
            Tampilkan
            <select name="limit" onchange="this.form.submit()" style="padding: 8px; border: 1px solid #ddd; border-radius: 5px; outline: none; cursor: pointer; margin: 0 5px;">
                <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
            </select> Data
        </div>

        <div style="display: flex; gap: 5px;">
            <input type="text" name="search" placeholder="Cari kategori..." value="<?= htmlspecialchars($search) ?>" 
                   style="padding: 10px 15px; width: 250px; border: 1px solid #ddd; border-radius: 8px; outline: none;">
            <button type="submit" style="padding: 10px 20px; background: #6F4E37; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
                Cari
            </button>
        </div>
    </form>
</div>

<table class="table-kategori">
    <thead>
        <tr>
            <th width="50">No</th>
            <th>Nama Kategori</th>
            <th width="150" style="text-align: center;">Opsi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($kategori as $k): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><strong><?= $k['nama_kategori'] ?></strong></td>
                <td style="text-align: center;">
                    <button class="btn-action btn-edit" onclick="bukaModalEdit('<?= $k['id_kategori'] ?>', '<?= $k['nama_kategori'] ?>')">Edit</button>
                    <a href="index.php?controller=admin&action=hapus_kategori&id=<?= $k['id_kategori'] ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="modalKategori" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div style="background:white; padding:25px; border-radius:12px; width:400px; box-shadow:0 5px 15px rgba(0,0,0,0.2);">
        <h3 id="judulModal" style="margin-top:0;">Form Kategori</h3>

        <form id="formKategori" action="" method="POST">
            <input type="hidden" name="id_kategori" id="id_kategori">
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-size:14px;">Nama Kategori</label>
                <input type="text" name="nama_kategori" id="nama_input" style="width:100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing:border-box;" required>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" style="flex:1; background:#6F4E37; color:white; padding:10px; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">Simpan</button>
                <button type="button" onclick="tutupModal()" style="flex:1; border:1px solid #ccc; background:#eee; border-radius:6px; cursor:pointer; font-weight:bold;">Batal</button>
            </div>
        </form>
    </div>
</div>

<div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
    <div style="font-size: 14px; color: #666;">
        Menampilkan <?= count($kategori) ?> dari <?= $total_data ?> kategori
    </div>
    <div style="display: flex; gap: 5px;">
        <?php if ($halaman_aktif > 1): ?>
            <a href="index.php?controller=admin&action=kategori_menu&halaman=<?= $halaman_aktif - 1 ?>&search=<?= urlencode($search) ?>&limit=<?= $limit ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #333; background: white; font-size: 14px;">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
            <a href="index.php?controller=admin&action=kategori_menu&halaman=<?= $i ?>&search=<?= urlencode($search) ?>&limit=<?= $limit ?>" 
               style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #333; font-size: 14px; <?= ($i == $halaman_aktif) ? 'background: #6F4E37; color: white; border-color: #6F4E37;' : 'background: white;' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($halaman_aktif < $total_halaman): ?>
            <a href="index.php?controller=admin&action=kategori_menu&halaman=<?= $halaman_aktif + 1 ?>&search=<?= urlencode($search) ?>&limit=<?= $limit ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #333; background: white; font-size: 14px;">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>

<script>
    function bukaModalTambah() {
        document.getElementById('modalKategori').style.display = 'flex';
        document.getElementById('judulModal').innerText = 'Tambah Kategori';
        document.getElementById('id_kategori').value = '';
        document.getElementById('nama_input').value = '';
        // Arahkan ke method tambah_kategori di AdminController
        document.getElementById('formKategori').action = 'index.php?controller=admin&action=tambah_kategori';
    }

    function bukaModalEdit(id, nama) {
        document.getElementById('modalKategori').style.display = 'flex';
        document.getElementById('judulModal').innerText = 'Edit Kategori';
        document.getElementById('id_kategori').value = id;
        document.getElementById('nama_input').value = nama;
        // Arahkan ke method edit_kategori di AdminController
        document.getElementById('formKategori').action = 'index.php?controller=admin&action=edit_kategori';
    }

    function tutupModal() {
        document.getElementById('modalKategori').style.display = 'none';
    }
</script>