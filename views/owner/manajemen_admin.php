<div style="padding: 20px; max-width: 1000px; margin: 0 auto;">

    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; border-top: 4px solid #10b981;">
        <h3 style="margin-top: 0; color: #333;">
            <i class="fa-solid fa-user-plus"></i> Tambah Admin Baru
        </h3>

        <form action="index.php?controller=owner&action=proses_tambah_admin" method="POST" style="margin-top: 15px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <div>
                    <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 5px; color: #555;">Nama Lengkap</label>
                    <input type="text" name="nama" required placeholder="Masukkan nama admin..."
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 5px; color: #555;">Username</label>
                    <input type="text" name="username" required placeholder="Buat username unik..."
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 5px; color: #555;">Password</label>
                    <input type="password" name="password" required placeholder="Buat password..."
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 14px;">
                </div>
            </div>
            <div style="margin-top: 20px; text-align: right;">
                <button type="submit" style="background: #10b981; color: white; padding: 10px 25px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Admin
                </button>
            </div>
        </form>
    </div>

    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <h3 style="margin-top: 0; color: #333;">
            <i class="fa-solid fa-users"></i> Daftar Admin Sistem
        </h3>
        <div style="overflow-x: auto; width: 100%; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                <thead>
                    <tr style="white-space: nowrap;">
                        <th style="background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #ddd; color: #555;">No</th>
                        <th style="background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #ddd; color: #555;">Nama Lengkap</th>
                        <th style="background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #ddd; color: #555;">Username</th>
                        <th style="background: #f8f9fa; padding: 12px; text-align: center; border-bottom: 2px solid #ddd; color: #555;">Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($admins as $ad):
                    ?>
                        <tr style="border-bottom: 1px solid #eee; white-space: nowrap;">
                            <td style="padding: 12px; color: #666;"><?= $no++ ?></td>
                            <td style="padding: 12px; font-weight: bold; color: #6F4E37;">
                                <?= htmlspecialchars($ad['nama_user']) ?>
                            </td>
                            <td style="padding: 12px; color: #555;">
                                <?= htmlspecialchars($ad['username']) ?>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <span style="background: #e0e7ff; color: #4338ca; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                                    <?= htmlspecialchars($ad['role']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($admins)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: #999; font-style: italic;">
                                Belum ada data admin yang terdaftar di sistem.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>