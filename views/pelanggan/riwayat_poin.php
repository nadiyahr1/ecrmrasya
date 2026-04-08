<div style="max-width: 700px; margin: 10px auto; padding: 20px;">
    <a href="index.php?controller=pelanggan&action=profil" style="text-decoration: none; color: #6F4E37; font-weight: bold;">
        ← Kembali ke Profil
    </a>

    <h3 style="margin-top: 20px; color: #333;">Riwayat Penggunaan Poin</h3>
    <p style="color: #888; font-size: 14px;">Catatan perolehan dan penukaran koin loyalitas Anda.</p>

    <div style="background: white; border-radius: 15px; border: 1px solid #eee; overflow: hidden; margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #fdfaf8; border-bottom: 2px solid #eee;">
                    <th style="padding: 15px; text-align: left; font-size: 14px;">Tanggal</th>
                    <th style="padding: 15px; text-align: left; font-size: 14px;">Keterangan</th>
                    <th style="padding: 15px; text-align: right; font-size: 14px;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="3" style="padding: 30px; text-align: center; color: #999;">Belum ada riwayat poin.</td>
                    </tr>
                    <?php else:
                    foreach ($history as $h):
                        // PERBAIKAN LOGIKA: Cek berdasarkan kolom 'tipe', bukan dari angka poinnya
                        $is_plus = (isset($h['tipe']) && $h['tipe'] == 'Masuk');
                    ?>
                        <tr style="border-bottom: 1px solid #f9f9f9;">
                            <td style="padding: 15px; font-size: 13px; color: #888;">
                                <?= date('d/m/Y H:i', strtotime($h['tgl_perubahan'])) ?>
                            </td>
                            <td style="padding: 15px; font-size: 14px; color: #333;">
                                <?= htmlspecialchars($h['keterangan']) ?>
                            </td>
                            <td style="padding: 15px; text-align: right; font-weight: bold; color: <?= $is_plus ? '#27ae60' : '#e74c3c' ?>">
                                <?= $is_plus ? '+' : '-' ?> <?= abs($h['poin']) ?>
                            </td>
                        </tr>
                <?php endforeach;
                endif; ?>
            </tbody>
        </table>
    </div>
</div>