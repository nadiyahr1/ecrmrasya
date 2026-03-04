<?php
require_once '../../config/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id        = $_POST['id_menu'];
    $nama      = $_POST['nama_menu'];
    $id_kat    = $_POST['id_kategori'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $foto_lama = $_POST['foto_lama'];

    // Logika Otomatisasi Status (Akan menyesuaikan stok baru yang diinput)
    $status_menu = ($stok > 0) ? 'Tersedia' : 'Habis';

    // Cek apakah ada foto baru yang diupload
    if ($_FILES['foto']['name'] != '') {
        $foto_nama = $_FILES['foto']['name'];
        $tmp_name  = $_FILES['foto']['tmp_name'];
        $size      = $_FILES['foto']['size'];
        $ekstensi  = strtolower(pathinfo($foto_nama, PATHINFO_EXTENSION));

        $ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'webp'];
        $max_size = 2 * 1024 * 1024;

        if (!in_array($ekstensi, $ekstensi_diizinkan)) {
            echo "<script>alert('Format tidak didukung!'); window.history.back();</script>";
            exit;
        }
        if ($size > $max_size) {
            echo "<script>alert('Ukuran maksimal 2MB!'); window.history.back();</script>";
            exit;
        }
        // Jika lolos, proses upload dan hapus foto lama
        $nama_baru = date('YmdHis') . "." . $ekstensi;
        if (move_uploaded_file($tmp_name, "../../assets/gambar/menu/" . $nama_baru)) {
            if (file_exists("../../assets/gambar/menu/" . $foto_lama) && $foto_lama != '') {
                unlink("../../assets/gambar/menu/" . $foto_lama);
            }
            // Update query dengan foto baru
            $stmt = $conn->prepare("UPDATE tb_menu SET id_kategori=?, nama_menu=?, harga=?, stok=?, status_menu=?, foto=? WHERE id_menu=?");
            $stmt->execute([$id_kat, $nama, $harga, $stok, $status_menu, $nama_baru, $id]);
        }
    } else {
        $stmt = $conn->prepare("UPDATE tb_menu SET id_kategori=?, nama_menu=?, harga=?, stok=?, status_menu=? WHERE id_menu=?");
        $stmt->execute([$id_kat, $nama, $harga, $stok, $status_menu, $id]);
    }

    echo "<script>alert('Menu berhasil diupdate!'); window.location.href='../index.php?page=menu';</script>";
}
