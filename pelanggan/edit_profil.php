<?php
session_start();
require_once '../config/koneksi.php';
include '../layout/header.php';

// Proteksi: Hanya Pelanggan yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Pelanggan') {
    header("Location: ../index.php"); exit;
}

$id_m = $_SESSION['id_member'];

// 1. Ambil data lama member
$stmt = $conn->prepare("SELECT * FROM tb_member WHERE id_member = ?");
$stmt->execute([$id_m]);
$user = $stmt->fetch();

// 2. Logika Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama    = $_POST['nama'];
    $no_telp = $_POST['no_telp'];
    $pass_baru = $_POST['password'];

    try {
        if (!empty($pass_baru)) {
            // Jika ganti password, gunakan password_hash
            $password_fix = password_hash($pass_baru, PASSWORD_DEFAULT);
            $sql = "UPDATE tb_member SET nama_member = ?, no_telp = ?, password = ? WHERE id_member = ?";
            $stmt_up = $conn->prepare($sql);
            $stmt_up->execute([$nama, $no_telp, $password_fix, $id_m]);
        } else {
            // Jika password dikosongkan, jangan update kolom password
            $sql = "UPDATE tb_member SET nama_member = ?, no_telp = ? WHERE id_member = ?";
            $stmt_up = $conn->prepare($sql);
            $stmt_up->execute([$nama, $no_telp, $id_m]);
        }

        // Update session nama agar di navbar langsung berubah
        $_SESSION['nama'] = $nama;

        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profil.php';</script>";
    } catch (PDOException $e) {
        echo "Gagal update: " . $e->getMessage();
    }
}
?>

<div style="max-width: 500px; margin: 40px auto; padding: 20px; background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
    <h3 style="color: #6F4E37;">Edit Profil Saya</h3>
    <p style="color: #888; font-size: 14px;">Pastikan data diri Anda valid untuk memudahkan proses verifikasi pesanan.</p>
    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

    <form method="POST">
        <label style="font-size: 14px; font-weight: bold;">Nama Lengkap:</label>
        <input type="text" name="nama" value="<?= $user['nama_member'] ?>" required 
               style="width: 100%; padding: 10px; margin: 10px 0 20px 0; border: 1px solid #ddd; border-radius: 8px;">

        <label style="font-size: 14px; font-weight: bold;">Nomor Telepon/WA:</label>
        <input type="text" name="no_telp" value="<?= $user['no_telp'] ?>" required 
               style="width: 100%; padding: 10px; margin: 10px 0 20px 0; border: 1px solid #ddd; border-radius: 8px;">

        <label style="font-size: 14px; font-weight: bold;">Ganti Password:</label>
        <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti" 
               style="width: 100%; padding: 10px; margin: 10px 0 5px 0; border: 1px solid #ddd; border-radius: 8px;">
        <small style="color: #999; display: block; margin-bottom: 25px;">*Biarkan kosong jika tetap menggunakan password lama.</small>

        <div style="display: flex; gap: 10px;">
            <a href="profil.php" style="flex: 1; text-align: center; padding: 12px; background: #eee; color: #333; text-decoration: none; border-radius: 8px; font-weight: bold;">Batal</a>
            <button type="submit" style="flex: 2; padding: 12px; background: #6F4E37; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php include '../layout/footer.php'; ?>