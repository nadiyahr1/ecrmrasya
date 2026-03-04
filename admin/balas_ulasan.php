<?php
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_ulasan = $_POST['id_ulasan'];
    $balasan   = $_POST['balasan_admin'];

    // Update kolom balasan_admin
    $stmt = $conn->prepare("UPDATE tb_ulasan SET balasan_admin = ? WHERE id_ulasan = ?");
    
    if ($stmt->execute([$balasan, $id_ulasan])) {
        // Menggunakan alert JS agar admin tahu balasannya terkirim
        echo "<script>
                alert('Balasan berhasil dikirim!'); 
                window.location.href='index.php?page=ulasan';
              </script>";
    } else {
        echo "<script>
                alert('Gagal mengirim balasan!'); 
                window.location.href='index.php?page=ulasan';
              </script>";
    }
}
?>