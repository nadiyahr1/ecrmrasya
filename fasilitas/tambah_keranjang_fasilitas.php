<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_f     = $_POST['id_fasilitas'];
    $tgl      = $_POST['tgl_booking'];
    $jam      = $_POST['jam_mulai'];
    $durasi   = $_POST['durasi'];

    // Inisialisasi session jika belum ada
    if (!isset($_SESSION['keranjang_fasilitas'])) {
        $_SESSION['keranjang_fasilitas'] = [];
    }

    // Simpan data booking ke dalam array session
    // Kita gunakan ID fasilitas sebagai kunci agar tidak ada booking ganda untuk fasilitas yang sama dalam satu transaksi
    $_SESSION['keranjang_fasilitas'][$id_f] = [
        'tgl_booking' => $tgl,
        'jam_mulai'   => $jam,
        'durasi'      => $durasi
    ];

    echo "<script>
            alert('Fasilitas berhasil ditambahkan ke keranjang!');
            window.location='../keranjang.php';
          </script>";
}
?>