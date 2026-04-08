<div style="display: flex; flex-direction: column; gap: 15px; font-family: sans-serif;">

    <div style="display: flex; justify-content: space-between; align-items: center; background: #fdfaf8; padding: 12px; border-radius: 8px; border: 1px solid #eee;">
        <div>
            <span style="font-size: 12px; color: #666;">Level Saat Ini</span><br>
            <strong style="color: #6F4E37;"><?= $member['nama_level'] ?></strong>
        </div>
        <div style="text-align: right;">
            <span style="font-size: 12px; color: #666;">Total Saldo Poin</span><br>
            <strong style="font-size: 18px; color: #333;"><?= number_format($member['poin']) ?> Poin</strong>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead style="background: #f1f5f9;">
                <tr>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Keterangan</th>
                    <th style="padding: 10px; text-align: center; border-bottom: 2px solid #ddd;">Poin</th>
                    <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">Tipe</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($history) > 0): ?>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td style="padding: 12px 10px; border-bottom: 1px solid #eee;">
                                <strong><?= $h['keterangan'] ?></strong><br>
                                <small style="color: #888;">Waktu: <?= date('d/m/Y H:i', strtotime($h['tgl_perubahan'])) ?></small>
                            </td>
                            <td style="padding: 12px 10px; border-bottom: 1px solid #eee; text-align: center; font-weight: bold;">
                                <?= ($h['tipe'] == 'Masuk' ? '+' : '') . number_format($h['poin']) ?>
                            </td>
                            <td style="padding: 12px 10px; border-bottom: 1px solid #eee; text-align: right;">
                                <?php if ($h['tipe'] == 'Masuk'): ?>
                                    <span style="color: #059669; font-weight: bold;">Pemasukan</span>
                                <?php else: ?>
                                    <span style="color: #dc2626; font-weight: bold;">Penukaran</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="padding: 30px; text-align: center; color: #999;">
                            <i>Belum ada riwayat perolehan poin.</i>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <button onclick="tutupModalMember()" style="width: 100%; padding: 10px; background: #6F4E37; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; margin-top: 10px;">Tutup</button>

</div>