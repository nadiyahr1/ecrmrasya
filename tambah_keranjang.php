<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_menu = $_POST['id_menu'];
    $qty = 1; // Default tambah 1 tiap klik

    // Jika keranjang belum ada di session, buat array kosong
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Jika menu sudah ada di keranjang, tambah jumlahnya saja
    if (isset($_SESSION['keranjang'][$id_menu])) {
        $_SESSION['keranjang'][$id_menu] += $qty;
    } else {
        // Jika belum ada, masukkan id_menu baru
        $_SESSION['keranjang'][$id_menu] = $qty;
    }

    // Arahkan kembali ke halaman sebelumnya atau ke halaman keranjang
    echo "<script>alert('Menu berhasil ditambah ke keranjang!'); window.location='index.php';</script>";
}
?>