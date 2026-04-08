<?php
require_once 'config/koneksi.php';

class MenuController
{

    public function index()
    {
        global $conn;

        // ambil kategori
        $kategori = $conn->query("SELECT * FROM tb_kategori")->fetchAll();

        // ambil menu
        $menus = $conn->query("
            SELECT m.*, k.nama_kategori 
            FROM tb_menu m 
            JOIN tb_kategori k ON m.id_kategori = k.id_kategori 
            ORDER BY m.id_menu DESC
        ")->fetchAll();

        // kirim ke view
        require 'views/menu/index.php';
    }
    public function detailMenu()
    {
        global $conn;

        $id_menu = $_GET['id'] ?? null;
        if (!$id_menu) {
            echo "<script>alert('Pilih menu terlebih dahulu!'); window.location='index.php?controller=menu';</script>";
            exit;
        }

        // Ambil data menu spesifik
        $stmt = $conn->prepare("SELECT m.*, k.nama_kategori FROM tb_menu m JOIN tb_kategori k ON m.id_kategori = k.id_kategori WHERE m.id_menu = ?");
        $stmt->execute([$id_menu]);
        $menu = $stmt->fetch();

        if (!$menu) {
            echo "<script>alert('Menu tidak ditemukan!'); window.location='index.php?controller=menu';</script>";
            exit;
        }

        // Ambil ulasan untuk menu ini
        $stmt_ulasan = $conn->prepare("
            SELECT u.komentar, u.tgl_ulasan, mem.nama_member 
            FROM tb_ulasan u 
            JOIN tb_pesanan p ON u.id_pesanan = p.id_pesanan 
            JOIN tb_detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
            JOIN tb_member mem ON p.id_member = mem.id_member
            WHERE dp.id_menu = ? AND u.status_tampil = 'Y'
            ORDER BY u.id_ulasan DESC LIMIT 10
        ");
        $stmt_ulasan->execute([$id_menu]);
        $ulasan = $stmt_ulasan->fetchAll();

        // Panggil View
        require_once 'layout/header.php';
        require_once 'views/menu/detail_menu.php';
        require_once 'layout/footer.php';
    }
}
