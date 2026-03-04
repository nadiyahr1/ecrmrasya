<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id_ulasan = $_GET['id'];
    $status_baru = ($_GET['status'] == 'Y') ? 'Y' : 'N'; 

    try {
        $stmt = $conn->prepare("UPDATE tb_ulasan SET status_tampil = ? WHERE id_ulasan = ?");
        $stmt->execute([$status_baru, $id_ulasan]);
        
        // --- PERBAIKAN REDIRECT DI SINI ---
        echo "<script>
                alert('Status ulasan berhasil diperbarui!');
                window.location.href = 'index.php?page=ulasan'; 
              </script>";
    } catch (PDOException $e) {
        // --- PERBAIKAN REDIRECT DI SINI ---
        echo "<script>
                alert('Gagal memperbarui status: " . $e->getMessage() . "');
                window.location.href = 'index.php?page=ulasan';
              </script>";
    }
} else {
    // --- PERBAIKAN REDIRECT DI SINI ---
    header("Location: index.php?page=ulasan");
}
?>