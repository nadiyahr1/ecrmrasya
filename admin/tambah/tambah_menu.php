<?php
require_once '../../config/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama      = $_POST['nama_menu'];
    $id_kat    = $_POST['id_kategori'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];

    // Logika Otomatisasi Status
    $status_menu = ($stok > 0) ? 'Tersedia' : 'Habis';

    $foto_nama = $_FILES['foto']['name'];
    $tmp_name  = $_FILES['foto']['tmp_name'];
    $size      = $_FILES['foto']['size']; // Ambil ukuran file
    $ekstensi  = strtolower(pathinfo($foto_nama, PATHINFO_EXTENSION));

    // PENGATURAN: Format yang diizinkan & Ukuran Maksimal (2MB)
    $ekstensi_diizinkan = ['jpg', 'jpeg', 'png'];
    $max_size = 2 * 1024 * 1024; // 2MB dalam bytes

    // Validasi 1: Cek Ekstensi
    if (!in_array($ekstensi, $ekstensi_diizinkan)) {
        echo "<script>alert('Format file tidak didukung! Gunakan JPG, PNG, atau WEBP.'); window.history.back();</script>";
        exit;
    }

    // Validasi 2: Cek Ukuran File
    if ($size > $max_size) {
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 2MB.'); window.history.back();</script>";
        exit;
    }

    // Jika lolos validasi, baru lakukan pemindahan file
    $nama_baru = date('YmdHis') . "." . $ekstensi;
    if (move_uploaded_file($tmp_name, "../../assets/gambar/menu/" . $nama_baru)) {
        $stmt = $conn->prepare("INSERT INTO tb_menu (id_kategori, nama_menu, harga, stok, status_menu, foto) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$id_kat, $nama, $harga, $stok, $status_menu, $nama_baru])) {
            echo "<script>alert('Menu berhasil ditambahkan!'); window.location.href='../index.php?page=menu';</script>";
        }
    } else {
        echo "<script>alert('Gagal mengupload foto!'); window.history.back();</script>";
    }
}
