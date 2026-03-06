<?php
require_once 'config/koneksi.php';

class HomeController
{

    public function index()
    {
        global $conn;

        $kategori = $conn->query("SELECT * FROM tb_kategori")->fetchAll();

        $menus = $conn->query("
            SELECT m.*, k.nama_kategori 
            FROM tb_menu m 
            JOIN tb_kategori k ON m.id_kategori = k.id_kategori 
            ORDER BY m.id_menu DESC
        ")->fetchAll();

        $fasilitas = $conn->query("SELECT * FROM tb_fasilitas LIMIT 3")->fetchAll();

        $promos = $conn->query("
            SELECT * FROM tb_promo 
            WHERE tipe_promo = 'Loyalty' 
            LIMIT 3
        ")->fetchAll();

        try {
            $ulasan = $conn->query("
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
}
