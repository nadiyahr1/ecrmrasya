<?php
session_start();
require_once 'config/koneksi.php';

if (isset($_SESSION['id_member'])) {
    $id_member = $_SESSION['id_member'];

    // Kumpulkan keranjang menu dan fasilitas
    $cart_data = [
        'menu' => $_SESSION['keranjang'] ?? [],
        'fasilitas' => $_SESSION['keranjang_fasilitas'] ?? []
    ];

    // Ubah jadi teks JSON
    $json_cart = json_encode($cart_data);

    // Simpan ke database
    $stmt = $conn->prepare("UPDATE tb_member SET data_keranjang = ? WHERE id_member = ?");
    $stmt->execute([$json_cart, $id_member]);
}
session_destroy(); // Menghapus semua data login
header("Location: index.php"); // Kembali ke halaman login
exit;
