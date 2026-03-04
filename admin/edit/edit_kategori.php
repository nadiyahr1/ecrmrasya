<?php
require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_kategori'];
    $nama = $_POST['nama_kategori'];
    
    $stmt = $conn->prepare("UPDATE tb_kategori SET nama_kategori = ? WHERE id_kategori = ?");
    if($stmt->execute([$nama, $id])) {
        echo "<script>alert('Kategori berhasil diupdate!'); window.location.href='../index.php?page=kategori_menu';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate kategori!'); window.history.back();</script>";
    }
}
?>