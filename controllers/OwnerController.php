<?php
require_once __DIR__ . '/../config/koneksi.php';

class OwnerController
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
        if (session_status() === PHP_SESSION_NONE) session_start();

        // PROTEKSI: Pastikan hanya Owner yang bisa masuk ke sini
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Owner') {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }

    // Fungsi untuk menampilkan halaman utama Dashboard Owner
    public function index()
    {
        $page = 'dashboard_owner';
        $bulan_ini = date('Y-m');
        $tahun_ini = date('Y');
        $hari_ini = date('Y-m-d');

        // Membuat format Nama Bulan Indonesia (Contoh: "Maret 2026")
        $bulan_indo = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        $nama_bulan_ini = $bulan_indo[date('m')] . ' ' . $tahun_ini;

        // --- BAGIAN 1: PERFORMA BULANAN ---
        // 1. Omzet (Bulan Ini)
        $stmtOmzet = $this->conn->prepare("SELECT SUM(total_transaksi) as total FROM tb_pesanan WHERE DATE_FORMAT(tgl_pesanan, '%Y-%m') = ? AND status = 'Selesai'");
        $stmtOmzet->execute([$bulan_ini]);
        $omzet_bulan_ini = $stmtOmzet->fetch()['total'] ?? 0;

        // 2. Total Transaksi (Bulan Ini)
        $stmtPesanan = $this->conn->prepare("SELECT COUNT(*) as total FROM tb_pesanan WHERE DATE_FORMAT(tgl_pesanan, '%Y-%m') = ? AND status = 'Selesai'");
        $stmtPesanan->execute([$bulan_ini]);
        $total_pesanan = $stmtPesanan->fetch()['total'] ?? 0;

        // --- BAGIAN 2: AKUMULASI TAHUNAN ---
        // 3. Member Baru (Tahun Ini)
        $stmtMember = $this->conn->prepare("SELECT COUNT(*) as total FROM tb_member WHERE YEAR(tgl_daftar) = ?");
        $stmtMember->execute([$tahun_ini]);
        $total_member = $stmtMember->fetch()['total'] ?? 0;

        // 4. Poin Terpakai (Tahun Ini)
        $stmtPoin = $this->conn->prepare("SELECT SUM(poin) as total FROM tb_history_poin WHERE tipe = 'Keluar' AND YEAR(tgl_perubahan) = ?");
        $stmtPoin->execute([$tahun_ini]);
        $poin_terpakai = $stmtPoin->fetch()['total'] ?? 0;

        // Reservasi Per Hari
        $sql_res = "SELECT r.*, m.nama_member, f.nama_fasilitas 
                FROM tb_booking_fasilitas r
                LEFT JOIN tb_member m ON r.id_booking = m.id_member
                LEFT JOIN tb_fasilitas f ON r.id_fasilitas = f.id_fasilitas
                WHERE DATE(r.tgl_sewa) = '$hari_ini'
                ORDER BY r.tgl_sewa ASC";
        $reservasi_hari_ini = $this->conn->query($sql_res)->fetchAll();

        // Ambil Data Transaksi Hari Ini (Limit 5 transaksi terbaru)
        $sql_trx = "SELECT p.*, m.nama_member 
                FROM tb_pesanan p 
                LEFT JOIN tb_member m ON p.id_member = m.id_member 
                WHERE DATE(p.tgl_pesanan) = '$hari_ini'
                ORDER BY p.tgl_pesanan DESC LIMIT 5";
        $transaksi_hari_ini = $this->conn->query($sql_trx)->fetchAll();

        // Load Views
        require_once 'views/owner/header.php';
        require_once 'views/owner/index.php';
        require_once 'views/owner/footer.php';
    }

    // --- 1. FITUR PENGATURAN AKUN (EDIT PROFIL) ---
    public function profil()
    {
        $page = 'profil';
        $id_user = $_SESSION['id_user']; // ID Owner

        $stmt = $this->conn->prepare("SELECT * FROM tb_user WHERE id_user = ?");
        $stmt->execute([$id_user]);
        $user = $stmt->fetch();

        require_once 'views/owner/header.php';
        require_once 'views/owner/profil.php';
        require_once 'views/owner/footer.php';
    }

    public function proses_edit_profil()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_user = $_SESSION['id_user'];
            $username = htmlspecialchars(trim($_POST['username']));
            $nama = htmlspecialchars(trim($_POST['nama']));
            $password_baru = trim($_POST['password_baru']);

            try {
                // Cek username kembar
                $cek = $this->conn->prepare("SELECT id_user FROM tb_user WHERE username = ? AND id_user != ?");
                $cek->execute([$username, $id_user]);
                if ($cek->rowCount() > 0) throw new Exception("Username sudah digunakan.");

                if (!empty($password_baru)) {
                    $pass_db = password_hash($password_baru, PASSWORD_DEFAULT);
                    $stmt = $this->conn->prepare("UPDATE tb_user SET username = ?, nama_user = ?, password = ? WHERE id_user = ?");
                    $stmt->execute([$username, $nama, $pass_db, $id_user]);
                } else {
                    $stmt = $this->conn->prepare("UPDATE tb_user SET username = ?, nama_user = ? WHERE id_user = ?");
                    $stmt->execute([$username, $nama, $id_user]);
                }

                $_SESSION['nama'] = $nama; // Update nama di header
                echo "<script>alert('Profil Owner berhasil diperbarui!'); window.location='index.php?controller=owner&action=profil';</script>";
            } catch (Exception $e) {
                $err = addslashes($e->getMessage());
                echo "<script>alert('Gagal: $err'); window.history.back();</script>";
            }
        }
    }

    // --- 2. FITUR MANAJEMEN ADMIN ---
    public function manajemen_admin()
    {
        $page = 'manajemen_admin';

        // Ambil data admin saja (bukan Owner)
        $stmt = $this->conn->query("SELECT * FROM tb_user WHERE role = 'Admin' ORDER BY id_user ASC");
        $admins = $stmt->fetchAll();

        require_once 'views/owner/header.php';
        require_once 'views/owner/manajemen_admin.php';
        require_once 'views/owner/footer.php';
    }

    public function proses_tambah_admin()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = htmlspecialchars(trim($_POST['username']));
            $nama = htmlspecialchars(trim($_POST['nama']));
            $password = trim($_POST['password']);
            $role = 'Admin';

            try {
                // Cek apakah username sudah ada
                $cek = $this->conn->prepare("SELECT id_user FROM tb_user WHERE username = ?");
                $cek->execute([$username]);
                if ($cek->rowCount() > 0) throw new Exception("Username sudah terdaftar! Pilih username lain.");

                $pass_db = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare("INSERT INTO tb_user (nama_user, username, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nama, $username, $pass_db, $role]);

                echo "<script>alert('Berhasil! Admin baru telah ditambahkan.'); window.location='index.php?controller=owner&action=manajemen_admin';</script>";
            } catch (Exception $e) {
                $err = addslashes($e->getMessage());
                echo "<script>alert('Gagal menambah admin: $err'); window.history.back();</script>";
            }
        }
    }

    public function analisis_pelanggan()
    {
        $page = 'analisis_pelanggan';

        // 1. QUERY TOP 10 PELANGGAN (Berdasarkan Total Belanja)
        $sqlTop = "SELECT m.nama_member, m.id_member, l.nama_level, 
               SUM(p.total_transaksi) as total_belanja, 
               COUNT(p.id_pesanan) as jumlah_kunjungan
               FROM tb_member m
               JOIN tb_level_member l ON m.id_level = l.id_level
               JOIN tb_pesanan p ON m.id_member = p.id_member
               WHERE p.status = 'Selesai'
               GROUP BY m.id_member
               ORDER BY total_belanja DESC LIMIT 10";
        $top_customers = $this->conn->query($sqlTop)->fetchAll();

        // 2. QUERY PELANGGAN PASIF (Tidak berkunjung > 30 hari)
        $sqlPasif = "SELECT m.nama_member, m.no_telp, m.id_member,
                MAX(p.tgl_pesanan) as kunjungan_terakhir,
                DATEDIFF(CURDATE(), MAX(p.tgl_pesanan)) as jumlah_hari
                FROM tb_member m
                JOIN tb_pesanan p ON m.id_member = p.id_member
                GROUP BY m.id_member
                HAVING jumlah_hari >= 30
                ORDER BY jumlah_hari DESC";
        $pelanggan_pasif = $this->conn->query($sqlPasif)->fetchAll();

        // 3. QUERY PERFORMA LEVEL (Jumlah member per level)
        $sqlLevel = "SELECT l.nama_level, COUNT(m.id_member) as total
                 FROM tb_level_member l
                 LEFT JOIN tb_member m ON l.id_level = m.id_level
                 GROUP BY l.id_level";
        $performa_level = $this->conn->query($sqlLevel)->fetchAll();

        require_once 'views/owner/header.php';
        require_once 'views/owner/analisis_pelanggan.php';
        require_once 'views/owner/footer.php';
    }
}
