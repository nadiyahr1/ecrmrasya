<?php
require_once 'config/koneksi.php';

class MenuController {

    public function index() {
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
}