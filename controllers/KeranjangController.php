<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

$data_menu = [];
$data_fasilitas = [];

// ======================
// AMBIL DATA MENU
// ======================
if (isset($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $id_menu => $qty) {
        $stmt = $conn->prepare("SELECT * FROM tb_menu WHERE id_menu = ?");
        $stmt->execute([$id_menu]);
        $m = $stmt->fetch();

        if ($m) {
            $m['qty'] = $qty;
            $m['subtotal'] = $m['harga'] * $qty;
            $data_menu[] = $m;
        }
    }
}

// ======================
// AMBIL DATA FASILITAS
// ======================
if (isset($_SESSION['keranjang_fasilitas'])) {
    foreach ($_SESSION['keranjang_fasilitas'] as $id => $item) {
        $f = $conn->query("SELECT * FROM tb_fasilitas WHERE id_fasilitas = $id")->fetch();

        if ($f) {
            $f['pengali'] = $item['pengali'];
            $f['subtotal'] = $f['harga'] * $item['pengali'];
            $f['tgl_sewa'] = $item['tgl_sewa'];
            $f['jam_mulai'] = $item['jam_mulai'];
            $f['satuan'] = $item['satuan'];
            $data_fasilitas[] = $f;
        }
    }
}