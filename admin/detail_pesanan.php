<?php
// admin/ambil_detail_pesanan.php
require_once '../config/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (isset($_GET['id'])) {
    $id_p = $_GET['id'];

    // 1. AMBIL DATA PESANAN UTAMA & DATA MEMBER (E-CRM)
    $query = "SELECT p.*, m.nama_member, m.no_telp, l.nama_level, l.diskon as disc_persen, pr.nama_promo, pr.potongan, mj.no_meja 
              FROM tb_pesanan p 
              LEFT JOIN tb_member m ON p.id_member = m.id_member 
              LEFT JOIN tb_level_member l ON m.id_level = l.id_level
              LEFT JOIN tb_promo pr ON p.id_promo = pr.id_promo
              LEFT JOIN tb_meja mj ON p.id_meja = mj.id_meja
              WHERE p.id_pesanan = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_p]);
    $p = $stmt->fetch();

    if (!$p) {
        echo "<center>Data pesanan tidak ditemukan.</center>";
        exit;
    }

    // 2. AMBIL RINCIAN MENU
    $menus = $conn->prepare("SELECT dp.*, mn.nama_menu, mn.harga FROM tb_detail_pesanan dp 
                             JOIN tb_menu mn ON dp.id_menu = mn.id_menu 
                             WHERE dp.id_pesanan = ?");
    $menus->execute([$id_p]);
    $daftar_menu = $menus->fetchAll();

    // 3. PERBAIKAN: AMBIL RINCIAN DARI tb_booking_fasilitas (Bukan tb_detail_pesanan)
    $fasilitas = $conn->prepare("SELECT bf.*, f.nama_fasilitas, f.harga, f.satuan 
                                 FROM tb_booking_fasilitas bf 
                                 JOIN tb_fasilitas f ON bf.id_fasilitas = f.id_fasilitas 
                                 WHERE bf.id_pesanan = ?");
    $fasilitas->execute([$id_p]);
    $daftar_fasilitas = $fasilitas->fetchAll();
?>

    <div style="display: flex; flex-direction: column; gap: 20px;">

        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #6F4E37;">
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <span style="color: #64748b; font-size: 12px; font-weight: bold; text-transform: uppercase;">Pelanggan</span><br>
                    <strong><?= $p['nama_member'] ?: 'Pelanggan Umum' ?></strong>
                    <?php if ($p['nama_level']): ?>
                        <span style="font-size: 11px; background: #6F4E37; color: white; padding: 2px 6px; border-radius: 4px; margin-left: 5px;"><?= $p['nama_level'] ?></span>
                    <?php endif; ?>
                    <br><small style="color: #666;"><?= $p['no_telp'] ?: '-' ?></small>
                </div>
                <div style="text-align: right;">
                    <span style="color: #64748b; font-size: 12px; font-weight: bold; text-transform: uppercase;">Waktu Order</span><br>
                    <strong><?= date('d M Y, H:i', strtotime($p['tgl_pesanan'])) ?></strong><br>
                    <small style="color: #666;">Tipe: <strong><?= $p['tipe_pemesanan'] ?></strong></small><br>
                    <?php if (!empty($p['no_meja'])): ?>
                        <span style="display: inline-block; margin-top: 2px; background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; border: 1px solid #fcd34d;">
                            Nomor Meja: <?= $p['no_meja'] ?>
                        </span><br>
                    <?php endif; ?>
                    <small style="color: #666;">Metode: <?= $p['metode_pembayaran'] ?></small>
                </div>
            </div>
        </div>

        <div>
            <h4 style="margin: 0 0 10px 0; font-size: 14px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px;">Rincian Pembelian</h4>
            <table style="width: 100%; border-collapse: collapse;">
                <?php
                $subtotal_bersih = 0;
                
                // Tampilkan Menu
                foreach ($daftar_menu as $dm):
                    $subtotal_bersih += $dm['subtotal'];
                ?>
                    <tr>
                        <td style="padding: 8px 0;">
                            <?= $dm['nama_menu'] ?> <br> 
                            <small style="color: #888;">Rp <?= number_format($dm['harga']) ?> x <?= $dm['jumlah'] ?></small>
                        </td>
                        <td style="text-align: right; font-weight: bold;">Rp <?= number_format($dm['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php
                // Tampilkan Fasilitas (Logika baru berdasarkan tb_booking_fasilitas)
                foreach ($daftar_fasilitas as $df):
                    $subtotal_bersih += $df['subtotal_sewa'];
                    
                    // Cek apakah per jam atau per orang untuk teks rincian
                    $rincian_unit = ($df['satuan'] == 'Jam') ? $df['durasi_jam']." Jam" : $df['jumlah_orang']." Orang";
                ?>
                    <tr>
                        <td style="padding: 8px 0;">
                            <strong>[Fasilitas] <?= $df['nama_fasilitas'] ?></strong> <br> 
                            <small style="color: #888;">
                                Sewa: <?= date('d M Y', strtotime($df['tgl_sewa'])) ?> | <?= $rincian_unit ?> 
                                <?= ($df['jam_mulai'] != '00:00:00') ? " | Pukul ".$df['jam_mulai'] : "" ?>
                            </small>
                        </td>
                        <td style="text-align: right; font-weight: bold;">Rp <?= number_format($df['subtotal_sewa']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div style="background: #fff; border: 1px solid #eee; padding: 15px; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>Subtotal</span> <span>Rp <?= number_format($subtotal_bersih) ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>Pajak (10%)</span> <span>Rp <?= number_format($subtotal_bersih * 0.1) ?></span>
            </div>

            <?php if ($p['disc_persen'] > 0):
                $potongan_member = $subtotal_bersih * ($p['disc_persen'] / 100);
            ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #059669; font-weight: 500;">
                    <span>Diskon Member (<?= $p['nama_level'] ?> <?= $p['disc_persen'] ?>%)</span> <span>-Rp <?= number_format($potongan_member) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($p['id_promo']): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #059669; font-weight: 500;">
                    <span>Voucher: <?= $p['nama_promo'] ?></span> <span>-Rp <?= number_format($p['nilai_potongan']) ?></span>
                </div>
            <?php endif; ?>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 16px; color: #6F4E37;">
                <span>Total Bayar</span> <span>Rp <?= number_format($p['total_transaksi']) ?></span>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 10px;">
            <a href="cetak_struk.php?id=<?= $p['id_pesanan'] ?>" target="_blank" style="flex: 1; text-align: center; background: #6F4E37; color: white; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px;">🖨️ Cetak Struk</a>
            <button onclick="tutupDetail()" style="flex: 1; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px;">Tutup</button>
        </div>

    </div>

<?php
} 
?>