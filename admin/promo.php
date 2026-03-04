<?php
// admin/promo.php
// Menampilkan semua data promo
$query = "SELECT * FROM tb_promo ORDER BY id_promo ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$promos = $stmt->fetchAll();
?>

<style>
    .img-promo {
        width: 100px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
    }

    .badge-tipe {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: bold;
    }

    .tipe-Umum {
        background: #e0f2fe;
        color: #0369a1;
    }

    .tipe-Loyalty {
        background: #fef3c7;
        color: #92400e;
    }

    .img-promo {
        width: 80px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .table-promo {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-promo th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-size: 13px;
        border-bottom: 1px solid #eee;
    }

    .table-promo td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="margin:0; color: #333;">Manajemen Promo & Voucher</h2>
    <button onclick="bukaModalPromo()" style="background: #6F4E37; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">+ Tambah Promo</button>
</div>

<table class="table-promo">
    <thead>
        <tr>
            <th width="30">No</th>
            <th width="60">Flyer</th>
            <th>Nama Promo</th>
            <th>Tipe</th>
            <th>Potongan</th>
            <th>Masa Berlaku</th>
            <th>Deskripsi</th>
            <th>Status</th>
            <th width="120" style="text-align: center;">Opsi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($promos as $p): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><img src="../assets/gambar/promo/<?= $p['foto_promo'] ?>" class="img-promo"></td>
                <td><strong><?= $p['nama_promo'] ?></strong><br><small style="color:#888;"><?= $p['kode_promo'] ?></small></td>
                <td><span class="badge-tipe tipe-<?= $p['tipe_promo'] ?>"><?= $p['tipe_promo'] ?></span></td>
                <td><?= ($p['tipe_potongan'] == 'Persen') ? $p['potongan'] . '%' : 'Rp ' . number_format($p['potongan']) ?></td>
                <td style="font-size: 12px;"><?= date('d/m/y', strtotime($p['tgl_mulai'])) ?>
                    s/d <br>
                    <?= date('d/m/y', strtotime($p['tgl_selesai'])) ?></td>
                <td style="color: #666; font-size: 12px;"><?= $p['deskripsi'] ?></td>
                <td>
                    <span style="color: <?= ($p['status_promo'] == 'Aktif') ? '#059669' : '#dc2626' ?>; font-weight: bold;">
                        <?= $p['status_promo'] ?>
                    </span>
                </td>
                <td>
                    <button type="button" class="btn-action btn-edit"
                        onclick="editPromo('<?= $p['id_promo'] ?>', 
                           '<?= addslashes($p['nama_promo']) ?>', 
                           '<?= $p['kode_promo'] ?>', 
                           '<?= $p['tipe_promo'] ?>', 
                           '<?= $p['potongan'] ?>', 
                           '<?= $p['tipe_potongan'] ?>', 
                           '<?= $p['tgl_mulai'] ?>', 
                           '<?= $p['tgl_selesai'] ?>', 
                           '<?= $p['status_promo'] ?>', 
                           '<?= $p['foto_promo'] ?>', 
                           '<?= $p['min_poin'] ?>', 
                           '<?= addslashes($p['deskripsi']) ?>')"> Edit
                    </button>
                    <a href="hapus/hapus_promo.php?id=<?= $p['id_promo'] ?>" class="btn-action btn-delete" onclick="return confirm('Hapus promo ini?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="modalPromo" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div style="background:white; padding:25px; border-radius:12px; width:600px; box-shadow:0 5px 20px rgba(0,0,0,0.3); max-height: 90vh; overflow-y: auto;">
        <h3 id="judulPromo" style="margin-top:0;">Form Promo</h3>
        <form id="formPromo" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_promo" id="id_promo">
            <input type="hidden" name="foto_lama" id="foto_lama_p">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px 15px;">
                <div style="grid-column: span 2;">
                    <label style="display:block; font-size:13px; margin-bottom:8px;">Nama Promo</label>
                    <input type="text" name="nama_promo" id="nama_p" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;" required>
                </div>
                <div style="grid-column: span 2; margin-bottom: 5px;">
                    <label style="display:block; font-size:13px; margin-bottom:8px; font-weight: bold; color: #555;">Deskripsi Promo</label>
                    <textarea name="deskripsi" id="desc_p" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box; height: 80px;" placeholder="Tuliskan detail promo atau syarat & ketentuan..."></textarea>
                </div>
                <div>
                    <label style="display:block; font-size:13px; margin-bottom:8px;">Kode Promo</label>
                    <input type="text" name="kode_promo" id="kode_p" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;" placeholder="CONTOH: HEMAT5K">
                </div>
                <div>
                    <label style="display:block; font-size:13px; margin-bottom:8px;">Tipe Promo</label>
                    <select name="tipe_promo" id="tipe_p" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                        <option value="Umum">Umum</option>
                        <option value="Loyalty">Loyalty (Member)</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:13px; margin-bottom:8px;">Potongan</label>
                    <input type="number" name="potongan" id="potongan_p" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;" required>
                </div>
                <div>
                    <label style="display:block; font-size:13px; margin-bottom:8px;">Tipe Potongan</label>
                    <select name="tipe_potongan" id="tpotongan_p" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                        <option value="Nominal">Nominal (Rupiah)</option>
                        <option value="Persen">Persen (%)</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:13px; margin-bottom:8px;">Min. Poin (Untuk Member)</label>
                    <input type="number" name="min_poin" id="min_poin_p" value="0" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block; font-size:13px; margin-bottom:8px;">Status</label>
                    <select name="status_promo" id="status_p" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:13px; margin-bottom:8px;">Tgl Mulai</label>
                    <input type="date" name="tgl_mulai" id="tgl_m" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing: border-box;" required>
                </div>
                <div>
                    <label style="display:block; font-size:13px; margin-bottom:8px;">Tgl Selesai</label>
                    <input type="date" name="tgl_selesai" id="tgl_s" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;" required>
                </div>
                <div style="grid-column: span 2;">
                    <label style="display:block; font-size:13px; margin-bottom:8px;">Foto Promo <span id="info_foto_p" style="color:red; font-size:10px;"></span></label>
                    <input type="file" name="foto" id="foto_p" accept="image/*">
                </div>
            </div>

            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" style="flex:1; background:#6F4E37; color:white; padding:10px; border:none; border-radius:6px; font-weight:bold;">Simpan</button>
                <button type="button" onclick="tutupModalPromo()" style="flex:1; background:#eee; padding:10px; border:none; border-radius:6px;">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalPromo() {
        document.getElementById('modalPromo').style.display = 'flex';
        document.getElementById('judulPromo').innerText = 'Tambah Promo Baru';
        document.getElementById('formPromo').action = 'tambah/tambah_promo.php';
        document.getElementById('formPromo').reset();
        document.getElementById('foto_p').required = true;
    }

    function editPromo(id, nama, kode, tipe, pot, tpot, tm, ts, st, foto, minp, desc) {
        document.getElementById('modalPromo').style.display = 'flex';
        document.getElementById('judulPromo').innerText = 'Edit Promo';
        document.getElementById('formPromo').action = 'edit/edit_promo.php';
        document.getElementById('id_promo').value = id;
        document.getElementById('nama_p').value = nama;
        document.getElementById('kode_p').value = kode;
        document.getElementById('tipe_p').value = tipe;
        document.getElementById('potongan_p').value = pot;
        document.getElementById('tpotongan_p').value = tpot;
        document.getElementById('tgl_m').value = tm;
        document.getElementById('tgl_s').value = ts;
        document.getElementById('status_p').value = st;
        document.getElementById('foto_lama_p').value = foto;
        document.getElementById('min_poin_p').value = minp;
        document.getElementById('desc_p').value = desc;
        document.getElementById('foto_p').required = false;
        document.getElementById('info_foto_p').innerText = '(Kosongkan jika tidak ganti)';
    }

    function tutupModalPromo() {
        document.getElementById('modalPromo').style.display = 'none';
    }
</script>