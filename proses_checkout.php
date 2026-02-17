<?php
session_start();

// Cek apakah sudah login sebagai pelanggan
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Pelanggan') {
    // Jika belum login, beri notifikasi dan lempar ke halaman login
    echo "<script>
            alert('Silahkan Login atau Daftar akun terlebih dahulu untuk melakukan pesanan, mendapatkan poin, dan reward lainnya!');
            window.location='login.php';
          </script>";
    exit;
} else {
    // Jika sudah login, arahkan ke halaman formulir checkout sesungguhnya
    header("Location: checkout.php");
    exit;
}
?>