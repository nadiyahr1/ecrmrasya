<div style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <h2 style="color: #6F4E37; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-top: 0;">
            <i class="fa-solid fa-user-gear"></i> Profil Pengguna (<?= ucfirst($user['role']) ?>)
        </h2>

        <form action="index.php?controller=admin&action=proses_edit_profil" method="POST" style="margin-top: 20px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama_user']) ?>" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Username Login</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Password</label>
                <p style="margin: 0 0 10px 0; font-size: 12px;">Kosongkan jika tidak ingin mengganti password Anda.</p>
                <input type="password" name="password_baru" placeholder="Masukkan password baru..."
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
            </div>

            <div style="margin-top: 25px; text-align: right;">
                <button type="submit" style="background: #6F4E37; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 14px;">
                    <i class="fa-solid fa-save"></i> Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>
</div>