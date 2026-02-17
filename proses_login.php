<?php
// Memulai session untuk menyimpan tanda pengenal user
session_start();

// Memanggil file koneksi yang sudah kamu buat
require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_input = $_POST['username'];
    $pass_input = $_POST['password'];

    // 1. CEK DI TABEL TB_USER (Admin & Owner)
    $stmt = $conn->prepare("SELECT * FROM tb_user WHERE username = ?");
    $stmt->execute([$user_input]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass_input, $user['password'])) {
        // Jika login berhasil sebagai Admin atau Owner
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama']    = $user['nama_user'];
        $_SESSION['role']    = $user['role']; // Isinya 'Admin' atau 'Owner'

        if ($user['role'] == 'Admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: owner/index.php");
        }
        exit;
    }

    // 2. CEK DI TABEL TB_MEMBER (Pelanggan)
    $stmt_member = $conn->prepare("SELECT * FROM tb_member WHERE username = ?");
    $stmt_member->execute([$user_input]);
    $member = $stmt_member->fetch();

    if ($member && password_verify($pass_input, $member['password'])) {
        // Cek apakah akun aktif
        if ($member['status_akun'] == 'Aktif') {
            $_SESSION['id_member'] = $member['id_member'];
            $_SESSION['nama']      = $member['nama_member'];
            $_SESSION['role']      = 'Pelanggan';
            
            header("Location: pelanggan/index.php");
            exit;
        } else {
            echo "<script>alert('Akun Anda masih Pending. Tunggu verifikasi Admin.'); window.location='index.php';</script>";
            exit;
        }
    }

    // Jika tidak ditemukan di kedua tabel atau password salah
    echo "<script>alert('Username atau Password salah!'); window.location='index.php';</script>";
}
?>