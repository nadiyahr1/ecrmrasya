<?php
require_once '../../config/koneksi.php';
// ob_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama      = $_POST['nama_promo'];
    $kode      = $_POST['kode_promo'];
    $desc      = $_POST['deskripsi'];
    $tipe      = $_POST['tipe_promo'];
    $potongan  = $_POST['potongan'];
    $t_pot     = $_POST['tipe_potongan'];
    $min_poin  = $_POST['min_poin'];
    $tgl_m     = $_POST['tgl_mulai'];
    $tgl_s     = $_POST['tgl_selesai'];
    $status    = $_POST['status_promo'];
    
    // Penanganan File Foto
    $foto_name = $_FILES['foto']['name'];
    $tmp_name  = $_FILES['foto']['tmp_name'];
    $ekstensi  = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));
    $nama_baru = "PRM-" . date('YmdHis') . "." . $ekstensi;

    // Validasi Ekstensi (Keamanan)
    $ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'webp'];
    
    if (in_array($ekstensi, $ekstensi_diizinkan)) {
        if (move_uploaded_file($tmp_name, "../../assets/gambar/promo/" . $nama_baru)) {
            $stmt = $conn->prepare("INSERT INTO tb_promo (nama_promo, kode_promo, deskripsi, tipe_promo, potongan, tipe_potongan, min_poin, tgl_mulai, tgl_selesai, status_promo, foto_promo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $kode, $desc, $tipe, $potongan, $t_pot, $min_poin, $tgl_m, $tgl_s, $status, $nama_baru]);
        }
    }
    // Redirect aman tanpa halaman kosong
    // header("Location: ../index.php?page=promo");
    // exit();
    echo "<script>alert('Promo berhasil ditambahkan!'); window.location.href='../index.php?page=promo';</script>";
}
// ob_end_flush();
?>