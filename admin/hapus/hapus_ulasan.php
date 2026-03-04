<?php
require_once '../../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Hapus data dari tabel berdasarkan id_ulasan
    $stmt = $conn->prepare("DELETE FROM tb_ulasan WHERE id_ulasan = ?");
    
    if ($stmt->execute([$id])) {
        echo "<script>
                alert('Ulasan berhasil dihapus permanen!'); 
                window.location.href='../index.php?page=ulasan';
              </script>";
    }
}
?>