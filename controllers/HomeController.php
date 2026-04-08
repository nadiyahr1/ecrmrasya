<?php
require_once 'config/koneksi.php';

class HomeController
{
    private $conn;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        global $conn;
        $this->conn = $conn;
    }

    public function index()
    {
        $kategori = $this->conn->query("SELECT * FROM tb_kategori")->fetchAll();

        $menus = $this->conn->query("
            SELECT m.*, k.nama_kategori 
            FROM tb_menu m 
            JOIN tb_kategori k ON m.id_kategori = k.id_kategori 
            ORDER BY m.id_menu DESC
            LIMIT 4
        ")->fetchAll();

        $fasilitas = $this->conn->query("SELECT * FROM tb_fasilitas LIMIT 4")->fetchAll();

        $query_promo = "SELECT * FROM tb_promo 
                WHERE status_promo = 'Aktif' 
                AND tgl_mulai <= CURDATE() 
                AND tgl_selesai >= CURDATE() 
                ORDER BY id_promo DESC";
        $promos = $this->conn->query($query_promo)->fetchAll();

        try {
            $ulasan = $this->conn->query("
                SELECT u.komentar, u.tgl_ulasan, m.nama_member 
                FROM tb_ulasan u 
                JOIN tb_pesanan p ON u.id_pesanan = p.id_pesanan 
                JOIN tb_member m ON p.id_member = m.id_member 
                WHERE u.status_tampil = 'Y'
                ORDER BY u.id_ulasan DESC 
                LIMIT 5
            ")->fetchAll();
        } catch (Exception $e) {
            $ulasan = [];
        }

        require_once 'layout/header.php';
        require_once 'views/home/index.php';
        require_once 'layout/footer.php';
    }

    // Method untuk menampilkan detail promo (Sisi Pelanggan)
    public function detailPromo()
    {
        $id_promo = $_GET['id'] ?? null;
        if (!$id_promo) {
            echo "<script>window.location='index.php';</script>";
            exit;
        }

        $stmt = $this->conn->prepare("SELECT * FROM tb_promo WHERE id_promo = ?");
        $stmt->execute([$id_promo]);
        $promo = $stmt->fetch();

        if (!$promo) {
            echo "<script>alert('Promo tidak ditemukan!'); window.location='index.php';</script>";
            exit;
        }

        require_once 'views/promo/detail_promo.php';
    }
}
