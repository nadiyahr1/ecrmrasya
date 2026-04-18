<?php
// Logika Tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'riwayat';

// KODE BARU: Logika Progress Bar berdasarkan Jumlah Transaksi
$id_level_sekarang = $user['id_level'];
$stmt_next = $conn->prepare("SELECT * FROM tb_level_member WHERE id_level > ? ORDER BY id_level ASC LIMIT 1");
$stmt_next->execute([$id_level_sekarang]);
$next_level = $stmt_next->fetch();

if ($next_level) {
    $target_trx = $next_level['min_transaksi'];
    $target_rp  = $next_level['min_belanja'];

    $current_trx = $user['jml_transaksi'];
    $current_rp  = $user['total_belanja'];

    // Hitung sisa untuk mencapai target
    $sisa_trx = ($target_trx > $current_trx) ? ($target_trx - $current_trx) : 0;
    $sisa_rp  = ($target_rp > $current_rp) ? ($target_rp - $current_rp) : 0;

    // Hitung persentase untuk progress bar (ambil yang tertinggi)
    $prog_trx = ($target_trx > 0) ? ($current_trx / $target_trx) * 100 : 0;
    $prog_rp  = ($target_rp > 0) ? ($current_rp / $target_rp) * 100 : 0;
    $progress = max($prog_trx, $prog_rp);

    $teks_progress = "Butuh <strong>$sisa_trx Transaksi lagi</strong> atau <strong>Rp " . number_format($sisa_rp, 0, ',', '.') . " transaksi lagi</strong> untuk naik ke Level {$next_level['nama_level']}";
} else {
    $progress = 100;
    $teks_progress = "Selamat! Anda telah mencapai level tertinggi (Gold).";
}
?>

