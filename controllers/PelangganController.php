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
        $id_m = $_SESSION['id_member'];
        $tab = $_GET['tab'] ?? 'riwayat';

        // 1. Ambil Data User & Level untuk Kartu Member
        $stmt = $this->conn->prepare("SELECT m.*, l.nama_level FROM tb_member m JOIN tb_level_member l ON m.id_level = l.id_level WHERE m.id_member = ?");
        $stmt->execute([$id_m]);
        $user = $stmt->fetch();

        // 2. Ambil Riwayat Pesanan
        $stmt_riwayat = $this->conn->prepare("
            SELECT p.*, u.komentar, u.balasan_admin 
            FROM tb_pesanan p 
            LEFT JOIN tb_ulasan u ON p.id_pesanan = u.id_pesanan 
            WHERE p.id_member = ? 
            ORDER BY p.tgl_pesanan DESC
        ");
        $stmt_riwayat->execute([$id_m]);
        $riwayat = $stmt_riwayat->fetchAll();

        // 3. Ambil Voucher (Promo tipe Loyalty)
        $stmt_v = $this->conn->prepare("SELECT * FROM tb_promo WHERE tipe_promo = 'Loyalty'");
        $stmt_v->execute();
        $vouchers = $stmt_v->fetchAll();

        // 4. Ambil Menu Favorit
        $sql_fav = "SELECT m.id_menu, m.nama_menu, m.harga, m.foto, COUNT(dp.id_menu) as total_dipesan 
                    FROM tb_detail_pesanan dp 
                    JOIN tb_menu m ON dp.id_menu = m.id_menu 
                    JOIN tb_pesanan p ON dp.id_pesanan = p.id_pesanan 
                    WHERE p.id_member = ? 
                    GROUP BY dp.id_menu 
                    ORDER BY total_dipesan DESC LIMIT 6";
        $stmt_fav = $this->conn->prepare($sql_fav);
        $stmt_fav->execute([$id_m]);
        $favorit = $stmt_fav->fetchAll();

        // 5. Panggil View
        require_once 'layout/header.php';
        require_once 'views/pelanggan/profil.php';
        require_once 'layout/footer.php';
    }
}