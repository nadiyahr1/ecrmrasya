<?php
require_once '../../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("SELECT foto FROM tb_menu WHERE id_menu = ?");
    $stmt->execute([$id]);
    $f = $stmt->fetch();
    
    if($f) {
        if(file_exists("../../assets/gambar/menu/".$f['foto']) && $f['foto'] != '') {
            unlink("../../assets/gambar/menu/".$f['foto']);
        }
        
        $conn->prepare("DELETE FROM tb_menu WHERE id_menu = ?")->execute([$id]);
        echo "<script>alert('Menu berhasil dihapus!'); window.location.href='../index.php?page=menu';</script>";
    }
}
?>