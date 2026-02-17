<?php
session_start();
require_once '../config/koneksi.php';

if (isset($_GET['id'])) {
    $id_p = $_GET['id'];

    // Ambil data pesanan untuk cek tipe pemesanan
    $stmt = $conn->prepare("SELECT tipe_pemesanan FROM tb_pesanan WHERE id_pesanan = ?");
    $stmt->execute([$id_p]);
    $p = $stmt->fetch();

    if ($p) {
        // Logika Kondisional UI yang kamu inginkan:
        // Jika Ambil di Tempat -> Status jadi 'Dapat Diambil'
        // Jika Makan di Tempat -> Status jadi 'Diproses'
        $status_baru = ($p['tipe_pemesanan'] == 'Ambil di Cafe') ? 'Dapat Diambil' : 'Diproses';

        try {
            $conn->prepare("UPDATE tb_pesanan SET status = ? WHERE id_pesanan = ?")->execute([$status_baru, $id_p]);

            echo "<script>
                    alert('Pesanan Diverifikasi! Status sekarang: $status_baru');
                    // Gunakan perintah ini untuk membuka struk
                    var strukWindow = window.open('cetak_struk.php?id=$id_p', '_blank');
            
                    // Jika window.open diblokir, arahkan manual
                    if(!strukWindow || strukWindow.closed || typeof strukWindow.closed=='undefined') { 
                    alert('Pop-up diblokir! Silahkan izinkan pop-up di browser kamu atau buka manual.');
                    }
                    window.location='data_pesanan.php';
                  </script>";
        } catch (PDOException $e) {
            echo "Gagal verifikasi: " . $e->getMessage();
        }
    }
}