<div style="max-width: 900px; margin: 30px auto; padding: 25px 20px;">
    <div style="background: linear-gradient(135deg, #d4af37, #f1c40f); padding: 30px; border-radius: 20px; color: white; box-shadow: 0 10px 20px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0; font-size: 14px; text-transform: uppercase; letter-spacing: 2px;"><?= $user['nama_level'] ?> MEMBER</p>
                <h2 style="margin: 10px 0; font-size: 32px;"><?= $user['nama_member'] ?></h2>
                <h1 style="margin: 0; font-size: 40px; font-weight: bold;">
                    <?= number_format($user['poin']) ?>
                    <span style="font-size: 18px;">Poin Tersedia</span>
                </h1>
                <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">
                    Riwayat: <strong><?= number_format($user['jml_transaksi']) ?>x</strong> Transaksi | Total: <strong>Rp <?= number_format($user['total_belanja'], 0, ',', '.') ?></strong>
                </p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0; font-size: 12px; opacity: 0.8;"><?= $user['no_telp'] ?></p>
            </div>
        </div>

        <div style="margin-top: 25px;">
            <div style="background: rgba(255,255,255,0.3); height: 8px; border-radius: 10px;">
                <div style="background: white; width: <?= min($progress, 100) ?>%; height: 100%; border-radius: 10px;"></div>
            </div>
            <p style="font-size: 12px; margin-top: 8px;"><?= $teks_progress ?></p>
        </div>

        <div style="position: absolute; bottom: 20px; right: 20px; display: flex; gap: 10px;">
            <a href="index.php?controller=checkout&action=riwayat_poin"
                style="background: rgba(255,255,255,0.2); color: white; padding: 8px 15px; border-radius: 30px; text-decoration: none; font-size: 13px; border: 1px solid rgba(255,255,255,0.5);">
                <i class="fa-solid fa-clock-rotate-left"></i> RIWAYAT POIN
            </a>
            <a href="index.php?controller=pelanggan&action=edit_profil"
                style="background: rgba(0,0,0,0.2); color: white; padding: 8px 20px; border-radius: 30px; text-decoration: none; font-size: 14px;">
                EDIT PROFIL
            </a>
        </div>
    </div>

    <!-- <div style="margin-top: 20px; text-align: center;">
        <button onclick="document.getElementById('modalKlaim').style.display='block'"
            style="padding: 10px 20px; background: #333; color: white; border: none; border-radius: 30px; cursor: pointer; font-weight: bold;">
            ✨ Klaim Poin dari Struk
        </button>
    </div> -->

    <div id="modalKlaim" style="display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(3px);">
        <div style="background: white; margin: 15% auto; padding: 25px; width: 350px; border-radius: 15px; text-align: center;">
            <h3>Klaim Poin Kamu</h3>
            <p style="font-size: 13px; color: #888;">Masukkan ID Pesanan yang tertera pada struk belanja Anda.</p>
            <form action="proses_klaim.php" method="POST">
                <input type="text" name="id_pesanan" placeholder="Contoh: RSY-2026..." required
                    style="width: 100%; padding: 12px; margin: 15px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box;">
                <button type="submit" style="width: 100%; padding: 12px; background: #6F4E37; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
                    Verifikasi & Klaim
                </button>
            </form>
            <button onclick="document.getElementById('modalKlaim').style.display='none'" style="margin-top: 15px; background: none; border: none; color: #999; cursor: pointer;">Batal</button>
        </div>
    </div>

    <div style="display: flex; gap: 30px; margin: 40px 0 20px 0; border-bottom: 2px solid #eee;">
        <a href="index.php?controller=pelanggan&action=profil&tab=riwayat" style="padding-bottom: 10px; text-decoration: none; color: <?= $tab == 'riwayat' ? '#6F4E37' : '#888' ?>; border-bottom: 3px solid <?= $tab == 'riwayat' ? '#6F4E37' : 'transparent' ?>; font-weight: bold;">Riwayat Pesanan</a>
        <a href="index.php?controller=pelanggan&action=profil&tab=voucher" style="padding-bottom: 10px; text-decoration: none; color: <?= $tab == 'voucher' ? '#6F4E37' : '#888' ?>; border-bottom: 3px solid <?= $tab == 'voucher' ? '#6F4E37' : 'transparent' ?>; font-weight: bold;">Voucher Saya</a>
        <a href="index.php?controller=pelanggan&action=profil&tab=favorit" style="padding-bottom: 10px; text-decoration: none; color: <?= $tab == 'favorit' ? '#6F4E37' : '#888' ?>; border-bottom: 3px solid <?= $tab == 'favorit' ? '#6F4E37' : 'transparent' ?>; font-weight: bold;">Menu Favorit</a>
    </div>

    <div style="background: white; padding: 20px; border-radius: 15px;">

        <?php if ($tab == 'riwayat'): ?>
            <?php include 'views/pelanggan/riwayat_pesanan.php'; ?>

        <?php elseif ($tab == 'voucher'): ?>
            <h4 style="margin-top: 0; color: #6F4E37;">Voucher & Promo Saya</h4>
            <p style="font-size: 13px; color: #888; margin-bottom: 20px;">Berikut adalah promo dan voucher khusus yang bisa Anda gunakan. Klik untuk melihat kode dan detailnya.</p>

            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php
                // MENGGUNAKAN VARIABEL $vouchers DARI CONTROLLER, BUKAN QUERY NATIVE LAGI
                if (empty($vouchers)) : ?>
                    <div style="text-align: center; padding: 40px; border: 1px dashed #ddd; border-radius: 15px; background: #fafafa;">
                        <p style="color: #999; margin:0;">Belum ada voucher atau promo yang tersedia saat ini.</p>
                    </div>
                    <?php else :
                    foreach ($vouchers as $p):
                        $sudah_dipakai = in_array($p['id_promo'], $used_vouchers); // CEK PEMAKAIAN
                        // Logika Cek Poin (Hanya berlaku untuk tipe Tukar Poin)
                        $is_tukar_poin = ($p['tipe_promo'] == 'Tukar_Poin');
                        $bisa_tukar = $is_tukar_poin ? ($user['poin'] >= $p['min_poin']) : true;
                        $disable_card = $sudah_dipakai || !$bisa_tukar;
                    ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; border: 1px solid #f0f0f0; padding: 20px; border-radius: 15px; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.02); <?= !$bisa_tukar ? 'opacity: 0.5; filter: grayscale(100%);' : '' ?>">

                            <div style="display: flex; gap: 20px; align-items: center;">
                                <div style="width: 55px; height: 55px; background: #fffcf5; border: 1px solid #f9eed7; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                                    <?= $p['tipe_potongan'] == 'Produk' ? '🎁' : '🎫' ?>
                                </div>
                                <div>
                                    <strong style="font-size: 16px; color: #333;"><?= htmlspecialchars($p['nama_promo']) ?></strong><br>

                                    <?php if ($p['tipe_potongan'] == 'Produk'): ?>
                                        <span style="color: #e67e22; font-weight: bold; font-size: 14px;">Gratis Item Terpilih</span>
                                    <?php elseif ($p['tipe_potongan'] == 'Persen'): ?>
                                        <span style="color: #28a745; font-weight: bold; font-size: 14px;">Diskon <?= $p['potongan'] ?>%</span>
                                    <?php else: ?>
                                        <span style="color: #28a745; font-weight: bold; font-size: 14px;">Potongan Rp <?= number_format($p['potongan']) ?></span>
                                    <?php endif; ?><br>

                                    <small style="color: #888; display: inline-block; margin-top: 5px;">
                                        <span style="background: #eee; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; color: #555;">
                                            Promo <?= str_replace('_', ' ', $p['tipe_promo']) ?>
                                        </span>
                                        <?php if ($is_tukar_poin) echo " &nbsp;&bull;&nbsp; Harga: <strong>" . number_format($p['min_poin']) . " Poin</strong>"; ?>
                                    </small>
                                </div>
                            </div>

                            <div style="text-align: right;">
                                <?php if ($sudah_dipakai): ?>
                                    <button disabled style="background: #e5e7eb; color: #6b7280; border: none; padding: 10px 20px; border-radius: 30px; font-size: 12px; font-weight:bold;">
                                        Sudah Digunakan <i class="fa-solid fa-check"></i>
                                    </button>
                                <?php elseif ($bisa_tukar): ?>
                                    <a href="index.php?controller=promo&action=detailPromo&id=<?= $p['id_promo'] ?>"
                                        style="background: #6F4E37; color: white; border: none; padding: 10px 20px; border-radius: 30px; cursor: pointer; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-block; transition: 0.3s;">
                                        Lihat & Klaim
                                    </a>
                                <?php else: ?>
                                    <button disabled style="background: #eee; color: #aaa; border: none; padding: 10px 20px; border-radius: 30px; font-size: 12px; font-weight:bold;">
                                        Poin Kurang
                                    </button>
                                <?php endif; ?>
                            </div>

                        </div>
                <?php
                    endforeach;
                endif;
                ?>
            </div>

        <?php elseif ($tab == 'favorit'): ?>
            <h4 style="margin-top: 0; color: #6F4E37;">Paling Sering Kamu Pesan</h4>
            <p style="font-size: 13px; color: #888; margin-bottom: 20px;">Daftar menu yang menjadi andalanmu saat berkunjung ke Rasya.co.</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                <?php
                //Menghitung menu yang paling sering dibeli oleh member
                $id_m = $_SESSION['id_member'];
                $query_fav = "SELECT m.id_menu, m.nama_menu, m.harga, m.foto, COUNT(d.id_menu) as total_dipesan 
                      FROM tb_detail_pesanan d 
                      JOIN tb_pesanan p ON d.id_pesanan = p.id_pesanan 
                      JOIN tb_menu m ON d.id_menu = m.id_menu 
                      WHERE p.id_member = ? 
                      GROUP BY d.id_menu 
                      ORDER BY total_dipesan DESC 
                      LIMIT 6";
                $stmt_fav = $conn->prepare($query_fav);
                $stmt_fav->execute([$id_m]);
                $favorit = $stmt_fav->fetchAll();

                if (empty($favorit)) : ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;">
                        <p>Kamu belum memiliki menu favorit. Yuk, mulai pesan menu pilihanmu!</p>
                        <a href="<?= $base_url ?>index.php?controller=menu&action=index" style="color: #6F4E37; font-weight: bold;">Lihat Menu Cafe</a>
                    </div>
                    <?php else :
                    foreach ($favorit as $f): ?>
                        <div style="background: white; border: 1px solid #f0f0f0; border-radius: 15px; padding: 15px; text-align: center; transition: 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <img src="assets/gambar/menu/<?= $f['foto'] ?: 'default.jpg' ?>" width="100%" height="120" style="border-radius: 10px; object-fit: cover; margin-bottom: 10px;">
                            <h5 style="margin: 5px 0; font-size: 15px;"><?= $f['nama_menu'] ?></h5>
                            <p style="color: #6F4E37; font-weight: bold; margin: 5px 0; font-size: 14px;">Rp <?= number_format($f['harga']) ?></p>
                            <small style="color: #888; display: block; margin-bottom: 10px;">Dipesan <?= $f['total_dipesan'] ?>x</small>

                            <form action="../tambah_keranjang.php" method="POST">
                                <input type="hidden" name="id_menu" value="<?= $f['id_menu'] ?>">
                                <button type="submit" style="width: 100%; padding: 8px; background: #333; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 12px;">
                                    🛒 Pesan Lagi
                                </button>
                            </form>
                        </div>
                <?php endforeach;
                endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>