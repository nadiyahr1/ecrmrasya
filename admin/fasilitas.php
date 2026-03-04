<?php
// admin/fasilitas.php
// HANYA UNTUK TAMPILAN

$query = "SELECT * FROM tb_fasilitas ORDER BY id_fasilitas ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$fasilitas = $stmt->fetchAll();
?>

<style>
    .img-fasilitas {
        width: 80px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .table-fasilitas {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-fasilitas th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-size: 13px;
        border-bottom: 1px solid #eee;
    }

    .table-fasilitas td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="margin:0; color: #333;">Data Fasilitas Kafe</h2>
    <button onclick="bukaModalFasilitas()" style="background: #6F4E37; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">+ Tambah Fasilitas</button>
</div>

<table class="table-fasilitas">
    <thead>
        <tr>
            <th width="30">No</th>
            <th width="100">Foto</th>
            <th>Nama Fasilitas</th>
            <th>Harga</th>
            <th>Deskripsi</th>
            <th width="120" style="text-align: center;">Opsi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($fasilitas as $f): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><img src="../assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>" class="img-fasilitas"></td>
                <td><strong><?= $f['nama_fasilitas'] ?></strong></td>
                <td>Rp <?= number_format($f['harga']) ?> / <?= $f['satuan'] ?></td>
                <td style="color: #666; font-size: 12px;"><?= $f['deskripsi'] ?></td>
                <td style="text-align: center;">
                    <button type="button" class="btn-action btn-edit"
                        onclick="editFasilitas('<?= $f['id_fasilitas'] ?>', 
                               '<?= addslashes($f['nama_fasilitas']) ?>', 
                               '<?= $f['harga'] ?>',
                               '<?= $f['satuan'] ?>',
                               '<?= addslashes($f['deskripsi']) ?>', 
                               '<?= $f['status_fasilitas'] ?>',
                               '<?= $f['foto_fasilitas'] ?>')">
                        Edit
                    </button>
                    <a href="hapus/hapus_fasilitas.php?id=<?= $f['id_fasilitas'] ?>" class="btn-action btn-delete" onclick="return confirm('Hapus fasilitas ini?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="modalFasilitas" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div style="background:white; padding:25px; border-radius:12px; width:450px; box-shadow:0 5px 20px rgba(0,0,0,0.3);">
        <h3 id="judulFasilitas" style="margin-top:0;">Form Fasilitas</h3>
        <form id="formFasilitas" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_fasilitas" id="id_fasilitas">
            <input type="hidden" name="foto_lama" id="foto_lama_f">

            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:13px; margin-bottom:5px;">Nama Fasilitas</label>
                <input type="text" name="nama_fasilitas" id="nama_f" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display:block; font-size:13px; margin-bottom:8px; font-weight: bold;">Harga (Rp)</label>
                    <input type="number" name="harga" id="harga_f" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing: border-box;" required>
                </div>
                <div>
                    <label style="display:block; font-size:13px; margin-bottom:8px; font-weight: bold;">Satuan Hitung</label>
                    <select name="satuan" id="satuan_f" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing: border-box;" required>
                        <option value="Jam">Per Jam</option>
                        <option value="Orang">Per Orang</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:13px; margin-bottom:5px;">Deskripsi Singkat</label>
                <textarea name="deskripsi" id="deskripsi_f" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box; height:60px;"></textarea>
            </div>
            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:13px; margin-bottom:5px;">Foto Fasilitas <span id="info_foto_f" style="color:red; font-size:10px;"></span></label>
                <input type="file" name="foto" id="foto_input_f" accept="image/*">
            </div>
            <div style="margin-bottom: 12px;">
                <label style="display:block; font-size:13px; margin-bottom:8px; font-weight: bold;">Status Fasilitas</label>
                <select name="status_fasilitas" id="status_f" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;" required>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Penuh">Penuh</option>
                    <option value="Perbaikan">Perbaikan</option>
                </select>
            </div>
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" style="flex:1; background:#6F4E37; color:white; padding:10px; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">Simpan</button>
                <button type="button" onclick="tutupModalFasilitas()" style="flex:1; background:#eee; padding:10px; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalFasilitas() {
        document.getElementById('modalFasilitas').style.display = 'flex';
        document.getElementById('judulFasilitas').innerText = 'Tambah Fasilitas';
        document.getElementById('formFasilitas').action = 'tambah/tambah_fasilitas.php';
        document.getElementById('formFasilitas').reset();
        document.getElementById('foto_input_f').required = true;
    }

    function editFasilitas(id, nama, harga, satuan, desc, status, foto) {
        document.getElementById('modalFasilitas').style.display = 'flex';
        document.getElementById('judulFasilitas').innerText = 'Edit Fasilitas';
        document.getElementById('formFasilitas').action = 'edit/edit_fasilitas.php';
        document.getElementById('id_fasilitas').value = id;
        document.getElementById('nama_f').value = nama;
        document.getElementById('harga_f').value = harga;
        document.getElementById('satuan_f').value = satuan;
        document.getElementById('deskripsi_f').value = desc;
        document.getElementById('status_f').value = status;
        document.getElementById('foto_lama_f').value = foto;
        document.getElementById('foto_input_f').required = false;
        document.getElementById('info_foto_f').innerText = '(Abaikan jika tidak ganti foto)';
    }

    function tutupModalFasilitas() {
        document.getElementById('modalFasilitas').style.display = 'none';
    }
</script>