<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php"); exit;
}

// Ambil Kategori untuk Pilihan (Dropdown)
$kategori = $conn->query("SELECT * FROM tb_kategori")->fetchAll();

// Logika Tambah Menu
if (isset($_POST['tambah_menu'])) {
    $id_kategori = $_POST['id_kategori'];
    $nama_menu   = $_POST['nama_menu'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];

    $stmt = $conn->prepare("INSERT INTO tb_menu (id_kategori, nama_menu, harga, stok, status_menu) VALUES (?, ?, ?, ?, 'Tersedia')");
    $stmt->execute([$id_kategori, $nama_menu, $harga, $stok]);
    echo "<script>alert('Menu berhasil ditambah!'); window.location='menu.php';</script>";
}

$menu = $conn->query("SELECT tb_menu.*, tb_kategori.nama_kategori FROM tb_menu JOIN tb_kategori ON tb_menu.id_kategori = tb_kategori.id_kategori")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head><title>Manajemen Menu - Admin</title></head>
<body>
    <h2>Tambah Menu Baru</h2>
    <form method="POST">
        <select name="id_kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach($kategori as $k): ?>
                <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <input type="text" name="nama_menu" placeholder="Nama Menu" required><br><br>
        <input type="number" name="harga" placeholder="Harga" required><br><br>
        <input type="number" name="stok" placeholder="Stok Awal" required><br><br>
        <button type="submit" name="tambah_menu">Simpan Menu</button>
    </form>

    <h3>Daftar Menu Cafe</h3>
    <table border="1" cellpadding="10">
        <tr><th>Kategori</th><th>Nama Menu</th><th>Harga</th><th>Stok</th></tr>
        <?php foreach ($menu as $m) : ?>
        <tr>
            <td><?= $m['nama_kategori']; ?></td>
            <td><?= $m['nama_menu']; ?></td>
            <td>Rp <?= number_format($m['harga'], 0, ',', '.'); ?></td>
            <td><?= $m['stok']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br><a href="dashboard.php">Kembali ke Dashboard</a>
</body>
</html>