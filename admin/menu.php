<?php

// PENCARIAN & PAGINATION
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit  = 10;
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman_aktif - 1) * $limit;

$where_sql = "1=1";
$params = [];
if (!empty($search)) {
    $where_sql .= " AND (m.nama_menu LIKE ? OR k.nama_kategori LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// AMBIL DATA MENU
$query = "SELECT m.*, k.nama_kategori 
          FROM tb_menu m 
          JOIN tb_kategori k ON m.id_kategori = k.id_kategori 
          WHERE $where_sql 
          ORDER BY m.id_menu ASC LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$menus = $stmt->fetchAll();

// AMBIL KATEGORI UNTUK DROPDOWN
$categories = $conn->query("SELECT * FROM tb_kategori ORDER BY nama_kategori ASC")->fetchAll();
?>

<style>
    .img-thumbnail-menu {
        width: 55px;
        height: 55px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .table-menu {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-menu th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-size: 13px;
        border-bottom: 1px solid #eee;
    }

    .table-menu td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        vertical-align: middle;
    }

    .badge-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
        display: inline-block;
    }

    .status-Tersedia {
        background: #d1fae5;
        color: #059669;
        border: 1px solid #10b981;
    }

    .status-Habis {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #f87171;
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
    <h2 style="margin:0; color: #333;">Daftar Menu</h2>
    <button onclick="bukaModalTambah()" style="background: #6F4E37; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">+ Tambah Menu</button>
</div>

<div style="margin-bottom: 15px;">
    <form method="GET" action="index.php" style="display: flex; gap: 8px;">
        <input type="hidden" name="page" value="menu">
        <input type="text" name="search" placeholder="Cari nama menu atau kategori..." value="<?= htmlspecialchars($search) ?>" style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; width: 300px;">
        <button type="submit" style="padding: 8px 15px; background: #6F4E37; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cari</button>
    </form>
</div>

<table class="table-menu">
    <thead>
        <tr>
            <th width="30">No</th>
            <th width="60">Foto</th>
            <th>Menu</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Status</th>
            <th width="120" style="text-align: center;">Opsi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = $offset + 1;
        foreach ($menus as $m): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><img src="../assets/gambar/menu/<?= $m['foto'] ?>" class="img-thumbnail-menu"></td>
                <td><strong><?= $m['nama_menu'] ?></strong></td>
                <td><?= $m['nama_kategori'] ?></td>
                <td style="font-weight: bold;">Rp <?= number_format($m['harga']) ?></td>
                <td><?= $m['stok'] ?></td>
                <td>
                    <?php
                    // Logika otomatis: Paksa jadi Habis jika stok 0
                    $status_nyata = ($m['stok'] <= 0) ? 'Habis' : $m['status_menu'];
                    ?>
                    <span class="badge-status status-<?= $status_nyata ?>">
                        <?= $status_nyata ?>
                    </span>
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn-action btn-edit" onclick="bukaModalEdit('<?= $m['id_menu'] ?>', '<?= addslashes(htmlspecialchars($m['nama_menu'])) ?>', '<?= $m['id_kategori'] ?>', '<?= $m['harga'] ?>', '<?= $m['stok'] ?>', '<?= $m['foto'] ?>')">Edit</button>
                    <a href="hapus/hapus_menu.php?id=<?= $m['id_menu'] ?>" class="btn-action btn-delete" onclick="return confirm('Hapus menu ini beserta fotonya?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="modalMenu" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div style="background:white; padding:25px; border-radius:12px; width:500px; box-shadow:0 5px 20px rgba(0,0,0,0.3);">
        <h3 id="judulModal" style="margin-top:0;">Form Menu</h3>
        <form id="formMenu" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_menu" id="id_menu">
            <input type="hidden" name="foto_lama" id="foto_lama">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div style="grid-column: span 2;">
                    <label style="display:block; font-size:13px; margin-bottom:5px;">Nama Menu</label>
                    <input type="text" name="nama_menu" id="nama_menu" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;" required>
                </div>
                <div>
                    <label style="display:block; font-size:13px; margin-bottom:5px;">Kategori</label>
                    <select name="id_kategori" id="id_kategori" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id_kategori'] ?>"><?= $cat['nama_kategori'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:13px; margin-bottom:5px;">Harga (Rp)</label>
                    <input type="number" name="harga" id="harga" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;" required>
                </div>
                <div>
                    <label style="display:block; font-size:13px; margin-bottom:5px;">Stok <span style="font-size: 10px; color: #666;">(Status akan menyesuaikan otomatis)</span></label>
                    <input type="number" name="stok" id="stok" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;" required>
                </div>
                <div style="grid-column: span 2;">
                    <label style="display:block; font-size:13px; margin-bottom:5px;">Foto Menu <span id="info_foto" style="color:red; font-size:11px;"></span></label>
                    <input type="file" name="foto" id="foto_input" accept="image/png, image/jpeg, image/jpg">
                </div>
            </div>
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" style="flex:1; background:#6F4E37; color:white; padding:10px; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">Simpan</button>
                <button type="button" onclick="tutupModalMenu()" style="flex:1; background:#eee; padding:10px; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalTambah() {
        document.getElementById('modalMenu').style.display = 'flex';
        document.getElementById('judulModal').innerText = 'Tambah Menu Baru';
        document.getElementById('formMenu').action = 'tambah/tambah_menu.php';
        document.getElementById('formMenu').reset();
        document.getElementById('foto_input').required = true;
        document.getElementById('info_foto').innerText = '*Wajib diisi';
    }

    function bukaModalEdit(id, nama, id_kat, harga, stok, foto) {
        document.getElementById('modalMenu').style.display = 'flex';
        document.getElementById('judulModal').innerText = 'Edit Menu';
        document.getElementById('formMenu').action = 'edit/edit_menu.php';

        document.getElementById('id_menu').value = id;
        document.getElementById('nama_menu').value = nama;
        document.getElementById('id_kategori').value = id_kat;
        document.getElementById('harga').value = harga;
        document.getElementById('stok').value = stok;
        document.getElementById('foto_lama').value = foto;

        document.getElementById('foto_input').required = false;
        document.getElementById('info_foto').innerText = '(Abaikan jika tidak ingin ganti foto)';
    }

    function tutupModalMenu() {
        document.getElementById('modalMenu').style.display = 'none';
    }
</script>