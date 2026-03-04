<?php
session_start();
require_once '../config/koneksi.php';

$id_f = $_POST['id_fasilitas'];
$satuan = $_POST['satuan'];
$tgl_sewa = $_POST['tgl_sewa'];
$jam_mulai = $_POST['jam_mulai'];

if ($satuan == 'Jam') {
    $durasi = (int)$_POST['durasi'];
    // HITUNG JAM SELESAI: Jam Mulai + Durasi Jam
    $jam_selesai = date('H:i:s', strtotime($jam_mulai) + ($durasi * 3600));

    // VALIDASI BENTROK
    $query_cek = "
        SELECT bf.jam_mulai, bf.jam_selesai 
        FROM tb_booking_fasilitas bf
        JOIN tb_pesanan p ON bf.id_pesanan = p.id_pesanan
        WHERE bf.id_fasilitas = ? 
        AND bf.tgl_sewa = ? 
        AND p.status_pesanan != 'Dibatalkan' 
        AND (? < bf.jam_selesai AND ? > bf.jam_mulai)
    ";
    
    $stmt_cek = $conn->prepare($query_cek);
    $stmt_cek->execute([$id_f, $tgl_sewa, $jam_mulai, $jam_selesai]);
    
    if ($stmt_cek->rowCount() > 0) {
        echo "<script>
            alert('Maaf, jam tersebut sudah dipesan. Silakan pilih jam lain.');
            window.location.href = '../fasilitas/booking_fasilitas.php?id=$id_f';
        </script>";
        exit;
    }
    $pengali = $durasi;
} else {
    $pengali = (int)$_POST['jumlah_orang'];
    $jam_selesai = NULL; 
}

$_SESSION['keranjang_fasilitas'][$id_f] = [
    'id_fasilitas' => $id_f,
    'tgl_sewa'     => $tgl_sewa,
    'jam_mulai'    => $jam_mulai,
    'jam_selesai'  => $jam_selesai, // Simpan ke session
    'pengali'      => $pengali, 
    'satuan'       => $satuan
];

header("Location: ../keranjang.php");
exit;