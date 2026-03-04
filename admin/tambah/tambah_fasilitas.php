<?php
require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama   = $_POST['nama_fasilitas'];
    $harga  = $_POST['harga'];
    $satuan = $_POST['satuan'];
    $desc   = $_POST['deskripsi'];
    
    // Gunakan isset untuk mencegah error 'Undefined array key'
    $status = isset($_POST['status_fasilitas']) ? $_POST['status_fasilitas'] : 'Tersedia';
    
    // Ambil data foto
    $foto_name = $_FILES['foto']['name'];
    $tmp_name  = $_FILES['foto']['tmp_name'];
    
    if ($foto_name != '') {
        $ekstensi  = pathinfo($foto_name, PATHINFO_EXTENSION);
        $nama_baru = "FAC-" . date('YmdHis') . "." . $ekstensi;

        if (move_uploaded_file($tmp_name, "../../assets/gambar/fasilitas/" . $nama_baru)) {
            $stmt = $conn->prepare("INSERT INTO tb_fasilitas (nama_fasilitas, harga, satuan, deskripsi, foto_fasilitas, status_fasilitas) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $harga, $satuan, $desc, $nama_baru, $status]);
            
            echo "<script>alert('Fasilitas berhasil ditambah!'); window.location.href='../index.php?page=fasilitas';</script>";
        } else {
            echo "<script>alert('Gagal mengunggah gambar!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Mohon pilih foto fasilitas!'); window.history.back();</script>";
    }
}
?>