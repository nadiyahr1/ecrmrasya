<?php
require_once __DIR__ . '/../config/koneksi.php';

class PromoController
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // Menampilkan daftar semua promo
    public function index()
    {
        $stmt = $this->conn->prepare("SELECT * FROM tb_promo WHERE status_promo = 'Aktif' ORDER BY id_promo DESC");
        $stmt->execute();
        $promos = $stmt->fetchAll();

        require_once 'layout/header.php';
        require_once 'views/promo/index.php';
        require_once 'layout/footer.php';
    }

    // Menampilkan detail satu promo
    public function detailPromo()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=promo");
            exit;
        }

        $stmt = $this->conn->prepare("SELECT * FROM tb_promo WHERE id_promo = ?");
        $stmt->execute([$id]);
        $promo = $stmt->fetch();

        if (!$promo) {
            echo "<script>alert('Promo tidak ditemukan!'); window.location='index.php?controller=promo';</script>";
            exit;
        }

        require_once 'layout/header.php';
        require_once 'views/promo/detail_promo.php';
        require_once 'layout/footer.php';
    }
}
