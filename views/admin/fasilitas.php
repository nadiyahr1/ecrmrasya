<style>
    .img-fasilitas { width: 80px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
    .table-fasilitas { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
    .table-fasilitas th { background: #f8f9fa; padding: 15px; text-align: left; font-size: 13px; border-bottom: 1px solid #eee; white-space: nowrap; }
    .table-fasilitas td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 14px; white-space: nowrap; }
    .btn-action { padding: 5px 10px; border-radius: 4px; font-size: 12px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; }
    .btn-edit { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .btn-delete { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="margin:0; color: #333;">Data Fasilitas Kafe</h2>
    <button onclick="bukaModalFasilitas()" style="background: #6F4E37; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">+ Tambah Fasilitas</button>
</div>

<div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;">
    <form method="GET" action="index.php" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <input type="hidden" name="controller" value="admin">
        <input type="hidden" name="action" value="fasilitas">
        
        <div style="color: #555; font-size: 14px;">
            Tampilkan
            <select name="limit" onchange="this.form.submit()" style="padding: 8px; border: 1px solid #ddd; border-radius: 5px; outline: none; cursor: pointer; margin: 0 5px;">
                <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
            </select> Data
        </div>

        <div style="display: flex; gap: 5px;">
            <input type="text" name="search" placeholder="Cari fasilitas..." value="<?= htmlspecialchars($search ?? '') ?>" 
                   style="padding: 10px 15px; width: 250px; border: 1px solid #ddd; border-radius: 8px; outline: none;">
            <button type="submit" style="padding: 10px 20px; background: #6F4E37; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
                Cari
            </button>
        </div>
    </form>
</div>

<div style="overflow-x: auto; width: 100%; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
<table class="table-fasilitas">
    <thead>
        <tr>
            <th width="30">No</th>
            <th width="100">Foto</th>
            <th>Nama Fasilitas</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Deskripsi</th>
            <th width="120" style="text-align: center;">Opsi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($fasilitas as $f): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><img src="assets/gambar/fasilitas/<?= $f['foto_fasilitas'] ?>" class="img-fasilitas"></td>
                <td><strong><?= htmlspecialchars($f['nama_fasilitas']) ?></strong></td>
                <td>Rp <?= number_format($f['harga']) ?> / <?= htmlspecialchars($f['satuan']) ?></td>
                <td>
                    <?php if ($f['status_fasilitas'] == 'Tersedia'): ?>
                        <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Tersedia</span>
                    <?php elseif ($f['status_fasilitas'] == 'Penuh'): ?>
                        <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Penuh</span>
                    <?php else: ?>
                        <span style="background: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Perbaikan</span>
                    <?php endif; ?>
                </td>
                <td style="color: #666; font-size: 12px;"><?= htmlspecialchars($f['deskripsi']) ?></td>
                <td style="text-align: center;">
                    <button type="button" class="btn-action btn-edit"
                        onclick="editFasilitas('<?= $f['id_fasilitas'] ?>', '<?= addslashes(htmlspecialchars($f['nama_fasilitas'])) ?>', '<?= $f['harga'] ?>', '<?= htmlspecialchars($f['satuan']) ?>', '<?= addslashes(htmlspecialchars($f['deskripsi'])) ?>', '<?= $f['status_fasilitas'] ?>', '<?= $f['foto_fasilitas'] ?>')">
                        Edit
                    </button>
                    <a href="index.php?controller=admin&action=hapus_fasilitas&id=<?= $f['id_fasilitas'] ?>" class="btn-action btn-delete" onclick="return confirm('Hapus fasilitas ini?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
                    </div>

<div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
    <div style="font-size: 14px; color: #666;">
        Menampilkan <?= count($fasilitas) ?> dari <?= $total_data ?> fasilitas
    </div>
    <div style="display: flex; gap: 5px;">
        <?php if ($halaman_aktif > 1): ?>
            <a href="index.php?controller=admin&action=fasilitas&halaman=<?= $halaman_aktif - 1 ?>&search=<?= urlencode($search) ?>&limit=<?= $limit ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #333; background: white; font-size: 14px;">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
            <a href="index.php?controller=admin&action=fasilitas&halaman=<?= $i ?>&search=<?= urlencode($search) ?>&limit=<?= $limit ?>" 
               style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #333; font-size: 14px; <?= ($i == $halaman_aktif) ? 'background: #6F4E37; color: white; border-color: #6F4E37;' : 'background: white;' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($halaman_aktif < $total_halaman): ?>
            <a href="index.php?controller=admin&action=fasilitas&halaman=<?= $halaman_aktif + 1 ?>&search=<?= urlencode($search) ?>&limit=<?= $limit ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #333; background: white; font-size: 14px;">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>

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
        document.getElementById('formFasilitas').action = 'index.php?controller=admin&action=tambah_fasilitas';
        document.getElementById('formFasilitas').reset();
        document.getElementById('foto_input_f').required = true;
    }

    function editFasilitas(id, nama, harga, satuan, desc, status, foto) {
        document.getElementById('modalFasilitas').style.display = 'flex';
        document.getElementById('judulFasilitas').innerText = 'Edit Fasilitas';
        document.getElementById('formFasilitas').action = 'index.php?controller=admin&action=edit_fasilitas';
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