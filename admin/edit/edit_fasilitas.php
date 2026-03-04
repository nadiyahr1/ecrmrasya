<?php
require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id     = $_POST['id_fasilitas'];
    $nama   = $_POST['nama_fasilitas'];
    $harga  = $_POST['harga'];
    $satuan = $_POST['satuan'];
    $desc   = $_POST['deskripsi'];
    $lama   = $_POST['foto_lama']; // Pastikan di HTML <input name="foto_lama">
    $status = isset($_POST['status_fasilitas']) ? $_POST['status_fasilitas'] : 'Tersedia';

    if ($_FILES['foto']['name'] != '') {
        $foto_name = $_FILES['foto']['name'];
        $ekstensi  = pathinfo($foto_name, PATHINFO_EXTENSION);
        $nama_baru = "FAC-" . date('YmdHis') . "." . $ekstensi;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], "../../assets/gambar/fasilitas/" . $nama_baru)) {
            // Hapus foto lama jika ada dan file-nya eksis di folder
            if (!empty($lama) && file_exists("../../assets/gambar/fasilitas/" . $lama)) { 
                unlink("../../assets/gambar/fasilitas/" . $lama); 
            }
            
            $stmt = $conn->prepare("UPDATE tb_fasilitas SET nama_fasilitas=?, harga=?, satuan=?, deskripsi=?, foto_fasilitas=?, status_fasilitas=? WHERE id_fasilitas=?");
            $stmt->execute([$nama, $harga, $satuan, $desc, $nama_baru, $status, $id]);
        }
    } else {
        // PERBAIKAN: Menambahkan koma setelah satuan=? agar tidak error SQL
        $stmt = $conn->prepare("UPDATE tb_fasilitas SET nama_fasilitas=?, harga=?, satuan=?, deskripsi=?, status_fasilitas=? WHERE id_fasilitas=?");
        $stmt->execute([$nama, $harga, $satuan, $desc, $status, $id]);
    }
    
    echo "<script>alert('Data diperbarui!'); window.location.href='../index.php?page=fasilitas';</script>";
}
?>