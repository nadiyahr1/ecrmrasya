<?php
require_once '../../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ambil nama file foto dari database
    $res = $conn->prepare("SELECT foto_promo FROM tb_promo WHERE id_promo = ?");
    $res->execute([$id]);
    $p = $res->fetch(PDO::FETCH_ASSOC);

    // Hapus file fisik jika ada
    if ($p && !empty($p['foto_promo'])) {
        $path = "../../assets/gambar/promo/" . $p['foto_promo'];
        if (file_exists($path) && is_file($path)) {
            unlink($path);
        }
    }
    
    // Hapus data dari tabel
    $conn->prepare("DELETE FROM tb_promo WHERE id_promo = ?")->execute([$id]);
    
    echo "<script>alert('Promo berhasil dihapus!'); window.location.href='../index.php?page=promo';</script>";
}
?>