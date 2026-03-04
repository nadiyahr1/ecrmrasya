<?php
// admin/kategori_menu.php
// HANYA UNTUK TAMPILAN (READ DATA)

$kategori = $conn->query("SELECT * FROM tb_kategori ORDER BY id_kategori ASC")->fetchAll();
?>

<style>
    .btn-add { background: #6F4E37; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; display: inline-block; margin-bottom: 20px; border: none; cursor: pointer; }
    .table-kategori { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .table-kategori th { background: #f8f9fa; padding: 15px; text-align: left; font-size: 13px; border-bottom: 1px solid #eee; color: #555; }
    .table-kategori td { padding: 15px; border-bottom: 1px solid #eee; color: #333; }
    .btn-action { padding: 5px 10px; border-radius: 4px; font-size: 12px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; }
    .btn-edit { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .btn-delete { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2 style="color: #333; margin: 0;">Manajemen Kategori Menu</h2>
    <button class="btn-add" onclick="bukaModalTambah()" style="margin: 0;">+ Tambah Kategori</button>
</div>
<br>

<table class="table-kategori">
    <thead>
        <tr>
            <th width="50">No</th>
            <th>Nama Kategori</th>
            <th width="150" style="text-align: center;">Opsi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; foreach($kategori as $k): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><strong><?= $k['nama_kategori'] ?></strong></td>
            <td style="text-align: center;">
                <button class="btn-action btn-edit" onclick="bukaModalEdit('<?= $k['id_kategori'] ?>', '<?= $k['nama_kategori'] ?>')">Edit</button>
                <a href="hapus/hapus_kategori.php?id=<?= $k['id_kategori'] ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
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

<script>
    function bukaModalTambah() {
        document.getElementById('modalKategori').style.display = 'flex';
        document.getElementById('judulModal').innerText = 'Tambah Kategori';
        document.getElementById('id_kategori').value = '';
        document.getElementById('nama_input').value = '';
        // Ubah arah form ke folder tambah
        document.getElementById('formKategori').action = 'tambah/tambah_kategori.php';
    }

    function bukaModalEdit(id, nama) {
        document.getElementById('modalKategori').style.display = 'flex';
        document.getElementById('judulModal').innerText = 'Edit Kategori';
        document.getElementById('id_kategori').value = id;
        document.getElementById('nama_input').value = nama;
        // Ubah arah form ke folder edit
        document.getElementById('formKategori').action = 'edit/edit_kategori.php';
    }

    function tutupModal() {
        document.getElementById('modalKategori').style.display = 'none';
    }
</script>