<?php
require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_kategori'];
    
    $stmt = $conn->prepare("INSERT INTO tb_kategori (nama_kategori) VALUES (?)");
    if($stmt->execute([$nama])) {
        // Redirect kembali ke index.php di luar folder
        echo "<script>alert('Kategori berhasil ditambahkan!'); window.location.href='../index.php?page=kategori_menu';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan kategori!'); window.history.back();</script>";
    }
}
?>