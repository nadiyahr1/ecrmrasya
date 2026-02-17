<?php
require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $no_telp  = $_POST['no_telp'];
    $id_level = 1; // Default level untuk member baru (misal: Bronze/Basic)

    try {
        $sql = "INSERT INTO tb_member (id_level, nama_member, username, password, no_telp, total_poin, status_akun) 
                VALUES (?, ?, ?, ?, ?, 0, 'Pending')";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_level, $nama, $username, $password, $no_telp]);

        echo "<script>alert('Pendaftaran berhasil! Silahkan tunggu verifikasi Admin.'); window.location='index.php';</script>";
    } catch (PDOException $e) {
        echo "Gagal daftar: " . $e->getMessage();
    }
}