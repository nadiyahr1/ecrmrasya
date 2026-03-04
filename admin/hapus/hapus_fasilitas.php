<?php
require_once '../../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $res = $conn->prepare("SELECT foto_fasilitas FROM tb_fasilitas WHERE id_fasilitas = ?");
    $res->execute([$id]);
    $f = $res->fetch();

    if ($f) {
        if (file_exists("../../assets/gambar/fasilitas/" . $f['foto_fasilitas'])) { unlink("../../assets/gambar/fasilitas/" . $f['foto_fasilitas']); }
        $conn->prepare("DELETE FROM tb_fasilitas WHERE id_fasilitas = ?")->execute([$id]);
    }
    echo "<script>alert('Fasilitas dihapus!'); window.location.href='../index.php?page=fasilitas';</script>";
}
?>