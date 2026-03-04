<?php
require_once '../../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Proteksi: Cek apakah kategori masih dipakai di tabel menu
    $cek = $conn->prepare("SELECT COUNT(*) FROM tb_menu WHERE id_kategori = ?");
    $cek->execute([$id]);
    if ($cek->fetchColumn() > 0) {
        echo "<script>alert('Gagal! Kategori ini masih digunakan oleh beberapa menu.'); window.location.href='../index.php?page=kategori_menu';</script>";
    } else {
        $conn->prepare("DELETE FROM tb_kategori WHERE id_kategori = ?")->execute([$id]);
        echo "<script>alert('Kategori berhasil dihapus!'); window.location.href='../index.php?page=kategori_menu';</script>";
    }
}
?>