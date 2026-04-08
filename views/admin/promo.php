<style>
    .img-promo {
        width: 80px;
        height: 50px;
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

    .tipe-Level {
        background: #fef3c7;
        color: #92400e;
    }

    .tipe-Tukar_Poin {
        background: #fce7f3;
        color: #be185d;
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
        vertical-align: middle;
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
    <h2 style="margin:0; color: #333;">Manajemen Promo & Voucher</h2>
    <button onclick="bukaModalPromo()" style="background: #6F4E37; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">+ Tambah Promo</button>
</div>

<table class="table-promo">
    <thead>
        <tr>
            <th>No</th>
            <th>Flyer</th>
            <th>Info Promo</th>
            <th>Kategori & Kuota</th>
            <th>Potongan</th>
            <th>Masa Berlaku</th>
            <th>Status</th>
            <th style="text-align: center;">Opsi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($promos as $p): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><img src="assets/gambar/promo/<?= htmlspecialchars($p['foto_promo']) ?>" class="img-promo"></td>
                <td>
                    <strong><?= htmlspecialchars($p['nama_promo']) ?></strong><br>
                    <small style="color:#888;">Kode: <?= $p['kode_promo'] ?: '-' ?></small>
                </td>
                <td>
                    <span class="badge-tipe tipe-<?= $p['tipe_promo'] ?>"><?= str_replace('_', ' ', $p['tipe_promo']) ?></span><br>
                    <?php if ($p['tipe_promo'] == 'Level'): ?>
                        <small style="color: #6F4E37;">Target: <?= $p['nama_level'] ?></small><br>
                    <?php elseif ($p['tipe_promo'] == 'Tukar_Poin'): ?>
                        <small style="color: #6F4E37;">Biaya: <?= $p['min_poin'] ?> Poin</small><br>
                    <?php endif; ?>
                    <small style="color:#666;">Kuota: <?= $p['kuota'] ?: 'Tanpa Batas' ?></small>
                </td>
                <td>
                    <?php
                    if ($p['tipe_potongan'] == 'Persen') echo $p['potongan'] . '%';
                    elseif ($p['tipe_potongan'] == 'Nominal') echo 'Rp ' . number_format($p['potongan']);
                    else echo 'Free Item';
                    ?>
                </td>
                <td style="font-size: 12px;">
                    <?= date('d/m/y', strtotime($p['tgl_mulai'])) ?> - <?= date('d/m/y', strtotime($p['tgl_selesai'])) ?>
                </td>
                <td>
                    <span style="color: <?= ($p['status_promo'] == 'Aktif') ? '#059669' : '#dc2626' ?>; font-weight: bold;">
                        <?= $p['status_promo'] ?>
                    </span>
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn-action btn-edit"
                        onclick="editPromo(
                           '<?= $p['id_promo'] ?>', 
                           '<?= addslashes(htmlspecialchars($p['nama_promo'])) ?>', 
                           '<?= htmlspecialchars($p['kode_promo']) ?>', 
                           '<?= addslashes(htmlspecialchars($p['deskripsi'])) ?>',
                           '<?= addslashes(htmlspecialchars($p['syarat_ketentuan'] ?? '')) ?>',
                           '<?= $p['tipe_promo'] ?>', 
                           '<?= $p['potongan'] ?>', 
                           '<?= $p['tipe_potongan'] ?>', 
                           '<?= $p['min_poin'] ?>', 
                           '<?= $p['target_level'] ?>', 
                           '<?= $p['kuota'] ?>',
                           '<?= $p['min_belanja'] ?? 0 ?>',
                           '<?= $p['tgl_mulai'] ?>', 
                           '<?= $p['tgl_selesai'] ?>', 
                           '<?= $p['status_promo'] ?>', 
                           '<?= $p['foto_promo'] ?>',
                           '<?= $p['id_menu_trigger'] ?? '' ?>',
                           '<?= $p['id_menu_bonus'] ?? '' ?>',
                           '<?= $p['min_beli'] ?? '1' ?>'
                        )">Edit</button>
                    <a href="index.php?controller=admin&action=hapus_promo&id=<?= $p['id_promo'] ?>" class="btn-action btn-delete" onclick="return confirm('Hapus promo ini?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="modalPromo" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; backdrop-filter: blur(2px);">
    <div style="background:white; padding:30px; border-radius:16px; width:700px; box-shadow:0 10px 30px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; border-bottom:2px solid #f0f0f0; padding-bottom:15px;">
            <h3 id="judulPromo" style="margin:0; color: #6F4E37; font-size: 22px;">Form Data Promo</h3>
            <button type="button" onclick="tutupModalPromo()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#999;">&times;</button>
        </div>

        <form id="formPromo" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_promo" id="id_promo">
            <input type="hidden" name="foto_lama" id="foto_lama_p">

            <div style="display: grid; grid-template-columns: 1fr 1fr; column-gap: 30px; row-gap: 20px;">

                <div style="grid-column: span 2;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Nama Promo</label>
                    <input type="text" name="nama_promo" id="nama_p" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; box-sizing: border-box; outline:none;" required>
                </div>

                <div style="grid-column: span 2;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Deskripsi Promo</label>
                    <textarea name="deskripsi" id="desc_p" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; height: 60px; box-sizing: border-box; font-family:inherit; outline:none;"></textarea>
                </div>

                <div style="grid-column: span 2;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Syarat & Ketentuan Promo <small style="color:#999;">(Opsional, tulis poin per poin)</small></label>
                    <textarea name="syarat_ketentuan" id="syarat_p" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; height: 80px; box-sizing: border-box; font-family:inherit; outline:none;" placeholder="1. Promo tidak dapat digabung...&#10;2. Hanya berlaku untuk dine-in..."></textarea>
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Kode Promo</label>
                    <input type="text" name="kode_promo" id="kode_p" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; box-sizing: border-box; text-transform:uppercase;">
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Tipe Promo</label>
                    <select name="tipe_promo" id="tipe_p" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; box-sizing: border-box; background:white;" onchange="toggleTipePromo()" required>
                        <option value="Umum">Umum</option>
                        <option value="Level">Level (Khusus Level)</option>
                        <option value="Tukar_Poin">Tukar Poin</option>
                    </select>
                </div>

                <div id="form_group_level" style="display:none; grid-column: span 2; background: #fffbeb; padding: 15px; border-radius: 8px; border: 1px solid #fcd34d;">
                    <label style="display:block; font-size:13px; font-weight:bold; color:#92400e; margin-bottom:8px;">Target Level Member</label>
                    <select name="target_level" id="target_level_p" style="width:100%; padding:10px; border:1px solid #fcd34d; border-radius:6px; box-sizing: border-box;">
                        <option value="">-- Pilih Level --</option>
                        <?php foreach ($levels as $lvl): ?>
                            <option value="<?= $lvl['id_level'] ?>"><?= $lvl['nama_level'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="form_group_poin" style="display:none; grid-column: span 2; background: #fdf2f8; padding: 15px; border-radius: 8px; border: 1px solid #fbcfe8;">
                    <label style="display:block; font-size:13px; font-weight:bold; color:#be185d; margin-bottom:8px;">Harga Poin (Poin yang dibutuhkan)</label>
                    <input type="number" name="min_poin" id="min_poin_p" style="width:100%; padding:10px; border:1px solid #fbcfe8; border-radius:6px; box-sizing: border-box;">
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Kuota Penggunaan</label>
                    <input type="number" name="kuota" id="kuota_p" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; box-sizing: border-box;">
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Minimal Belanja (Rp) <small style="color:#999;">(Isi 0 jika tanpa minimal)</small></label>
                    <input type="number" name="min_belanja" id="min_belanja_p" value="0" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; box-sizing: border-box;">
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Tipe Potongan</label>
                    <select name="tipe_potongan" id="tpotongan_p" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; box-sizing: border-box; background:white;" onchange="toggleTipePotongan()" required>
                        <option value="Nominal">Nominal (Rupiah)</option>
                        <option value="Persen">Persen (%)</option>
                        <option value="Produk">Produk (Buy X Get Y)</option>
                    </select>
                </div>

                <div id="form_group_produk" style="display:none; grid-column: span 2; background: #f0fdf4; padding: 15px; border-radius: 8px; border: 1px solid #bbf7d0;">
                    <p style="margin:0 0 10px 0; font-weight:bold; color:#166534; font-size:13px;">Pengaturan Promo Produk</p>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 80px; gap: 15px;">
                        <div>
                            <label style="display:block; font-size:12px; margin-bottom:5px;">Jika membeli menu...</label>
                            <select name="id_menu_trigger" id="id_menu_trigger" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing: border-box;">
                                <option value="">-- Pilih Menu Syarat --</option>
                                <?php foreach ($menus as $m): ?>
                                    <option value="<?= $m['id_menu'] ?>"><?= htmlspecialchars($m['nama_menu']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; margin-bottom:5px;">Maka gratis menu...</label>
                            <select name="id_menu_bonus" id="id_menu_bonus" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing: border-box;">
                                <option value="">-- Pilih Menu Hadiah --</option>
                                <?php foreach ($menus as $m): ?>
                                    <option value="<?= $m['id_menu'] ?>"><?= htmlspecialchars($m['nama_menu']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; margin-bottom:5px;">Min. Beli</label>
                            <input type="number" name="min_beli" id="min_beli_p" value="1" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing: border-box;">
                        </div>
                    </div>
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Nilai Potongan</label>
                    <input type="number" name="potongan" id="potongan_p" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; box-sizing: border-box;" required>
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Status Promo</label>
                    <select name="status_promo" id="status_p" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; box-sizing: border-box; background:white;">
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" id="tgl_m" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; box-sizing: border-box;" required>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" id="tgl_s" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; box-sizing: border-box;" required>
                </div>

                <div style="grid-column: span 2; background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px dashed #ccc;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:8px;">Flyer Promo <span id="info_foto_p" style="color:#6F4E37; font-size:11px;"></span></label>
                    <input type="file" name="foto" id="foto_p" accept="image/*" style="font-size: 13px;">
                </div>
            </div>

            <div style="display:flex; gap:15px; margin-top:30px; border-top: 1px solid #f0f0f0; padding-top: 20px;">
                <button type="button" onclick="tutupModalPromo()" style="flex:1; background:#f3f4f6; color:#4b5563; padding:12px; border:none; border-radius:8px; font-weight:bold; cursor:pointer;">Batal</button>
                <button type="submit" style="flex:1; background:#6F4E37; color:white; padding:12px; border:none; border-radius:8px; font-weight:bold; cursor:pointer; box-shadow: 0 4px 10px rgba(111,78,55,0.2);">Simpan Promo</button>
            </div>
        </form>
    </div>
</div>

<script>
    // JS Logika Dinamis Form
    function toggleTipePromo() {
        let tipe = document.getElementById('tipe_p').value;
        document.getElementById('form_group_level').style.display = (tipe === 'Level') ? 'block' : 'none';
        document.getElementById('form_group_poin').style.display = (tipe === 'Tukar_Poin') ? 'block' : 'none';

        // Reset required
        document.getElementById('target_level_p').required = (tipe === 'Level');
        document.getElementById('min_poin_p').required = (tipe === 'Tukar_Poin');
    }

    function toggleTipePotongan() {
        let tpot = document.getElementById('tpotongan_p').value;
        document.getElementById('form_group_produk').style.display = (tpot === 'Produk') ? 'block' : 'none';

        let potBox = document.getElementById('potongan_p');
        if (tpot === 'Produk') {
            potBox.value = 0;
            potBox.readOnly = true;
            potBox.style.background = '#eee';
            document.getElementById('id_menu_trigger').required = true;
            document.getElementById('id_menu_bonus').required = true;
        } else {
            potBox.readOnly = false;
            potBox.style.background = 'white';
            document.getElementById('id_menu_trigger').required = false;
            document.getElementById('id_menu_bonus').required = false;
        }
    }

    function bukaModalPromo() {
        document.getElementById('modalPromo').style.display = 'flex';
        document.getElementById('judulPromo').innerText = 'Tambah Promo Baru';
        document.getElementById('formPromo').action = 'index.php?controller=admin&action=tambah_promo';
        document.getElementById('formPromo').reset();
        document.getElementById('foto_p').required = true;
        toggleTipePromo();
        toggleTipePotongan();
    }

    function editPromo(id, nama, kode, desc, syarat, tipe, pot, tpot, minp, tlvl, kuota, min_belanja, tm, ts, st, foto, idTrig, idBon, minBeli) {
        document.getElementById('modalPromo').style.display = 'flex';
        document.getElementById('judulPromo').innerText = 'Edit Promo';
        document.getElementById('formPromo').action = 'index.php?controller=admin&action=edit_promo';

        document.getElementById('id_promo').value = id;
        document.getElementById('nama_p').value = nama;
        document.getElementById('kode_p').value = kode;
        document.getElementById('desc_p').value = desc;
        document.getElementById('syarat_p').value = syarat;
        document.getElementById('tipe_p').value = tipe;
        document.getElementById('potongan_p').value = pot;
        document.getElementById('tpotongan_p').value = tpot;
        document.getElementById('id_menu_trigger').value = idTrig;
        document.getElementById('id_menu_bonus').value = idBon;
        document.getElementById('min_beli_p').value = minBeli;
        document.getElementById('min_poin_p').value = minp;
        document.getElementById('target_level_p').value = tlvl;
        document.getElementById('kuota_p').value = kuota;
        document.getElementById('min_belanja_p').value = min_belanja;
        document.getElementById('tgl_m').value = tm;
        document.getElementById('tgl_s').value = ts;
        document.getElementById('status_p').value = st;
        document.getElementById('foto_lama_p').value = foto;

        document.getElementById('foto_p').required = false;
        document.getElementById('info_foto_p').innerText = '(Kosongkan jika tak ganti)';

        toggleTipePromo(); // Update tampilan berdasarkan data DB
    }

    function tutupModalPromo() {
        document.getElementById('modalPromo').style.display = 'none';
    }
</script>