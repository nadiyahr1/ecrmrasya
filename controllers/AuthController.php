<?php
session_start();
require_once '../config/koneksi.php';

$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // LOGIN
    if ($action == 'login') {

        $user_input = $_POST['username'];
        $pass_input = $_POST['password'];

        // 1. CEK TB_USER (Admin & Owner)
        $stmt = $conn->prepare("SELECT * FROM tb_user WHERE username = ?");
        $stmt->execute([$user_input]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass_input, $user['password'])) {

            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama']    = $user['nama_user'];
            $_SESSION['role']    = $user['role'];

            if ($user['role'] == 'Admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../owner/dashboard.php");
            }
            exit;
        }

        // 2. CEK TB_MEMBER (Pelanggan)
        $stmt_member = $conn->prepare("SELECT * FROM tb_member WHERE username = ?");
        $stmt_member->execute([$user_input]);
        $member = $stmt_member->fetch();

        if ($member && password_verify($pass_input, $member['password'])) {

            if ($member['status_akun'] == 'Aktif') {

                $_SESSION['id_member'] = $member['id_member'];
                $_SESSION['nama']      = $member['nama_member'];
                $_SESSION['role']      = 'Pelanggan';
                $_SESSION['id_level']  = $member['id_level'];

                header("Location: ../index.php");
                exit;

            } else {
                echo "<script>alert('Akun belum aktif'); 
                      window.location='../views/auth/login.php';</script>";
                exit;
            }
        }

        // LOGIN GAGAL
        echo "<script>alert('Username atau Password salah!'); 
              window.location='../views/auth/login.php';</script>";
        exit;
    }

    // REGISTRASI
    if ($action == 'register') {

        $nama     = $_POST['nama'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $no_telp  = $_POST['no_telp'];

        try {

            $sql = "INSERT INTO tb_member 
                    (id_level, poin, nama_member, username, password, no_telp, total_poin, status_akun) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([1, 0, $nama, $username, $password, $no_telp, 0, 'Aktif']);

            echo "<script>alert('Registrasi berhasil! Silakan login.'); 
                  window.location='../views/auth/login.php';</script>";

        } catch (PDOException $e) {

            if ($e->getCode() == 23000) {
                echo "<script>alert('Username sudah digunakan!'); 
                      window.history.back();</script>";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }

        exit;
    }

}
?>