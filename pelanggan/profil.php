<?php
session_start();
require_once '../config/koneksi.php';
include '../layout/header.php';

$id_m = $_SESSION['id_member'];
$stmt = $conn->prepare("SELECT m.*, l.nama_level FROM tb_member m JOIN tb_level_member l ON m.id_level = l.id_level WHERE m.id_member = ?");
$stmt->execute([$id_m]);
$user = $stmt->fetch();

// Logika Tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'riwayat';
?>

<div style="max-width: 900px; margin: 30px auto; padding: 65px 20px;">
    <div style="background: linear-gradient(135deg, #d4af37, #f1c40f); padding: 30px; border-radius: 20px; color: white; box-shadow: 0 10px 20px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0; font-size: 14px; text-transform: uppercase; letter-spacing: 2px;"><?= $user['nama_level'] ?> MEMBER</p>
                <h2 style="margin: 10px 0; font-size: 32px;"><?= $user['nama_member'] ?></h2>
                <h1 style="margin: 0; font-size: 40px; font-weight: bold;"><?= number_format($user['total_poin']) ?> <span style="font-size: 18px;">Poin</span></h1>
            </div>
            <div style="text-align: right;">
                <!-- <p style="margin: 0; font-size: 12px; opacity: 0.8;"><?= $user['email'] ?? 'Member@rasya.co' ?></p> -->
                <p style="margin: 0; font-size: 12px; opacity: 0.8;"><?= $user['no_telp'] ?></p>
            </div>
        </div>

        <div style="margin-top: 25px;">
            <div style="background: rgba(255,255,255,0.3); height: 8px; border-radius: 10px;">
                <div style="background: white; width: <?= min(($user['total_poin'] / 1000) * 100, 100) ?>%; height: 100%; border-radius: 10px;"></div>
            </div>
            <p style="font-size: 12px; margin-top: 8px;">Poin dibutuhkan untuk level selanjutnya: 1,000 Poin</p>
        </div>
        <a href="edit_profil.php" style="position: absolute; bottom: 20px; right: 20px; background: rgba(0,0,0,0.2); color: white; padding: 8px 20px; border-radius: 30px; text-decoration: none; font-size: 14px;">EDIT PROFIL</a>
    </div>
    <div style="margin-top: 20px; text-align: center;">
        <button onclick="document.getElementById('modalKlaim').style.display='block'"
            style="padding: 10px 20px; background: #333; color: white; border: none; border-radius: 30px; cursor: pointer; font-weight: bold;">
            ✨ Klaim Poin dari Struk
        </button>
    </div>

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
        <a href="?tab=riwayat" style="padding-bottom: 10px; text-decoration: none; color: <?= $tab == 'riwayat' ? '#6F4E37' : '#888' ?>; border-bottom: 3px solid <?= $tab == 'riwayat' ? '#6F4E37' : 'transparent' ?>; font-weight: bold;">Riwayat Pesanan</a>
        <a href="?tab=voucher" style="padding-bottom: 10px; text-decoration: none; color: <?= $tab == 'voucher' ? '#6F4E37' : '#888' ?>; border-bottom: 3px solid <?= $tab == 'voucher' ? '#6F4E37' : 'transparent' ?>; font-weight: bold;">Voucher Saya</a>
        <a href="?tab=favorit" style="padding-bottom: 10px; text-decoration: none; color: <?= $tab == 'favorit' ? '#6F4E37' : '#888' ?>; border-bottom: 3px solid <?= $tab == 'favorit' ? '#6F4E37' : 'transparent' ?>; font-weight: bold;">Menu Favorit</a>
    </div>

    <div style="background: white; padding: 20px; border-radius: 15px;">
        <?php if ($tab == 'riwayat'): ?>
            <h4 style="margin-top: 0; color: #6F4E37;">Daftar Pesanan Terakhir</h4>

            <?php
            // Ambil data riwayat pesanan khusus untuk member ini
            $query = "SELECT * FROM tb_pesanan WHERE id_member = ? ORDER BY tgl_pesanan DESC";
            $stmt_r = $conn->prepare($query);
            $stmt_r->execute([$id_m]);
            $riwayat = $stmt_r->fetchAll();

            if (empty($riwayat)) : ?>
                <p style="color: #999; text-align: center; padding: 20px;">Belum ada riwayat pesanan.</p>
            <?php else : ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach ($riwayat as $r) : ?>
                        <div style="border: 1px solid #eee; padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <small style="color: #888;"><?= date('d M Y', strtotime($r['tgl_pesanan'])) ?></small>
                                <div style="font-weight: bold; margin: 3px 0;"><?= $r['id_pesanan'] ?></div>
                                <span style="font-size: 12px; padding: 2px 8px; border-radius: 10px; color: white; background: 
                            <?php
                            if ($r['status'] == 'Menunggu Verifikasi') echo '#ffc107';
                            elseif ($r['status'] == 'Diproses' || $r['status'] == 'Dapat Diambil') echo '#17a2b8';
                            elseif ($r['status'] == 'Selesai') echo '#28a745';
                            else echo '#dc3545';
                            ?>;">
                                    <?= $r['status'] ?>
                                </span>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: bold; color: #6F4E37;">Rp <?= number_format($r['total_transaksi']) ?></div>

                                <?php if ($r['status'] == 'Selesai'): ?>
                                    <?php
                                    // Cek dan ambil data ulasan (Komentar & Balasan) untuk ID Pesanan ini
                                    $cek_u = $conn->prepare("SELECT komentar, balasan_admin FROM tb_ulasan WHERE id_pesanan = ?");
                                    $cek_u->execute([$r['id_pesanan']]);
                                    $data_ulasan = $cek_u->fetch();

                                    if (!$data_ulasan):
                                    ?>
                                        <a href="form_ulasan.php?id=<?= $r['id_pesanan'] ?>" style="display:inline-block; margin-top:5px; font-size: 12px; background: #ffc107; color: #333; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-weight: bold;">Beri Ulasan</a>
                                    <?php else: ?>
                                        <div style="background: #f9fafb; padding: 12px; border-radius: 8px; margin-top: 15px; text-align: left; border: 1px solid #eee;">
                                            <div style="font-size: 12px; color: #444;">
                                                <strong style="color: #6F4E37;">Ulasan Kamu:</strong><br>
                                                <?= nl2br(htmlspecialchars($data_ulasan['komentar'])) ?>
                                            </div>

                                            <?php if (!empty($data_ulasan['balasan_admin'])): ?>
                                                <div style="font-size: 12px; color: #0369a1; margin-top: 10px; background: #e0f2fe; padding: 10px; border-radius: 6px; border-left: 3px solid #0284c7;">
                                                    <strong>Balasan Rasya.co:</strong><br>
                                                    <?= nl2br(htmlspecialchars($data_ulasan['balasan_admin'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <a href="../detail_pesanan_pelanggan.php?id=<?= $r['id_pesanan'] ?>" style="display:block; font-size: 12px; color: #6F4E37; text-decoration: none; margin-top:5px;">Lihat Detail →</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php elseif ($tab == 'voucher'): ?>
            <h4 style="margin-top: 0; color: #6F4E37;">Voucher & Hadiah Saya</h4>
            <p style="font-size: 13px; color: #888; margin-bottom: 20px;">Tukarkan poin Anda atau gunakan voucher yang sudah tersedia untuk mendapatkan potongan harga.</p>

            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php
                // Mengambil daftar voucher tipe 'Loyalty' dari database
                $promos = $conn->query("SELECT * FROM tb_promo WHERE tipe_promo = 'Loyalty' ORDER BY potongan DESC")->fetchAll();

                if (empty($promos)) : ?>
                    <p style="color: #999; text-align: center; padding: 20px;">Belum ada voucher tersedia saat ini.</p>
                    <?php else :
                    foreach ($promos as $p):
                        $bisa_tukar = ($user['total_poin'] >= $p['poin_dibutuhkan']);
                    ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; border: 1px solid #f0f0f0; padding: 20px; border-radius: 15px; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <div style="display: flex; gap: 20px; align-items: center;">
                                <div style="width: 50px; height: 50px; background: #fffcf5; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                    ☕
                                </div>
                                <div>
                                    <strong style="font-size: 16px; color: #333;"><?= $p['nama_promo'] ?></strong><br>
                                    <span style="color: #28a745; font-weight: bold; font-size: 14px;">Potongan Rp <?= number_format($p['nominal_potongan']) ?></span><br>
                                    <small style="color: #bbb;">Syarat: <?= $p['poin_dibutuhkan'] ?> Poin</small>
                                </div>
                            </div>

                            <?php if ($bisa_tukar): ?>
                                <a href="../menu.php" style="background: #6F4E37; color: white; border: none; padding: 10px 25px; border-radius: 30px; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold;">
                                    Gunakan
                                </a>
                            <?php else: ?>
                                <button disabled style="background: #eee; color: #aaa; border: none; padding: 10px 20px; border-radius: 30px; font-size: 12px;">
                                    Poin Kurang
                                </button>
                            <?php endif; ?>
                        </div>
                <?php endforeach;
                endif; ?>
            </div>

        <?php elseif ($tab == 'favorit'): ?>
            <h4 style="margin-top: 0; color: #6F4E37;">Paling Sering Kamu Pesan</h4>
            <p style="font-size: 13px; color: #888; margin-bottom: 20px;">Daftar menu yang menjadi andalanmu saat berkunjung ke Rasya.co.</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                <?php
                // Logika Senior Developer: Menghitung menu yang paling sering dibeli oleh member ini
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
                        <a href="../menu_publik.php" style="color: #6F4E37; font-weight: bold;">Lihat Menu Cafe</a>
                    </div>
                    <?php else :
                    foreach ($favorit as $f): ?>
                        <div style="background: white; border: 1px solid #f0f0f0; border-radius: 15px; padding: 15px; text-align: center; transition: 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <img src="../assets/gambar/menu/<?= $f['foto'] ?: 'default.jpg' ?>" width="100%" height="120" style="border-radius: 10px; object-fit: cover; margin-bottom: 10px;">
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

<?php include '../layout/footer.php'; ?>