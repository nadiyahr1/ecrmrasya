<?php
session_start();
require_once '../config/koneksi.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php"); exit;
}

// Tambah Kategori
if (isset($_POST['tambah'])) {
    $nama_kategori = $_POST['nama_kategori'];
    $stmt = $conn->prepare("INSERT INTO tb_kategori (nama_kategori) VALUES (?)");
    $stmt->execute([$nama_kategori]);
    echo "<script>alert('Kategori berhasil ditambah!'); window.location='kategori.php';</script>";
}

// Ambil semua kategori
$kategori = $conn->query("SELECT * FROM tb_kategori")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head><title>Manajemen Kategori - Admin</title></head>
<body>
    <h2>Manajemen Kategori Menu</h2>
    <form method="POST">
        <input type="text" name="nama_kategori" placeholder="Nama Kategori (Misal: Makanan)" required>
        <button type="submit" name="tambah">Tambah</button>
    </form>

    <table border="1" cellpadding="10" style="margin-top: 20px;">
        <tr><th>ID</th><th>Nama Kategori</th></tr>
        <?php foreach ($kategori as $k) : ?>
        <tr>
            <td><?= $k['id_kategori']; ?></td>
            <td><?= $k['nama_kategori']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br><a href="dashboard.php">Kembali ke Dashboard</a>
</body>
</html>