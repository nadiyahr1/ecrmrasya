<?php
require_once __DIR__ . '/../config/koneksi.php';

class PelangganController
{
    private $conn;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. TANGKAP KONEKSI GLOBAL
        global $conn;
        $this->conn = $conn;

        // Proteksi: Harus login
        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }

    public function profil()
    {
        $id_member = $_SESSION['id_member'];
        $tab = $_GET['tab'] ?? 'riwayat';

        // 1. Ambil Data User & Level untuk Kartu Member
        $stmt = $this->conn->prepare("SELECT m.*, l.nama_level FROM tb_member m JOIN tb_level_member l ON m.id_level = l.id_level WHERE m.id_member = ?");
        $stmt->execute([$id_member]);
        $user = $stmt->fetch();

        // 2. Ambil Riwayat Pesanan
        $stmt_riwayat = $this->conn->prepare("
            SELECT p.*, u.komentar, u.balasan_admin 
            FROM tb_pesanan p 
            LEFT JOIN tb_ulasan u ON p.id_pesanan = u.id_pesanan 
            WHERE p.id_member = ? 
            ORDER BY p.tgl_pesanan DESC
        ");
        $stmt_riwayat->execute([$id_member]);
        $riwayat = $stmt_riwayat->fetchAll();

        // 3. Ambil Voucher (Promo tipe Loyalty)
        // Asumsi $id_level_member adalah id_level dari member yang sedang login
        $query_voucher = "SELECT * FROM tb_promo 
                  WHERE status_promo = 'Aktif' 
                  AND tgl_mulai <= CURDATE() 
                  AND tgl_selesai >= CURDATE()
                  AND (kuota IS NULL OR kuota > 0)
                  AND (
                      tipe_promo = 'Umum' 
                      OR tipe_promo = 'Tukar_Poin' 
                      OR (tipe_promo = 'Level' AND target_level = ?)
                  )
                  ORDER BY id_promo DESC";

        $stmt_voucher = $this->conn->prepare($query_voucher);
        $stmt_voucher->execute([$user['id_level']]);
        $vouchers = $stmt_voucher->fetchAll();
        // Cek promo mana saja yang sudah dipakai oleh user ini
        $stmt_used = $this->conn->prepare("SELECT id_promo FROM tb_pesanan WHERE id_member = ? AND id_promo IS NOT NULL AND status != 'Dibatalkan'");
        $stmt_used->execute([$id_member]);
        $used_vouchers = $stmt_used->fetchAll(PDO::FETCH_COLUMN);

        // 4. Ambil Menu Favorit
        $sql_fav = "SELECT m.id_menu, m.nama_menu, m.harga, m.foto, COUNT(dp.id_menu) as total_dipesan 
                    FROM tb_detail_pesanan dp 
                    JOIN tb_menu m ON dp.id_menu = m.id_menu 
                    JOIN tb_pesanan p ON dp.id_pesanan = p.id_pesanan 
                    WHERE p.id_member = ? 
                    GROUP BY dp.id_menu 
                    ORDER BY total_dipesan DESC LIMIT 6";
        $stmt_fav = $this->conn->prepare($sql_fav);
        $stmt_fav->execute([$id_member]);
        $favorit = $stmt_fav->fetchAll();

        // 5. Panggil View
        require_once 'layout/header.php';
        require_once 'views/pelanggan/profil.php';
        require_once 'layout/footer.php';
    }

    public function edit_profil()
    {
        $id_member = $_SESSION['id_member'];
        $stmt = $this->conn->prepare("SELECT * FROM tb_member WHERE id_member = ?");
        $stmt->execute([$id_member]);
        $user = $stmt->fetch();

        require_once 'layout/header.php';
        require_once 'views/pelanggan/edit_profil.php';
        require_once 'layout/footer.php';
    }

    public function proses_edit_profil()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_member = $_SESSION['id_member'];
            $username = htmlspecialchars(trim($_POST['username'])); // Tambahkan ini
            $nama = htmlspecialchars(trim($_POST['nama']));
            $no_telp = htmlspecialchars(trim($_POST['no_telp']));
            $password = $_POST['password_baru'];

            try {
                // Validasi: Cek apakah username sudah dipakai orang lain
                $cek_user = $this->conn->prepare("SELECT id_member FROM tb_member WHERE username = ? AND id_member != ?");
                $cek_user->execute([$username, $id_member]);
                if ($cek_user->rowCount() > 0) {
                    throw new Exception("Username sudah digunakan. Pilih username lain.");
                }

                if (!empty($password)) {
                    $pass_hash = password_hash($password, PASSWORD_DEFAULT);
                    // Tambahkan username di query update
                    $stmt = $this->conn->prepare("UPDATE tb_member SET username = ?, nama_member = ?, no_telp = ?, password = ? WHERE id_member = ?");
                    $stmt->execute([$username, $nama, $no_telp, $pass_hash, $id_member]);
                } else {
                    // Tambahkan username di query update
                    $stmt = $this->conn->prepare("UPDATE tb_member SET username = ?, nama_member = ?, no_telp = ? WHERE id_member = ?");
                    $stmt->execute([$username, $nama, $no_telp, $id_member]);
                }

                $_SESSION['nama'] = $nama;
                echo "<script>alert('Profil dan Username berhasil diperbarui!'); window.location='index.php?controller=pelanggan&action=profil';</script>";
            } catch (Exception $e) {
                $err = addslashes($e->getMessage());
                echo "<script>alert('Gagal: $err'); window.history.back();</script>";
            }
        }
    }

    public function detail_pesanan()
    {
        $id_pesanan = $_GET['id'] ?? '';
        if (empty($id_pesanan)) {
            header("Location: index.php?controller=pelanggan&action=profil");
            exit;
        }

        // 1. Ambil Data Pesanan Utama
        // 1. Ambil Data Pesanan Utama (Tambahkan JOIN ke tb_meja)
        $stmt = $this->conn->prepare("
            SELECT p.*, pr.nama_promo, mj.no_meja 
            FROM tb_pesanan p 
            LEFT JOIN tb_promo pr ON p.id_promo = pr.id_promo 
            LEFT JOIN tb_meja mj ON p.id_meja = mj.id_meja 
            WHERE p.id_pesanan = ?
        ");
        $stmt->execute([$id_pesanan]);
        $p = $stmt->fetch();

        if (!$p) {
            echo "<script>alert('Pesanan tidak ditemukan!'); window.location='index.php?controller=pelanggan&action=profil&tab=riwayat';</script>";
            exit;
        }

        // 2. Ambil Detail Menu
        $stmt_m = $this->conn->prepare("SELECT dm.*, m.nama_menu, m.foto FROM tb_detail_pesanan dm JOIN tb_menu m ON dm.id_menu = m.id_menu WHERE dm.id_pesanan = ?");
        $stmt_m->execute([$id_pesanan]);
        $detail_menu = $stmt_m->fetchAll();

        // 3. Ambil Detail Fasilitas
        $stmt_f = $this->conn->prepare("SELECT df.*, f.nama_fasilitas, f.foto_fasilitas FROM tb_booking_fasilitas df JOIN tb_fasilitas f ON df.id_fasilitas = f.id_fasilitas WHERE df.id_pesanan = ?");
        $stmt_f->execute([$id_pesanan]);
        $detail_fas = $stmt_f->fetchAll();

        // 4. Ambil Ulasan jika sudah ada
        $stmt_u = $this->conn->prepare("SELECT * FROM tb_ulasan WHERE id_pesanan = ?");
        $stmt_u->execute([$id_pesanan]);
        $ulasan = $stmt_u->fetch();

        require_once 'layout/header.php';
        require_once 'views/pelanggan/detail_pesanan.php';
        require_once 'layout/footer.php';
    }

    public function batalkan_pesanan()
    {
        $id_pesanan = $_GET['id'] ?? '';
        $id_member = $_SESSION['id_member'];

        if (empty($id_pesanan)) {
            header("Location: index.php?controller=pelanggan&action=profil&tab=riwayat");
            exit;
        }

        try {
            $this->conn->beginTransaction();

            require_once dirname(__FILE__) . '/../midtrans/Midtrans.php';
            \Midtrans\Config::$serverKey = 'Mid-server-Dd4FZEE0vr-iZ4DiNLEmMVhM';
            \Midtrans\Config::$isProduction = false;

            try {
                // Memberitahu Midtrans agar VA ini tidak bisa dibayar lagi
                \Midtrans\Transaction::cancel($id_pesanan);
            } catch (Exception $e) {
                // Jika gagal (misal transaksi belum ada di Midtrans), abaikan saja agar proses lokal tetap jalan
            }

            // 1. Cek validitas pesanan (Pastikan milik member ini dan statusnya Menunggu Konfirmasi)
            $stmt = $this->conn->prepare("SELECT * FROM tb_pesanan WHERE id_pesanan = ? AND id_member = ?");
            $stmt->execute([$id_pesanan, $id_member]);
            $pesanan = $stmt->fetch();

            if (!$pesanan) {
                throw new Exception("Pesanan tidak ditemukan.");
            }

            // Pengecekan Keamanan Ganda (Double Validation)
            $metode_db = $pesanan['metode_pembayaran'];
            $status_db = $pesanan['status'];

            if (strpos($metode_db, 'Transfer') !== false) {
                // Untuk transfer, tolak jika statusnya BUKAN "Belum Bayar"
                if ($status_db !== 'Belum Bayar') {
                    throw new Exception("Pesanan yang sudah dibayar tidak dapat dibatalkan oleh pelanggan.");
                }
            } else {
                // Untuk kasir, tolak jika sudah diproses Admin
                if ($status_db !== 'Menunggu Konfirmasi' && $status_db !== '' && $status_db !== null) {
                    throw new Exception("Pesanan sedang diproses dan tidak dapat dibatalkan.");
                }
            }

            // 2. Kembalikan Stok Menu
            $stmt_menu = $this->conn->prepare("SELECT id_menu, jumlah FROM tb_detail_pesanan WHERE id_pesanan = ?");
            $stmt_menu->execute([$id_pesanan]);
            $menus = $stmt_menu->fetchAll();

            foreach ($menus as $m) {
                $upd_stok = $this->conn->prepare("UPDATE tb_menu SET stok = stok + ?, status_menu = 'Tersedia' WHERE id_menu = ?");
                $upd_stok->execute([$m['jumlah'], $m['id_menu']]);
            }

            // 3. Kembalikan Kuota Promo & Poin (jika memakai promo)
            if (!empty($pesanan['id_promo'])) {
                $stmt_promo = $this->conn->prepare("SELECT * FROM tb_promo WHERE id_promo = ?");
                $stmt_promo->execute([$pesanan['id_promo']]);
                $promo = $stmt_promo->fetch();

                if ($promo) {
                    // Kembalikan kuota
                    if ($promo['kuota'] !== null) {
                        $this->conn->prepare("UPDATE tb_promo SET kuota = kuota + 1 WHERE id_promo = ?")->execute([$pesanan['id_promo']]);
                    }

                    // Kembalikan poin jika tipe promosinya adalah Tukar_Poin
                    if ($promo['tipe_promo'] == 'Tukar Poin' && $promo['min_poin'] > 0) {
                        $this->conn->prepare("UPDATE tb_member SET poin = poin + ? WHERE id_member = ?")->execute([$promo['min_poin'], $id_member]);

                        // Catat di History Poin Masuk
                        $hist = $this->conn->prepare("INSERT INTO tb_history_poin (id_member, poin, tipe, keterangan, tgl_perubahan) VALUES (?, ?, 'Masuk', ?, NOW())");
                        $hist->execute([$id_member, $promo['min_poin'], "Pengembalian Poin (Batal Pesanan #" . $id_pesanan . ")"]);
                    }
                }
            }

            // 4. Ubah Status Pesanan Menjadi Dibatalkan
            $upd_status = $this->conn->prepare("UPDATE tb_pesanan SET status = 'Dibatalkan' WHERE id_pesanan = ?");
            $upd_status->execute([$id_pesanan]);

            $this->conn->commit();
            echo "<script>alert('Berhasil! Pesanan Anda telah dibatalkan.'); window.location='index.php?controller=pelanggan&action=profil&tab=riwayat';</script>";
        } catch (Exception $e) {
            $this->conn->rollBack();
            $err = addslashes($e->getMessage());
            echo "<script>alert('Gagal membatalkan pesanan: $err'); window.location='index.php?controller=pelanggan&action=profil&tab=riwayat';</script>";
        }
    }

    public function simpan_ulasan()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_pesanan = $_POST['id_pesanan'];
            $komentar = htmlspecialchars($_POST['komentar']);
            $id_member = $_SESSION['id_member'];
            $poin_hadiah = 5; // jumlah poin hadiah

            try {
                $this->conn->beginTransaction();

                // 1. Simpan ke tb_ulasan
                $stmt = $this->conn->prepare("INSERT INTO tb_ulasan (id_pesanan, id_member, komentar, tgl_ulasan) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$id_pesanan, $id_member, $komentar]);

                // 2. Tambah Poin dan Total Poin di tb_member
                $updatePoin = $this->conn->prepare("UPDATE tb_member SET poin = poin + ? WHERE id_member = ?");
                $updatePoin->execute([$poin_hadiah, $id_member]);

                // 3. Catat di history poin (Tambahkan 'Masuk')
                $stmt_hist = $this->conn->prepare("INSERT INTO tb_history_poin (id_member, poin, tipe, keterangan, tgl_perubahan) VALUES (?, ?, 'Masuk', ?, NOW())");
                if ($stmt_hist) {
                    $stmt_hist->execute([$id_member, $poin_hadiah, "Bonus Ulasan Pesanan $id_pesanan"]);
                }

                $this->conn->commit();

                echo "<script>alert('Terima kasih! Ulasan berhasil dikirim dan Anda mendapatkan $poin_hadiah poin.'); window.location='index.php?controller=pelanggan&action=profil';</script>";
            } catch (Exception $e) {
                $this->conn->rollBack();
                die("Error Database: " . $e->getMessage());
            }
        }
    }
}
