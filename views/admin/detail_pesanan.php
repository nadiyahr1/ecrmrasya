<?php if (!$p): ?>
    <div style="text-align: center; padding: 20px;">Data tidak ditemukan.</div>
<?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 20px; font-family: sans-serif;">
        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #6F4E37;">
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <span style="color: #64748b; font-size: 11px; font-weight: bold; text-transform: uppercase;">Pelanggan</span><br>
                    <strong><?= $p['nama_member'] ?: 'Pelanggan Umum' ?></strong>
                    <?php if (!empty($p['nama_level'])): ?>
                        <span style="font-size: 10px; background: #6F4E37; color: white; padding: 2px 6px; border-radius: 4px; margin-left: 5px;"><?= $p['nama_level'] ?></span>
                    <?php endif; ?>
                </div>
                <div style="text-align: right;">
                    <span style="color: #64748b; font-size: 11px; font-weight: bold; text-transform: uppercase;">Metode Pembayaran</span><br>
                    <strong>
                        <?php
                        $metode = $p['metode_pembayaran'];
                        if (strpos($metode, ' - VA: ') !== false) {
                            $parts = explode(' - VA: ', $metode);
                            // Menampilkan Bank dan VA di baris berbeda agar Admin mudah membaca
                            echo $parts[0] . "<br><span style='font-size:12px; color:#6F4E37;'>VA: " . $parts[1] . "</span>";
                        } else {
                            echo htmlspecialchars($metode);
                        }
                        ?>
                    </strong>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 1px dashed #cbd5e1;">
                <div>
                    <span style="color: #64748b; font-size: 11px; font-weight: bold; text-transform: uppercase;">Tipe Pemesanan</span><br>
                    <strong style="color: #0369a1;"><?= htmlspecialchars($p['tipe_pemesanan'] ?? 'Tidak Diketahui') ?></strong>
                </div>
                <div style="text-align: right;">
                    <span style="color: #64748b; font-size: 11px; font-weight: bold; text-transform: uppercase;">Nomor Meja</span><br>
                    <strong>
                        <?php if (($p['tipe_pemesanan'] ?? '') == 'Makan di Tempat' && !empty($p['no_meja'])): ?>
                            <span style="background: #16a34a; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px;">Meja <?= htmlspecialchars($p['no_meja']) ?></span>
                        <?php else: ?>
                            <span style="color: #94a3b8; font-size: 13px;">-</span>
                        <?php endif; ?>
                    </strong>
                </div>
            </div>
        </div>

        <div>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr style="border-bottom: 2px solid #eee; text-align: left; color: #888;">
                    <th style="padding: 10px 0;">Item</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
                <?php
                $total_item = 0;
                foreach ($daftar_menu as $dm):
                    $total_item += $dm['subtotal'];
                    // Mencegah harga satuan terlihat normal jika subtotalnya 0 (gratis)
                    $harga_satuan = $dm['jumlah'] > 0 ? ($dm['subtotal'] / $dm['jumlah']) : 0;
                ?>
                    <tr style="border-bottom: 1px solid #f9f9f9;">
                        <td style="padding: 10px 0;">
                            <?= htmlspecialchars($dm['nama_menu']) ?> <br>
                            <small style="color:#888;"><?= $dm['jumlah'] ?> x Rp <?= number_format($harga_satuan) ?></small>
                        </td>
                        <td style="text-align: right; font-weight: bold;">Rp <?= number_format($dm['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php
                foreach ($daftar_fasilitas as $df):
                    $total_item += $df['subtotal_sewa'];
                    $tgl = date('d M Y', strtotime($df['tgl_sewa']));
                    $jam_mulai = date('H:i', strtotime($df['jam_mulai']));
                    $jam_selesai = !empty($df['jam_selesai']) ? date('H:i', strtotime($df['jam_selesai'])) : '-';
                ?>
                    <tr style="border-bottom: 1px solid #f9f9f9;">
                        <td style="padding: 10px 0;">
                            <strong>[Fasilitas]</strong> <?= htmlspecialchars($df['nama_fasilitas']) ?> <br>
                            <small style="color:#888;">
                                📅 Jadwal: <?= $tgl ?> (<?= $jam_mulai ?> - <?= $jam_selesai ?>) <br>
                                <?php if ($df['satuan'] == 'Orang'): ?>
                                    👥 Kapasitas: <?= $df['jumlah_orang'] ?? 1 ?> Orang
                                <?php else: ?>
                                    ⏳ Durasi: <?= $df['jumlah_jam'] ?? 1 ?> Jam
                                <?php endif; ?>
                            </small>
                        </td>
                        <td style="text-align: right; font-weight: bold;">Rp <?= number_format($df['subtotal_sewa']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div style="background: #fff; border: 1px solid #eee; padding: 15px; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>Subtotal</span> <span>Rp <?= number_format($total_item) ?></span>
            </div>

            <?php
            $pajak = $total_item * 0.1;
            $total_seharusnya = $total_item + $pajak;
            $diskon = $total_seharusnya - $p['total_transaksi'];
            ?>

            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>Pajak (10%)</span> <span>Rp <?= number_format($pajak) ?></span>
            </div>

            <?php if ($diskon > 0): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #e63946; font-weight: bold;">
                    <span>Diskon Promo</span> <span>- Rp <?= number_format($diskon) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($p['id_promo'])): ?>
                <div style="background: #f0fdf4; border: 1px dashed #22c55e; padding: 10px 15px; border-radius: 8px; margin-top: 15px;">
                    <p style="margin: 0; font-size: 13px; color: #166534;">
                        <i class="fa-solid fa-tags"></i> <strong>Promo Digunakan:</strong> <br>
                        <?= htmlspecialchars($p['nama_promo']) ?>
                    </p>
                </div>
            <?php endif; ?>

            <div style="border-top: 1px solid #eee; margin-top: 10px; padding-top: 10px; display: flex; justify-content: space-between; font-weight: bold; font-size: 16px; color: #6F4E37;">
                <span>Total Bayar</span> <span>Rp <?= number_format($p['total_transaksi']) ?></span>
            </div>

            <?php
            // Ubah pengecekan menggunakan strpos agar tetap terbaca meskipun ada tambahan nama bank
            if (strpos($p['metode_pembayaran'], 'Transfer') !== false):
            ?>
                <div class="alert alert-info mt-3">
                    <strong>Info Pembayaran:</strong> Pembayaran otomatis (Payment Gateway Midtrans).
                    <br>
                    Status Pembayaran Saat Ini:

                    <?php if ($p['status'] != 'Belum Bayar' && $p['status'] != 'Dibatalkan'): ?>
                        <span class="badge bg-success">Telah Lunas Dikonfirmasi Sistem</span>
                    <?php elseif ($p['status'] == 'Dibatalkan'): ?>
                        <span class="badge bg-danger">Dibatalkan / Kadaluarsa</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Menunggu Pembayaran Pelanggan</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="display: flex; gap: 10px;">
            <?php if ($p['status'] == 'Dibatalkan'): ?>
                <button disabled style="flex: 1; text-align: center; background: #cbd5e1; color: #ffffff; padding: 12px; border-radius: 6px; border: none; font-weight: bold; cursor: not-allowed;">
                    🖨️ Struk Dibatalkan
                </button>
            <?php else: ?>
                <a href="index.php?controller=admin&action=cetak_struk&id=<?= $p['id_pesanan'] ?>" target="_blank" style="flex: 1; text-align: center; background: #6F4E37; color: white; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold; transition: 0.3s;">
                    🖨️ Cetak Struk
                </a>
            <?php endif; ?>

            <button onclick="tutupDetail()" style="flex: 1; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 12px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.3s;">Tutup</button>
        </div>
    </div>
<?php endif; ?>