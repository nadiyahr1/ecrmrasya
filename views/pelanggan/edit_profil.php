<div style="max-width: 600px; margin: 40px auto; padding: 30px; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
    <h2 style="color: #6F4E37; margin-top: 0; text-align: center; border-bottom: 2px dashed #eee; padding-bottom: 15px;">
        <i class="fa-solid fa-user-pen"></i> Edit Profil Saya
    </h2>
    <p style="color: #888; font-size: 14px;">Pastikan data diri Anda valid untuk memudahkan proses verifikasi pesanan.</p>


    <form action="index.php?controller=pelanggan&action=proses_edit_profil" method="POST" style="margin-top: 25px;">
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #555;">Nama Lengkap</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($user['nama_member']) ?>" required
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #555;">Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #555;">No. Telp / WhatsApp</label>
            <input type="text" name="no_telp" value="<?= htmlspecialchars($user['no_telp']) ?>" required
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #555;">Ganti Password (Opsional)</label>
            <input type="password" name="password_baru" placeholder="Kosongkan jika tidak ingin ganti"
                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="index.php?controller=pelanggan&action=profil" style="flex: 1; text-align: center; padding: 12px; background: #f1f5f9; color: #475569; text-decoration: none; border-radius: 8px; border: 1px solid #cbd5e1; font-weight: bold;">Batal</a>
            <button type="submit" style="flex: 2; padding: 12px; background: #6F4E37; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Simpan Perubahan</button>
        </div>
    </form>
</div>